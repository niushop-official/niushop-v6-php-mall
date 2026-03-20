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

namespace app\service\core\wechat;

use core\base\BaseCoreService;
use core\exception\CommonException;
use EasyWeChat\Kernel\Exceptions\BadRequestException;
use EasyWeChat\Kernel\Exceptions\InvalidArgumentException;
use EasyWeChat\Kernel\Exceptions\InvalidConfigException;
use Overtrue\Socialite\Contracts\UserInterface;
use ReflectionException;
use Symfony\Component\HttpFoundation\Response;

/**
 * 微信服务提供
 * Class CoreWechatServeService
 * @package app\service\core\wechat
 */
class CoreWechatServeService extends BaseCoreService
{


    /**
     * 网页授权
     * @param string $url
     * @param string $scopes
     * @return string
     */
    public function authorization(string $url = '', string $scopes = 'snsapi_base')
    {
        $oauth = CoreWechatService::app()->getOauth();
        return $oauth->scopes([ $scopes ])->redirect($url);
    }

    /**
     * 处理授权回调
     * @param string $code
     * @return UserInterface
     */
    public function userFromCode(string $code)
    {
        try {
            $oauth = CoreWechatService::app()->getOauth();
            return $oauth->userFromCode($code);
        } catch (\Exception $e) {
            throw new CommonException($e->getCode() . '：' . $e->getMessage());
        }
    }

    public function getUser($user)
    {
        $user->getId(); //对应微信的 openid
        $user->getNickname(); //对应微信的 nickname
        $user->getName(); //对应微信的 nickname
        $user->getAvatar(); //头像地址
        $user->getRaw(); //原始 API 返回的结果
        $user->getAccessToken(); //access_token
        $user->getRefreshToken(); //refresh_token
        $user->getExpiresIn(); //expires_in，Access Token 过期时间
        $user->getTokenResponse(); //返回 access_token 时的响应值
        //user中没有openid,  可以用id取
        return $user;
    }

    /**
     * 事件推送
     * @return Response
     * @throws BadRequestException
     * @throws InvalidArgumentException
     * @throws ReflectionException
     * @throws InvalidConfigException
     */
    public function serve()
    {

        $app = CoreWechatService::app();
        $server = $app->getServer();
        $server->with(function($message, \Closure $next) {
            // 你的自定义逻辑
            return ( new CoreWechatMessageService )->message($message);
        });
        $response = $server->serve();
        return $response;

    }

    /**
     * 配置 生成 JS-SDK 签名
     * @param string $url
     * @return mixed[]
     * @throws InvalidArgumentException
     * @throws \EasyWeChat\Kernel\Exceptions\HttpException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function jssdkConfig(string $url = '')
    {
        $utils = CoreWechatService::app()->getUtils();
        return $utils->buildJsSdkConfig(
            url: $url,
            jsApiList: [],
            openTagList: [],
            debug: false,
        );
    }

    /**
     * 生成临时二维码
     * @param string $key
     * @param int $expire_seconds
     * @param $params
     * @return \EasyWeChat\Kernel\HttpClient\Response|\Symfony\Contracts\HttpClient\ResponseInterface
     * @throws InvalidArgumentException
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function scan(string $key, int $expire_seconds = 6 * 24 * 3600, $params = [])
    {
        $api = CoreWechatService::appApiClient();
        if (is_int($key) && $key > 0) {
            $type = 'QR_SCENE';
            $sceneKey = 'scene_id';
        } else {
            $type = 'QR_STR_SCENE';
            $sceneKey = 'scene_str';
        }
        $scene = [ $sceneKey => $key ];
        $param = [
            'expire_seconds' => $expire_seconds,
            'action_name' => $type,
            'action_info' => [
                'scene' => $scene
            ],
        ];
        $response = $api->postJson('cgi-bin/qrcode/create', $param);
        return json_decode($response->getContent(), true);
    }
}
