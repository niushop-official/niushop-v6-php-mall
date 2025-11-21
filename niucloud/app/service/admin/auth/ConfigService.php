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

namespace app\service\admin\auth;

use app\dict\sys\ConfigKeyDict;
use app\model\sys\SysConfig;
use app\service\core\sys\CoreConfigService;
use core\base\BaseAdminService;
use think\Model;

/**
 * 登录服务层
 * Class BaseService
 * @package app\service
 */
class ConfigService extends BaseAdminService
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 获取注册与登录设置
     * @return array
     */
    public function getConfig()
    {
        $info = (new CoreConfigService())->getConfig(ConfigKeyDict::ADMIN_LOGIN)['value'] ?? [];
        return [
            'login_logo' => '',
            'login_bg_img' => '',
            'is_captcha' => $info['is_captcha'] ?? 0,//是否启用验证码
            'bg' => $info['bg'] ?? config('install.admin_login_bg'),//平台登录端 背景
        ];
    }

    /**
     * 注册与登录设置
     * @param array $data
     * @return true
     */
    public function setConfig(array $data)
    {
        $config = [
            'is_captcha' => $data['is_captcha'] ?? 0,//是否启用验证码
            'bg' => $data['bg'] ?? '',//平台登录端 背景
            'login_logo' => $data['login_logo'] ?? '',//平台登录端 背景
            'login_bg_img' => $data['login_bg_img'] ?? '',//平台登录端 背景
        ];
        (new CoreConfigService())->setConfig(ConfigKeyDict::ADMIN_LOGIN, $config);
        return true;
    }

}
