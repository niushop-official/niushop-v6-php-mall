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

use addon\shop\app\dict\local_delivery\LocalDeliveryStatusDict;
use addon\shop\app\model\local_delivery\LocalDeliveryOrder;
use addon\shop\app\service\core\delivery\CoreConfigService;
use app\service\core\map\CoreMapService;
use core\base\BaseCoreService;
use core\exception\CommonException;
use think\db\exception\DbException;

/**
 * 同城配送服务层
 * Class CoreExpressService
 * @package addon\shop\app\service\admin\delivery
 */
class CoreLocalDeliveryOrderService extends BaseCoreService
{
    protected $local_delivery_event;
    public function __construct()
    {
        parent::__construct();
        $this->model = new LocalDeliveryOrder();
        $this->local_delivery_event = new CoreLocalDeliveryEventService();
    }

    /**
     * 同步订单信息
     * @param int $id
     * @return bool
     * @throws \Exception
     */
    public function syncOrder(int $id)
    {
        $info = $this->model->where([['id', '=', $id]])->findOrEmpty();
        if ($info->isEmpty()) throw new CommonException('DELIVERY_ORDER_NOT_EXIT');
        $delivery_service = $info['delivery_service'];
        $config = (new CoreLocalDeliveryService())->getConfig($delivery_service);
        $request_data = [
            'delivery_no' => $info['delivery_no']
        ];
        $save_data = $this->local_delivery_event->init(type: $delivery_service, config: $config)->queryOrderInfo($request_data);
        if (empty($save_data)) {
            return false;
        }
        return $info->save($save_data);
    }

    /**
     * 检查当前交易下是否有在进行中的配送订单
     * @param $data
     * @return bool
     */
    public function getUnderwayDeliveryOrder($data){
        $order = $this->model->where([
            ['trade_no', '=', $data['trade_no']],
            ['status', '<>', LocalDeliveryStatusDict::CANCELED]
        ])->findOrEmpty();
        if ($order->isEmpty()) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 获取配送轨迹
     * @param $param
     * @return array|array[]
     */
    public function getTrackInfo($param){
        $local_delivery_order_model = new LocalDeliveryOrder();
        $local_delivery_order = $local_delivery_order_model->where([['trade_no', '=', $param['trade_no']]])->order('create_time desc')->findOrEmpty();
//        //查询配送订单的起始点
        $local_delivery_order['member_id'] = $param['member_id'];
        $points = array_filter(event('GetLocalDeliveryTrack', $local_delivery_order))[0] ?? [];
        if(empty($points)) return [];
        $data = [
            'points' => $points,
        ];
        //增加
//        $config = new CoreConfigService();
//        $config_value = $config->getDeliveryConfig();
//        $is_show_polyline = $config_value['is_show_polyline'] ?? false;
        //todo 增加配送轨迹是否开启配置
        $is_show_polyline = true;
        if(count($data['points']) > 1){
            $center = $this->calculateCenter($data['points']);
            $driving =  $data['driving'] ?? [];
            if($is_show_polyline){
                $from_point = $data['points'][0] ?? [];
                $to_point = $data['points'][1] ?? [];
                try {
                    $driving = (new CoreMapService())->getPolyline([
                        'from' => $from_point['latitude'] . ',' . $from_point['longitude'],
                        'to' => $to_point['latitude'] . ',' . $to_point['longitude']]
                    );
                } catch (DbException $e) {
                }
            }

        }else{
            $center = [
                'latitude' => $data['points'][0]['latitude'],
                'longitude' => $data['points'][0]['longitude'],
            ];
        }
        $data['center'] = $center;
        $data['driving'] = $driving ?? [];
        $data['local_delivery_order'] = $local_delivery_order;
        $data['step'] = [
            [
                'desc' => '已接单',
                'logo' => ''
            ],
            [
                'desc' => '召唤骑手取货',
                'logo' => ''
            ],
            [
                'desc' => '骑手配送中',
                'logo' => ''
            ],
            [
                'desc' => '配送完成',
                'logo' => ''
            ],
        ];
        $data['expected_finish_time'] = $this->getExpectedFinishTime($data);
        //{"step":[{"desc":"已接单","logo":""},{"desc":"召唤骑手取货","logo":""},{"desc":"骑手配送中","logo":""},{"desc":"配送完成","logo":""}],"center":{"latitude":0,"longitude":0},"points":[{"name":"","latitude":0,"longitude":0,"avayar":"","logo":"","tab":{"content":"骑手:王大锤 | 工号:1214564646546464"}},{"name":"","latitude":0,"longitude":0,"avayar":"","logo":"","tab":{"content":""}}],"driving":{}}
        return $data;
    }

    /**
     * 计算预计送达时间
     * @param $data
     * @return string
     */
    function getExpectedFinishTime($data)
    {
        $local_delivery_order = $data['local_delivery_order'];
        $points = $data['points'];
        $current_time = time();
        $rider_speed = 25; // 骑手速度，单位：公里/小时
        $expected_finish_time = $current_time;

        switch ($local_delivery_order['status']){
            case LocalDeliveryStatusDict::ORDER_ACCEPTED:
            case LocalDeliveryStatusDict::PENDING_ACCEPTANCE:
                $distance = get_map_distance($local_delivery_order['delivery_start_lat'], $local_delivery_order['delivery_start_lng'], $local_delivery_order['delivery_end_lat'], $local_delivery_order['delivery_end_lng']);
                $expected_finish_time += ($distance / $rider_speed) * 3600 + 600;
                break;
            case LocalDeliveryStatusDict::PENDING_PICKUP:
                if(count($data['points']) > 1) {
                    $distance1 = get_map_distance($points[0]['latitude'], $points[0]['longitude'], $points[1]['latitude'], $points[1]['longitude']);
                } else {
                    $distance1 = 0;
                }
                $distance2 = get_map_distance($local_delivery_order['delivery_start_lat'], $local_delivery_order['delivery_start_lng'], $local_delivery_order['delivery_end_lat'], $local_delivery_order['delivery_end_lng']);
                $expected_finish_time += ($distance1 + $distance2) / $rider_speed * 3600;
                break;
            case LocalDeliveryStatusDict::RIDER_ARRIVED:
                $distance1 = get_map_distance($points[0]['latitude'], $points[0]['longitude'], $points[1]['latitude'], $points[1]['longitude']);
                $distance2 = get_map_distance($local_delivery_order['delivery_start_lat'], $local_delivery_order['delivery_start_lng'], $local_delivery_order['delivery_end_lat'], $local_delivery_order['delivery_end_lng']);
                $expected_finish_time += ($distance1 + $distance2) / $rider_speed * 3600;
                break;
            case LocalDeliveryStatusDict::IN_DELIVERY:
                $distance = get_map_distance($points[0]['latitude'], $points[0]['longitude'], $points[1]['latitude'], $points[1]['longitude']);
                $expected_finish_time += ($distance / $rider_speed) * 3600;
                break;
        }
        return date('H:i', $expected_finish_time);
    }

    /**
     * 计算中心点
     * @param $points
     * @return float[]|int[]
     */
    function calculateCenter($points) {
        $sumX = 0;
        $sumY = 0;
        $count = count($points); // 获取点的数量
        foreach ($points as $point) {
            $sumX += $point['latitude'];
            $sumY += $point['longitude'];
        }
        $xCenter = $sumX / $count;
        $yCenter = $sumY / $count;
        return ['latitude' => $xCenter, 'longitude' => $yCenter];
    }
}
