<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustommerAddress extends Model
{
    /**
     * @var string
     */
    protected $table = 'customer_addresses';

    /**
     * @var array
     */
    protected $fillable = [
        'customer_id',
        'recipient_name',
        'recipient_phone',
        'province_code',
        'city_code',
        'county_code',
        'detailed',
        'last_used',
        'is_default'
    ];

    /**
     * @var array
     */
    protected $casts = [
        'is_default' => 'boolean'
    ];
    
    /**
     * @var array
     */
    protected $dates = [
        'last_used'
    ];
}