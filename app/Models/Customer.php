<?php
namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;

class Customer extends Authenticatable
{
    use HasApiTokens, SoftDeletes;

    /**
     * @var string
     */
    protected $table = "customers";

    /**
     * @var array
     */
    protected $fillable = [
        'email',
        'nick',
        'mobile',
        'secret',
        'openid'
    ];

    /**
     * @var array
     */
    protected $hidden = [
        'secret'
    ];

    /**
     * 由于oauth会去调用laravel自带的getAuthPassword(\Illuminate\Auth\Authenticatable::getAuthPassword)这个方法 这个方法默认获取的密码字段为password字段，本表中的密码字段为secret，所以需要自定义一个获取器
     *
     * @return mixed
     */
    public function getPasswordAttribute()
    {
        return $this->secret;
    }

    /**
     * 通过用户名找到对应的用户信息
     * @param $username
     * @return mixed
     */
    public function findForPassport($username)
    {
        return $this->where('email', $username)->orWhere('mobile', $username)->first();
    }
}