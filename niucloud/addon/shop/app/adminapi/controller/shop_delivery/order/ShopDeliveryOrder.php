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

namespace addon\shop\app\adminapi\controller\shop_delivery\order;

use addon\shop\app\service\admin\shop_delivery\order\ShopDeliveryOrderService;
use core\base\BaseAdminController;
use think\Response;


/**
 * 商家配送订单控制器
 * Class ShopDeliveryOrder
 * @package addon\shop\app\adminapi\controller\shop_delivery\order
 */
class ShopDeliveryOrder extends BaseAdminController
{
    /**
     * 商家配送订单分页列表
     * @return Response
     */
    public function pages(): Response
    {
        $data = $this->request->params([
            ['delivery_no', ''],
            ['trade_no', ''],
            ['status', ''],
            ['create_time', []],
        ]);
        return success(( new ShopDeliveryOrderService() )->getPage($data));
    }

    /**
     * 商家配送订单详情
     * @param int $id
     * @return Response
     */
    public function info(int $id): Response
    {
        return success(( new ShopDeliveryOrderService() )->getInfo($id));
    }

    /**
     * 取消订单
     * @param int $id
     * @return Response
     */
    public function closeOrder(int $id): Response
    {
        $data = $this->request->params([
            ['cancel_reason', '']
        ]);
        $res = ( new ShopDeliveryOrderService() )->closeOrder($id, $data);
        if ($res) {
            return success('CANCEL_SUCCESS');
        } else {
            return fail('FAIL');
        }
    }

    /**
     * 完成订单
     * @param int $id
     * @return Response
     */
    public function finishOrder(int $id): Response
    {
        $res = ( new ShopDeliveryOrderService() )->finishOrder($id);
        if ($res) {
            return success('SUCCESS');
        } else {
            return fail('FAIL');
        }
    }

    /**
     * 获取订单状态列表
     * @return Response
     */
    public function getOrderStatusList(): Response
    {
        return success((new ShopDeliveryOrderService())->getOrderStatusList());
    }
}
