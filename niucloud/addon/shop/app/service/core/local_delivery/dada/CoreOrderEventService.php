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

namespace addon\shop\app\service\core\local_delivery\dada;

use addon\shop\app\dict\local_delivery\LocalDeliveryStatusDict;
use addon\shop\app\dict\local_delivery\OrderLogDict;
use addon\shop\app\model\local_delivery\LocalDeliveryOrder;
use addon\shop\app\service\core\local_delivery\CoreLocalDeliveryOrderLogService;
use core\base\BaseCoreService;
use core\exception\CommonException;

/**
 * 同城配送服务层
 * Class CoreExpressService
 * @package addon\shop\app\service\admin\delivery
 */
class CoreOrderEventService extends BaseCoreService
{
    public function __construct()
    {
        parent::__construct();
        $this->model = new LocalDeliveryOrder();
    }


    public function create(){

    }

    /**
     * 商家呼叫配送
     * @param $param
     * @param $delivery_no
     * @return true
     */
    public function orderCall($param, $delivery_no){
        $local_delivery_order = $this->model->where([['delivery_no', '=', $delivery_no]])->findOrEmpty();
        if ($local_delivery_order->isEmpty()) throw new CommonException('DELIVERY_ORDER_NOT_EXIT');
        if ($local_delivery_order->status == LocalDeliveryStatusDict::PENDING_ACCEPTANCE) {
            return true;
        }

        $notify_data = $param['notify_data'];

        $data = [
            'status' => LocalDeliveryStatusDict::PENDING_ACCEPTANCE,
            'out_status' => $notify_data['order_status'],
            'out_delivery_no' => $notify_data['client_id'],
        ];
        $this->model->where([['delivery_no', '=', $delivery_no]])->update($data);
        //todo 消息发送
//        (new NoticeService())->send('');
        //添加配送订单日志
        $operate = OrderLogDict::getOperate(LocalDeliveryStatusDict::PENDING_ACCEPTANCE);
        $log_data = [
            'order_id' => $local_delivery_order->id,
            'main_id' => 0,
            'main_name' => '',
            'main_type' => OrderLogDict::STORE,
            'operate' => $operate['operate'],
            'operate_desc' => $operate['operate_desc'],
            'status' => LocalDeliveryStatusDict::PENDING_ACCEPTANCE,
            'remark' => $operate['operate_desc'],
        ];
        (new CoreLocalDeliveryOrderLogService())->addLog($log_data);
        return true;
    }

    /**
     * 商家指派骑手
     * @param $param
     * @param $delivery_no
     * @return true
     */
    public function dispatchOrder($param, $delivery_no){
        $local_delivery_order = $this->model->where([['delivery_no', '=', $delivery_no]])->findOrEmpty();
        if ($local_delivery_order->isEmpty()) throw new CommonException('DELIVERY_ORDER_NOT_EXIT');
        if ($local_delivery_order->status == LocalDeliveryStatusDict::DISPATCH_ORDER) {
            return true;
        }

        $notify_data = $param['notify_data'];

        $data = [
            'status' => LocalDeliveryStatusDict::DISPATCH_ORDER,
            'out_status' => $notify_data['order_status'],
            'out_delivery_no' => $notify_data['client_id'],
        ];
        $this->model->where([['delivery_no', '=', $delivery_no]])->update($data);
        //todo 消息发送
//        (new NoticeService())->send('');
        //添加配送订单日志
        $operate = OrderLogDict::getOperate(LocalDeliveryStatusDict::DISPATCH_ORDER);
        $log_data = [
            'order_id' => $local_delivery_order->id,
            'main_id' => 0,
            'main_name' => '',
            'main_type' => OrderLogDict::STORE,
            'operate' => $operate['operate'],
            'operate_desc' => $operate['operate_desc'],
            'status' => LocalDeliveryStatusDict::DISPATCH_ORDER,
            'remark' => $operate['operate_desc'],
        ];
        (new CoreLocalDeliveryOrderLogService())->addLog($log_data);
        return true;
    }

    /**
     * 骑手已接单
     * @param $param
     * @param $delivery_no
     * @return true
     */
    public function riderAccepted($param, $delivery_no){
        $local_delivery_order = $this->model->where([['delivery_no', '=', $delivery_no]])->findOrEmpty();
        if ($local_delivery_order->isEmpty()) throw new CommonException('DELIVERY_ORDER_NOT_EXIT');
        if ($local_delivery_order->status == LocalDeliveryStatusDict::PENDING_PICKUP) {
            return true;
        }

        $notify_data = $param['notify_data'];

        $data = [
            'status' => LocalDeliveryStatusDict::PENDING_PICKUP,
            'out_status' => $notify_data['order_status'],
            'rider_name' => $notify_data['dm_name'] ?? '',
            'rider_mobile' => $notify_data['dm_mobile'] ?? '',
        ];
        $this->model->where([['delivery_no', '=', $delivery_no]])->update($data);
        //todo 消息发送
//        (new NoticeService())->send('');
        //添加配送订单日志
        $operate = OrderLogDict::getOperate(LocalDeliveryStatusDict::PENDING_PICKUP);
        $log_data = [
            'order_id' => $local_delivery_order->id,
            'main_id' => $notify_data['dm_id'] ?? 0,
            'main_name' => $notify_data['dm_name'] ?? '',
            'main_type' => OrderLogDict::RIDER,
            'operate' => $operate['operate'],
            'operate_desc' => $operate['operate_desc'],
            'status' => LocalDeliveryStatusDict::PENDING_PICKUP,
            'remark' => $operate['operate_desc'].'，骑手'.($notify_data['dm_name'] ?? ''),
        ];
        (new CoreLocalDeliveryOrderLogService())->addLog($log_data);
        return true;
    }

    /**
     * 骑手已到店
     * @param $param
     * @param $delivery_no
     * @return true
     */
    public function riderArrived($param, $delivery_no){
        $local_delivery_order = $this->model->where([['delivery_no', '=', $delivery_no]])->findOrEmpty();
        if ($local_delivery_order->isEmpty()) throw new CommonException('DELIVERY_ORDER_NOT_EXIT');
        if ($local_delivery_order->status == LocalDeliveryStatusDict::RIDER_ARRIVED) {
            return true;
        }

        $notify_data = $param['notify_data'];

        $data = [
            'status' => LocalDeliveryStatusDict::RIDER_ARRIVED,
            'out_status' => $notify_data['order_status'],
        ];
        $this->model->where([['delivery_no', '=', $delivery_no]])->update($data);
        //todo 消息发送
//        (new NoticeService())->send('');
        //添加配送订单日志
        $operate = OrderLogDict::getOperate(LocalDeliveryStatusDict::RIDER_ARRIVED);
        $log_data = [
            'order_id' => $local_delivery_order->id,
            'main_id' => $notify_data['dm_id'] ?? 0,
            'main_name' => $notify_data['dm_name'] ?? '',
            'main_type' => OrderLogDict::RIDER,
            'operate' => $operate['operate'],
            'operate_desc' => $operate['operate_desc'],
            'status' => LocalDeliveryStatusDict::RIDER_ARRIVED,
            'remark' => $operate['operate_desc'].'，骑手'.($notify_data['dm_name'] ?? ''),
        ];
        (new CoreLocalDeliveryOrderLogService())->addLog($log_data);
        return true;
    }

    /**
     * 骑手已取货
     * @param $param
     * @param $delivery_no
     * @return true
     */
    public function riderPickedUp($param, $delivery_no){
        $local_delivery_order = $this->model->where([['delivery_no', '=', $delivery_no]])->findOrEmpty();
        if ($local_delivery_order->isEmpty()) throw new CommonException('DELIVERY_ORDER_NOT_EXIT');
        if ($local_delivery_order->status == LocalDeliveryStatusDict::IN_DELIVERY) {
            return true;
        }

        $notify_data = $param['notify_data'];

        $data = [
            'status' => LocalDeliveryStatusDict::IN_DELIVERY,
            'out_status' => $notify_data['order_status'],
        ];
        $this->model->where([['delivery_no', '=', $delivery_no]])->update($data);
        //todo 消息发送
//        (new NoticeService())->send('');
        //添加配送订单日志
        $operate = OrderLogDict::getOperate(LocalDeliveryStatusDict::IN_DELIVERY);
        $log_data = [
            'order_id' => $local_delivery_order->id,
            'main_id' => $notify_data['dm_id'] ?? 0,
            'main_name' => $notify_data['dm_name'] ?? '',
            'main_type' => OrderLogDict::RIDER,
            'operate' => $operate['operate'],
            'operate_desc' => $operate['operate_desc'],
            'status' => LocalDeliveryStatusDict::IN_DELIVERY,
            'remark' => $operate['operate_desc'].'，骑手'.($notify_data['dm_name'] ?? ''),
        ];
        (new CoreLocalDeliveryOrderLogService())->addLog($log_data);
        return true;
    }

    /**
     * 物品返回中
     * @param $param
     * @param $delivery_no
     * @return true
     */
    public function returnApplied($param, $delivery_no){
        $local_delivery_order = $this->model->where([['delivery_no', '=', $delivery_no]])->findOrEmpty();
        if ($local_delivery_order->isEmpty()) throw new CommonException('DELIVERY_ORDER_NOT_EXIT');
        if ($local_delivery_order->status == LocalDeliveryStatusDict::RETURN_IN_PROGRESS) {
            return true;
        }

        $notify_data = $param['notify_data'];

        $data = [
            'status' => LocalDeliveryStatusDict::RETURN_IN_PROGRESS,
            'out_status' => $notify_data['order_status'],
        ];
        $this->model->where([['delivery_no', '=', $delivery_no]])->update($data);
        //todo 消息发送
//        (new NoticeService())->send('');
        //添加配送订单日志
        $operate = OrderLogDict::getOperate(LocalDeliveryStatusDict::RETURN_IN_PROGRESS);
        $log_data = [
            'order_id' => $local_delivery_order->id,
            'main_id' => $notify_data['dm_id'] ?? 0,
            'main_name' => $notify_data['dm_name'] ?? '',
            'main_type' => OrderLogDict::RIDER,
            'operate' => $operate['operate'],
            'operate_desc' => $operate['operate_desc'],
            'status' => LocalDeliveryStatusDict::RETURN_IN_PROGRESS,
            'remark' => $operate['operate_desc'].'，骑手'.($notify_data['dm_name'] ?? ''),
        ];
        (new CoreLocalDeliveryOrderLogService())->addLog($log_data);
        return true;
    }

    /**
     * 物品返回完成
     * @param $param
     * @param $delivery_no
     * @return true
     */
    public function returnCompleted($param, $delivery_no){
        $local_delivery_order = $this->model->where([['delivery_no', '=', $delivery_no]])->findOrEmpty();
        if ($local_delivery_order->isEmpty()) throw new CommonException('DELIVERY_ORDER_NOT_EXIT');
        if ($local_delivery_order->status == LocalDeliveryStatusDict::RETURN_COMPLETED) {
            return true;
        }

        $notify_data = $param['notify_data'];

        $data = [
            'status' => LocalDeliveryStatusDict::RETURN_COMPLETED,
            'out_status' => $notify_data['order_status'],
        ];
        $this->model->where([['delivery_no', '=', $delivery_no]])->update($data);
        //todo 消息发送
//        (new NoticeService())->send('');
        //添加配送订单日志
        $operate = OrderLogDict::getOperate(LocalDeliveryStatusDict::RETURN_COMPLETED);
        $log_data = [
            'order_id' => $local_delivery_order->id,
            'main_id' => $notify_data['dm_id'] ?? 0,
            'main_name' => $notify_data['dm_name'] ?? '',
            'main_type' => OrderLogDict::RIDER,
            'operate' => $operate['operate'],
            'operate_desc' => $operate['operate_desc'],
            'status' => LocalDeliveryStatusDict::RETURN_COMPLETED,
            'remark' => $operate['operate_desc'],
        ];
        (new CoreLocalDeliveryOrderLogService())->addLog($log_data);
        return true;
    }

    public function cancel($param, $delivery_no){
        $local_delivery_order = $this->model->where([['delivery_no', '=', $delivery_no]])->findOrEmpty();
        if ($local_delivery_order->isEmpty()) throw new CommonException('DELIVERY_ORDER_NOT_EXIT');
        if ($local_delivery_order->status == LocalDeliveryStatusDict::CANCELED) {
            return true;
        }

        $notify_data = $param['notify_data'];

        $data = [
            'status' => LocalDeliveryStatusDict::CANCELED,
            'out_status' => $notify_data['order_status'],
            'cancel_time' =>  time()
        ];
        if (!empty($notify_data['cancel_reason'])) {
            $data['remark'] = $notify_data['cancel_reason'];
        }
        $delivery_order_data = $local_delivery_order->toArray();
        $delivery_order_data['event'] = 'cancel';
        $this->model->where([['delivery_no', '=', $delivery_no]])->update($data);
        event('LocalDeliveryEvent', $delivery_order_data);

        //添加配送订单日志
        $operate = OrderLogDict::getOperate(LocalDeliveryStatusDict::CANCELED);
        $log_data = [
            'order_id' => $local_delivery_order->id,
            'main_id' => 0,
            'main_name' => '',
            'operate' => $operate['operate'],
            'operate_desc' => $operate['operate_desc'],
            'status' => LocalDeliveryStatusDict::CANCELED,
            'remark' => $operate['operate_desc'],
        ];
        if ($notify_data['cancel_from'] == 1) {
            $log_data['main_id'] = $notify_data['dm_id'] ?? 0;
            $log_data['main_name'] = $notify_data['dm_name'] ?? '';
            $log_data['main_type'] = OrderLogDict::RIDER;
            $log_data['remark'] = $operate['operate_desc'].'，骑手'.($notify_data['dm_name'] ?? '');
        } elseif ($notify_data['cancel_from'] == 2) {
            $log_data['main_type'] = OrderLogDict::STORE;
        } elseif ($notify_data['cancel_from'] == 3) {
            $log_data['main_type'] = OrderLogDict::SYSTEM;
        } else {
            $log_data['main_type'] = OrderLogDict::SYSTEM;
        }
        (new CoreLocalDeliveryOrderLogService())->addLog($log_data);
        return true;
    }

    public function finish($param, $delivery_no){
        $local_delivery_order = $this->model->where([['delivery_no', '=', $delivery_no]])->findOrEmpty();
        if ($local_delivery_order->isEmpty()) throw new CommonException('DELIVERY_ORDER_NOT_EXIT');
        if ($local_delivery_order->status == LocalDeliveryStatusDict::COMPLETED) {
            return true;
        }

        $notify_data = $param['notify_data'];

        $data = [
            'status' => LocalDeliveryStatusDict::COMPLETED,
            'out_status' => $notify_data['order_status'],
            'finish_time' => time()
        ];
        $delivery_order_data = $local_delivery_order->toArray();
        $delivery_order_data['event'] = 'finish';
        $this->model->where([['delivery_no', '=', $delivery_no]])->update($data);
        event('LocalDeliveryEvent', $delivery_order_data);
        //添加配送订单日志
        $operate = OrderLogDict::getOperate(LocalDeliveryStatusDict::COMPLETED);
        $log_data = [
            'order_id' => $local_delivery_order->id,
            'main_id' => $notify_data['dm_id'] ?? 0,
            'main_name' => $notify_data['dm_name'] ?? '',
            'main_type' => OrderLogDict::RIDER,
            'operate' => $operate['operate'],
            'operate_desc' => $operate['operate_desc'],
            'status' => LocalDeliveryStatusDict::COMPLETED,
            'remark' => $operate['operate_desc'],
        ];
        (new CoreLocalDeliveryOrderLogService())->addLog($log_data);
        return true;
    }

    /**
     * 创建达达运单失败
     * @param $param
     * @param $delivery_no
     * @return true
     */
    public function createOrderFailed($param, $delivery_no){
        $local_delivery_order = $this->model->where([['delivery_no', '=', $delivery_no]])->findOrEmpty();
        if ($local_delivery_order->isEmpty()) throw new CommonException('DELIVERY_ORDER_NOT_EXIT');
        if ($local_delivery_order->status == LocalDeliveryStatusDict::CREATE_ORDER_FAILED) {
            return true;
        }

        $notify_data = $param['notify_data'];

        $data = [
            'status' => LocalDeliveryStatusDict::CREATE_ORDER_FAILED,
            'out_status' => $notify_data['order_status'],
        ];
        $delivery_order_data = $local_delivery_order->toArray();
        $delivery_order_data['event'] = 'createOrderFailed';
        $this->model->where([['delivery_no', '=', $delivery_no]])->update($data);
        event('LocalDeliveryEvent', $delivery_order_data);
        return true;
    }

    /**
     * 售后取件单送达门店
     * @param $param
     * @param $delivery_no
     * @return true
     */
    public function returnRiderArrived($param, $delivery_no){
        $local_delivery_order = $this->model->where([['delivery_no', '=', $delivery_no]])->findOrEmpty();
        if ($local_delivery_order->isEmpty()) throw new CommonException('DELIVERY_ORDER_NOT_EXIT');
        if ($local_delivery_order->status == LocalDeliveryStatusDict::RETURN_RIDER_ARRIVED) {
            return true;
        }

        $notify_data = $param['notify_data'];

        $data = [
            'status' => LocalDeliveryStatusDict::RETURN_RIDER_ARRIVED,
            'out_status' => $notify_data['order_status'],
        ];
        $this->model->where([['delivery_no', '=', $delivery_no]])->update($data);
        return true;
    }

}
