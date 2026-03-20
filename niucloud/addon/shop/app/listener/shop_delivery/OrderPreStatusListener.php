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

namespace addon\shop\app\listener\shop_delivery;

use addon\shop\app\dict\local_delivery\LocalDeliveryStatusDict;
use addon\shop\app\dict\shop_delivery\ShopDeliveryDict;

/**
 * 配送订单操作前置状态
 * @package addon\shop\app\listener\shop_delivery
 */
class OrderPreStatusListener
{

    public function handle($params)
    {
        if (!empty($params['service']) && $params['service'] == ShopDeliveryDict::MERCHANT) {
            return [
                'finish' => [
                    LocalDeliveryStatusDict::IN_DELIVERY
                ], //完成订单
                'cancel' => [
                    LocalDeliveryStatusDict::IN_DELIVERY
                ], //取消订单
            ];
        }
    }
}
