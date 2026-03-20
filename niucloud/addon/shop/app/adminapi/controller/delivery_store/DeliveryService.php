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

namespace addon\shop\app\adminapi\controller\delivery_store;

use addon\shop\app\service\admin\delivery_store\DeliveryServiceService;
use core\base\BaseAdminController;
use think\Response;


/**
 * 配送服务商控制器
 * Class DeliveryService
 * @package addon\shop\app\adminapi\controller\delivery_store
 */
class DeliveryService extends BaseAdminController
{

    /**
     * 获取配送服务商列表
     */
    public function getDeliveryServiceList($id)
    {
        return success((new DeliveryServiceService())->deliveryServiceList($id));
    }

    /**
     * 配送门店开通配送服务
     * @return Response
     */
    public function deliveryServiceOpen($id)
    {
        $data = $this->request->params([
            ['delivery_type', ''],
            ['business', ''],
            ['extend_data', []]
        ]);
        (new DeliveryServiceService())->deliveryServiceOpen($id, $data);
        return success('SUCCESS');
    }

    /**
     * 配送门店品类修改
     * @return Response
     */
    public function deliveryServiceEdit($id)
    {
        $data = $this->request->params([
            ['delivery_type', ''],
            ['business', '']
        ]);
        (new DeliveryServiceService())->deliveryServiceEdit($id, $data);
        return success('SUCCESS');
    }
}
