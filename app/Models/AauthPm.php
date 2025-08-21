<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class AauthPm
 * 
 * @property int $id
 * @property int $sender_id
 * @property int $receiver_id
 * @property string $title
 * @property string|null $message
 * @property Carbon|null $date_sent
 * @property Carbon|null $date_read
 * @property int|null $pm_deleted_sender
 * @property int|null $pm_deleted_receiver
 *
 * @package App\Models
 */
class AauthPm extends Model
{
	protected $table = 'aauth_pms';
	public $timestamps = false;

	protected $casts = [
		'sender_id' => 'int',
		'receiver_id' => 'int',
		'pm_deleted_sender' => 'int',
		'pm_deleted_receiver' => 'int'
	];

	protected $dates = [
		'date_sent',
		'date_read'
	];

	protected $fillable = [
		'sender_id',
		'receiver_id',
		'title',
		'message',
		'date_sent',
		'date_read',
		'pm_deleted_sender',
		'pm_deleted_receiver'
	];
}
