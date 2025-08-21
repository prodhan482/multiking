<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class AauthUser
 * 
 * @property string $id
 * @property string $email
 * @property string $pass
 * @property string|null $username
 * @property bool|null $banned
 * @property Carbon|null $last_login
 * @property Carbon|null $last_activity
 * @property Carbon|null $date_created
 * @property string|null $forgot_exp
 * @property Carbon|null $remember_time
 * @property string|null $remember_exp
 * @property string|null $verification_code
 * @property string|null $totp_secret
 * @property string|null $ip_address
 * @property int|null $created_by
 * @property string|null $user_type
 * @property int|null $store_id
 * @property string|null $store_admin
 * @property int|null $modified_by
 * @property Carbon|null $modified_at
 * @property string|null $insecure
 *
 * @package App\Models
 */
class AauthUser extends Model
{
	protected $table = 'aauth_users';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'banned' => 'bool',
		'created_by' => 'int',
		'store_id' => 'int',
		'modified_by' => 'int'
	];

	protected $dates = [
		'last_login',
		'last_activity',
		'date_created',
		'remember_time',
		'modified_at'
	];

	protected $hidden = [
		'totp_secret'
	];

	protected $fillable = [
		'email',
		'pass',
		'username',
		'banned',
		'last_login',
		'last_activity',
		'date_created',
		'forgot_exp',
		'remember_time',
		'remember_exp',
		'verification_code',
		'totp_secret',
		'ip_address',
		'created_by',
		'user_type',
		'store_id',
		'store_admin',
		'modified_by',
		'modified_at',
		'insecure'
	];
}
