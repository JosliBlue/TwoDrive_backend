<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CodigoVerificacion extends Mailable
{
    use Queueable, SerializesModels;

    public $code;
    public $year;

    public function __construct($code)
    {
        $this->code = $code;
        $this->year = date('Y');
    }

    public function build()
    {
        return $this->subject('Código de verificación TwoDrive')
            ->view('emails.codigo_verificacion')
            ->with([
                'code' => $this->code,
                'year' => $this->year,
            ]);
    }
}
