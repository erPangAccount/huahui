<?php
namespace App\Utils;

use App\Facades\UtilsFacade;
use GuzzleHttp\Client;
use Illuminate\Http\Request;

class Oauth
{
    /**
     * @param Request $request
     * @return mixed
     */
    public function getPasswordToken(Request $request)
    {
        try {
            $guzzle = new Client();
            $response = $guzzle->post($request->root() . '/oauth/token', [
                'form_params' => [
                    'grant_type' => 'password',
                    'client_id' => env('OAUTH_PASSWORD_CLIENT_ID', ''),
                    'client_secret' => env('OAUTH_PASSWORD_CLIENT_SECRET', ''),
                    'username' => $request->get('username', 'forget'),
                    'password' => $request->get('secret', 'forget'),
                    'scope' => '',
                ],
            ]);
        } catch (\Exception $exception) {
            return UtilsFacade::render(null, 401, $exception->getMessage());
        }
        return json_decode((string) $response->getBody(), true);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function refreshPasswordToken(Request $request)
    {
        try {
            $http = new Client();

            $response = $http->post($request->root() . '/oauth/token', [
                'form_params' => [
                    'grant_type' => 'refresh_token',
                    'refresh_token' => $request->get('refresh_token', 'forget'),
                    'client_id' => env('OAUTH_PASSWORD_CLIENT_ID', ''),
                    'client_secret' => env('OAUTH_PASSWORD_CLIENT_SECRET', ''),
                    'scope' => '',
                ],
            ]);
        } catch (\Exception $exception) {
            return UtilsFacade::render(null, 401, $exception->getMessage());
        }

        return json_decode((string) $response->getBody(), true);
    }
}