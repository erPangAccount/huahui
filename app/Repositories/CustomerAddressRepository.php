<?php

namespace App\Repositories;

use App\Models\CustommerAddress;

class CustomerAddressRepository
{
    /**
     * @var mixed
     */
    protected $area;

    /**
     * CustomerAddressRepository constructor.
     */
    public function __construct()
    {
        $this->area = require_once resource_path('area.php');
    }

    /**
     * @return mixed
     */
    public function getArea()
    {
        return $this->area;
    }

    /**
     * @param array $params
     * @param string $sortField
     * @param string $sortOrder
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function page(array $params, $sortField = 'created_at', $sortOrder = 'asc')
    {
        return $this->query($params, $sortField, $sortOrder)->paginate(array_get($params, 'pageSize', 10));
    }

    /**
     * @param array $params
     * @param string $sortField
     * @param string $sortOrder
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function get(array $params, $sortField = 'created_at', $sortOrder = 'asc')
    {
        return $this->query($params, $sortField, $sortOrder)->get();
    }


    /**
     * @param array $params
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object|null
     */
    public function detail(array $params)
    {
        return $this->query($params)->first();
    }

    /**
     * @param array $params
     * @return CustommerAddress|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object|void|null
     */
    public function save(array $params)
    {
        if (isset($params['is_default']) && $params['is_default']) {
            CustommerAddress::query()->where('customer_id', '=', $params['customer_id'])->update(['is_default' => (int) false]);
        }

        if (isset($params['id']) && $params['id']) {
            $address = $this->query($params)->first();
            if (!$address) {
                return;
            }
        } else {
            $address = new CustommerAddress();
        }

        $address->fill($params);
        $address->save();

        return $address;
    }

    /**
     * @param $params
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object|null
     * @throws \Exception
     */
    public function destroy($params)
    {
        $address = $this->query($params)->first();

        if ($address) {
            $address->delete();
        }

        return $address;
    }

    /**
     * @param array $params
     * @param string $sortField
     * @param string $sortOrder
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function query(array $params, $sortField = 'created_at', $sortOrder = 'asc')
    {
        $query = CustommerAddress::query()
            ->orderBy($sortField, $sortOrder);

        if (isset($params['id'])) {
            $query->where('id', '=', $params['id']);
        }

        if (isset($params['customer_id'])) {
            $query->where('customer_id', '=', $params['customer_id']);
        }

        return $query;
    }
}