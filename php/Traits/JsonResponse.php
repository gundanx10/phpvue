<?php

namespace App\Traits;

trait JsonResponse
{
    protected function jsonData($data, $type = "data")
    {
        $arr = [];

        if ($type == "data") {
            if (is_array($data) || is_object($data)) {
                $arr = $data;
            } else {
                $arr = ["code" => (string) $data];
            }
        } elseif ($type == "error") {
            $arr["error"] = $data;
        }

        return response()->json($arr);
    }

    protected function jsonError($data)
    {
        $arr = [];

        if (is_numeric($data) || is_string($data)) {
            $arr = $this->getError($data);
        } elseif (is_array($data)) {
            if (isset($data["id"])) {
                $arr["id"] = $data["id"];
            }
            if (isset($data["message"])) {
                $arr["message"] = $data["message"];
            }
        }

        if (!$arr) {
            $arr["message"] = "unknow error";
        }

        return $this->jsonData($arr, "error");
    }

    public function getError($code)
    {
        $arr = [];

        $error_list = \Config::get("error");

        if (is_numeric($code) && isset($error_list[$code])) {
            $arr = ["message" => $error_list[$code]];
        } else {
            $arr = ["message" => $code];
        }

        return $arr;
    }
}
