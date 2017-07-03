<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class Messages extends Model
{
	public $timestamps = false;

	protected $fillable = array(
			'uid',
			'title',
			'date',
			'sender',
			'read',
			'stared',
			'senderadr',
			'body',
			'mailbox',
			'timeID');


}