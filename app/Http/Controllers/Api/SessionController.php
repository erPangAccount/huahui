<?php

namespace App\Http\Controllers\Api;

use App\Facades\CustomerFacade;
use App\Facades\OauthFacade;
use App\Facades\UtilsFacade;
use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use Illuminate\Http\Request;

class SessionController extends Controller
{

    public function session(Request $request)
    {
        $customer = CustomerFacade::first($request->all());

        if (!$customer) {
            try {
                $guzzle = new Client();
                $response = $guzzle->post($request->root() . '/api/register', [
                    'form_params' => $request->all(),
                    'headers' => [
                        'Authorization' => $_SERVER['HTTP_AUTHORIZATION']
                    ]
                ]);
                if (json_decode((string) $response->getBody(), true)['status']) {
                    throw new \Exception(json_decode((string) $response->getBody(), true)['messages']);
                }
            } catch (\Exception $exception) {
                return UtilsFacade::render(null, 1, $exception->getMessage());
            }
        }

        return OauthFacade::getPasswordToken($request);
    }
}