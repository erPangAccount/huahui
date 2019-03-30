<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CommoditySkuAttribue extends Model
{
    use SoftDeletes;

    /**
     * @var string
     */
    protected $table = "commodity_sku_attribute";

    /**
     * @var array
     */
    protected $fillable = [
        'sku_id',
        'attribute_info'
    ];

    /**
     * @var array
     */
    protected $casts = [
      'attribute_info' => 'array'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function sku()
    {
        return $this->belongsTo(CommoditySku::class, 'sku_id');
    }

}