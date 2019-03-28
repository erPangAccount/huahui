<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommodityCategory extends Model
{
    /**
     * @var string
     */
    protected $table = "commodity_categories";

    /**
     * @var array
     */
    protected $fillable = [
        'parent_id',
        'name'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function commodities()
    {
        return $this->hasMany(Commodity::class, 'category_id');
    }
}