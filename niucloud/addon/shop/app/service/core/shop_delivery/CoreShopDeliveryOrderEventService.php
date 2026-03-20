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

namespace addon\shop\app\service\core\shop_delivery;

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
class CoreShopDeliveryOrderEventService extends BaseCoreService
{
    public function __construct()
    {
        parent::__construct();
        $this->model = new LocalDeliveryOrder();
    }


    public function create(){

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
            'out_status' => $notify_data['status'],
        ];
        $this->model->where([['delivery_no', '=', $delivery_no]])->update($data);
        //todo 消息发送
//        (new NoticeService())->send('', []);
        //添加配送订单日志
        $operate = OrderLogDict::getOperate(LocalDeliveryStatusDict::IN_DELIVERY);
        $log_data = [
            'order_id' => $local_delivery_order->id,
            'main_id' => $notify_data['main_id'] ?? 0,
            'main_name' => $notify_data['main_name'] ?? '',
            'main_type' => OrderLogDict::RIDER,
            'operate' => $operate['operate'],
            'operate_desc' => $operate['operate_desc'],
            'status' => LocalDeliveryStatusDict::IN_DELIVERY,
            'remark' => $operate['operate_desc'].'，骑手'.($notify_data['main_name'] ?? ''),
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
            'out_status' => $notify_data['status'],
            'remark' => $notify_data['cancel_reason'] ?? '',
            'cancel_time' => time()
        ];
        $delivery_order_data = $local_delivery_order->toArray();
        $delivery_order_data['event'] = 'cancel';
        $this->model->where([['delivery_no', '=', $delivery_no]])->update($data);
        event('LocalDeliveryEvent', $delivery_order_data);

        //添加配送订单日志
        $operate = OrderLogDict::getOperate(LocalDeliveryStatusDict::CANCELED);
        $log_data = [
            'order_id' => $local_delivery_order->id,
            'main_id' => $notify_data['main_id'] ?? 0,
            'main_name' => $notify_data['main_name'] ?? '',
            'main_type' => OrderLogDict::SYSTEM,
            'operate' => $operate['operate'],
            'operate_desc' => $operate['operate_desc'],
            'status' => $notify_data['status'],
            'remark' => $operate['operate_desc'],
        ];
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
            'out_status' => $notify_data['status'],
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
            'main_id' => $notify_data['main_id'] ?? 0,
            'main_name' => $notify_data['main_name'] ?? '',
            'main_type' => OrderLogDict::SYSTEM,
            'operate' => $operate['operate'],
            'operate_desc' => $operate['operate_desc'],
            'status' => LocalDeliveryStatusDict::COMPLETED,
            'remark' => $operate['operate_desc'],
        ];
        (new CoreLocalDeliveryOrderLogService())->addLog($log_data);
        return true;
    }

}
