<?php

namespace App\Http\Controllers;

use App\Mailboxes;
use App\Messages;
use App\Services\Imap\Imap;

use Illuminate\Http\Request;

class MailboxController extends Controller
{

    public function Start()
    {


        if (Mailboxes::all()->isEmpty()) {
            $stream = new Imap ();
            $menu = $stream->getMenu();
            foreach ($menu as  $valBox) {
                $stream->reOpen($valBox['pathBox']);
                $msg = $stream->getMessages();
                if (Messages::all()->isEmpty()) {
                    Messages::insert( $msg );
                } else {
                    foreach ($msg as $key => $value) {
                        Messages::firstOrCreate(array(
                            'uid' => $msg [$key]['uid'],
                            'title' => $msg [$key]['title'],
                            'date' => $msg[$key]['date'],
                            'sender' => $msg[$key]['sender'],
                            'read' => $msg[$key]['read'],
                            'stared' => $msg[$key]['stared'],
                            'senderadr' => $msg[$key]['senderadr'],
                            'body' => $msg[$key]['body'],
                            'timeID' => $msg[$key]['timeID'],
                            'mailbox' =>  $msg[$key]['mailbox']
                        ));
                    }
                }
            }
            Mailboxes::insert($menu);
            $stream->close();
            return response()->json($menu);
        }
        $menu = Mailboxes::all();
        return response()->json($menu);

    }


    public function Mailbox(Request $request)
    {
        $mailbox = $request->mailbox;
        $msg = Messages::where('mailbox', '=', $mailbox)->get();
        return response()->json($msg);
    }

    public function UpdateDB(Request $request)
    {
        $stream = new Imap ();
        $imapMenu = $stream->getMenu();
        $dbMenu = Mailboxes::all()->toArray();

        foreach ($imapMenu as $imapBox) {
            foreach ($dbMenu as $dbBox) {
                if ($imapBox['nameBox'] == $dbBox['nameBox']) {
                    if ($imapBox['numMSG'] == $dbBox['numMSG'] &&
                        $imapBox['lastUID'] == $dbBox['lastUID']) break;
                    $stream->reOpen($imapBox['pathBox']);
                    $messages = $stream->getMessages();
                    foreach ($messages as $message) {
                        Messages::firstOrCreate(array(
                            'uid' => $message['uid'],
                            'title' => $message['title'],
                            'date' => $message['date'],
                            'sender' => $message['sender'],
                            'read' => $message['read'],
                            'stared' => $message['stared'],
                            'senderadr' => $message['senderadr'],
                            'body' => $message['body'],
                            'timeID' => $message['timeID'],
                            'mailbox' => $message['mailbox']
                        ));
                    }
                    Mailboxes::where('nameBox',$imapBox['nameBox'])
                        ->update ([
                            'numMSG'  => $imapBox['numMSG'],
                            'lastUID' => $imapBox['lastUID']
                        ]);
                    break;
                }
                if( !next( $dbMenu )) {
                    $stream->reOpen($imapBox['pathBox']);
                    $messages = $stream->getMessages();
                    Messages::insert($messages);
                    Mailboxes::insert($imapBox);
                }
            }
        }
        $stream->close();
        $msg = Messages::where('mailbox', '=', $request->mailbox)->get();
        return response()->json($msg);
    }

    public function Read (Request $request) {
        $stream = new Imap ($request->mailbox);
        $success = $stream->SetStatusSeen ($request->UID);
        $stream->close();
         if ($success) {
             Messages::where('timeID', '=',  $request->timeID)->update([
                 'read' => $request->read
             ]);
             echo  1;
         } else {
             echo 0;
         }
    }

    public function Stared (Request $request) {
        $stream = new Imap ($request->mailbox);
        if ($request->stared == 'true') {
            $success = $stream->SetStatusFlagged($request->UID);
        }else if ($request->stared == 'false') {
            $success = $stream->ClearStatusFlagged($request->UID);
        }
        $stream->close();
        if ($success) {
            Messages::where('timeID', '=', $request->timeID)->update([
                'stared' => $request->stared
            ]);
            echo  1;
        } else {
            echo 0;
        }

    }

    public function Delete (Request $request) {
        $stream = new Imap ( $request->mailbox);
        $success =  $stream->DeleteMSG ($request->delete);
        $stream->close();

        if ($success) {
            Messages::where('timeID', $request->delete)->delete();
            echo  1;
        } else {
            echo 0;
        }

    }

}


