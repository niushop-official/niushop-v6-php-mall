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

use app\service\core\channel\CoreAppService;
use core\base\BaseCoreService;
use core\exception\CommonException;
use core\exception\WechatException;
use EasyWeChat\Kernel\Exceptions\InvalidArgumentException;
use EasyWeChat\OfficialAccount\Application;

/**
 * 微信服务api提供
 * Class CoreWechatApiService
 * @package app\service\core\wechat
 */
class CoreWechatAppService extends BaseCoreService
{
    /**
     * 获取公众号的handle
     * @return Application
     * @throws InvalidArgumentException
     */
    public static function app()
    {
        $app_config = (new CoreAppService())->getConfig();

        if (empty($app_config['wechat_app_id']) || empty($app_config['wechat_app_secret'])) throw new WechatException('WECHAT_NOT_EXIST');//公众号未配置

        $config = array(
            'app_id' => $app_config['wechat_app_id'],
            'secret' => $app_config['wechat_app_secret'],
            'token' => "",
            'aes_key' => 'not_encrypt',
            'http' => [
                'timeout' => 5.0,
                'retry' => true, // 使用默认重试配置
            ]
        );
        return new Application($config);
    }

    /**
     * 微信实例接口调用
     * @return \EasyWeChat\Kernel\HttpClient\AccessTokenAwareClient
     * @throws InvalidArgumentException
     */
    public static function appApiClient()
    {
        return self::app()->getClient();
    }

    /**
     * 处理授权回调
     * @param string $code
     * @return \Overtrue\Socialite\Contracts\UserInterface
     */
    public static function userFromCode(string $code)
    {
        try {
            $oauth = self::app()->getOauth();
            return $oauth->userFromCode($code);
        } catch (\Exception $e) {
            throw new CommonException($e->getCode() . '：' . $e->getMessage());
        }
    }
}
