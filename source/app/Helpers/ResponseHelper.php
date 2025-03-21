<?php

namespace App\Helpers;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class ResponseHelper
{
    public static function success($message, $dynamicAttributes = [], $statusCode = 200)
    {
        $response = [
            'success' => true,
            'rc' => $statusCode,
            'message' => $message,
        ];
        foreach ($dynamicAttributes as $key => $value) { $response[$key] = $value; }
        return response()->json($response, $statusCode);
    }
    public static function success_delete($message, $statusCode = 200)
    {
        $response = [
            'success' => true,
            'rc' => $statusCode,
            'message' => $message,
        ];
        return response()->json($response);
    }
    public static function data_not_found($message, $statusCode = 404)
    {
        $response = [
            'success' => false,
            'rc' => $statusCode,
            'message' => $message,
        ];
        return response()->json($response);
    }
    public static function data_conflict($message, $statusCode = 409)
    {
        $response = [
            'success' => false,
            'rc' => $statusCode,
            'message' => $message,
        ];
        return response()->json($response);
    }
    public static function error_validation($message, $dynamicAttributes = [], $statusCode = 422)
    {
        $response = [
            'success' => false,
            'rc' => $statusCode,
            'message' => $message,
        ];
        foreach ($dynamicAttributes as $key => $value) { $response[$key] = $value; }
        return response()->json($response, $statusCode);
    }

    public static function error($th)
    {
        $message = (is_int($th) ?__('error.'.$th.'_error') : __('error.500_error'));
        if ((bool)env('APP_DEBUG_MESSAGE') && !is_int($th)){
            $message .= ". Pesan Kesalahan : ".$th->getMessage();
        }
        DB::table('log_error_app')->insert([
            'level' => 'error',
            'messages' => $th->getMessage(),
            'context' => json_encode([
                'code' => is_int($th) ? $th : $th->getCode(),
                'file' => !is_int($th) ? $th->getFile() : null,
                'line' => !is_int($th) ? $th->getLine() : null,
                'trace' => !is_int($th) ? $th->getTraceAsString() : null
            ]),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        return response()->json([
            'success' => false,
            'rc' => (int)(is_int($th) == true ? $th : $th->getCode()),
            'message' => substr($message, 0),
        ], 500);
    }
    public static function data($message, $dynamicAttributes = [], $statusCode = 200)
    {
        $response = [
            'success' => true,
            'rc' => $statusCode,
            'message' => $message,
        ];
        foreach ($dynamicAttributes as $key => $value) { $response[$key] = $value; }
        return response()->json($response, $statusCode);
    }
    public static function data_req_token_csrf($message, $statusCode = 500)
    {
        $response = [
            'success' => true,
            'rc' => $statusCode,
            'message' => $message,
        ];
        return response()->json($response, $statusCode);
    }
}
