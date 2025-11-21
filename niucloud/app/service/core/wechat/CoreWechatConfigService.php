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

use app\dict\channel\WechatDict;
use app\dict\sys\ConfigKeyDict;
use app\model\sys\SysConfig;
use app\service\core\sys\CoreConfigService;
use app\service\core\sys\CoreSysConfigService;
use core\base\BaseCoreService;
use think\Model;

/**
 * 微信配置模型
 * Class WechatConfigService
 * @package app\service\core\wechat
 */
class CoreWechatConfigService extends BaseCoreService
{
    /**
     * 获取微信公众号配置
     * @return array
     */
    public function getWechatConfig()
    {
        $info = (new CoreConfigService())->getConfig(ConfigKeyDict::WECHAT)['value'] ?? [];
        return [
            'wechat_name' => $info['wechat_name'] ?? '',//公众号名称
            'wechat_original' => $info['wechat_original'] ?? '',//原始ID
            'app_id' => $info['app_id'] ?? '',//AppID
            'app_secret' => $info['app_secret'] ?? '',//AppSecret
            'qr_code' => $info['qr_code'] ?? '',//公众号二维码
            'token' => $info['token'] ?? '',
            'encoding_aes_key' => $info['encoding_aes_key'] ?? '',
            'encryption_type' => $info['encryption_type'] ?? 'not_encrypt',//加解密模式   not_encrypt 明文   compatible 兼容  safe 安全
            'is_authorization' => $info['is_authorization'] ?? 0,
            'base_uri' => $info['base_uri'] ?? ''
        ];
    }

    /**
     * 设置微信公众号配置
     * @param array $data
     * @return SysConfig|bool|Model
     */
    public function setWechatConfig(array $data)
    {
        $old = $this->getWechatConfig();
        $config = [
            'wechat_name' => $data['wechat_name'] ?? '',//公众号名称
            'wechat_original' => $data['wechat_original'] ?? '',//原始ID
            'app_id' => $data['app_id'] ?? '',//AppID
            'app_secret' => $data['app_secret'] ?? '',//AppSecret
            'qr_code' => $data['qr_code'] ?? '',//公众号二维码
            'token' => $data['token'] ?? '',
            'encoding_aes_key' => $data['encoding_aes_key'] ?? '',
            'encryption_type' => $data['encryption_type'] ?? '',
            'is_authorization' => $data['is_authorization'] ?? $old['is_authorization']
        ];
        return (new CoreConfigService())->setConfig(ConfigKeyDict::WECHAT, $config);
    }

    /**
     * 查询微信需要的静态信息
     * @return array
     */
    public function getWechatStaticInfo()
    {
        $wap_domain = (new CoreSysConfigService())->getSceneDomain()['wap_domain'] ?? '';
        $wap_domain = str_replace('http://', '', $wap_domain);
        $wap_domain = str_replace('https://', '', $wap_domain);
        return [
            'serve_url' => (string)url('/api/wechat/serve', [], '', true),
            'business_domain' => $wap_domain,
            'js_secure_domain' => $wap_domain,
            'web_auth_domain' => $wap_domain,
            'encryption_type' => WechatDict::getEncryptionType()
        ];
    }

    /**
     * 获取小程序授权信息
     * @return mixed
     */
    public function getWechatAuthorizationInfo()
    {
        return (new CoreConfigService())->getConfigValue(ConfigKeyDict::WECHAT_AUTHORIZATION_INFO);
    }

    /**
     * 设置小程序授权信息
     * @param array $config
     * @return SysConfig|bool|Model
     */
    public function setWechatAuthorizationInfo(array $config)
    {
        return (new CoreConfigService())->setConfig(ConfigKeyDict::WECHAT_AUTHORIZATION_INFO, $config);
    }

    /**
     * 清除授权信息
     * @return mixed
     */
    public function clearWechatAuthorizationInfo()
    {
        return (new CoreConfigService())->clearConfig(ConfigKeyDict::WECHAT_AUTHORIZATION_INFO);
    }
}
