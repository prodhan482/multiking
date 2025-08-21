<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Order
 * 
 * @property string $row_id
 * @property int $store_id
 * @property string $purchaseOrderId
 * @property int $customerOrderId
 * @property string $customerEmailId
 * @property int $orderDate
 * @property Carbon $orderDate_Formated
 * @property array $shippingInfo
 * @property array $orderLines
 * @property array $shipNode
 *
 * @package App\Models
 */
class Order extends Model
{
	protected $table = 'orders';
	protected $primaryKey = 'row_id';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'store_id' => 'int',
		'customerOrderId' => 'int',
		'orderDate' => 'int',
		'shippingInfo' => 'json',
		'orderLines' => 'json',
		'shipNode' => 'json'
	];

	protected $dates = [
		'orderDate_Formated'
	];

	protected $fillable = [
		'store_id',
		'purchaseOrderId',
		'customerOrderId',
		'customerEmailId',
		'orderDate',
		'orderDate_Formated',
		'shippingInfo',
		'orderLines',
		'shipNode'
	];
}
