<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Commodity extends Model
{
    use SoftDeletes;

    /**
     * @var string
     */
    protected $table = 'commodities';

    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'category_id',
        'description',
        'image',
        'on_sale',
        'rating',
        'sold_count',
        'review_count',
        'price'
    ];

    /**
     * @var array
     */
    protected $casts = [
        'on_sale' => 'boolean'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function skus()
    {
        return $this->hasMany(CommoditySku::class, 'commodity_id');
    }
}