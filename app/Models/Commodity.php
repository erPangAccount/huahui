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
        'images',
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
     * @param $image
     */
    public function setImagesAttribute($image)
    {
        if (!is_array($image)) {
            $image = [$image];
        }

        $this->attributes['images'] = json_encode($image);
    }

    /**
     * @param $image
     * @return mixed
     */
    public function getImagesAttribute($image)
    {
        return json_decode($image, true);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function skus()
    {
        return $this->hasMany(CommoditySku::class, 'commodity_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category()
    {
        return $this->belongsTo(CommodityCategory::class, 'category_id');
    }
}