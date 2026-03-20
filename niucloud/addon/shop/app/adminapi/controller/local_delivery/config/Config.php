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

namespace addon\shop\app\adminapi\controller\local_delivery\config;

use addon\shop\app\service\admin\local_delivery\config\ConfigService;
use core\base\BaseAdminController;
use think\Response;


/**
 * 配送配置
 * Class Config
 * @package addon\shop\app\adminapi\controller\local_delivery\config
 */
class Config extends BaseAdminController
{
    /**
     * 设置配置
     * @return Response
     */
    public function setConfig(): Response
    {
        $data = $this->request->params([
            ['is_show_polyline',0],//是否显示配送轨迹
            ['is_local_delivery_store_select',0],//是否开启门店选择
        ]);
        (new ConfigService())->setConfig($data);
        return success('SUCCESS');

    }

    /**
     * 获取配置
     * @return Response
     */
    public function getConfig(): Response
    {
        return success((new ConfigService())->getConfig());
    }
}