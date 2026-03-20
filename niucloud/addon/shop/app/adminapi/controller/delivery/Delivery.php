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

namespace addon\shop\app\adminapi\controller\delivery;

use addon\shop\app\dict\order\OrderDeliveryDict;
use addon\shop\app\service\admin\delivery\DeliveryService;
use core\base\BaseAdminController;


/**
 * 物流配置
 * Class Config
 * @package addon\shop\app\adminapi\controller\config
 */
class Delivery extends BaseAdminController
{

    /**
     * 配送信息设置
     * @description 设置物流配置
     * @return \think\Response
     */
    public function setDeliveryConfig()
    {
        $data = $this->request->params([
            [ "value", "" ],
        ]);

        ( new DeliveryService() )->setConfig($data[ 'value' ]);
        return success('SUCCESS');
    }

    /**
     * 配送页信息
     * @description 获取配送页信息
     * @return \think\Response
     */
    public function getDelivery()
    {
        return success(( new DeliveryService() )->getDeliveryList());
    }

    /**
     * 配送页信息
     * @description 获取配送页信息配置
     * @return \think\Response
     */
    public function getDeliveryList()
    {
        return success(( new DeliveryService() )->getDeliveryConfigList());
    }

    /**
     * 获取配送方式
     * @return \think\Response
     */
    public function getDeliveryType()
    {
        return success(OrderDeliveryDict::getType());
    }
}
