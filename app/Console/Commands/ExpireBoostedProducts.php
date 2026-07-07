<?php

namespace App\Console\Commands;

use App\Models\VendorBoost;
use App\Models\BoostedProduct;
use App\Models\Product;
use App\Mail\BoostExpiryMail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class ExpireBoostedProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'boost:expire';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Expire boosted products that have passed their end date';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $startTime = Carbon::now();
        $this->info('Starting boost expiration check...');
        Log::info('Boost expiration check started', [
            'started_at' => $startTime->toDateTimeString(),
            'timezone' => config('app.timezone')
        ]);

        DB::beginTransaction();
        try {
            $now = Carbon::now();
            $this->info("Current time: {$now->toDateTimeString()} (Timezone: " . config('app.timezone') . ")");

            // 1. Expire vendor boosts that have passed end_date
            $expiredBoosts = VendorBoost::where('is_active', true)
                ->where('end_date', '<=', $now)
                ->with(['vendor', 'package', 'position'])
                ->get();

            $expiredBoostCount = 0;
            $emailsSent = 0;
            foreach ($expiredBoosts as $boost) {
                $boost->update(['is_active' => false]);
                
                // Send expiration email to vendor
                try {
                    if ($boost->vendor && $boost->vendor->email) {
                        Mail::to($boost->vendor->email)->queue(new BoostExpiryMail($boost));
                        $emailsSent++;
                        Log::info('Boost expiry email queued to vendor', [
                            'vendor_boost_id' => $boost->id,
                            'vendor_email' => $boost->vendor->email
                        ]);
                    }
                } catch (\Exception $emailException) {
                    Log::error('Failed to send boost expiry email', [
                        'vendor_boost_id' => $boost->id,
                        'vendor_email' => $boost->vendor->email ?? 'N/A',
                        'error' => $emailException->getMessage()
                    ]);
                }
                
                Log::info('Vendor boost expired', [
                    'vendor_boost_id' => $boost->id,
                    'vendor_id' => $boost->vendor_id,
                    'end_date' => $boost->end_date->toDateTimeString(),
                    'expired_at' => $now->toDateTimeString(),
                    'email_sent' => isset($boost->vendor) && $boost->vendor->email ? true : false
                ]);
                $expiredBoostCount++;
            }

            // 2. Expire boosted products that have passed boost_end
            $expiredBoostedProducts = BoostedProduct::where('is_active', true)
                ->where('boost_end', '<=', $now)
                ->get();

            $expiredProductCount = 0;
            foreach ($expiredBoostedProducts as $boostedProduct) {
                $boostedProduct->update(['is_active' => false]);
                
                // Update product is_top flag
                $product = Product::find($boostedProduct->product_id);
                if ($product) {
                    // Check if product has any other active boosts
                    $hasActiveBoosts = BoostedProduct::where('product_id', $product->id)
                        ->where('is_active', true)
                        ->where('boost_end', '>', $now)
                        ->exists();

                    if (!$hasActiveBoosts) {
                        $product->update([
                            'is_top' => false,
                            'top_expire_date' => null
                        ]);
                        Log::info('Product boost expired and is_top removed', [
                            'product_id' => $product->id,
                            'boosted_product_id' => $boostedProduct->id,
                            'boost_end' => $boostedProduct->boost_end->toDateTimeString(),
                            'expired_at' => $now->toDateTimeString()
                        ]);
                    }
                }
                $expiredProductCount++;
            }

            // 3. Also check products with top_expire_date
            $expiredTopProducts = Product::where('is_top', true)
                ->whereNotNull('top_expire_date')
                ->where('top_expire_date', '<=', $now)
                ->get();

            $expiredTopCount = 0;
            foreach ($expiredTopProducts as $product) {
                // Check if product has any active boosts
                $hasActiveBoosts = BoostedProduct::where('product_id', $product->id)
                    ->where('is_active', true)
                    ->where('boost_end', '>', $now)
                    ->exists();

                if (!$hasActiveBoosts) {
                    $product->update([
                        'is_top' => false,
                        'top_expire_date' => null
                    ]);
                    $expiredTopCount++;
                }
            }

            DB::commit();

            $endTime = Carbon::now();
            $duration = $endTime->diffInSeconds($startTime);
            
            $this->info("Expired {$expiredBoostCount} vendor boosts");
            $this->info("Expired {$expiredProductCount} boosted products");
            $this->info("Expired {$expiredTopCount} top products");
            $this->info("Sent {$emailsSent} expiration reminder emails");
            $this->info('Boost expiration check completed successfully!');
            $this->info("Execution time: {$duration} seconds");

            Log::info('Boost expiration check completed', [
                'expired_boosts' => $expiredBoostCount,
                'expired_boosted_products' => $expiredProductCount,
                'expired_top_products' => $expiredTopCount,
                'expiration_emails_sent' => $emailsSent,
                'started_at' => $startTime->toDateTimeString(),
                'completed_at' => $endTime->toDateTimeString(),
                'duration_seconds' => $duration
            ]);

            return Command::SUCCESS;
        } catch (\Exception $e) {
            DB::rollBack();
            $errorMessage = 'Error expiring boosts: ' . $e->getMessage();
            $this->error($errorMessage);
            Log::error('Boost expiration check failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'started_at' => $startTime->toDateTimeString()
            ]);
            return Command::FAILURE;
        }
    }
}

