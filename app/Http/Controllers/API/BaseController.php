<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller as Controller;

class BaseController extends Controller
{
    /**
     * success json response method.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendJsonResponse($response, $code = 200)
    {
        return response()->json($response, $code);
    }

    /**
     * success raw response method.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendResponse($message, $code = 200)
    {
        return response($message, $code);
    }

    /**
     * return error response.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendError($error, $code = 404)
    {
        return response($error, $code);
    }

    /**
     * return error response.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendInvalidInput($message = 'Invalid input', $code = 405)
    {
        return response($message, $code);
    }
}
