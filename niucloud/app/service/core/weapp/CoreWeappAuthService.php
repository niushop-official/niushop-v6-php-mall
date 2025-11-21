<?php
// +----------------------------------------------------------------------
// | Niucloud-admin 企业快速开发的多应用管理平台
// +----------------------------------------------------------------------
// | 官方网址：https://www.niucloud.com
// +----------------------------------------------------------------------
// | niucloud团队 版权所有 开源版本可自由商用
// +----------------------------------------------------------------------
// | Author: Niucloud Team
// +----------------------------------------------------------------------

namespace app\service\core\weapp;

use core\base\BaseCoreService;
use EasyWeChat\Kernel\Exceptions\DecryptException;
use EasyWeChat\Kernel\Exceptions\InvalidArgumentException;

/**
 * 微信小程序服务提供
 * Class CoreWeappAuthService
 * @package app\service\core\weapp
 */
class CoreWeappAuthService extends BaseCoreService
{

    /**
     * 网页授权
     * @param string|null $code
     * @return array
     * @throws InvalidArgumentException
     * @throws \EasyWeChat\Kernel\Exceptions\HttpException
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function session(?string $code)
    {
        $utils = CoreWeappService::app()->getUtils();
        return $utils->codeToSession($code);
    }

    /**
     * 开发者后台校验与解密开放数据
     * @param string $session
     * @param string $iv
     * @param string $encrypted_data
     * @return array
     * @throws DecryptException
     * @throws InvalidArgumentException
     */
    public function decryptData(string $session, string $iv, string $encrypted_data)
    {
        $utils = CoreWeappService::app()->getUtils();
        return $utils->decryptSession($session, $iv, $encrypted_data);
    }

    /**
     * 获取用户手机号
     * @param string $code
     * @return \EasyWeChat\Kernel\HttpClient\Response|\Symfony\Contracts\HttpClient\ResponseInterface
     * @throws InvalidArgumentException
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function getUserPhoneNumber(string $code)
    {
        $api = CoreWeappService::appApiClient();
        CoreWeappService::refreshToken(); // 为防止多次拒绝手机号的情况，需要刷新token
        return $api->postJson('wxa/business/getuserphonenumber', [
            'code' => (string) $code
        ]);
    }
}
