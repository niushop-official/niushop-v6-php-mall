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

namespace addon\shop\app\adminapi\controller\local_delivery\order;

use addon\shop\app\service\admin\local_delivery\order\LocalDeliveryOrderService;
use core\base\BaseAdminController;
use think\Response;


/**
 * 本地配送订单控制器
 * Class LocalDeliveryOrder
 * @package addon\shop\app\adminapi\controller\local_delivery\order
 */
class LocalDeliveryOrder extends BaseAdminController
{
    /**
     * 本地配送订单分页列表
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
        return success(( new LocalDeliveryOrderService() )->getPage($data));
    }

    /**
     * 本地配送订单详情
     * @param int $id
     * @return Response
     */
    public function info(int $id): Response
    {
        return success(( new LocalDeliveryOrderService() )->getInfo($id));
    }

    /**
     * 取消订单
     * @param int $id
     * @return Response
     */
    public function closeOrder(int $id): Response
    {
        $data = $this->request->params([
            ['cancel_reason_id', ''],
            ['cancel_reason', '']
        ]);
        $res = ( new LocalDeliveryOrderService() )->closeOrder($id, $data);
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
        $res = ( new LocalDeliveryOrderService() )->finishOrder($id);
        if ($res) {
            return success('SUCCESS');
        } else {
            return fail('FAIL');
        }
    }

    /**
     * 同步订单信息
     * @param int $id
     * @return Response
     */
    public function syncOrder(int $id): Response
    {
        $res = ( new LocalDeliveryOrderService() )->syncOrder($id);
        if ($res) {
            return success('SUCCESS');
        } else {
            return fail('FAIL');
        }
    }

    /**
     * 订单取消原因
     * @return Response
     */
    public function getCancelReasonList(): Response
    {
        $data = $this->request->params([
            ['service', '']
        ]);
        return success(( new LocalDeliveryOrderService() )->getCancelReasonList($data['service']));
    }

    /**
     * 获取订单状态列表
     * @return Response
     */
    public function getOrderStatusList(): Response
    {
        return success((new LocalDeliveryOrderService())->getOrderStatusList());
    }
}
