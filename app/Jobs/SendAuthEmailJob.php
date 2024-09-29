<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use App\Mail\AuthMail;

class SendAuthEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $email;
    protected $password;
    protected $nom;
    protected $prenom;
    protected $pdfPath;
    protected $message;

    public function __construct($email, $password, $nom, $prenom, $pdfPath, $message)
    {
        $this->email = $email;
        $this->password = $password;
        $this->nom = $nom;
        $this->prenom = $prenom;
        $this->pdfPath = $pdfPath;
        $this->message = $message;
    }

    public function handle()
    {
        Mail::to($this->email)->send(new AuthMail(
            $this->nom,
            $this->prenom,
            $this->email,
            $this->password,
            $this->pdfPath,
            $this->message
        ));
    }
}
