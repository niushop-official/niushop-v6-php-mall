<?php
// +----------------------------------------------------------------------
// | Niucloud-admin 企业快速开发的多应用管理平台
// +----------------------------------------------------------------------
// | 官方网址：https://www.niucloud.com
// +----------------------------------------------------------------------
// | niucloud团队 版权所有 开源版本可自由商用
// +----------------------------------------------------------------------
// | Author=>Niucloud Team
// +----------------------------------------------------------------------
namespace addon\shop\core\local_delivery;

use addon\shop\app\service\core\shop_delivery\CoreShopDeliveryOrderCloseService;
use addon\shop\app\service\core\shop_delivery\CoreShopDeliveryOrderCreateService;
use addon\shop\app\service\core\shop_delivery\CoreShopDeliveryOrderFinishService;
use addon\shop\app\service\core\shop_delivery\CoreShopDeliveryOrderMessageService;
use addon\shop\app\service\core\shop_delivery\CoreShopDeliveryOrderService;
use think\facade\Log;

class Merchant extends BaseLocalDelivery
{

    public function initialize(array $config = [])
    {
        $this->config = $config;
    }

    /**
     * ADMINAPI 发货使用
     * @param $data
     * @return array
     */
    public function createOrder($data)
    {
        return (new CoreShopDeliveryOrderCreateService())->createOrder($data);
    }

    /**
     * ADMINAPI 重新下单
     * @param $data
     * @return array
     */
    public function reCreateOrder($data)
    {

    }

    /**
     * API使用
     * @param $data
     * @return array
     */
    public function calculate($data)
    {

    }

    /**
     *
     * @param $delivery_third_order_no
     * @return string[]
     */
    public function createOrderAfterCalculate($delivery_third_order_no)
    {

    }

    /**
     * @param $order_no `订单号`
     * @return string[]
     */
    public function queryOrderInfo($data)
    {

    }

    /**
     * @param $order_no `订单号`
     * @param $cancel_reason_id `取消原因类型ID`
     * @param $cancel_reason `取消原因`
     * @return false|string[]
     */
    public function closeOrder($data)
    {
        $param = [
            'out_delivery_no' => $data['delivery_no'],
            'cancel_reason' => $data['cancel_reason'],
            'main_id' => $data['main_id'] ?? 0,
            'main_type' => $data['main_type'] ?? '',
            'main_name' => $data['main_name'] ?? '',
        ];
        $res = (new CoreShopDeliveryOrderCloseService())->closeOrder($param);
        if ($res) {
            return ['cancel_time' => time()];
        } else {
            return [];
        }
    }

    /**
     * 完成订单
     * @param $data
     * @return false|string[]
     */
    public function finishOrder($data)
    {
        $param = [
            'out_delivery_no' => $data['delivery_no'],
            'main_id' => $data['main_id'] ?? 0,
            'main_type' => $data['main_type'] ?? '',
            'main_name' => $data['main_name'] ?? '',
        ];
        $res = (new CoreShopDeliveryOrderFinishService())->finishOrder($param);
        if ($res) {
            return ['finish_time' => time()];
        } else {
            return [];
        }
    }

    public function getTransporterPosition($data)
    {
        $param = [
            'out_delivery_no' => $data['delivery_no']
        ];
        $res = (new CoreShopDeliveryOrderService())->getOrderInfo($param);
        if ($res) {
            return [
                'rider_latitude' => $res['delivery_start_lat'] ?? '',
                'rider_longitude' => $res['delivery_start_lng'] ?? '',
                'rider_name' => $res['deliver_name'] ?? '',
                'rider_mobile' => $res['deliver_mobile'] ?? '',
            ];
        } else {
            return [];
        }
    }

    /**
     * 添加门店
     * @param $data
     * @return false|mixed
     */
    public function addShop($data)
    {

    }

    /**
     * 更新门店
     * @param $data
     * @return false|mixed
     */
    public function editShop($data)
    {

    }

    function notify(string $action, array $params)
    {
        try {
            Log::write('商家配送订单状态通知回调：' . json_encode($params));
            switch ($action) {
                case 'order'://订单状态通知
                    (new CoreShopDeliveryOrderMessageService())->orderNotify($params);
                    break;
                //                    case 'store'://商户//todo
                //                        break;
                //                    case 'recharge'://充值//todo
                //                        break;
            }
            return true;
        } catch (\Exception $e) {
            Log::write('商家配送订单状态通知回调异常：' . $e->getMessage());
            return false;
        }
    }
}