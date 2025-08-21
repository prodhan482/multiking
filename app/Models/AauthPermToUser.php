<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class AauthPermToUser
 * 
 * @property int $perm_id
 * @property int $user_id
 *
 * @package App\Models
 */
class AauthPermToUser extends Model
{
	protected $table = 'aauth_perm_to_user';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'perm_id' => 'int',
		'user_id' => 'int'
	];
}
