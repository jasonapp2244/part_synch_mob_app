<?php

namespace App\Http\Controllers\Vendor;

use App\Models\Product;
use App\Models\VendorBoost;
use App\Models\BoostPackage;
use App\Models\BoostPosition;
use App\Models\BoostedProduct;
use App\Models\VendorPayment;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Services\StripeService;
use App\Services\PaypalService;
use App\Mail\BoostPurchaseMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class VendorBoostController extends Controller
{
    /**
     * Send boost purchase email with error handling
     */
    private function sendBoostEmail($vendorBoost, $email, $recipientType, $context = '')
    {
        try {
            // Queue email for background processing
            Mail::to($email)->queue(new BoostPurchaseMail($vendorBoost, $recipientType));
            Log::info("Boost email queued successfully {$context}", [
                'email' => $email,
                'recipient_type' => $recipientType,
                'vendor_boost_id' => $vendorBoost->id
            ]);
            return true;
        } catch (\Exception $e) {
            Log::error("Failed to queue boost email {$context}", [
                'email' => $email,
                'recipient_type' => $recipientType,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'vendor_boost_id' => $vendorBoost->id
            ]);
            
            // Try to send directly (fallback) - but don't block the response
            try {
                Mail::to($email)->send(new BoostPurchaseMail($vendorBoost, $recipientType));
                Log::info("Boost email sent directly (fallback) {$context}", [
                    'email' => $email,
                    'vendor_boost_id' => $vendorBoost->id
                ]);
            } catch (\Exception $fallbackException) {
                Log::error("Fallback email send also failed {$context}", [
                    'email' => $email,
                    'error' => $fallbackException->getMessage(),
                    'vendor_boost_id' => $vendorBoost->id
                ]);
            }
            
            return false;
        }
    }

    /**
     * Get all available boost packages
     */
    public function getBoostPackages()
    {
        $packages = BoostPackage::where('status', true)
            ->orderBy('price', 'asc')
            ->get();

        return response()->json([
            'status' => true,
            'message' => 'Boost packages fetched successfully',
            'data' => $packages
        ]);
    }

    /**
     * Get all packages with position-wise pricing
     */
    public function getBoostPricing()
    {
        $packages = BoostPackage::where('status', true)->orderBy('price', 'asc')->get();
        $positions = BoostPosition::where('status', true)->orderBy('priority', 'asc')->get();

        $data = $packages->map(function ($package) use ($positions) {
            return [
                'package_id' => $package->id,
                'package' => $package->name,
                'duration_days' => $package->duration_days,
                'product_limit' => $package->product_limit,
                'base_price' => $package->price,
                'currency' => $package->currency,
                'positions' => $positions->map(function ($position) use ($package) {
                    return [
                        'position_id' => $position->id,
                        'name' => $position->name,
                        'price_multiplier' => $position->price_multiplier,
                        'final_price' => round($package->price * $position->price_multiplier, 2)
                    ];
                })
            ];
        });

        return response()->json([
            'status' => true,
            'message' => 'Boost pricing fetched successfully',
            'data' => $data
        ]);
    }

    /**
     * Get price preview for package and position combination
     */
    public function getPricePreview(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'package_id' => 'required|exists:boost_packages,id',
            'position_id' => 'required|exists:boost_positions,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 400);
        }

        $package = BoostPackage::find($request->package_id);
        $position = BoostPosition::find($request->position_id);

        $finalPrice = round($package->price * $position->price_multiplier, 2);

        return response()->json([
            'status' => true,
            'message' => 'Price preview',
            'data' => [
                'package_name' => $package->name,
                'position_name' => $position->name,
                'base_price' => $package->price,
                'price_multiplier' => $position->price_multiplier,
                'final_price' => $finalPrice,
                'duration_days' => $package->duration_days,
                'product_limit' => $package->product_limit,
                'currency' => $package->currency
            ]
        ]);
    }

    /**
     * Get boost positions (Top, Middle, Bottom sections)
     */
    public function getBoostPositions()
    {
        $positions = BoostPosition::where('status', true)
            ->orderBy('priority', 'asc')
            ->get();

        return response()->json([
            'status' => true,
            'message' => 'Boost positions fetched successfully',
            'data' => $positions
        ]);
    }

    /**
     * Purchase a boost package
     */
    public function purchaseBoost(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'package_id' => 'required|exists:boost_packages,id',
            'boost_position_id' => 'nullable|exists:boost_positions,id',
            'product_ids' => 'required|array|min:1',
            'product_ids.*' => 'exists:products,id',
            'payment_method' => 'required|in:stripe,paypal'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 400);
        }

        $vendor = Auth::user();

        // Check if vendor
        if ($vendor->role_id != 2) {
            return response()->json([
                'status' => false,
                'message' => 'Only vendors can purchase boosts'
            ], 403);
        }

        $package = BoostPackage::findOrFail($request->package_id);
        
        // Get position for price calculation
        $position = $request->boost_position_id 
            ? BoostPosition::find($request->boost_position_id) 
            : null;
        $priceMultiplier = $position ? $position->price_multiplier : 1.00;
        $finalPrice = round($package->price * $priceMultiplier, 2);

        // Check product limit
        if (count($request->product_ids) > $package->product_limit) {
            return response()->json([
                'status' => false,
                'message' => "You can only boost {$package->product_limit} products with this package"
            ], 400);
        }

        // Verify all products belong to this vendor
        $products = Product::whereIn('id', $request->product_ids)
            ->where('user_id', $vendor->id)
            ->get();

        if ($products->count() != count($request->product_ids)) {
            return response()->json([
                'status' => false,
                'message' => 'Some products do not belong to you'
            ], 400);
        }

        // Check if any product already has an active boost for this vendor
        $now = now();
        $activeBoostedProducts = BoostedProduct::whereIn('product_id', $request->product_ids)
            ->where('is_active', true)
            ->where('boost_end', '>', $now)
            ->whereHas('vendorBoost', function ($query) use ($vendor) {
                $query->where('vendor_id', $vendor->id)
                    ->where('is_active', true)
                    ->where('payment_status', 'success');
            })
            ->with(['product', 'vendorBoost'])
            ->get();

        if ($activeBoostedProducts->count() > 0) {
            $productNames = $activeBoostedProducts->pluck('product.name')->unique()->implode(', ');
            $boostEndDates = $activeBoostedProducts->pluck('boost_end')->map(function ($date) {
                return $date->format('Y-m-d H:i:s');
            })->unique()->implode(', ');
            
            return response()->json([
                'status' => false,
                'message' => 'This product already has an active boost. Please delete the existing boost before adding a new one.',
                'conflicting_products' => $activeBoostedProducts->map(function ($bp) {
                    return [
                        'product_id' => $bp->product_id,
                        'product_name' => $bp->product->name ?? 'N/A',
                        'boost_end' => $bp->boost_end->format('Y-m-d H:i:s')
                    ];
                }),
                'delete_endpoint' => '/api/vendor/boost/delete'
            ], 400);
        }

        DB::beginTransaction();
        try {

            // Create vendor boost record
            $vendorBoost = VendorBoost::create([
                'vendor_id' => $vendor->id,
                'package_id' => $package->id,
                'boost_position_id' => $request->boost_position_id,
                'amount' => $finalPrice,
                'currency' => $package->currency,
                'start_date' => now(),
                'end_date' => now()->addDays($package->duration_days),
                'payment_status' => 'pending',
                'payment_method' => $request->payment_method,
                'is_active' => false
            ]);

            // Create payment intent/URL based on payment method
            if ($request->payment_method == 'stripe') {
                $stripeService = new StripeService();
                $paymentData = $stripeService->createPaymentIntent($finalPrice, $package->currency);

                // Store payment intent ID in metadata
                $vendorBoost->update([
                    'metadata' => ['payment_intent_id' => $paymentData['payment_intent_id']]
                ]);

                // Commit transaction before returning
                DB::commit();

                return response()->json([
                    'status' => true,
                    'message' => 'Payment intent created',
                    'vendor_boost_id' => $vendorBoost->id,
                    'client_secret' => $paymentData['client_secret'],
                    'payment_intent_id' => $paymentData['payment_intent_id'],
                    'amount' => $finalPrice,
                    'base_price' => $package->price,
                    'price_multiplier' => $priceMultiplier,
                    'currency' => $package->currency
                ]);
            } else {
                $paypalService = new PaypalService();
                $approvalUrl = $paypalService->createPaypalPayment($finalPrice, $package->currency);

                // Commit transaction before returning
                DB::commit();

                return response()->json([
                    'status' => true,
                    'message' => 'PayPal payment created',
                    'vendor_boost_id' => $vendorBoost->id,
                    'approval_url' => $approvalUrl,
                    'amount' => $finalPrice,
                    'base_price' => $package->price,
                    'price_multiplier' => $priceMultiplier,
                    'currency' => $package->currency
                ]);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Failed to create boost purchase',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Confirm boost payment and activate boost
     */
    public function confirmBoostPayment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'vendor_boost_id' => 'required|exists:vendor_boosts,id',
            'payment_token' => 'required',
            'payment_method' => 'required|in:stripe,paypal',
            'PayerID' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 400);
        }

        $vendor = Auth::user();
        $vendorBoost = VendorBoost::where('id', $request->vendor_boost_id)
            ->where('vendor_id', $vendor->id)
            ->firstOrFail();

        if ($vendorBoost->payment_status == 'success') {
            return response()->json([
                'status' => false,
                'message' => 'This boost has already been paid'
            ], 400);
        }

        // Get product IDs from request (they should be sent)
        $productIds = $request->product_ids ?? [];

        if (empty($productIds)) {
            return response()->json([
                'status' => false,
                'message' => 'Product IDs are required'
            ], 400);
        }

        // Check if any product already has an active boost for this vendor
        $now = now();
        $activeBoostedProducts = BoostedProduct::whereIn('product_id', $productIds)
            ->where('is_active', true)
            ->where('boost_end', '>', $now)
            ->whereHas('vendorBoost', function ($query) use ($vendor) {
                $query->where('vendor_id', $vendor->id)
                    ->where('is_active', true)
                    ->where('payment_status', 'success');
            })
            ->with(['product', 'vendorBoost'])
            ->get();

        if ($activeBoostedProducts->count() > 0) {
            $productNames = $activeBoostedProducts->pluck('product.name')->unique()->implode(', ');
            $boostEndDates = $activeBoostedProducts->pluck('boost_end')->map(function ($date) {
                return $date->format('Y-m-d H:i:s');
            })->unique()->implode(', ');
            
            return response()->json([
                'status' => false,
                'message' => 'This product already has an active boost. Please delete the existing boost before adding a new one.',
                'conflicting_products' => $activeBoostedProducts->map(function ($bp) {
                    return [
                        'product_id' => $bp->product_id,
                        'product_name' => $bp->product->name ?? 'N/A',
                        'boost_end' => $bp->boost_end->format('Y-m-d H:i:s')
                    ];
                }),
                'delete_endpoint' => '/api/vendor/boost/delete'
            ], 400);
        }

        DB::beginTransaction();
        try {
            // Verify payment
            if ($request->payment_method == 'stripe') {
                $stripeService = new StripeService();
                $stripeService->verifyPaymentIntent($request->payment_token);
            } else {
                $paypalService = new PaypalService();
                $paypalService->verifyPaypalPayment(
                    $request->payment_token,
                    $request->PayerID ?? $request->payerID
                );
            }

            // Update vendor boost
            $vendorBoost->update([
                'payment_status' => 'success',
                'transaction_id' => $request->payment_token,
                'is_active' => true,
                'start_date' => now(),
                'end_date' => now()->addDays($vendorBoost->package->duration_days)
            ]);

            // Verify products belong to vendor
            $products = Product::whereIn('id', $productIds)
                ->where('user_id', $vendor->id)
                ->get();

            if ($products->count() != count($productIds)) {
                DB::rollBack();
                return response()->json([
                    'status' => false,
                    'message' => 'Some products do not belong to you'
                ], 400);
            }

            // Create boosted products
            foreach ($products as $product) {
                BoostedProduct::create([
                    'vendor_boost_id' => $vendorBoost->id,
                    'product_id' => $product->id,
                    'boost_start' => now(),
                    'boost_end' => $vendorBoost->end_date,
                    'is_active' => true
                ]);

                // Update product is_top flag
                $product->update([
                    'is_top' => true,
                    'top_start_date' => now(),
                    'top_expire_date' => $vendorBoost->end_date
                ]);
            }

            // Create vendor payment record
            if ($vendor->vendor_type_id) {
                VendorPayment::create([
                    'user_id' => $vendor->id,
                    'vendor_id' => $vendor->vendor_type_id, // vendor_type_id from users table
                    'amount' => $vendorBoost->amount,
                    'status' => 'paid'
                ]);
            }

            DB::commit();

            // Send emails to vendor and admin (using queue for better reliability)
            $this->sendBoostEmail($vendorBoost, $vendor->email, 'vendor', '(Payment Confirmed)');

                // Email to admin
                $adminEmail = env('Admin_Email');
                if ($adminEmail) {
                $this->sendBoostEmail($vendorBoost, $adminEmail, 'admin', '(Payment Confirmed)');
            } else {
                Log::warning('Admin email not configured', [
                    'vendor_boost_id' => $vendorBoost->id
                ]);
            }

            return response()->json([
                'status' => true,
                'message' => 'Boost activated successfully',
                'data' => $vendorBoost->load(['package', 'position', 'boostedProducts.product'])
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Payment verification failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Boost existing products (select through checkbox)
     */
    public function boostExistingProducts(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'package_id' => 'required|exists:boost_packages,id',
            'product_ids' => 'required|array|min:1',
            'product_ids.*' => 'exists:products,id',
            'boost_position_id' => 'nullable|exists:boost_positions,id',
            'payment_method' => 'required|in:stripe,paypal'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 400);
        }

        // Use the same purchase boost logic
        return $this->purchaseBoost($request);
    }

    /**
     * Get vendor's active boosts
     */
    public function getMyBoosts()
    {
        $vendor = Auth::user();

        $boosts = VendorBoost::where('vendor_id', $vendor->id)
            ->with(['package', 'position', 'boostedProducts.product.productImages'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'status' => true,
            'message' => 'Your boosts fetched successfully',
            'data' => $boosts
        ]);
    }

    /**
     * Cancel boost purchase (before payment)
     */
    public function cancelBoostPurchase(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'vendor_boost_id' => 'required|exists:vendor_boosts,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 400);
        }

        $vendor = Auth::user();
        $vendorBoost = VendorBoost::where('id', $request->vendor_boost_id)
            ->where('vendor_id', $vendor->id)
            ->firstOrFail();

        // Only allow cancellation if payment is pending
        if ($vendorBoost->payment_status !== 'pending') {
            return response()->json([
                'status' => false,
                'message' => 'Cannot cancel boost. Payment status is: ' . $vendorBoost->payment_status
            ], 400);
        }

        try {
            // Update payment status to cancelled
            $vendorBoost->update([
                'payment_status' => 'failed',
                'is_active' => false
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Boost purchase cancelled successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to cancel boost purchase',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get vendor's active boosted products
     */
    public function getMyBoostedProducts()
    {
        $vendor = Auth::user();

        $boostedProducts = BoostedProduct::whereHas('vendorBoost', function ($query) use ($vendor) {
                $query->where('vendor_id', $vendor->id)
                    ->where('is_active', true)
                    ->where('payment_status', 'success');
            })
            ->where('is_active', true)
            ->where('boost_end', '>', now())
            ->with(['product.productImages', 'vendorBoost.package', 'vendorBoost.position'])
            ->get();

        return response()->json([
            'status' => true,
            'message' => 'Boosted products fetched successfully',
            'data' => $boostedProducts
        ]);
    }

    /**
     * Delete active boost for specific products
     * Allows vendor to delete active boost so they can add new boost for same products
     */
    public function deleteBoost(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_ids' => 'required|array|min:1',
            'product_ids.*' => 'exists:products,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 400);
        }

        $vendor = Auth::user();

        // Check if vendor
        if ($vendor->role_id != 2) {
            return response()->json([
                'status' => false,
                'message' => 'Only vendors can delete boosts'
            ], 403);
        }

        // Verify all products belong to this vendor
        $products = Product::whereIn('id', $request->product_ids)
            ->where('user_id', $vendor->id)
            ->get();

        if ($products->count() != count($request->product_ids)) {
            return response()->json([
                'status' => false,
                'message' => 'Some products do not belong to you'
            ], 400);
        }

        DB::beginTransaction();
        try {
            $now = now();
            $deletedCount = 0;
            $productIds = $products->pluck('id')->toArray();

            // Find all active boosted products for these products and this vendor
            $activeBoostedProducts = BoostedProduct::whereIn('product_id', $productIds)
                ->where('is_active', true)
                ->where('boost_end', '>', $now)
                ->whereHas('vendorBoost', function ($query) use ($vendor) {
                    $query->where('vendor_id', $vendor->id)
                        ->where('is_active', true)
                        ->where('payment_status', 'success');
                })
                ->with(['vendorBoost', 'product'])
                ->get();

            if ($activeBoostedProducts->count() == 0) {
                return response()->json([
                    'status' => false,
                    'message' => 'No active boosts found for these products'
                ], 404);
            }

            // Group by vendor_boost_id to handle multiple products in same boost
            $boostIds = $activeBoostedProducts->pluck('vendor_boost_id')->unique();
            
            // Deactivate all boosted products
            foreach ($activeBoostedProducts as $boostedProduct) {
                $boostedProduct->update(['is_active' => false]);
                $deletedCount++;
            }

            // Deactivate vendor boosts if all their products are deactivated
            foreach ($boostIds as $boostId) {
                $boost = VendorBoost::find($boostId);
                if ($boost) {
                    $remainingActiveProducts = BoostedProduct::where('vendor_boost_id', $boostId)
                        ->where('is_active', true)
                        ->where('boost_end', '>', $now)
                        ->count();
                    
                    if ($remainingActiveProducts == 0) {
                        $boost->update(['is_active' => false]);
                    }
                }
            }

            // Remove is_top flag from products if no other active boosts exist
            foreach ($products as $product) {
                $otherActiveBoosts = BoostedProduct::where('product_id', $product->id)
                    ->where('is_active', true)
                    ->where('boost_end', '>', $now)
                    ->whereHas('vendorBoost', function ($query) {
                        $query->where('is_active', true)
                            ->where('payment_status', 'success');
                    })
                    ->count();

                if ($otherActiveBoosts == 0) {
                    $product->update([
                        'is_top' => false,
                        'top_start_date' => null,
                        'top_expire_date' => null
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => "Successfully deleted {$deletedCount} active boost(s). You can now add new boost for these products.",
                'deleted_count' => $deletedCount,
                'product_ids' => $productIds
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete boost', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'vendor_id' => $vendor->id,
                'product_ids' => $request->product_ids
            ]);
            
            return response()->json([
                'status' => false,
                'message' => 'Failed to delete boost',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Test endpoint - Skip payment verification (Development/Testing only)
     * Use this for Postman testing when you can't complete Stripe payment
     */
    public function testConfirmBoostPayment(Request $request)
    {
        // Only allow in non-production environment
        if (app()->environment('production')) {
            return response()->json([
                'status' => false,
                'message' => 'This endpoint is not available in production'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'vendor_boost_id' => 'required|exists:vendor_boosts,id',
            'product_ids' => 'required|array|min:1',
            'product_ids.*' => 'exists:products,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 400);
        }

        $vendor = Auth::user();
        $vendorBoost = VendorBoost::where('id', $request->vendor_boost_id)
            ->where('vendor_id', $vendor->id)
            ->firstOrFail();

        if ($vendorBoost->payment_status == 'success') {
            return response()->json([
                'status' => false,
                'message' => 'This boost has already been paid'
            ], 400);
        }

        // Check if any product already has an active boost for this vendor
        $now = now();
        $activeBoostedProducts = BoostedProduct::whereIn('product_id', $request->product_ids)
            ->where('is_active', true)
            ->where('boost_end', '>', $now)
            ->whereHas('vendorBoost', function ($query) use ($vendor) {
                $query->where('vendor_id', $vendor->id)
                    ->where('is_active', true)
                    ->where('payment_status', 'success');
            })
            ->with(['product', 'vendorBoost'])
            ->get();

        if ($activeBoostedProducts->count() > 0) {
            $productNames = $activeBoostedProducts->pluck('product.name')->unique()->implode(', ');
            $boostEndDates = $activeBoostedProducts->pluck('boost_end')->map(function ($date) {
                return $date->format('Y-m-d H:i:s');
            })->unique()->implode(', ');
            
            return response()->json([
                'status' => false,
                'message' => 'This product already has an active boost. Please delete the existing boost before adding a new one.',
                'conflicting_products' => $activeBoostedProducts->map(function ($bp) {
                    return [
                        'product_id' => $bp->product_id,
                        'product_name' => $bp->product->name ?? 'N/A',
                        'boost_end' => $bp->boost_end->format('Y-m-d H:i:s')
                    ];
                }),
                'delete_endpoint' => '/api/vendor/boost/delete'
            ], 400);
        }

        DB::beginTransaction();
        try {
            // Skip payment verification - Directly activate (TEST MODE)
            $vendorBoost->update([
                'payment_status' => 'success',
                'transaction_id' => 'test_' . time(),
                'is_active' => true,
                'start_date' => now(),
                'end_date' => now()->addDays($vendorBoost->package->duration_days)
            ]);

            $products = Product::whereIn('id', $request->product_ids)
                ->where('user_id', $vendor->id)
                ->get();

            if ($products->count() != count($request->product_ids)) {
                DB::rollBack();
                return response()->json([
                    'status' => false,
                    'message' => 'Some products do not belong to you'
                ], 400);
            }

            // Create boosted products
            foreach ($products as $product) {
                BoostedProduct::create([
                    'vendor_boost_id' => $vendorBoost->id,
                    'product_id' => $product->id,
                    'boost_start' => now(),
                    'boost_end' => $vendorBoost->end_date,
                    'is_active' => true
                ]);

                $product->update([
                    'is_top' => true,
                    'top_start_date' => now(),
                    'top_expire_date' => $vendorBoost->end_date
                ]);
            }

            // Create vendor payment record
            if ($vendor->vendor_type_id) {
                VendorPayment::create([
                    'user_id' => $vendor->id,
                    'vendor_id' => $vendor->vendor_type_id, // vendor_type_id from users table
                    'amount' => $vendorBoost->amount,
                    'status' => 'paid'
                ]);
            }

            DB::commit();

            // Send emails (using queue for better reliability)
            $this->sendBoostEmail($vendorBoost, $vendor->email, 'vendor', '(TEST MODE)');

            // Email to admin
                $adminEmail = env('Admin_Email');
                if ($adminEmail) {
                $this->sendBoostEmail($vendorBoost, $adminEmail, 'admin', '(TEST MODE)');
            } else {
                Log::warning('Admin email not configured (TEST MODE)', [
                    'vendor_boost_id' => $vendorBoost->id
                ]);
            }

            return response()->json([
                'status' => true,
                'message' => 'Boost activated successfully (TEST MODE - Payment verification skipped)',
                'data' => $vendorBoost->load(['package', 'position', 'boostedProducts.product'])
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Failed to activate boost',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

