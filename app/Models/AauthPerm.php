<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class AauthPerm
 * 
 * @property int $id
 * @property string|null $name
 * @property string|null $definition
 *
 * @package App\Models
 */
class AauthPerm extends Model
{
	protected $table = 'aauth_perms';
	public $timestamps = false;

	protected $fillable = [
		'name',
		'definition'
	];
}
