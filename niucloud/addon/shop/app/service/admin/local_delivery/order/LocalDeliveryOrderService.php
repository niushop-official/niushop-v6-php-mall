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

namespace addon\shop\app\service\admin\local_delivery\order;

use addon\shop\app\dict\local_delivery\LocalDeliveryStatusDict;
use addon\shop\app\dict\local_delivery\OrderCancelReasonDict;
use addon\shop\app\dict\local_delivery\OrderLogDict;
use addon\shop\app\model\local_delivery\LocalDeliveryOrder;
use app\model\sys\SysUser;
use addon\shop\app\service\core\local_delivery\CoreLocalDeliveryOrderCloseService;
use addon\shop\app\service\core\local_delivery\CoreLocalDeliveryOrderFinishService;
use addon\shop\app\service\core\local_delivery\CoreLocalDeliveryOrderService;
use core\base\BaseAdminService;
use think\db\exception\DbException;

/**
 * 本地配送订单服务层
 * Class LocalDeliveryOrderService
 * @package app\service\admin\delivery\site
 */
class LocalDeliveryOrderService extends BaseAdminService
{

    public function __construct()
    {
        parent::__construct();
        $this->model = new LocalDeliveryOrder();
    }

    /**
     * 查询本地配送订单列表
     * @param array $where
     * @return array
     * @throws DbException
     */
    public function getPage(array $where)
    {
        $field = 'id, delivery_service, delivery_no, trade_id, trade_no, trade_type, delivery_money, delivery_start, delivery_start_lng, delivery_start_lat, delivery_end, delivery_end_lng, delivery_end_lat, delivery_distance, status, create_time, remark, body';
        $search_model = $this->model->where([ ['id', '>', 0] ])->withSearch([ 'delivery_no', 'trade_no', 'status', 'create_time' ], $where)->field($field)->order('create_time desc')->append([ 'delivery_service_name' , 'status_name', 'trade_type_name', 'pre_status_name' ]);
        return $this->pageQuery($search_model);
    }

    /**
     * 查询本地配送订单详情
     * @return array
     */
    public function getInfo(int $id)
    {
        $field = 'id, delivery_service, delivery_no, out_delivery_no, trade_id, trade_no, trade_type, delivery_money, delivery_start, delivery_start_lng, delivery_start_lat, delivery_end, delivery_end_lng, delivery_end_lat, delivery_distance, status, create_time, finish_time, cancel_time, rider_name, rider_mobile, remark, body';
        return $this->model->where([ [ 'id', '=', $id ] ])->field($field)->with(['orderLog'])->append([ 'delivery_service_name' , 'status_name', 'trade_type_name' ])->findOrEmpty()->toArray();
    }

    /**
     * 取消订单
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function closeOrder(int $id, array $data)
    {
        $data['main_id'] = $this->uid;
        $data['main_type'] = OrderLogDict::STORE;
        $data['main_name'] = (new SysUser())->where('uid', $this->uid)->value('username') ?? '';
        $data['id'] = $id;
        return (new CoreLocalDeliveryOrderCloseService())->closeOrder($data);
    }

    /**
     * 完成订单
     * @param int $id
     * @return bool
     */
    public function finishOrder(int $id)
    {
        $data['main_id'] = $this->uid;
        $data['main_type'] = OrderLogDict::STORE;
        $data['main_name'] = (new SysUser())->where('uid', $this->uid)->value('username') ?? '';
        $data['id'] = $id;
        return (new CoreLocalDeliveryOrderFinishService())->finishOrder($data);
    }

    /**
     * 同步订单信息
     * @param int $id
     * @return bool
     */
    public function syncOrder(int $id)
    {
        return (new CoreLocalDeliveryOrderService())->syncOrder($id);
    }

    /**
     * 订单取消原因
     * @return array
     */
    public function getCancelReasonList(string $service)
    {
        return OrderCancelReasonDict::getType('', $service);
    }

    /**
     * 获取订单状态列表
     * @return array
     */
    public function getOrderStatusList()
    {
        return LocalDeliveryStatusDict::getStatus();
    }
}
