<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderPlacedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $order;
    public $orderItem;
    public $cartItems;
    public $userRecord;
    public $vendorRecord;
    public $completeAddress;
    public $recipientType;
    public $subtotal;
    public $grandTotal;

    /**
     * Create a new message instance.
     */
    public function __construct($order, $orderItem, $cartItems, $userRecord, $vendorRecord, $completeAddress, $recipientType, $subtotal = 0, $grandTotal = 0)
    {
        $this->order = $order;
        $this->orderItem = $orderItem;
        $this->cartItems = $cartItems;
        $this->userRecord = $userRecord;
        $this->vendorRecord = $vendorRecord;
        $this->completeAddress = $completeAddress;
        $this->recipientType = $recipientType;
        $this->subtotal = $subtotal;
        $this->grandTotal = $grandTotal;
    }





    
    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Order Placed: ' . $this->order->order_number)
                    ->view('Mails.order_placed');
    }
}
