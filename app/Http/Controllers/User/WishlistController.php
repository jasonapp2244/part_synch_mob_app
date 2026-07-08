<?php

namespace App\Http\Controllers\User;

use App\Models\User;
use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\WishlistNotificationMail;

class WishlistController extends Controller
{
    public function storeWishlist(Request $request)
    {
        $request->validate([
            'type' => 'required|in:product,vendor',
            'product_id' => 'nullable|exists:products,id',
            'vendor_id' => 'nullable|exists:users,id',
        ]);

        $userId = Auth::id();
        $user = Auth::user();

        try {
            $data = [
                'type' => $request->type,
                'by_user_name' => $user->first_name,
                'by_user_email' => $user->email,
            ];

            // Check if already in wishlist
            $exists = Wishlist::where('user_id', $userId)
                ->when($request->type === 'product', function ($query) use ($request) {
                    return $query->where('product_id', $request->product_id);
                })
                ->when($request->type === 'vendor', function ($query) use ($request) {
                    return $query->where('vendor_id', $request->vendor_id);
                })
                ->where('type', $request->type)
                ->exists();

            if ($exists) {
                return response()->json([
                    'status' => false,
                    'message' => "Product / Vendor is already in your wishlist.",
                ], 400);
            }

            // Prepare email data for product wishlist
            if ($request->type === 'product' && $request->product_id) {
                $product = Product::with(['productImages', 'user'])->find($request->product_id);
                if (!$product) {
                    return response()->json(['status' => false, 'message' => 'Product not found.'], 400);
                }

                $data['product_id'] = $product->id;
                $data['product_name'] = $product->name ?? '';
                $data['product_image'] = $product->productImages->first()->image_url ?? 'https://via.placeholder.com/200';
                $data['vendor_id'] = $product->user->id ?? null;
                $data['vendor_name'] = $product->user->first_name ?? '';
                $data['product_url'] = route('product.details', $product->id);

                Mail::to($user->email)->send(new WishlistNotificationMail($data));
                if (!empty($product->user->email)) {
                    Mail::to($product->user->email)->send(new WishlistNotificationMail($data));
                }
            }

            // Prepare email data for vendor wishlist
            if ($request->type === 'vendor' && $request->vendor_id) {
                $vendor = User::where('id', $request->vendor_id)->where('role_id', 2)->first();
                if (!$vendor) {
                    return response()->json(['status' => false, 'message' => 'Vendor not found.'], 400);
                }
                $data['vendor_id'] = $vendor->id;
                $data['vendor_name'] = $vendor->first_name ?? '';
                $data['vendor_logo'] = $vendor->profile_image ?? 'https://via.placeholder.com/200';
                $data['vendor_url'] = route('vendor.store', $vendor->id);

                Mail::to($user->email)->send(new WishlistNotificationMail($data));
                Mail::to($vendor->email)->send(new WishlistNotificationMail($data));
            }

            $wishlist = Wishlist::create([
                'user_id' => $userId,
                'product_id' => $request->product_id,
                'vendor_id' => $request->vendor_id,
                'type' => $request->type,
                'status' => 'save',
                'process_status' => 'inactive',
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Added to wishlist successfully. Notifications sent.',
                'data' => $wishlist
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to save wishlist.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function removeWishlist(Request $request)
    {
        $request->validate([
            'wishlist_id' => 'required|exists:wishlists,id',
        ]);

        $wishlist = Wishlist::where('id', $request->wishlist_id)
            ->where('user_id', Auth::id())
            ->first();

        if (!$wishlist) {
            return response()->json([
                'status' => false,
                'message' => 'Wishlist item not found.',
            ], 404);
        }

        $wishlist->delete();

        return response()->json([
            'status' => true,
            'message' => 'Removed from wishlist successfully.',
        ]);
    }
}
