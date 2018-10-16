<?php
/**
 * User: james
 * Date: 13/02/2018
 * Time: 22:37.
 */

 // if ($rs) {
 //     return ApiHelper::Successfully();
 // } else {
 //     return ApiHelper::Error();
 // }

namespace App\Helpers\Api;

use Illuminate\Http\Response;

class ApiHelper
{
    public static function sendResponse($message, $error_code = Response::HTTP_OK)
    {
        $message = self::delNull($message);

        return response()->json([
            'result' => $message,
            'status' => Response::$statusTexts[$error_code],
            'status_code' => $error_code,
        ])->setStatusCode($error_code, Response::$statusTexts[$error_code]);
    }

    public static function Successfully($error_code = Response::HTTP_OK)
    {
        return response()->json([
            'result' => 'Successfully',
            'status' => Response::$statusTexts[$error_code],
            'status_code' => $error_code,
        ])->setStatusCode($error_code, Response::$statusTexts[$error_code]);
    }

    public static function ErrorMsg($msg)
    {
        return response()->json([
            'result' => $msg,
            'status' => Response::$statusTexts[417],
            'status_code' => 200,
        ])->setStatusCode(200, Response::$statusTexts[417]);
    }

    public static function Msg($msg)
    {
        $error_code = 200;

        return response()->json([
            'result' => $msg,
            'status' => Response::$statusTexts[$error_code],
            'status_code' => $error_code,
        ])->setStatusCode($error_code, Response::$statusTexts[$error_code]);
    }

    public static function Error($error_code = 417)
    {
        return response()->json([
            'result' => 'Error',
            'status' => Response::$statusTexts[$error_code],
            'status_code' => $error_code,
        ])->setStatusCode($error_code, Response::$statusTexts[$error_code]);
    }

    public static function return($rs)
    {
        if ($rs) {
            if (is_array($rs) || is_object($rs)) {
                return self::sendResponse($rs);
            } else {
                return self::Successfully();
            }
        } else {
            return self::Error();
        }
    }

    public static function delNull($message)
    {
        if (is_array($message)) {
            array_walk_recursive($message, function (&$val, $key) {
                if ($val === null) {
                    $val = '';
                }
            });
        }
        return $message;
    }
}
