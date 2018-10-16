<?php

namespace App\Traits;

trait TestHelper
{
    private $checkStructure = [];

    public static function is_debug()
    {
        return in_array('--debug', $_SERVER['argv'], true);
    }

    public function apiCheck($options)
    {
        $data = $options["data"] ?? [];

        if (self::is_debug()) {
            dump(
                "url : "
                . config("app.url")
                . $options["url"]
                . "?"
                . http_build_query($data)
            );
        }

        $response = $this->json(
            $options["method"] ?? "POST",
            $options["url"],
            $data
        );

        $content = $response->getContent();
        $json = json_decode($content, true);

        if (self::is_debug()) {
            dump($json);
        }

        $this->checkStructure = $options["structure"] ?? [];

        //检查返回状态
        $response->assertSuccessful();

        //是否包含 error 代码
        $response->assertJsonMissing(["error"]);

        //验证数据结构
        if (isset($options["structure"])) {
            $response->assertJsonStructure(
                $options["structure"]
            );
        }

        foreach ($json as $key => $value) {
            if (is_array($value)) {
                foreach ($value as $subkey => $subvalue) {
                    if (!is_array($subvalue)) {
                        $this->apiRules($subkey, $subvalue);
                    }
                }
            } else {
                $this->apiRules($key, $value);
            }
        }
    }

    public function apiRules($key, $value)
    {
        $check_arr = ["id", "name"];

        if (!in_array($key, $check_arr)) {
            return null;
        }

        $arr = [
            "key" => $key,
            "value" => $value,
            "result" => false,
        ];

        if ($key == "id") {
            $arr["result"] = $value > 100000 && is_int($value);
        } elseif (in_array($key, ["name", "title", "desc", "description"])) {
            $arr["result"] = mb_strlen($value) > 1;
        }

        if (!$arr["result"]) {
            $this->log("the " . $key . " with value " . $value . " is fail on test");
        }

        $this->assertTrue($arr["result"]);
    }

    public function log($message)
    {
        $output = new \Symfony\Component\Console\Output\ConsoleOutput();
        $output->writeln("<error>$message</error>");
    }
}
