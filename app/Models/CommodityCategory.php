<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CommodityCategory extends Model
{
    use SoftDeletes;
    /**
     * @var string
     */
    protected $table = "commodity_categories";

    /**
     * @var array
     */
    protected $fillable = [
        'parent_id',
        'name',
        'level'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function commodities()
    {
        return $this->hasMany(Commodity::class, 'category_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parent()
    {
        return $this->belongsTo(CommodityCategory::class, 'parent_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function children()
    {
        return $this->hasMany(CommodityCategory::class, 'parent_id');
    }

    /**
     * @param array $options
     * @return bool
     */
    public function save(array $options = [])
    {
        if ($this->parent()->exists() && $this->parent->parent()->exists()) {
            $this->level = 3;
        } else if ($this->parent()->exists() && !$this->parent->parent()->exists()) {
            $this->level = 2;
        } else {
            $this->level = 1;
        }


        return parent::save($options); // TODO: Change the autogenerated stub
    }
}