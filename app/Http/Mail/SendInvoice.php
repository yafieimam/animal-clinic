<?php

namespace App\Http\Mail;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendInvoice extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $email;
    public $data;
    public $pdf;

    public function __construct($email, $data, $pdf)
    {
        $this->email = $email;
        $this->data = $data;
        $this->pdf = $pdf;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('no-reply@amoreanimalclinic.com', 'Amore Animal Clinic')->attach($this->pdf)->subject("E-INVOICE")->view('transaksi.pembayaran.reminder_invoice');
    }
}
