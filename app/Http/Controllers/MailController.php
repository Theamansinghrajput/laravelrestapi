<?php

namespace App\Http\Controllers;

use App\Models\Mail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class MailController extends Controller
{

    public function mail_smtp_setup(string $mail_to,string $mail_body, string $mail_sub, array $CC_sender)
    {
        //smtp_code_for_mail
    }

    public function phpmailer_smtp_setup(string $mail_to,string $mail_body, string $mail_sub, array $CC_sender)
    {
        //phpmailer_smtp_setp
    }

    public function php_normal_setup(string $mail_to,string $mail_body, string $mail_sub, array $CC_sender)
    {
        //php_normal_setup
    }

    public function aws_ses_smtp_setup(string $mail_to,string $mail_body, string $mail_sub, array $CC_sender)
    {
        //aws_ses_smtp_setp
    }


}
