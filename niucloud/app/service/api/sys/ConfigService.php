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

namespace app\service\api\sys;

use app\service\core\addon\CoreAddonService;
use app\service\core\channel\CoreAppService;
use app\service\core\sys\CoreConfigService;
use app\service\core\sys\CoreSysConfigService;
use app\service\core\weapp\CoreWeappConfigService;
use core\base\BaseApiService;

/**
 * 配置服务层
 * Class ConfigService
 * @package app\service\core\sys
 */
class ConfigService extends BaseApiService
{
    //系统配置文件


    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 获取版权信息(网站整体，不按照站点设置)
     * @return array|mixed
     */
    public function getCopyright()
    {
       return (new CoreSysConfigService())->getCopyright();
    }

    /**
     * 获取前端域名
     * @return array|string[]
     */
    public function getSceneDomain(){
        return (new CoreSysConfigService())->getSceneDomain();
    }

    /**
     * 获取手机端首页列表
     * @param $data
     * @return array
     */
    public function getWapIndexList($data = [])
    {
        return ( new CoreSysConfigService() )->getWapIndexList($data);
    }

    public function getWebSite()
    {
        $info = ( new CoreConfigService() )->getConfigValue('WEB_SITE_INFO');
        if (empty($info)) {
            $info = [
                'site_name' => '',
                'logo' => '',
                'desc' => '',
                'latitude' => '',
                'longitude' => '',
                'province_id' => 0,
                'city_id' => 0,
                'district_id' => 0,
                'address' => '',
                'full_address' => '',
                'phone' => '',
                'business_hours' => '',
                'front_end_name' => '',
                'front_end_logo' => '',
                'icon' => '',
            ];
        }
        $info['site_addons'] = (new CoreAddonService())->getInstallAddonList();
        return $info;
    }

    public function getMap(){
        return (new CoreSysConfigService())->getMap();
    }

    public function getAppConfig() {
        $config = (new CoreAppService())->getConfig();
        if (!empty($config) && isset($config['wechat_app_secret'])) unset($config['wechat_app_secret']);
        $weapp_config = (new CoreWeappConfigService())->getWeappConfig();
        $config['weapp_original'] = $weapp_config['weapp_original'];
        return $config;
    }

}
