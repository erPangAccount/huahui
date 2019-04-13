<?php

namespace App\Service;

use App\Repositories\CustomerAddressRepository;
use function GuzzleHttp\Psr7\uri_for;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CustomerAddressService
{
    /**
     * @var CustomerAddressRepository
     */
    protected $customerAddressRepository;

    /**
     * CustomerAddressService constructor.
     */
    public function __construct()
    {
        $this->customerAddressRepository = new CustomerAddressRepository();
    }

    /**
     * @param array $params
     * @param int $customer_id
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function page(array $params, $customer_id = 0)
    {
        $sortField = array_get($params, 'sort_field', 'last_used');
        $sortOrder = array_get($params, 'sort_order', 'asc');
        if ($customer_id) {
            $params['customer_id'] = $customer_id;
        }

        return $this->customerAddressRepository->get($params, $sortField, $sortOrder);
    }

    /**
     * @param $id
     * @param int $customer_id
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object|null
     */
    public function detail($id, $customer_id = 0)
    {
        $params = [
            'id' => $id,
            'customer_id' => $customer_id
        ];

        return $this->customerAddressRepository->detail($params);
    }

    /**
     * @param $params
     * @param $customer_id
     * @return \App\Models\CustommerAddress|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object|void|null
     */
    public function save($params, $customer_id)
    {
        if ($customer_id) {
            $params['customer_id'] = $customer_id;
        }
        return $this->customerAddressRepository->save($params);
    }

    /**
     * @param $params
     * @param $id
     * @param $customer_id
     * @return \App\Models\CustommerAddress|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object|void|null
     */
    public function update($params, $id, $customer_id)
    {
        $params['id'] = $id;
        $params['customer_id'] = $customer_id;
        return $this->customerAddressRepository->save($params);
    }

    /**
     * @param $id
     * @param $customer_id
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object|null
     * @throws \Exception
     */
    public function destroy($id, $customer_id)
    {
        $params = [
            'id' => $id,
            'customer_id' => $customer_id
        ];

        return $this->customerAddressRepository->destroy($params);
    }

    /**
     * @param array $params
     * @param int $customer_id
     * @return string
     */
    public function validateSave(array $params, $customer_id = 0)
    {
        $rules = [
            'recipient_name' => [
                'required',
                'max:20'
            ],
            'recipient_phone' => [
                'required',
                'mobile',
                'max:11'
            ],
            'province_code' => [
                'required',
                Rule::in(array_keys($this->customerAddressRepository->getArea()['province_list']))
            ],
            'city_code' => [
                'required',
                Rule::in(array_keys($this->customerAddressRepository->getArea()['city_list']))
            ],
            'county_code' => [
                'required',
                Rule::in(array_keys($this->customerAddressRepository->getArea()['county_list']))
            ],
            'is_default' => [
                'boolean'
            ]
        ];

        if (isset($params['province_code'])) {
            $params['province_code'] = str_pad($params['province_code'], 6, 0, STR_PAD_RIGHT);
        }
        if (isset($params['city_code'])) {
            $params['city_code'] = str_pad($params['city_code'], 6, 0, STR_PAD_RIGHT);
        }
        if (isset($params['county_code'])) {
            $params['county_code'] = str_pad($params['county_code'], 6, 0, STR_PAD_RIGHT);
        }

        $validate = Validator::make($params, $rules);
        if ($validate->errors()->first()) {
            return $validate->errors()->first();
        }
        return '';
    }

}