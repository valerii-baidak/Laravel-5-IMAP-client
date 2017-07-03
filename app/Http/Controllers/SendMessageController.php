<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Mail\Mailer;
use Mail;

class SendMessageController extends Controller
{

    public function SendEmail (Request $request){

	    Mail::raw($request->message, function ($message)use ($request){
		    $message->to($request->email, $name = null);
		    $message->subject($request->subject);
	    });
	    if( count(Mail::failures()) > 0 ) {
		    echo 0;
	    } else {
		    echo 1;
	    }
    }
}
