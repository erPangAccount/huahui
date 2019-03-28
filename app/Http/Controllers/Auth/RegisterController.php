<?php

namespace App\Http\Controllers\Auth;

use App\Facades\UtilsFacade;
use App\Models\Customer;
use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validatorCustomer(array $data)
    {
        return Validator::make($data, [
            'mobile' => ['nullable', 'string', 'mobile', 'max:11'],
            'email' => ['required', 'string', 'email', 'max:120', 'unique:customers'],
            'nick' => ['nullable', 'string', 'max:15'],
            'secret' => ['required', 'string', 'min:6', 'confirmed'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     * @param array $data
     * @return mixed
     */
    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
    }

    /**
     * @param array $data
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model
     */
    protected function createCustomer(array $data)
    {
        return Customer::query()->create([
            'email' => $data['email'],
            'mobile' => $data['mobile'] ?? '',
            'nick' => $data['nick'] ?? '',
            'secret' => Hash::make($data['secret'])
        ]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function registerCustomer(Request $request)
    {
        $validate = $this->validatorCustomer($request->all());

        if ($validate->messages()->first()) {
            return UtilsFacade::render(null, 1, $validate->messages()->first());
        }

        $customer = $this->createCustomer($request->all());

        $this->guard()->login($customer);

        if ($this->registered($request, $customer)) {
            return UtilsFacade::render(null, 1, __('customize.notice.register.account_exists'));
        }

       return UtilsFacade::render($customer, 0, __('customize.notice.register.success'));
    }


}
