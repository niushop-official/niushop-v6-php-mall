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

namespace addon\shop\app\service\admin\local_delivery\config;

use addon\shop\app\service\core\delivery\CoreConfigService;
use app\model\sys\SysConfig;
use core\base\BaseAdminService;
use think\Model;

/**
 * 配送配置服务层
 * Class ConfigService
 * @package addon\shop\app\service\admin\local_delivery\config
 */
class ConfigService extends BaseAdminService
{
    /**
     * 设置配送配置
     * @param $data
     * @return SysConfig|bool|Model
     */
    public function setConfig($data)
    {
        return (new CoreConfigService())->setLocalDeliveryConfig($data);
    }

    /**
     * 获取配送配置
     * @return array
     */
    public function getConfig()
    {
        return (new CoreConfigService())->getLocalDeliveryConfig();
    }
}
