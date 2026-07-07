<?php

namespace App\Mail;

use App\Models\VendorBoost;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Carbon\Carbon;

class BoostExpiryMail extends Mailable implements ShouldQueue
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
    public $expiredDate;

    /**
     * Create a new message instance.
     */
    public function __construct(VendorBoost $vendorBoost)
    {
        $this->vendorBoost = $vendorBoost;
        $this->vendor = $vendorBoost->vendor;
        $this->package = $vendorBoost->package;
        $this->products = $vendorBoost->boostedProducts()->with('product')->get();
        $this->expiredDate = Carbon::parse($vendorBoost->end_date)->format('F d, Y h:i A');
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Your Boost Package Has Expired - Renew Now')
                    ->view('Mails.boost_expiry');
    }
}

