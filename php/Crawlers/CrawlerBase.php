<?php

namespace App\Crawlers;

use GuzzleHttp\Pool;
use GuzzleHttp\Client;
use Log;

abstract class CrawlerBase
{
    //是否添加单独抓取 video detail 的 job

    //TODO 每日增量数据 (incrase_view, increase_follower)，历史每日增量数据
    protected $options;

    protected $httpClient;
    protected $proxyAddress = "";
    protected $defaultRequestOptions = [
        "timeout" => 10,
    ];

    protected $currentRequestOptions = [];

    public static $current_page = 1;
    public static $last_time = 0;

    use \App\Traits\OutputHelper;

    protected $userAgents = [
        "iPhone" => "Mozilla/5.0 (iPhone; CPU iPhone OS 6_0 like Mac OS X) AppleWebKit/536.26 (KHTML, like Gecko) Version/6.0 Mobile/10A5376e Safari/8536.25",
        "Android" => "Mozilla/5.0 (Linux; Android 4.4.2; Nexus 4 Build/KOT49H) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/34.0.1847.114 Mobile Safari/537.36",
        "Firefox" => "Mozilla/5.0 (Windows NT 6.1; WOW64; rv:54.0) Gecko/20100101 Firefox/54.0",
        "Safari" => "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_9_3) AppleWebKit/537.75.14 (KHTML, like Gecko) Version/7.0.3 Safari/7046A194A",
        "Chrome" => "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/60.0.3112.113 Safari/537.36",
        "IE" => "Mozilla/5.0 (Windows NT 6.1; WOW64; Trident/7.0; rv:11.0) like Gecko",
    ];

    public function __construct($options = [])
    {
        self::$current_page = 1;

        $this->initHttpClient();
    }

    final public function getRandomUserAgent(): string
    {
        return array_random($this->userAgents);
    }

    final public function getProxyAddress(): string
    {
        return $this->proxyAddress;
    }

    final protected function initHttpClient(): Client
    {
        $this->httpClient = new Client($this->getRequestOptions());

        return $this->httpClient;
    }

    final public function getDebugInfo(): array
    {
        return [
            "url" => $this->currentRequestOptions["url"] ?? "",
            "proxy" => $this->currentRequestOptions["proxy"] ?? "",
            "user-agent" => $this->currentRequestOptions["headers"]["User-Agent"] ?? "",
        ];
    }

    final protected function httpRequest($method, $url, $options = [])
    {
        $requestOptions = $this->getRequestOptions();
        $requestOptions = array_merge($requestOptions, $options);

        $this->currentRequestOptions["url"] = $url;

        self::debug_output($method . " : " . $url);
        self::debug_output($requestOptions);
        $response = $this->httpClient->$method($url, $requestOptions);

        $content = $response->getBody()->getContents();

        $encode = mb_detect_encoding($content, ["ASCII", 'UTF-8', "GB2312", "GBK", 'BIG5']);
        $content = iconv($encode, "UTF-8", $content);

        $json = json_decode($content, true);

        //check if json
        if (JSON_ERROR_NONE === json_last_error()) {
            return $json;
        } else {
            return $content;
        }
    }

    final protected function httpGet($url, $options = [])
    {
        return $this->httpRequest("get", $url, $options);
    }

    final protected function httpPost($url, $options = [])
    {
        return $this->httpRequest("post", $url, $options);
    }

    final protected function getRequestOptions(): array
    {
        $requestOptions = array_merge($this->defaultRequestOptions, $this->requestOptions ?? []);

        //phpunit mode disable proxy
        if (defined('PHPUNIT_IS_RUNNING')) {
            unset($requestOptions["proxy"]);
        }

        //set user-agent
        if (isset($requestOptions["user_agent"])) {
            if (isset($this->userAgents[$requestOptions["user_agent"]])) {
                $user_agent = $this->userAgents[$requestOptions["user_agent"]];
            } else {
                $user_agent = $requestOptions["user_agent"];
            }
            unset($requestOptions["user_agent"]);
        } else {
            $user_agent = $this->getRandomUserAgent();
        }

        $requestOptions["headers"] = array_merge($requestOptions["header"] ?? [], [
            "User-Agent" => $user_agent,
        ]);

        $this->currentRequestOptions = $requestOptions;

        return $requestOptions;
    }

    public function jsonp_decode($jsonp, $assoc = false)
    {
        if ('[' !== $jsonp[0] && '{' !== $jsonp[0]) { // we have JSONP
            $jsonp = substr($jsonp, strpos($jsonp, '('));
        }

        return json_decode(trim($jsonp, '();'), $assoc);
    }

    public function fomartNum($num)
    {
        $rule = "/(\d+|\d+.\d+)(万|亿|w)/";
        preg_match($rule, $num, $result);
        if (isset($result[1])) {
            if ('亿' == $result[2]) {
                return intval($result[1] * 100000000);
            } elseif ('万' == $result[2] || 'w' == $result[2]) {
                return intval($result[1] * 10000);
            } else {
                return intval($result[1]);
            }
        } else {
            return intval(str_replace(",", "", $num));
        }
    }

    public static function getCurrentPage()
    {
        return self::$current_page;
    }

    public static function increaseCurrentPage()
    {
        self::$current_page++;
    }

    public static function getLastTime()
    {
        return self::$last_time;
    }

    public static function setLastTime($time)
    {
        self::$last_time = $time;
    }
}
