<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class Mailboxes extends Model
{
	public $timestamps = false;

	protected $fillable = array (
			'pathBox',
			'nameBox',
			'numMSG',
			'lastUID'
	);
}