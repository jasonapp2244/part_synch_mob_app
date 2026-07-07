<?php

namespace App\Mail;

use App\Models\VendorBoost;
use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Carbon\Carbon;

class BoostPurchaseMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     */
    public $tries = 3;

    /**
     * The number of seconds to wait before retrying the job.
     */
    public $backoff = [60, 180, 300]; // Retry after 1min, 3min, 5min

    public $vendorBoost;
    public $vendor;
    public $package;
    public $products;
    public $recipientType; // 'vendor' or 'admin'
    public $remainingDays;
    public $remainingHours;

    /**
     * Create a new message instance.
     */
    public function __construct(VendorBoost $vendorBoost, $recipientType = 'vendor')
    {
        $this->vendorBoost = $vendorBoost;
        $this->vendor = $vendorBoost->vendor;
        $this->package = $vendorBoost->package;
        $this->products = $vendorBoost->boostedProducts()->with('product')->get();
        $this->recipientType = $recipientType;
        
        // Calculate remaining time
        $now = Carbon::now();
        $endDate = Carbon::parse($vendorBoost->end_date);
        $this->remainingDays = $now->diffInDays($endDate, false);
        $this->remainingHours = $now->diffInHours($endDate, false);
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $subject = $this->recipientType === 'admin' 
            ? 'New Boost Purchase: ' . $this->vendor->first_name . ' - ' . $this->package->name
            : 'Boost Purchase Confirmed: ' . $this->package->name;

        return $this->subject($subject)
                    ->view('Mails.boost_purchase');
    }
}

