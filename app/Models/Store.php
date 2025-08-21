<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Store
 * 
 * @property string $store_id
 * @property string $store_name
 * @property string $status
 * @property string $created_by
 * @property Carbon $created_at
 * @property Carbon $modified_at
 * @property string $wallmart_private_key
 * @property string $wallmart_comsumer_key
 *
 * @package App\Models
 */
class Store extends Model
{
	protected $table = 'store';
	protected $primaryKey = 'store_id';
	public $incrementing = false;
	public $timestamps = false;

	protected $dates = [
		'modified_at'
	];

	protected $fillable = [
		'store_name',
		'status',
		'created_by',
		'modified_at',
		'wallmart_private_key',
		'wallmart_comsumer_key'
	];
}
