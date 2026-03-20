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
use addon\shop\app\dict\shop_delivery\ShopDeliveryStatusDict;
use core\base\BaseCoreService;

/**
 * 同城配送服务层
 * Class CoreExpressService
 * @package addon\shop\app\service\admin\delivery
 */
class CoreShopDeliveryOrderMessageService extends BaseCoreService
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
        // 同城配送订单号
        $delivery_no = $data['out_delivery_no'];
        // 同城配送订单状态转换
        $status = ShopDeliveryStatusDict::convertStatus($data['status']);

        $core_order_event_service = new CoreShopDeliveryOrderEventService();
        switch ($status) {
            case LocalDeliveryStatusDict::IN_DELIVERY:
                $core_order_event_service->riderPickedUp(['notify_data' => $data], $delivery_no);
                break;
            case LocalDeliveryStatusDict::CANCELED:
                $core_order_event_service->cancel(['notify_data' => $data], $delivery_no);
                break;
            case LocalDeliveryStatusDict::COMPLETED:
                $core_order_event_service->finish(['notify_data' => $data], $delivery_no);
                break;
            default:
                break;
        }
    }

}
