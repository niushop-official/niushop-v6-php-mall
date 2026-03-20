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

namespace addon\shop\app\service\core\local_delivery;

use addon\shop\app\dict\local_delivery\OrderCancelReasonDict;
use addon\shop\app\dict\local_delivery\OrderLogDict;
use addon\shop\app\model\local_delivery\LocalDeliveryOrder;
use core\base\BaseCoreService;
use core\exception\CommonException;

/**
 * 同城配送服务层
 * Class CoreExpressService
 * @package addon\shop\app\service\admin\delivery
 */
class CoreLocalDeliveryOrderCloseService extends BaseCoreService
{
    protected $local_delivery_event;
    public function __construct()
    {
        parent::__construct();
        $this->model = new LocalDeliveryOrder();
        $this->local_delivery_event = new CoreLocalDeliveryEventService();
    }

    /**
     * 取消订单
     * @param array $data
     * @return bool
     * @throws \Exception
     */
    public function closeOrder(array $data)
    {
        $delivery_order_condition = [];
        $id = $data['id'] ?? 0;
        if(empty($id)){
            $trade_no =  $data['trade_no'];
            $trade_type = $data['trade_type'];
            $delivery_order_condition[] = ['trade_no', '=', $trade_no];
            $delivery_order_condition[] =  ['trade_type', '=', $trade_type];
        }else{
            $delivery_order_condition[] = ['id', '=', $id];
        }
        $info = $this->model->where($delivery_order_condition)->findOrEmpty();
        if ($info->isEmpty()) throw new CommonException('DELIVERY_ORDER_NOT_EXIT');
        $delivery_service = $info['delivery_service'];
        $save_data = [
            'remark' => $data['cancel_reason_id'] ? OrderCancelReasonDict::getType($data['cancel_reason_id'], $delivery_service) : $data['cancel_reason']
        ];
        $config = (new CoreLocalDeliveryService())->getConfig($delivery_service);
        $request_data = [
            'delivery_no' => $info['delivery_no'],
            'cancel_reason_id' => $data['cancel_reason_id'],
            'cancel_reason' => $data['cancel_reason'],
            'main_id' => $data['main_id'] ?? 0,
            'main_type' => $data['main_type'] ?? OrderLogDict::STORE,
            'main_name' => $data['main_name'] ?? '',
        ];
        $response_data = $this->local_delivery_event->init(type: $delivery_service, config: $config)->closeOrder($request_data);
        if (empty($response_data)) {
            return false;
        }
        $save_data = array_merge($save_data, $response_data);
        $info->save($save_data);
        return true;
    }

}
