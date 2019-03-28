<?php
namespace App\Service;

use App\Resitories\CustomerResitory;

class CustomerService
{
    protected $customerResitory;

    public function __construct()
    {
        $this->customerResitory = new CustomerResitory();
    }

    /**
     * @param $params
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object|null
     */
    public function first($params)
    {
        return $this->customerResitory->first($params);
    }
}