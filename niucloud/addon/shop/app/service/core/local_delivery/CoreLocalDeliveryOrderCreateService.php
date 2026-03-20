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

use addon\shop\app\dict\local_delivery\LocalDeliveryDict;
use addon\shop\app\dict\local_delivery\LocalDeliveryStatusDict;
use addon\shop\app\dict\local_delivery\OrderLogDict;
use addon\shop\app\model\delivery\Store;
use addon\shop\app\model\local_delivery\LocalDeliveryOrder;
use core\base\BaseCoreService;
use core\exception\CommonException;

/**
 * 同城配送服务层
 * Class CoreExpressService
 * @package addon\shop\app\service\admin\delivery
 */
class CoreLocalDeliveryOrderCreateService extends BaseCoreService
{
    protected $local_delivery_event;
    public function __construct()
    {
        parent::__construct();
        $this->model = new LocalDeliveryOrder();
        $this->local_delivery_event = new CoreLocalDeliveryEventService();
    }

    /**
     * 订单创建
     * @param $data
     * @return true
     * @throws \Exception
     */
    public function create($data)
    {
        $order_data = $data['order_data'];
        $order_goods_data = $data['order_goods_data'];
        $local_delivery_no = $data['local_delivery_no'];
        $param = $data['param'];
        $delivery_service = $param['local_delivery_type'];
        $store_id = empty($param['store_id']) ? $order_data['take_store_id'] : $param['store_id'];//提货点id

        $config = $this->checkDeliveryService($store_id, $delivery_service);

        $delivery_order_data = [
            'store_id' => $store_id,
            'delivery_service' => $delivery_service,
            'delivery_no' => $local_delivery_no,
            'status' => LocalDeliveryStatusDict::ORDER_ACCEPTED,
            'trade_id' => $order_data['order_id'],
            'trade_no' => $data['trade_no'],
            'delivery_end' => $order_data['taker_full_address'],
            'delivery_end_province_id' => $order_data['taker_province'],
            'delivery_end_city_id' => $order_data['taker_city'],
            'delivery_end_district_id' => $order_data['taker_district'],
            'delivery_end_lng' => $order_data['taker_longitude'],
            'delivery_end_lat' => $order_data['taker_latitude'],
            'body' => $order_data['body'] ?? ''
        ];
        $order_id = $this->createOrder($delivery_order_data);

        if (!empty($order_id)) {
            //第三方配送下单
            $request_data = [
                'order_data' => $order_data,
                'trade_no' => $data['trade_no'],
                'order_goods_data' => $order_goods_data,
                'delivery_no' => $local_delivery_no,
                'param' => $param,
                'deliver_id' => $param['local_deliver_id'],
                'body' =>  $order_data['body'] ?? ''
            ];
            $response_data = (new CoreLocalDeliveryEventService())->init($delivery_service, $local_delivery_no, $config)->createOrder($request_data);
            if (empty($response_data)) {
                throw new CommonException('呼叫配送失败');
            }
            $this->model->where([['id', '=', $order_id]])->update($response_data);
        }

        return true;
    }

    /**
     * 创建配送订单
     * @param $params
     * @return mixed
     */
    public function createOrder($params)
    {
        $store = (new Store())->where([['store_id', '=', $params['store_id']]])->findOrEmpty()->toArray();
        $data = [
            'delivery_service' => $params['delivery_service'],
            'delivery_no' => $params['delivery_no'],
            'out_delivery_no' => $params['out_delivery_no'] ?? '',
            'delivery_money' => $params['delivery_money'] ?? 0,
            'delivery_distance' => $params['delivery_distance'] ?? 0,
            'status' => $params['status'],
            'out_status' => $params['out_status'] ?? '',
            'rider_name' => $params['rider_name'] ?? '',
            'rider_mobile' => $params['rider_mobile'] ?? '',
            'trade_id' => $params['trade_id'] ?? 0,
            'trade_no' => $params['trade_no'],
            'delivery_start' => $store['full_address'] ?? '',
            'delivery_start_province_id' => $store['province_id'] ?? '',
            'delivery_start_city_id' => $store['city_id'] ?? '',
            'delivery_start_district_id' => $store['district_id'] ?? '',
            'delivery_start_lng' => $store['longitude'] ?? '',
            'delivery_start_lat' => $store['latitude'] ?? '',
            'delivery_end' => $params['delivery_end'],
            'delivery_end_province_id' => $params['delivery_end_province_id'] ?? '',
            'delivery_end_city_id' => $params['delivery_end_city_id'] ?? '',
            'delivery_end_district_id' => $params['delivery_end_district_id'] ?? '',
            'delivery_end_lng' => $params['delivery_end_lng'] ?? '',
            'delivery_end_lat' => $params['delivery_end_lat'] ?? '',
            'remark' => $params['remark'] ?? '',
            'body' => $params['body'] ?? '',
            'create_time' => time()
        ];
        //添加配送订单记录
        $res = $this->model->create($data);
        //添加配送订单日志
        $operate = OrderLogDict::getOperate($res['status']);
        (new CoreLocalDeliveryOrderLogService())->addLog([
            'order_id' => $res['id'],
            'main_id' => '',
            'main_type' => OrderLogDict::STORE,
            'main_name' => '',
            'operate' => $operate['operate'],
            'operate_desc' => $operate['operate_desc'],
            'status' => $res['status'],
            'remark' => $operate['operate_desc']
        ]);
        return $res->id;
    }

    /**
     * 配送费用计算
     * @param $data
     * @return mixed
     * @throws \Exception
     */
    public function getDeliveryFee($data)
    {
        $order_data = $data['order_data'];
        $store_id = empty($data['store_id']) ? $order_data['take_store_id'] : $data['store_id'];//提货点id
        $order_goods_data = $data['order_goods_data'];
        if (empty($data['local_delivery_type'])) throw new CommonException('DELIVERY_SERVICE_NOT_SELECT');
        $delivery_service = $data['local_delivery_type'];

        $config = $this->checkDeliveryService($store_id, $delivery_service);
        $delivery_no = create_no();//创建本地配送单号
        $request_data = [
            'order_data' => $order_data,
            'order_goods_data' => $order_goods_data,
            'param' => $data,
            'delivery_no' => $delivery_no,
        ];
        $response_data = $this->local_delivery_event->init($delivery_service, $delivery_no, $config)->calculate($request_data);
        return $response_data['delivery_money'] ?? 0;
    }

    public function checkDeliveryService($store_id, $delivery_service)
    {
        $config = [];
        $store = (new Store())->field('store_no,support_delivery')->where([['store_id', '=', $store_id]])->findOrEmpty()->toArray();
        if (empty($store)) throw new CommonException('DELIVERY_STORE_NOT_EXIST');
        if ($delivery_service != LocalDeliveryDict::MERCHANT) {
            $config = (new CoreLocalDeliveryService())->getConfig($delivery_service);
            if (empty($store['support_delivery']) || empty($store['support_delivery'][$delivery_service]) || $store['support_delivery'][$delivery_service]['open_status'] != LocalDeliveryDict::OPEN_PASS) {
                throw new CommonException('配送门店未开通'.LocalDeliveryDict::getType($delivery_service));
            }
            if (isset($config['is_use']) && $config['is_use'] != 1) {
                throw new CommonException(LocalDeliveryDict::getType($delivery_service).'未启用');
            }
        }
        $config['store_no'] = $store['store_no'];
        return $config;
    }

}
