<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class AauthPermsGroup
 * 
 * @property int $perm_group_id
 * @property int $prem_id
 * @property string|null $group_defination
 *
 * @package App\Models
 */
class AauthPermsGroup extends Model
{
	protected $table = 'aauth_perms_group';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'perm_group_id' => 'int',
		'prem_id' => 'int'
	];

	protected $fillable = [
		'group_defination'
	];
}
