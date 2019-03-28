<?php
namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CommoditySku extends Model
{
    use SoftDeletes;

    /**
     * @var string
     */
    protected $table = 'commodities_sku';

    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'price',
        'stock',
        'commodity_id'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function commodity()
    {
        return $this->belongsTo(Commodity::class, 'commodity_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function attribute()
    {
        return $this->hasMany(CommoditySkuAttribue::class, 'sku_id');
    }
}