<?php
namespace App\Repositories;

use App\Models\Customer;

class CustomerRepository
{
    /**
     * @param $params
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object|null
     */
    public function first($params)
    {
        $query = Customer::query();

        if (array_key_exists('email', $params)) {
            $query->where('email', $params['email']);
        }

        if (array_key_exists('mobile', $params)) {
            $query->where('mobile', $params['mobile']);
        }

        if (array_key_exists('nick', $params)) {
            $query->where('nick', 'like', '%' . $params['nick'] . '%');
        }
        return $query->first();
    }
}