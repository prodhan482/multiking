<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class OrderDetail
 * 
 * @property string $row_id
 * @property string $order_row_id
 * @property string $store_id
 * @property string $product_name
 * @property int $qty
 * @property float $total_price
 * @property float $product_price
 * @property array $charges
 * @property array $orderLineQuantity
 * @property string $statusDate
 * @property string $product_sku
 * @property array $refund
 * @property array $fulfillment
 * @property Carbon $created_at
 *
 * @package App\Models
 */
class OrderDetail extends Model
{
	protected $table = 'order_details';
	protected $primaryKey = 'row_id';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'qty' => 'int',
		'total_price' => 'float',
		'product_price' => 'float',
		'charges' => 'json',
		'orderLineQuantity' => 'json',
		'refund' => 'json',
		'fulfillment' => 'json'
	];

	protected $fillable = [
		'order_row_id',
		'store_id',
		'product_name',
		'qty',
		'total_price',
		'product_price',
		'charges',
		'orderLineQuantity',
		'statusDate',
		'product_sku',
		'refund',
		'fulfillment'
	];
}
