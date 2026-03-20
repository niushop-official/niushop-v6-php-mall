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

use addon\shop\app\dict\local_delivery\OrderLogDict;
use addon\shop\app\dict\shop_delivery\ShopDeliveryDict;
use addon\shop\app\dict\shop_delivery\ShopDeliveryStatusDict;
use addon\shop\app\model\delivery\Store;
use addon\shop\app\model\shop_delivery\Deliver;
use addon\shop\app\model\shop_delivery\ShopDeliveryOrder;
use addon\shop\app\service\core\local_delivery\CoreLocalDeliveryOrderNotifyService;
use core\base\BaseCoreService;
use Location\Coordinate;
use Location\Distance\Vincenty;

/**
 * 同城配送服务层
 * Class CoreShopDeliveryOrderService
 * @package app\service\core\delivery
 */
class CoreShopDeliveryOrderCreateService extends BaseCoreService
{
    public function __construct()
    {
        parent::__construct();
        $this->model = new ShopDeliveryOrder();
    }

    /**
     * 创建配送订单
     * @param $params
     * @return array
     * @throws \Exception
     */
    public function createOrder($params)
    {
        $order_data = $params['order_data'];
        $deliver = (new Deliver())->field('deliver_name,deliver_mobile')->where([['deliver_id', '=', $params['deliver_id']]])->findOrEmpty()->toArray();
        $delivery_store = (new Store())->where([['store_id', '=', $order_data['take_store_id']]])->findOrEmpty()->toArray();
        // 收货地址
        $receiver_address_point = new Coordinate($order_data[ 'taker_latitude' ], $order_data[ 'taker_longitude' ]);
        // 取货地址
        $local_address_point = new Coordinate($delivery_store[ 'latitude' ], $delivery_store[ 'longitude' ]);
        // 计算收货地址与取货地址的距离
        $distance = round(( new Vincenty() )->getDistance($receiver_address_point, $local_address_point) / 1000, 2);

        $delivery_no = create_no();//创建商家配送单号
        $data = [
            'delivery_no' => $delivery_no,
            'out_delivery_no' => $params['delivery_no'],
            'trade_id' => $order_data['order_id'],
            'trade_no' => $params['trade_no'],
            'delivery_money' => $order_data['delivery_money'],
            'delivery_start' => $delivery_store['full_address'],
            'delivery_start_province_id' => $delivery_store['province_id'] ?? 0,
            'delivery_start_city_id' => $delivery_store['city_id'] ?? 0,
            'delivery_start_district_id' => $delivery_store['district_id'] ?? 0,
            'delivery_start_lng' => $delivery_store['longitude'] ?? '',
            'delivery_start_lat' => $delivery_store['latitude'] ?? '',
            'delivery_end' => $order_data['taker_full_address'],
            'delivery_end_province_id' => $order_data['taker_province'] ?? 0,
            'delivery_end_city_id' => $order_data['taker_city'] ?? 0,
            'delivery_end_district_id' => $order_data['taker_district'] ?? 0,
            'delivery_end_lng' => $order_data['taker_longitude'] ?? '',
            'delivery_end_lat' => $order_data['taker_latitude'] ?? '',
            'delivery_distance' => $distance,
            'deliver_id' => $params['deliver_id'],
            'deliver_name' => $deliver['deliver_name'],
            'deliver_mobile' => $deliver['deliver_mobile'],
            'status' => ShopDeliveryStatusDict::IN_DELIVERY,//配送中
            'remark' => $params['remark'] ?? '',
            'body' => $params['body'] ?? ''
        ];
        //添加配送订单记录
        $res = $this->model->create($data);
        if ($res->id) {
            $res = $res->toArray();
            // 商家订单状态回调
            (new CoreLocalDeliveryOrderNotifyService())->notify(ShopDeliveryDict::MERCHANT, $res['out_delivery_no'], 'order', [
                'main_id' => $res['deliver_id'],
                'main_name' => $res['deliver_name'],
                'delivery_no' => $res['delivery_no'],
                'out_delivery_no' => $res['out_delivery_no'],
                'status' => ShopDeliveryStatusDict::IN_DELIVERY
            ]);
            //添加配送订单日志
            $operate = OrderLogDict::getOperate(ShopDeliveryStatusDict::convertStatus($res['status']));
            (new CoreShopDeliveryOrderLogService())->addLog([
                'order_id' => $res['id'],
                'main_id' => $res['deliver_id'],
                'main_type' => OrderLogDict::RIDER,
                'main_name' => $res['deliver_name'],
                'operate' => $operate['operate'],
                'operate_desc' => $operate['operate_desc'],
                'status' => ShopDeliveryStatusDict::IN_DELIVERY,
                'remark' => $operate['operate_desc'].'，骑手'.$res['deliver_name']
            ]);
            return [
                'out_delivery_no' => $res['delivery_no'],//商家配送的配送单号就是同城配送的三方配送单号
                'delivery_money' => $res['delivery_money'],
                'delivery_distance' => $res['delivery_distance'],
                'rider_name' => $res['deliver_name'],
                'rider_mobile' => $res['deliver_mobile'],
            ];
        }
        return [];
    }
}
