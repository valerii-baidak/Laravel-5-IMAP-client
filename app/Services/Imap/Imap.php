<?php
/**
 * Created by PhpStorm.
 * User: Max
 * Date: 01.03.2017
 * Time: 21:13
 */

namespace App\Services\Imap;

class Imap
{
    public $server;
    public $server_port;
    public $login;
    public $pass ;
    public $path;
    public $boxPath;

    public $stream;


    public function __construct ($boxPath = 'INBOX') {
        $this->server = config('app.imap_server');
        $this->server_port = config('app.imap_server_port');
        $this->login = config('app.imap_login');
        $this->pass =  config('app.imap_password');
        $this->boxPath = $boxPath;
        $this->path = '{' . $this->server . ':' . $this->server_port . '/imap/ssl}' . $boxPath;
        $this->stream = imap_open ($this->path, $this->login, $this->pass) or die ('Connection failed');
    }

    public function close() {
        imap_close($this->stream);
    }

    public function reOpen($boxPath) {
        $this->boxPath = $boxPath;
        $this->path = '{' . $this->server . ':' . $this->server_port . '/imap/ssl}' . $boxPath;
        imap_reopen($this->stream, $this->path) or die('ReOpen failed');
    }


    public function getMenu() {
        $menu = imap_list($this->stream,'{'.$this->server.'}', "*");
        $menuArray=[];
        if ($menu == false) {
            echo "Error <br /> \n";
        } else {
            foreach ( $menu as $val) {
                $text = str_replace('&', '+', $val);
                $text  = str_replace(',', '/', $text);
                $text = iconv('UTF-7','UTF-8', $text);
                $pathBox = str_replace('{imap.gmail.com}','',$text);
                $nameBox = str_replace('[Gmail]/', '', $pathBox);
                $status = imap_status( $this->stream, '{imap.gmail.com}'.$pathBox, SA_ALL);
                $boxStatus ['nameBox'] =$nameBox;
                $boxStatus ['numMSG'] =  $status -> messages;
                $boxStatus ['lastUID'] =  ($status -> uidnext) - 1;
                $boxStatus ['pathBox'] = $pathBox;
                $menuArray[] = $boxStatus;
            }
        }
        return $menuArray;
    }



    public function SetStatusSeen ($uid) {
        $success = imap_setflag_full($this->stream, $uid, "\\Seen", ST_UID);
        return $success;
    }

    public function SetStatusFlagged ($uid) {
        $success = imap_setflag_full($this->stream, $uid, "\\Flagged", ST_UID);
        return $success;
    }

    public function ClearStatusFlagged ($uid) {
        $success = imap_clearflag_full ($this->stream, $uid, "\\Flagged", ST_UID);
        return $success;
    }

    public function DeleteMSG ($messages) {
        foreach ($messages as $message) {
            $uid = $message['UID'];
            imap_delete ($this->stream, $uid, FT_UID);
        }
        $success = imap_expunge($this->stream);
        return $success;
    }

    protected function MsgDate ($msgDate){
        $msgDate=trim($msgDate);
        $msgDate2=strtotime($msgDate);
        $msgDate=date("d-m-Y", $msgDate2);
        return $msgDate;
    }

    protected function MsgTimeID ($msgDate){
        $msgDate=trim($msgDate);
        $msgDate2=strtotime($msgDate);
        return $msgDate2;
    }

    protected function unseenMSG ($msgUnseen){
        if ($msgUnseen != 'U')return "true";
        return "false";
    }

    protected function starredMSG ($msgFlagged){
        if ($msgFlagged == 'F')return "true";
        return "false";
    }

    protected function senderAdr ($senderAddr,$senderName){
        $mailbox = $senderAddr[0]->mailbox;
        $host =$senderAddr[0]->host;
        $sender =$senderName. '&lt'.$mailbox.'@'.$host.'&gt';
        return 	$sender;

    }

    protected function encodeMsgBody ($message,$msgStructure ){
        if (isset($msgStructure->parts[1]->encoding)) {
            $idEncode = $msgStructure->parts[1]->encoding;
            switch ($idEncode) {
                case 0:
                    $message = $message;
                    break;
                case 1:
                    $message = imap_8bit($message);
                    break;
                case 2:
                    $message = imap_binary($message);
                    break;
                case 3:
                    $message = imap_base64($message);
                    break;
                case 4:
                    $message = imap_base64($message);
                    break;
                case 5:
                    $message = $message;
                    break;
            }
        } else {
            $message = $message;
        }
        $message = str_replace('<',' ',$message);
        $message = str_replace('>',' ',$message);
        $x=explode(" ",$message);
        for ($j=0; $j<count($x); $j++) {
            if (preg_match
            ("/(https:\\/\\/)?([a-z_0-9-.]+\\.[a-z]{2,3}(([ \"'>\r\n\t])|(\\/([^ \"'>\r\n\t]*)?)))/",
                $x[$j],$ok)) {
                $messageArr[$j] = str_replace($ok[2], "<a href='https://$ok[2]' target='_blank'>$ok[2]</a>",
                        str_replace("https://", "", $x[$j])) . " ";
                $messageArr[$j] = str_replace("http://", '',$messageArr[$j] );
            }else
                $messageArr[$j] =  $x[$j]." ";
        }
        $message= implode (' ',$messageArr);
        $message= preg_replace("/\[image(.*)\]/i", ' ',$message);
        return $message;
    }

    public function getMessages(){
        $msg = [];
        $msgNum = imap_num_msg ($this->stream);
        for ($i=1; $i <= $msgNum; $i++) {
            $info= imap_header($this->stream, $i);
            $uid = imap_uid ($this->stream,$i);
            $structure = imap_fetchstructure($this->stream,$i);
            $body = imap_fetchbody($this->stream,$i,1,FT_PEEK);

            if (isset($info->subject)) {
                $title = imap_utf8($info->subject);
            } else {
                $title = '(no subject)';
            }

            $msg[$i]['title'] = $title;
            $msg[$i]['date'] = $this->MsgDate($info->MailDate);
            $msg[$i]['timeID'] = $this->MsgTimeID ($info->MailDate);

            if ($this->path == '{' . $this->server . ':' . $this->server_port . '/imap/ssl}[Gmail]/Sent Mail' ) {
                $msg[$i]['sender'] = imap_utf8( $info->fromaddress);
            } else {
                $msg[$i]['sender'] = imap_utf8( $info->senderaddress);
            }

            $msg[$i]['read'] = $this->unseenMSG($info->Unseen);
            $msg[$i]['stared'] = $this->starredMSG($info->Flagged);
            $msg[$i]['senderadr']=$this->senderAdr( $info->sender, $msg[$i]['sender']);
            $msg[$i]['uid']= $uid;
            $msg[$i]['body']=$this->encodeMsgBody($body, $structure);
            $msg[$i]['mailbox']=$this->boxPath;
        }
        return $msg;
    }

}