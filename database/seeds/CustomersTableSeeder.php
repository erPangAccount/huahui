<?php

use Illuminate\Database\Seeder;

class CustomersTableSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $customers = [
            [
                'email' => 'example@example.com',
                'mobile' => '13311111111',
                'nick' => '预定义用户',
                'secret' => \Illuminate\Support\Facades\Hash::make('secret')
            ]
        ];

        foreach ($customers as $customer) {
            if (!\App\Models\Customer::query()->where('email', '=', $customer['email'])->orWhere('mobile', '=', $customer['mobile'])->exists())
                \App\Models\Customer::query()->firstOrCreate($customer);
        }
    }
}
