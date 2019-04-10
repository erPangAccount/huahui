<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    /**
     * @var string
     */
    protected $table = 'carts';

    /**
     * @var array
     */
    protected $fillable = [
        'customer_id',
        'commodity_sku_id',
        'number'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function commositySku()
    {
        return $this->belongsTo(CommoditySku::class, 'commodity_sku_id');
    }
}