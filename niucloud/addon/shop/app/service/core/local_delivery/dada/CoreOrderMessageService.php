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

use addon\shop\app\dict\local_delivery\dada\DadaDeliveryStatusDict;
use addon\shop\app\dict\local_delivery\LocalDeliveryStatusDict;
use core\base\BaseCoreService;
use core\exception\CommonException;

/**
 * 同城配送服务层
 * Class CoreExpressService
 * @package addon\shop\app\service\admin\delivery
 */
class CoreOrderMessageService extends BaseCoreService
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 订单状态回调
     * @param array $data
     * @return void
     */
    public function orderNotify(array $data)
    {
        // 回调数据签名校验
        $signature = $this->signature([ $data['client_id'], $data['order_id'], $data['update_time'] ]);
        if ($signature != $data['signature']) {
            throw new CommonException('DADA_ORDER_NOTIFY_SIGN_ERROR');
        }
        // 同城配送订单号
        $delivery_no = $data['order_id'];
        // 同城配送订单状态转换
        $status = DadaDeliveryStatusDict::convertStatus($data['order_status']);

        $core_order_event_service = new CoreOrderEventService();
        switch ($status) {
            case LocalDeliveryStatusDict::PENDING_ACCEPTANCE:
                $core_order_event_service->orderCall(['notify_data' => $data],  $delivery_no);
                break;
            case LocalDeliveryStatusDict::DISPATCH_ORDER:
                $core_order_event_service->dispatchOrder(['notify_data' => $data],  $delivery_no);
                break;
            case LocalDeliveryStatusDict::PENDING_PICKUP:
                $core_order_event_service->riderAccepted(['notify_data' => $data],  $delivery_no);
                break;
            case LocalDeliveryStatusDict::RIDER_ARRIVED:
                $core_order_event_service->riderArrived(['notify_data' => $data],  $delivery_no);
                break;
            case LocalDeliveryStatusDict::IN_DELIVERY:
                $core_order_event_service->riderPickedUp(['notify_data' => $data],  $delivery_no);
                break;
            case LocalDeliveryStatusDict::COMPLETED:
                $core_order_event_service->finish(['notify_data' => $data],  $delivery_no);
                break;
            case LocalDeliveryStatusDict::RETURN_IN_PROGRESS:
                $core_order_event_service->returnApplied(['notify_data' => $data],  $delivery_no);
                break;
            case LocalDeliveryStatusDict::RETURN_COMPLETED:
                $core_order_event_service->returnCompleted(['notify_data' => $data],  $delivery_no);
                break;
            case LocalDeliveryStatusDict::CANCELED:
                $core_order_event_service->cancel(['notify_data' => $data],  $delivery_no);
                break;
            case LocalDeliveryStatusDict::RETURN_RIDER_ARRIVED:
                $core_order_event_service->returnRiderArrived(['notify_data' => $data],  $delivery_no);
                break;
            case LocalDeliveryStatusDict::CREATE_ORDER_FAILED:
                $core_order_event_service->createOrderFailed(['notify_data' => $data],  $delivery_no);
                break;
            default:
                break;
        }
    }

    /**
     * 回调数据签名校验
     * @param array $data
     * @return string
     */
    public function signature(array $data)
    {
        // 对client_id, order_id, update_time的值进行字符串升序排列
        usort($data, function($a, $b) {
            return strcmp($a, $b);
        });

        // 属性值拼接拼接字符串
        $signStr = implode('', $data);

        // MD5加密
        return md5($signStr);
    }

}
