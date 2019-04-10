<?php
namespace App\Utils;

class Utils
{
    /**
     * @param null $data
     * @param int $status
     * @param string $message
     * @return \Illuminate\Http\JsonResponse
     */
    public function render($data = null, $status = 0, $message = '')
    {
        return response()->json(compact('data', 'message', 'status'), 200)->header('Content-Type', 'application/json');
    }

}