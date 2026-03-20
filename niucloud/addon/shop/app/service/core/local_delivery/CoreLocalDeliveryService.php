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

use addon\shop\app\dict\order\OrderDeliveryDict;
use addon\shop\app\dict\goods\GoodsDict;
use addon\shop\app\dict\local_delivery\LocalDeliveryDict;
use addon\shop\app\dict\local_delivery\LocalDeliveryStatusDict;
use addon\shop\app\model\delivery\Store;
use addon\shop\app\model\local_delivery\Local;
use addon\shop\app\model\local_delivery\LocalDeliveryOrder;
use addon\shop\app\model\local_delivery\LocalDeliveryOrderLog;
use addon\shop\app\model\local_delivery\LocalDeliveryService;
use core\base\BaseCoreService;
use core\exception\CommonException;
use Location\Coordinate;
use Location\Distance\Vincenty;
use Location\Polygon;

/**
 * 同城配送服务层
 * Class CoreExpressService
 * @package addon\shop\app\service\admin\delivery
 */
class CoreLocalDeliveryService extends BaseCoreService
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getConfig(string $delivery_service)
    {
        $config = [];
        if ($delivery_service == LocalDeliveryDict::MERCHANT) return $config;
        $service_data = (new LocalDeliveryService())->where([['key', '=', $delivery_service]])->findOrEmpty()->toArray();
        if (empty($service_data) || empty($service_data['config'])) {
            throw new CommonException(LocalDeliveryDict::getType($delivery_service).'未配置');
        }
        $config = $service_data['config'];
        $config['is_use'] = $service_data['is_use'];
        return $config;
    }

    /**
     * 配送费用计算
     * @param $order
     * @return void
     */
    public static function calculate(&$order)
    {
        $address = $order->delivery[ 'take_address' ] ?? [];
        $take_store = $order->delivery['take_store'] ?? [];

        if (empty($address)) {
            $order->error[] = get_lang('NOT_SELECT_ADDRESS');
            return;
        }

        if (empty($take_store)) {
            $order->error[] = get_lang('NOT_SELECT_LOCAL_DELIVERY_STORE');
            $order->delivery['error'] = empty($order->delivery['error']) ? get_lang('NOT_SELECT_LOCAL_DELIVERY_STORE') : $order->delivery['error'];
            return;
        }

        $goods_money = 0; // 商品价格
        $goods_weight = 0; // 商品重量

        foreach ($order->goods_data as $k => &$v) {
            $goods_type = $v[ 'goods' ][ 'goods_type' ];
            if ($goods_type == GoodsDict::REAL) {
                if (in_array(OrderDeliveryDict::LOCAL_DELIVERY, $v[ 'goods' ][ 'delivery_type' ])) {
                    $goods_money += $v[ 'goods_money' ];
                    $goods_weight += $v[ 'weight' ] * $v[ 'num' ];
                } else {
                    $v[ 'not_support_delivery' ] = 1;
                    $order->error[] = '“'.$v['goods']['goods_name'].'”'.get_lang('NOT_SUPPORT_DELIVERY_TYPE');//“'.$v['goods']['goods_name'].'”不支持选择的配送方式
                    $order->goods_data[$v[ 'sku_id' ]]['error'] = get_lang('GOODS_NOT_SUPPORT_LOCAL_DELIVERY');//该商品不支持同城配送
                }
            }
        }
        ( new self() )->feeCalculate($order, $goods_money, $goods_weight);
    }

    /**
     * 运费计算
     * @param $order
     * @param float $goods_money
     * @param float $goods_weight
     * @return void
     */
    public function feeCalculate(&$order, float $goods_money, float $goods_weight)
    {
        $address = $order->delivery[ 'take_address' ] ?? [];
        $take_store = $order->delivery['take_store'] ?? [];
        $local = ( new Local() )->where([ [ 'local_id', '>', 0 ] ])->field('fee_type,base_dist,base_price,grad_dist,grad_price,weight_start,weight_unit,weight_price,delivery_type,area,center')->findOrEmpty();
        if ($local->isEmpty()) {
            $order->error[] = get_lang('NOT_CONFIGURED_LOCAL_DELIVERY');
            $order->delivery['error'] = empty($order->delivery['error']) ? get_lang('NOT_CONFIGURED_LOCAL_DELIVERY') : $order->delivery['error'];
            return;
        }
        if (empty($address[ 'lat' ]) || empty($address[ 'lng' ])) {
            $order->error[] = get_lang('NOT_SUPPORT_DELIVERY_ADDRESS');
            $order->delivery['error'] = empty($order->delivery['error']) ? get_lang('NOT_SUPPORT_DELIVERY_ADDRESS') : $order->delivery['error'];
            return;
        }
        if (empty($take_store[ 'store_id' ])) {
            $order->error[] = get_lang('NOT_SELECT_LOCAL_DELIVERY_STORE');
            $order->delivery['error'] = empty($order->delivery['error']) ? get_lang('NOT_SELECT_LOCAL_DELIVERY_STORE') : $order->delivery['error'];
            return;
        }
        $store = ( new Store() )->where([['store_id', '=', $take_store[ 'store_id' ] ] ])->field('area,latitude,longitude')->findOrEmpty()->toArray();
        if (empty($store)) {
            $order->error[] = get_lang('DELIVERY_STORE_NOT_EXIST');
            $order->delivery['error'] = empty($order->delivery['error']) ? get_lang('DELIVERY_STORE_NOT_EXIST') : $order->delivery['error'];
            return;
        }

        // 收货地址
        $address_point = new Coordinate($address[ 'lat' ], $address[ 'lng' ]);
        // 取货地址
        $local_address_point = new Coordinate($store[ 'latitude' ], $store[ 'longitude' ]);
        $local = $local->toArray();

        if (empty($store[ 'area' ])) {
            $order->error[] = get_lang('STORE_NOT_SET_DELIVERY_RANGE');
            $order->delivery['error'] = empty($order->delivery['error']) ? get_lang('STORE_NOT_SET_DELIVERY_RANGE') : $order->delivery['error'];
            return;
        }
        // 判断所在区域
        $located_in_area = null;
        foreach ($store[ 'area' ] as $area) {
            if (empty($area[ 'area_json' ])) {
                continue;
            }
            if ($area[ 'area_type' ] == 'radius') {
                $center = new Coordinate($area[ 'area_json' ][ 'center' ][ 'lat' ], $area[ 'area_json' ][ 'center' ][ 'lng' ]);
                $distance = ( new Vincenty() )->getDistance($address_point, $center);
                if ($distance <= $area[ 'area_json' ][ 'radius' ]) {
                    $located_in_area = $area;
                    break;
                }
            } else {
                $geofence = new Polygon();
                $geofence->addPoints(array_map(function ($latlng) {
                    return new Coordinate($latlng[ 'lat' ], $latlng[ 'lng' ]);
                }, $area[ 'area_json' ][ 'paths' ]));
                if ($geofence->contains($address_point)) {
                    $located_in_area = $area;
                    break;
                }
            }
        }
        if (!$located_in_area) {
            $order->error[] = get_lang('NOT_SUPPORT_DELIVERY_RANGE');
            $order->delivery['error'] = empty($order->delivery['error']) ? get_lang('NOT_SUPPORT_DELIVERY_RANGE') : $order->delivery['error'];
            return;
        }

        if (bccomp($goods_money, $located_in_area[ 'start_price' ], 2) == -1) {
            $order->error[] = '差' . $located_in_area[ 'start_price' ] - $goods_money . '元起送';
            $order->delivery['error'] = empty($order->delivery['error']) ? ('差' . $located_in_area[ 'start_price' ] - $goods_money . '元起送') : $order->delivery['error'];
            return;
        }

        if ($local[ 'fee_type' ] == 'distance') {
            // 按距离收费
            // 计算收货地址与取货地址的距离
            $distance = round(( new Vincenty() )->getDistance($address_point, $local_address_point) / 1000, 2);
            $order->basic[ 'delivery_money' ] = $local[ 'base_price' ];
            if ($distance > $local[ 'base_dist' ] && $local[ 'grad_dist' ] > 0) {
                $order->basic[ 'delivery_money' ] += round(ceil(( $distance - $local[ 'base_dist' ] ) / $local[ 'grad_dist' ]) * $local[ 'grad_price' ], 2);
            }
        } else {
            // 按配送区域收费
            $order->basic[ 'delivery_money' ] = $located_in_area[ 'delivery_price' ];
        }

        // 计算续重费用
        if ($goods_weight > 0 && $goods_weight > $local[ 'weight_start' ] && $local[ 'weight_unit' ] > 0 && $local[ 'weight_price' ] > 0) {
            $order->basic[ 'delivery_money' ] += round(ceil(( $goods_weight - $local[ 'weight_start' ] ) / $local[ 'weight_unit' ]) * $local[ 'weight_price' ], 2);
        }
    }

    public function getLocalDeliveryOrderInfo($local_delivery_order_id)
    {
        $data = [
            'show_local_delivery_info' => false,
        ];
        $local_delivery_order_info = (new LocalDeliveryOrder())->where([['delivery_no', '=', $local_delivery_order_id]])->append(['status_name'])->findOrEmpty()->toArray();
        if (!empty($local_delivery_order_info)) {
            $data['local_delivery_status_name'] = $local_delivery_order_info['status_name'] ?? '';
            if ($local_delivery_order_info['delivery_service'] != LocalDeliveryDict::MERCHANT && in_array($local_delivery_order_info['status'], [ LocalDeliveryStatusDict::ORDER_ACCEPTED,
                LocalDeliveryStatusDict::PENDING_ACCEPTANCE,
                LocalDeliveryStatusDict::PENDING_PICKUP,
                LocalDeliveryStatusDict::RIDER_ARRIVED,
                LocalDeliveryStatusDict::IN_DELIVERY ])) {
                $data['show_local_delivery_info'] = true;
            }
            $data['local_delivery_order_log'] = (new LocalDeliveryOrderLog())
                ->withJoin('localDeliveryOrder')
                ->where([['delivery_no', '=', $local_delivery_order_id]])
                ->append(['main_type_name'])->select()->toArray();
        }
        return $data;
    }

}
