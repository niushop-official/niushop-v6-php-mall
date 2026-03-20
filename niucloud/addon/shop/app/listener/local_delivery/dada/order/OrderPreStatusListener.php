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

namespace addon\shop\app\listener\local_delivery\dada\order;

use addon\shop\app\dict\local_delivery\dada\DadaDeliveryDict;
use addon\shop\app\dict\local_delivery\LocalDeliveryStatusDict;

/**
 * 配送订单操作前置状态
 * @package addon\b2b2c_dada\app\listener\order
 */
class OrderPreStatusListener
{

    public function handle($params)
    {
        if (!empty($params['service']) && $params['service'] == DadaDeliveryDict::DADA) {
            return [
                'cancel' => [
                    LocalDeliveryStatusDict::WAIT_RELEASE,
                    LocalDeliveryStatusDict::PENDING_ACCEPTANCE,
                    LocalDeliveryStatusDict::PENDING_PICKUP,
                    LocalDeliveryStatusDict::RIDER_ARRIVED,
                ], //取消订单
                'sync' => [
                    LocalDeliveryStatusDict::ORDER_ACCEPTED,
                    LocalDeliveryStatusDict::PENDING_ACCEPTANCE,
                    LocalDeliveryStatusDict::PENDING_PICKUP,
                    LocalDeliveryStatusDict::RIDER_ARRIVED,
                    LocalDeliveryStatusDict::IN_DELIVERY,
                    LocalDeliveryStatusDict::COMPLETED,
                    LocalDeliveryStatusDict::DISPATCH_ORDER,
                    LocalDeliveryStatusDict::RETURN_IN_PROGRESS,
                    LocalDeliveryStatusDict::RETURN_COMPLETED,
                    LocalDeliveryStatusDict::RETURN_RIDER_ARRIVED,
                    LocalDeliveryStatusDict::CREATE_ORDER_FAILED,
                    LocalDeliveryStatusDict::CANCELED,
                ], //同步订单
            ];
        }
    }
}
