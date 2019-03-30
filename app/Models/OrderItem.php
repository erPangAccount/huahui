<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderItem extends Model
{
    use SoftDeletes;

    /**
     * @var string
     */
    protected $table = 'order_items';

    /**
     * @var array
     */
    protected $fillable = [
        'order_id',
        'commodity_id',
        'commodity_sku_id',
        'number',
        'price',
        'rating',
        'review',
        'reviewed_at',
    ];

    /**
     * @var array
     */
    protected $dates = [
        'reviewed_at'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function commosity()
    {
        return $this->belongsTo(Commodity::class, 'commodity_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function commositySku()
    {
        return $this->belongsTo(CommoditySku::class, 'commodity_sku_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }
}