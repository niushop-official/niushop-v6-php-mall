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

use app\dict\sys\ConfigKeyDict;
use app\model\sys\SysConfig;
use app\service\core\sys\CoreConfigService;
use core\base\BaseCoreService;
use think\Model;

/**
 * 微信小程序配置
 * Class CoreWeappConfigService
 * @package app\service\core\weapp
 */
class  CoreWeappConfigService extends BaseCoreService
{
    /**
     * 获取微信小程序设置
     * @return array
     */
    public function getWeappConfig()
    {
        $info = ( new CoreConfigService() )->getConfig(ConfigKeyDict::WEAPP)[ 'value' ] ?? [];
        return [
            'weapp_name' => $info[ 'weapp_name' ] ?? '',//小程序名称
            'weapp_original' => $info[ 'weapp_original' ] ?? '',//原始ID
            'app_id' => $info[ 'app_id' ] ?? '',//AppID
            'app_secret' => $info[ 'app_secret' ] ?? '',//AppSecret
            'qr_code' => $info[ 'qr_code' ] ?? '',//小程序二维码
            'token' => $info[ 'token' ] ?? '',
            'encoding_aes_key' => $info[ 'encoding_aes_key' ] ?? '',
            'encryption_type' => $info[ 'encryption_type' ] ?? 'not_encrypt',//加解密模式   not_encrypt 明文   compatible 兼容  safe 安全
            'upload_private_key' => $info[ 'upload_private_key' ] ?? '',
            'is_authorization' => $info[ 'is_authorization' ] ?? 0
        ];
    }

    /**
     * 微信小程序配置
     * @param array $data
     * @return SysConfig|bool|Model
     */
    public function setWeappConfig(array $data)
    {
        $old = $this->getWeappConfig();
        $config = [
            'weapp_name' => $data[ 'weapp_name' ] ?? '',//小程序名称
            'weapp_original' => $data[ 'weapp_original' ] ?? '',//原始ID
            'app_id' => $data[ 'app_id' ] ?? '',//AppID
            'app_secret' => $data[ 'app_secret' ] ?? '',//AppSecret
            'qr_code' => $data[ 'qr_code' ] ?? '',//小程序二维码
            'token' => $data[ 'token' ] ?? '',
            'encoding_aes_key' => $data[ 'encoding_aes_key' ] ?? '',
            'encryption_type' => $data[ 'encryption_type' ] ?? 'not_encrypt',//加解密模式   not_encrypt 明文   compatible 兼容  safe 安全
            'upload_private_key' => $data[ 'upload_private_key' ] ?? '',
            'is_authorization' => $data[ 'is_authorization' ] ?? $old[ 'is_authorization' ]
        ];
        return ( new CoreConfigService() )->setConfig(ConfigKeyDict::WEAPP, $config);
    }

    /**
     * 获取小程序授权信息
     * @return mixed
     */
    public function getWeappAuthorizationInfo()
    {
        return ( new CoreConfigService() )->getConfigValue(ConfigKeyDict::WEAPP_AUTHORIZATION_INFO);
    }

    /**
     * 设置小程序授权信息
     * @param array $config
     * @return SysConfig|bool|Model
     */
    public function setWeappAuthorizationInfo(array $config)
    {
        return ( new CoreConfigService() )->setConfig(ConfigKeyDict::WEAPP_AUTHORIZATION_INFO, $config);
    }

    /**
     * 清除小程序授权信息
     * @param array $config
     * @return SysConfig|bool|Model
     */
    public function clearWeappAuthorizationInfo()
    {
        return ( new CoreConfigService() )->clearConfig(ConfigKeyDict::WEAPP_AUTHORIZATION_INFO);
    }
}
