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

use addon\shop\app\dict\local_delivery\dada\DadaDeliveryStatusDict;
use addon\shop\app\dict\local_delivery\LocalDeliveryDict;
use addon\shop\app\service\core\local_delivery\dada\CoreOrderMessageService;
use addon\shop\core\local_delivery\sdk\dada\OrderCancelRequest;
use addon\shop\core\local_delivery\sdk\dada\OrderCreateByFeeOrderNoRequest;
use addon\shop\core\local_delivery\sdk\dada\OrderCreateRequest;
use addon\shop\core\local_delivery\sdk\dada\OrderFeeRequest;
use addon\shop\core\local_delivery\sdk\dada\OrderInfoRequest;
use addon\shop\core\local_delivery\sdk\dada\OrderTransporterPositionH5Request;
use addon\shop\core\local_delivery\sdk\dada\OrderTransporterPositionRequest;
use addon\shop\core\local_delivery\sdk\dada\ShopAddRequest;
use addon\shop\core\local_delivery\sdk\dada\ShopEditRequest;
use think\facade\Log;

class Dada extends BaseLocalDelivery
{
    public $app_secret;//App_secret
    public $app_key;//App_key
    public $source_id;//商户ID
    public $shop_no;//门店编号
    public $config;
    public $callback;//回调地址

    public function initialize(array $config = [])
    {
        $this->app_secret = $config['app_secret'] ?? '';
        $this->app_key = $config['app_key'] ?? '';
        $this->source_id = $config['source_id'] ?? '';
        $this->shop_no = $config['store_no'] ?? '';
        $this->callback = $config['notify_url'] ?? '';
        $this->config = $config;
    }

    /**
     * ADMINAPI 发货使用
     * @param $data
     * @return array
     * @throws \Exception
     */
    public function createOrder($data)
    {
        $order_data = $data['order_data'];
        $order_goods_data = $data['order_goods_data'];
        $param = $data['param'];
        $delivery_no = $data['delivery_no'];

        $request = new OrderCreateRequest();
        $product_list = [];
//        $weight = $num = 0;
        $weight = max($param['goods_weight'], 1);
        $num = 0;
        foreach ($order_goods_data as $sku_info) {
            $product_list[] = [
                'count' => $sku_info['num'],
                'sku_name' => $sku_info['goods_name'] . $sku_info['sku_name'],
                'src_product_no' => $sku_info['sku_id'],
                'unit' => $sku_info['goods']['unit'],
            ];
//            $weight += $sku_info['sku']['weight'] * $sku_info['num'];
            $num += $sku_info['num'];
        }
        // 创建请求对象并链式设置参数
        $request->setBaseParams($this->config)
            ->setCallback($this->callback)
            ->setShopNo($this->shop_no)
            ->setOriginId($delivery_no)
            ->setReceiverName($order_data->taker_name)
            ->setReceiverPhone($order_data->taker_mobile)
            ->setReceiverAddress($order_data->taker_address)
            ->setReceiverLat($order_data->taker_latitude)
            ->setReceiverLng($order_data->taker_longitude)
            ->setCargoNum($num)
            ->setCargoWeight($weight)
            ->setCargoPrice($order_data->order_money)
            ->setProductList($product_list);
        if (!empty($order_data['buyer_ask_delivery_time'])) {
            //2025-06-16 08:21~09:21
            $buyer_ask_delivery_time_array = explode(' ', $order_data['buyer_ask_delivery_time']);
            $buyer_ask_delivery_time_date = $buyer_ask_delivery_time_array[0];
            $buyer_ask_delivery_time_hour = $buyer_ask_delivery_time_array[1];
            $buyer_ask_delivery_time_hour_array = explode('~',$buyer_ask_delivery_time_hour);
            $expect_finish_time_limit = strtotime($buyer_ask_delivery_time_date . ' ' . $buyer_ask_delivery_time_hour_array[1]);
            $request->setIsExpectFinishOrder(1)->setExpectFinishTimeLimit($expect_finish_time_limit);
        }
        try {
            $res = $request->sendRequest();
            Log::write('达达接口请求：' . json_encode($res));
            $result = $res['result'] ?? [];
            if (empty($result)) {
                return [];
            }
            return [
                'delivery_money' => $result['fee'] ?? 0,
                'delivery_distance' => ($result['distance'] ?? 0) / 1000,//转km
            ];
        } catch (\Exception $e) {
            Log::write('达达接口请求异常：' . $e->getMessage());
            throw new \Exception($e->getMessage());

        }

    }

    /**
     * ADMINAPI 重新下单
     * @param $data
     * @return array
     * @throws \Exception
     */
    public function reCreateOrder($data)
    {
        $order_data = $data['order_data'];
        $order_goods_data = $data['order_goods_data'];
        $param = $data['param'];
        $delivery_no = $data['delivery_no'];
//        return [//调试数据
//            'delivery_price' => 100,//$res['fee'],
//            'third_order_no' => $order_no,//$res['fee'],
//        ];
        $request = new OrderCreateRequest();
        $product_list = [];
//        $weight = $num = 0;
        $weight = max($param['goods_weight'], 1);
        $num = 0;
        foreach ($order_goods_data as $sku_info) {
            $product_list[] = [
                'count' => $sku_info['num'],
                'sku_name' => $sku_info['goods_name'] . $sku_info['sku_name'],
                'src_product_no' => $sku_info['sku_id'],
                'unit' => $sku_info['goods']['unit'],
            ];
//            $weight += $sku_info['sku']['weight'] * $sku_info['num'];
            $num += $sku_info['num'];
        }
        // 创建请求对象并链式设置参数
        $request = $request->setBaseParams($this->config)
            ->setCallback($this->callback)
            ->setShopNo($this->shop_no)
            ->setOriginId($delivery_no)
            ->setReceiverName($order_data->taker_name)
            ->setReceiverPhone($order_data->taker_mobile)
            ->setReceiverAddress($order_data->taker_address)
            ->setReceiverLat($order_data->taker_latitude)
            ->setReceiverLng($order_data->taker_longitude)
            ->setCargoNum($num)
            ->setCargoWeight($weight)
            ->setCargoPrice($order_data->order_money)
            ->setProductList($product_list);
        if (!empty($order_data['buyer_ask_delivery_time'])) {
            //2025-06-16 08:21~09:21
            $buyer_ask_delivery_time_array = explode(' ', $order_data['buyer_ask_delivery_time']);
            $buyer_ask_delivery_time_date = $buyer_ask_delivery_time_array[0];
            $buyer_ask_delivery_time_hour = $buyer_ask_delivery_time_array[1];
            $buyer_ask_delivery_time_hour_array = explode('~',$buyer_ask_delivery_time_hour);
            $expect_finish_time_limit = strtotime($buyer_ask_delivery_time_date . ' ' . $buyer_ask_delivery_time_hour_array[1]);
            $request->setIsExpectFinishOrder(1)
                ->setExpectFinishTimeLimit($expect_finish_time_limit);
        }
        try {
            $res = $request->sendRequest();
            return $res['result'] ?? [];
        } catch (\Exception $e) {
            Log::write('达达接口请求异常：' . $e->getMessage());
            throw new \Exception($e->getMessage());

        }

    }

    /**
     * API使用
     * @param $data
     * @return array
     * @throws \Exception
     */
    public function calculate($data)
    {
        $order_data = $data['order_data'];
        $order_goods_data = $data['order_goods_data'];
        $param = $data['param'];
        $delivery_no = $data['delivery_no'];
        $request = new OrderFeeRequest();
        $product_list = [];
        $weight = max($param['goods_weight'] ?? 0, 1);
        $num = 0;
        foreach ($order_goods_data as $sku_info) {
            $product_list[] = [
                'count' => $sku_info['num'],
                'sku_name' => $sku_info['goods_name'] . $sku_info['sku_name'],
                'src_product_no' => $sku_info['sku_id'],
                'unit' => $sku_info['goods']['unit'],
            ];
//            $weight += $sku_info['weight'] * $sku_info['num'];
            $num += $sku_info['num'];
        }
        // 创建请求对象并链式设置参数
        $request = $request->setBaseParams($this->config)
            ->setCallback($this->callback)
            ->setShopNo($this->shop_no)
            ->setOriginId($delivery_no)
            ->setCargoPrice($order_data->order_money)
            ->setReceiverName($order_data->taker_name)
            ->setReceiverPhone($order_data->taker_mobile)
            ->setReceiverAddress($order_data->taker_address)
            ->setReceiverLat($order_data->taker_latitude)
            ->setReceiverLng($order_data->taker_longitude)
            ->setCargoNum($num)
            ->setCargoWeight($weight)
            ->setProductList($product_list);
        try {
            $res = $request->sendRequest();
            $result = $res['result'] ?? [];
            return [
                'delivery_money' => $result['fee'] ?? 0,
            ];
        } catch (\Exception $e) {
            Log::write('达达接口请求异常：' . $e->getMessage());
            throw new \Exception($e->getMessage());
        }
    }

    /**
     *
     * @param $delivery_third_order_no `计算运费接口返回的  配送方单号 queryFeeMoney.response.delivery_third_order_no`
     * @return string[]
     * @throws \Exception
     */
    public function createOrderAfterCalculate($delivery_third_order_no)
    {
        try {
            $res = (new OrderCreateByFeeOrderNoRequest())
                ->setBaseParams($this->config)
                ->setDeliveryNo($delivery_third_order_no)
                ->sendRequest();
            return $res['result'] ?? [];
        } catch (\Exception $e) {
            Log::write('达达接口请求异常：' . $e->getMessage());
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * @param $order_no `订单号`
     * @return string[]
     */
    public function queryOrderInfo($data)
    {
        try {
            $res = (new OrderInfoRequest())
                ->setBaseParams($this->config)
                ->setOrderId($data['delivery_no'])
                ->sendRequest();
            $result = $res['result'] ?? [];
            if (empty($result)) {
                return [];
            }
            $status = DadaDeliveryStatusDict::convertStatus($result['statusCode']);
            return [
                'status' => $status,
                'out_status' => $result['statusCode'],
                'rider_name' => $result['transporterName'],
                'rider_mobile' => $result['transporterPhone'],
                'delivery_money' => $result['actualFee'],
                'delivery_distance' => ($result['distance'] ?? 0) / 1000,//转km
                'create_time' => !empty($result['createTime']) && is_string($result['createTime']) ? strtotime($result['createTime']) : 0,
                'finish_time' => !empty($result['finishTime']) && is_string($result['finishTime']) ? strtotime($result['finishTime']) : 0,
                'cancel_time' => !empty($result['cancelTime']) && is_string($result['cancelTime']) ? strtotime($result['cancelTime']) : 0,
                'deduct_money' => $result['deductFee'] ?? 0,
            ];
        } catch (\Exception $e) {
            Log::write('达达接口请求异常：' . $e->getMessage());
            return [];
        }

    }

    /**
     * @param array $data
     * @return int[]
     * @throws \Exception
     */
    public function closeOrder($data)
    {
        try {
            $res = (new OrderCancelRequest())
                ->setBaseParams($this->config)
                ->setOrderId($data['delivery_no'])
                ->setCancelReasonId($data['cancel_reason_id'])
                ->setCancelReason($data['cancel_reason'])
                ->sendRequest();
            $result = $res['result'] ?? [];
            return [
                'deduct_money' => $result['deductFee'] ?? 0,
            ];
        } catch (\Exception $e) {
            Log::write('达达接口请求异常：' . $e->getMessage());
            throw new \Exception($e->getMessage());
        }

    }

    /**
     * 完成订单
     * @param $data
     * @return void
     */
    public function finishOrder($data)
    {

    }

    public function getTransporterPosition($data)
    {
        try {
            if (!empty($data['is_h5'])) {
                $res = (new OrderTransporterPositionH5Request())
                    ->setBaseParams($this->config)
                    ->setOrderId($data['delivery_no'])
                    ->sendRequest();
                return $res['result'] ?? [];
            } else {
                $res = (new OrderTransporterPositionRequest())
                    ->setBaseParams($this->config)
                    ->setOrderIds([$data['delivery_no']])
                    ->sendRequest();
                $result = $res['result'] ?? [];
                if (empty($result)) {
                    return [];
                }
                return [
                    'rider_latitude' => $result[0]['transporterLat'] ?? '',
                    'rider_longitude' => $result[0]['transporterLng'] ?? '',
                    'rider_name' => $result[0]['transporterName'] ?? '',
                    'rider_mobile' => $result[0]['transporterPhone'] ?? '',
                ];
            }
        } catch (\Exception $e) {
            Log::write('达达接口请求异常：' . $e->getMessage());
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * 添加门店
     * @param $data
     * @return array[]
     * @throws \Exception
     */
    public function addShop($data)
    {
        $delivery_type = $data['delivery_type'];
        $delivery_store = $data['delivery_store'];
        try {
            $res = (new ShopAddRequest())
                ->setBaseParams($this->config)
                ->setStationName($delivery_store['store_name'])
                ->setBusiness($data['business'])
                ->setStationAddress($delivery_store['full_address'])
                ->setLng($delivery_store['longitude'])
                ->setLat($delivery_store['latitude'])
                ->setContactName($delivery_store['contact_name'])
                ->setPhone($delivery_store['store_mobile'])
                ->setOriginShopId($delivery_store['store_no'])
                ->sendRequest();
            $result = $res['result'] ?? [];
            $support_delivery_array = [];
            if (!empty($result)) {
                if (!empty($result['successList'])) {
                    $support_delivery_array[$delivery_type] = [
                        'delivery_type' => $delivery_type,
                        'business' => $data['business'],
                        'store_no' => $result['successList'][0]['originShopId'],
                        'open_status' => LocalDeliveryDict::OPEN_PASS,
                        'edit_status' => '',
                        'reason' => ''
                    ];
                }
                if (!empty($result['failedList'])) {
                    $support_delivery_array[$delivery_type] = [
                        'delivery_type' => $delivery_type,
                        'business' => '',
                        'store_no' => '',
                        'open_status' => LocalDeliveryDict::OPEN_REFUND,
                        'edit_status' => '',
                        'reason' => $result['failedList'][0]['msg']
                    ];
                }
            }
            return [
                'support_delivery' => $support_delivery_array,
            ];
        } catch (\Exception $e) {
            Log::write('达达接口请求异常：' . $e->getMessage());
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * 更新门店
     * @param $data
     * @return array[]
     */
    public function editShop($data)
    {
        $delivery_type = $data['delivery_type'];
        $delivery_store = $data['delivery_store'];
        $support_delivery_array = $delivery_store['support_delivery'];
        if (!empty($support_delivery_array[$delivery_type])) {
            try {
                $request = (new ShopEditRequest())
                    ->setBaseParams($this->config)
                    ->setOriginShopId($delivery_store['store_no']);

                if (!empty($delivery_store['store_name'])) {
                    $request->setStationName($delivery_store['store_name']);
                }
                if (!empty($data['business'])) {
                    $request->setBusiness($data['business']);
                }
                if (!empty($delivery_store['full_address'])) {
                    $request->setStationAddress($delivery_store['full_address']);
                }
                if (!empty($delivery_store['longitude'])) {
                    $request->setLng($delivery_store['longitude']);
                }
                if (!empty($delivery_store['latitude'])) {
                    $request->setLat($delivery_store['latitude']);
                }
                if (!empty($delivery_store['contact_name'])) {
                    $request->setContactName($delivery_store['contact_name']);
                }
                if (!empty($delivery_store['store_mobile'])) {
                    $request->setPhone($delivery_store['store_mobile']);
                }
                $res = $request->sendRequest();
                if ($res['code'] == 0) {
                    $support_delivery_array[$delivery_type]['edit_status'] = LocalDeliveryDict::EDIT_PASS;
                    $support_delivery_array[$delivery_type]['business'] = $data['business'];
                    $support_delivery_array[$delivery_type]['reason'] = '';
                }
            } catch (\Exception $e) {
                Log::write('达达接口请求异常：' . $e->getMessage());
                $support_delivery_array[$delivery_type]['edit_status'] = LocalDeliveryDict::EDIT_REFUND;
                $support_delivery_array[$delivery_type]['reason'] = $e->getMessage();
            }
        }
        return [
            'support_delivery' => $support_delivery_array,
        ];
    }

    /**
     * 达达订单状态通知异步回调
     * @param string $action
     * @param array $params
     * @return \think\Response
     */
    function notify(string $action, array $params = [])
    {
        try {
            Log::write('达达订单状态通知回调：' . json_encode($params));
            switch ($action) {
                case 'order'://订单状态通知
                    (new CoreOrderMessageService())->orderNotify($params);
                    break;
                //                    case 'store'://商户//todo
                //                        break;
                //                    case 'recharge'://充值//todo
                //                        break;
            }
            return $this->success();
        } catch (\Exception $e) {
            Log::write('达达订单状态通知回调异常：' . $e->getMessage());
            return $this->fail($e->getMessage());
        }
    }

    public function success($message = '')
    {
        $response = [
            'code' => 200,
            'message' => $message ?: '成功',
        ];
        return response($response, 200, [], 'json');
    }

    public function fail($message = '')
    {
        $response = [
            'code' => 400,
            'message' => $message ?: '失败',
        ];
        return response($response, 400, [], 'json');
    }
}