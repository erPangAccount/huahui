<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CommodityAttribute extends Model
{
    use SoftDeletes;

    /**
     * @var string
     */
    protected $table = "commodity_attributes";

    /**
     * @var array
     */
    protected $fillable = [
        'attribute_name',
        'icon'
    ];
}