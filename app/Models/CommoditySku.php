<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Validator;

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
        'sku_name',
        'sku_description',
        'sku_price',
        'sku_stock',
        'sku_image',
        'commodity_id'
    ];

    /**
     * 减库存
     * @param $amount
     * @return int
     * @throws \Exception
     */
    public function decreaseStock($amount)
    {
        if ($amount < 0) {
            throw new \Exception('减库存不可小于0');
        }

        return $this->newQuery()->where('id', $this->id)->where('sku_stock', '>=', $amount)->decrement('sku_stock', $amount);
    }

    /**
     * 加库存
     * @param $amount
     * @throws \Exception
     */
    public function addStock($amount)
    {
        if ($amount < 0) {
            throw new \Exception('加库存不可小于0');
        }
        $this->increment('sku_stock', $amount);
    }

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
    public function skuAttributes()
    {
        return $this->hasMany(CommoditySkuAttribue::class, 'sku_id');
    }


    public function getSkuImageAttribute($value)
    {
        if ($value) {
            $validate = Validator::make(compact('value'), [
                'value' => 'url'
            ]);

            if ($validate->errors()->first()) {
                $value = env('APP_URL') . '/app/public/admin/' . $value;
            }

            return $value;
        }
    }
}