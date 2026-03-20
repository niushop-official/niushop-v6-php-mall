<?php

namespace addon\shop\core\local_delivery\sdk\dada;

use core\util\http\HttpClient;
use think\facade\Log;

class BaseRequest
{
    protected $test_domain = 'https://newopen.imdada.cn';
    protected $domain = 'https://newopen.imdada.cn';
    protected $app_secret = '';
    protected $app_key = '';
    protected $source_id = '';
    protected $callback = null;

    // 设置App Secret
    public function setAppSecret(string $app_secret): self
    {
        $this->app_secret = $app_secret;
        return $this;
    }

    public function getAppSecret(): string
    {
        return $this->app_secret;
    }

    // 设置App Key
    public function setAppKey(string $app_key): self
    {
        $this->app_key = $app_key;
        return $this;
    }

    public function getAppKey(): string
    {
        return $this->app_key;
    }

    // 设置商户ID
    public function setSourceId(string $source_id): self
    {
        $this->source_id = $source_id;
        return $this;
    }

    public function getSourceId(): string
    {
        return $this->source_id;
    }

    // 回调地址
    public function setCallback(string $callback): self
    {
        $this->callback = $callback;
        return $this;
    }

    public function getCallback()
    {
        return $this->callback;
    }

    // 获取基础参数
    public function getBaseParams(): array
    {
        return [
            'app_key' => $this->app_key,
            'source_id' => $this->source_id,
            'app_secret' => $this->app_secret,
        ];
    }

    // 获取基础参数
    public function setBaseParams($config)
    {
        $this->app_key = $config['app_key'];
        $this->source_id = $config['source_id'];
        $this->app_secret = $config['app_secret'];
        return $this;
    }


    /**
     * 构建请求参数
     *
     * @param mixed $body 接口的body参数内容
     * @return array 构建真正的api接口参数
     */
    protected function generateParams($body)
    {
        $data = [
            'source_id' => $this->source_id,
            'app_key' => $this->app_key,
            'timestamp' => time(),
            'format' => 'json',
            'v' => '1.0',
            'body' => empty($body) ? '' : json_encode($body),
        ];

        $data['signature'] = $this->signature($data);
        return $data;
    }

    /**
     * 生成签名
     *
     * @param array $data 请求参数
     * @param string $app_secret 应用密钥
     * @return string 签名结果
     */
    private function signature($data)
    {
        // 请求参数按照【属性名】字典升序排序
        ksort($data);

        // 按照属性名+属性值拼接
        $signStr = '';
        foreach ($data as $key => $value) {
            $signStr .= $key . $value;
        }

        // 拼接后的结果首尾加上appSecret
        $finalSignStr = $this->app_secret . $signStr . $this->app_secret;

        // MD5加密并转为大写
        return strtoupper(md5($finalSignStr));
    }

    protected function request($body, $method_url)
    {
        Log::write('dada：请求参数：' . json_encode($body));
        $product_type = env('product_type');
        if ($product_type != 'product') {
            $domain = $this->test_domain;
        } else {
            $domain = $this->domain;
        }
        $request_data = $this->generateParams($body);
        $res = (new HttpClient())->httpPost($domain . $method_url, $request_data, [
            'verify' => false
        ]);
        Log::write('dada：响应参数：' . json_encode($res));
        if ($res['code'] != 0) {
            throw new \Exception($res['msg']);
        }
        return $res;
    }
}