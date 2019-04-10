<?php
namespace App\Http\Controllers\Api;

use App\Facades\CustomerFacade;
use App\Facades\OauthFacade;
use App\Facades\UtilsFacade;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SessionController extends Controller
{

    public function session(Request $request)
    {
        $customer = CustomerFacade::first($request->all());

        if (!$customer) {
            return UtilsFacade::render(null, 1, '账号或密码错误！');
        }

        return OauthFacade::getPasswordToken($request);
    }
}