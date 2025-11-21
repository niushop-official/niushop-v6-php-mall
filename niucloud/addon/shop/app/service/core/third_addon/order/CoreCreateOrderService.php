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

namespace addon\shop\app\service\core\third_addon\order;

use addon\shop\app\dict\goods\GoodsDict;
use addon\shop\app\dict\order\OrderDeliveryDict;
use addon\shop\app\dict\order\OrderDict;
use addon\shop\app\dict\order\OrderGoodsDict;
use addon\shop\app\dict\order\OrderLogDict;
use addon\shop\app\model\goods\Goods;
use addon\shop\app\model\goods\GoodsSku;
use addon\shop\app\model\order\Order;
use addon\shop\app\model\order\OrderGoods;
use addon\shop\app\service\core\order\CoreOrderEventService;
use core\base\BaseCoreService;
use think\facade\Log;

/**
 *  三方插件订单服务层
 */
class CoreCreateOrderService extends BaseCoreService
{

    /**
     * 拼团系统对接订单
     * @param array $data
     * @return array|void
     * @throws \Exception
     */
    public function pintuanCreateOrder(array $data)
    {
        $return_order_list = [];
        $source_id = $data['source_id'] ?? 0;
        $source = $data['source'] ?? '';
        $relate_order_list = $data['order_data'] ?? [];
        $total_orders = count($relate_order_list);

        // 合并初始日志：包含关键参数和总数
        Log::write("SHOP_PINTUAN - pintuanCreateOrder 开始处理拼团订单，订单信息" . json_encode($relate_order_list, 256));
        Log::write("SHOP_PINTUAN - pintuanCreateOrder 开始处理拼团订单，来源ID: {$source_id}，总订单数: {$total_orders}");

        if (empty($relate_order_list)) {
            Log::warning("SHOP_PINTUAN - pintuanCreateOrder 接收到的订单数据为空，来源ID: {$source_id}");
            return;
        }

        $processed_orders = 0;
        $failed_orders = []; // 记录失败订单信息

        foreach ($relate_order_list as $index => $relate_order_info) {
            $order_index = $index + 1;
            //兼容以后得数据 如果不是商城的订单全部跳过
            if ($relate_order_info['source'] != 'shop') {
                Log::warning("SHOP_PINTUAN - pintuanCreateOrder 接收到的订单数据 商品来源非商城 跳过，来源ID: {$source_id} 拼团数据来源:" . $relate_order_info['source']);
                continue;
            }
            try {
                // 提取SKU和商品信息
                $sku_info = $relate_order_info['sku'] ?? [];
                $goods_id = $sku_info['source_id'] ?? 0;
                $sku_id = $sku_info['source_sku_id'] ?? 0;
                $relate_order_id = $relate_order_info['id'] ?? 0;

                list($check_res, $reason) = $this->checkGoods( $goods_id, $sku_id, $relate_order_info['num']);
                if (!$check_res) {
                    $failed_orders[] = "第{$order_index}个订单（ID: {$relate_order_id}）" . $reason;
                    $return_order_list[$relate_order_id] = [
                        'relate_order_id' => 0,
                        'relate_order_item_id' => 0,
                        'is_success' => 0,
                        'reason' => $reason
                    ];
                    continue;
                }


                // 检查必要参数
                if (empty($goods_id) || empty($sku_id)) {
                    $failed_orders[] = "第{$order_index}个订单（ID: {$relate_order_id}）缺少商品或SKU信息";
                    continue;
                }

                // 获取商品类型
                $goods_model_info = (new Goods())->where([
                    'goods_id' => $goods_id,
                ])->field('goods_type')->findOrEmpty()->toArray();

                $goods_type = $goods_model_info['goods_type'] ?? '';
                $third_type = $goods_type == GoodsDict::VIRTUAL
                    ? OrderDeliveryDict::VIRTUAL
                    : OrderDeliveryDict::EXPRESS;
                $has_goods_types = [$goods_type];

                $third_cost_list = array_column($relate_order_info['third_cost_list'], null, 'key');
                // 构建订单数据
                $temp_order_data = [
                    'order_type' => OrderDict::TYPE,
                    'status' => OrderDict::WAIT_DELIVERY,
                    'body' => $relate_order_info['body'] ?? '',
                    'member_id' => $relate_order_info['member_id'] ?? 0,
                    'goods_money' => $relate_order_info['goods_money'] ?? 0,
                    'delivery_money' => $third_cost_list['delivery_money']['amount'] ?? 0,
                    'discount_money' => 0,
                    'order_from' => $relate_order_info['order_from'],
                    'order_money' => $relate_order_info['order_money'] ?? 0,
                    'has_goods_types' => $has_goods_types,
                    'delivery_type' => $third_type,
                    'taker_name' => $relate_order_info['taker_name'] ?? '',
                    'taker_mobile' => $relate_order_info['taker_mobile'] ?? '',
                    'buyer_ask_delivery_time' => '',
                    'taker_province' => $relate_order_info['taker_province'] ?? 0,
                    'taker_city' => $relate_order_info['taker_city'] ?? 0,
                    'taker_district' => $relate_order_info['taker_district'] ?? 0,
                    'taker_address' => $relate_order_info['taker_address'] ?? '',
                    'taker_full_address' => $relate_order_info['taker_full_address'] ?? '',
                    'taker_longitude' => $relate_order_info['taker_longitude'] ?? '',
                    'taker_latitude' => $relate_order_info['taker_latitude'] ?? '',
                    'take_store_id' => 0,
                    'member_remark' => '',
                    'order_no' => create_no(),
                    'is_enable_refund' => 1,
                    'invoice_id' => 0,
                    'out_trade_no' => $relate_order_info['out_trade_no'] ?? '',
                    'relate_id' => $source_id,
                    'relate_order_id' => $relate_order_id,
                    'relate_source' => $source,
                    'activity_type' => $source,
                    'create_time' => strtotime($relate_order_info['create_time']),
                    'pay_time' => strtotime($relate_order_info['pay_time']),
                    'pay_money' => $relate_order_info['pay_money'] ?? 0,
                ];
                $order_info = (new Order())->where([
                    'relate_id' => $source_id,
                    'relate_source' => $source,
                    'relate_order_id' => $relate_order_id,
                ])->findOrEmpty();
                if (!$order_info->isEmpty()) {
                    // 修改订单
                    $shop_order_id = $order_info['order_id'];
                    Log::write("SHOP_PINTUAN - pintuanCreateOrder 订单已存在 订单ID: {$shop_order_id}");
                } else {
                    // 创建订单
                    $shop_order_id = (new Order())->insertGetId($temp_order_data);
                    Log::write("SHOP_PINTUAN - pintuanCreateOrder 创建订单成功 订单ID: {$shop_order_id}");
                }


                if (!$shop_order_id) {
                    $failed_orders[] = "第{$order_index}个订单（ID: {$relate_order_id}）创建失败";
                    continue;
                }

                // 构建订单商品数据
                $goods_info = $sku_info['goods'] ?? [];
                $goods_single_money = ($relate_order_info['goods_money'] ?? 0) / ($relate_order_info['num'] ?? 1);
                $order_goods_data = [
                    'member_id' => $relate_order_info['member_id'] ?? 0,
                    'goods_id' => $goods_id,
                    'sku_id' => $sku_id,
                    'goods_name' => $goods_info['title'] ?? '',
                    'sku_name' => $sku_info['sku_name'] ?? '',
                    'goods_image' => $goods_info['cover'] ?? '',
                    'sku_image' => $sku_info['sku_image'] ?? '',
                    'price' => $goods_single_money,
                    'original_price' => $sku_info['price_config']['original'] ?? 0,
                    'num' => $relate_order_info['num'] ?? 0,
                    'goods_money' => ($relate_order_info['goods_money'] ?? 0),
                    'goods_type' => $goods_type,
                    'order_goods_money' => $goods_single_money,
                    'order_id' => $shop_order_id,
                    'discount_money' => 0,
                    'status' => OrderGoodsDict::NORMAL,
                    'delivery_status' => OrderDeliveryDict::WAIT_DELIVERY,
                    'extend' => '',
                    'is_gift' => 0,
                    'is_enable_refund' => 1,
                ];
                $order_goods_id = (new OrderGoods())->insertGetId($order_goods_data);
                Log::write("SHOP_PINTUAN - pintuanCreateOrder 创建订单商品成功 订单商品ID: {$order_goods_id}");

                $return_order_list[$relate_order_id] = [
                    'relate_order_id' => $shop_order_id,
                    'relate_order_item_id' => $order_goods_id,
                    'is_success' => 1
                ];
                $temp_order_data['order_id'] = $shop_order_id;
                Log::write("SHOP_PINTUAN - orderCreateAfter 触发事件");

                CoreOrderEventService::orderCreateAfter([
                    'order_id' => $shop_order_id,
                    'order_data' => $temp_order_data,
                    'order_goods_data' => [$order_goods_data],
                    'cart_ids' => [],
                    'basic' => [],
                    'main_type' => OrderLogDict::MEMBER,
                    'main_id' => $relate_order_info['member_id'] ?? 0,
                    'time' => time()
                ]);
                Log::write("SHOP_PINTUAN - orderPayAfter 触发事件");

                CoreOrderEventService::orderPayAfter([
                    'order_id' => $shop_order_id,
                    'order_data' => $temp_order_data,
                    'order_goods_data' => [$order_goods_data],
                    'cart_ids' => [],
                    'basic' => [],
                    'main_type' => OrderLogDict::MEMBER,
                    'main_id' => $relate_order_info['member_id'] ?? 0,
                    'time' => time(),
                    'send_notice' => 0
                ]);
                $processed_orders++;
            } catch (\Exception $e) {
                $failed_orders[] = "第{$order_index}个订单（ID: {$shop_order_id}）处理异常: {$e->getMessage()}";
                Log::error("SHOP_PINTUAN - pintuanCreateOrder 拼团订单处理失败，" . $e->getFile() . $e->getFile() . $e->getMessage() . $e->getTraceAsString());

            }
        }
        // 合并结果日志：包含统计和失败信息
        $result_log = ["总订单数: {$total_orders}", "成功处理: {$processed_orders}", "失败订单: " . count($failed_orders)];
        if (!empty($failed_orders)) {
            $result_log[] = "失败详情: " . implode('; ', $failed_orders);
        }
        Log::write("SHOP_PINTUAN - pintuanCreateOrder 拼团订单处理完成，" . implode('; ', $result_log));

        return $return_order_list;
    }
    public function friendHelpCreateOrder(array $data)
    {
        $return_order_list = [];
        $source_id = $data['source_id'] ?? 0;
        $source = $data['source'] ?? '';
        $relate_order_list = $data['order_data'] ?? [];
        $total_orders = count($relate_order_list);

        // 合并初始日志：包含关键参数和总数
        Log::write("SHOP_FRIEND_HELP - friend_helpCreateOrder 开始处理好友助力订单，订单信息" . json_encode($relate_order_list, 256));
        Log::write("SHOP_FRIEND_HELP - friend_helpCreateOrder 开始处理好友助力订单，来源ID: {$source_id}，总订单数: {$total_orders}");

        if (empty($relate_order_list)) {
            Log::warning("SHOP_FRIEND_HELP - friend_helpCreateOrder 接收到的订单数据为空，来源ID: {$source_id}");
            return;
        }

        $processed_orders = 0;
        $failed_orders = []; // 记录失败订单信息

        foreach ($relate_order_list as $index => $relate_order_info) {
            $order_index = $index + 1;
            //兼容以后得数据 如果不是商城的订单全部跳过
            if ($relate_order_info['source'] != 'shop') {
                Log::warning("SHOP_FRIEND_HELP - friend_helpCreateOrder 接收到的订单数据 商品来源非商城 跳过，来源ID: {$source_id} 好友助力数据来源:" . $relate_order_info['source']);
                continue;
            }
            try {
                // 提取SKU和商品信息
                $sku_info = $relate_order_info['sku'] ?? [];
                $goods_id = $sku_info['source_id'] ?? 0;
                $sku_id = $sku_info['source_sku_id'] ?? 0;
                $relate_order_id = $relate_order_info['id'] ?? 0;

                list($check_res, $reason) = $this->checkGoods( $goods_id, $sku_id, $relate_order_info['num']);
                if (!$check_res) {
                    $failed_orders[] = "第{$order_index}个订单（ID: {$relate_order_id}）" . $reason;
                    $return_order_list[$relate_order_id] = [
                        'relate_order_id' => 0,
                        'relate_order_item_id' => 0,
                        'is_success' => 0,
                        'reason' => $reason
                    ];
                    continue;
                }


                // 检查必要参数
                if (empty($goods_id) || empty($sku_id)) {
                    $failed_orders[] = "第{$order_index}个订单（ID: {$relate_order_id}）缺少商品或SKU信息";
                    continue;
                }

                // 获取商品类型
                $goods_model_info = (new Goods())->where([
                    'goods_id' => $goods_id,
                ])->field('goods_type')->findOrEmpty()->toArray();

                $goods_type = $goods_model_info['goods_type'] ?? '';
                $third_type = $goods_type == GoodsDict::VIRTUAL
                    ? OrderDeliveryDict::VIRTUAL
                    : OrderDeliveryDict::EXPRESS;
                $has_goods_types = [$goods_type];

                $third_cost_list = array_column($relate_order_info['third_cost_list'], null, 'key');
                // 构建订单数据
                $temp_order_data = [
                    'order_type' => OrderDict::TYPE,
                    'status' => OrderDict::WAIT_DELIVERY,
                    'body' => $relate_order_info['body'] ?? '',
                    'member_id' => $relate_order_info['member_id'] ?? 0,
                    'goods_money' => $relate_order_info['goods_money'] ?? 0,
                    'delivery_money' => $third_cost_list['delivery_money']['amount'] ?? 0,
                    'discount_money' => 0,
                    'order_from' => $relate_order_info['order_from'],
                    'order_money' => $relate_order_info['order_money'] ?? 0,
                    'has_goods_types' => $has_goods_types,
                    'delivery_type' => $third_type,
                    'taker_name' => $relate_order_info['taker_name'] ?? '',
                    'taker_mobile' => $relate_order_info['taker_mobile'] ?? '',
                    'buyer_ask_delivery_time' => '',
                    'taker_province' => $relate_order_info['taker_province'] ?? 0,
                    'taker_city' => $relate_order_info['taker_city'] ?? 0,
                    'taker_district' => $relate_order_info['taker_district'] ?? 0,
                    'taker_address' => $relate_order_info['taker_address'] ?? '',
                    'taker_full_address' => $relate_order_info['taker_full_address'] ?? '',
                    'taker_longitude' => $relate_order_info['taker_longitude'] ?? '',
                    'taker_latitude' => $relate_order_info['taker_latitude'] ?? '',
                    'take_store_id' => 0,
                    'member_remark' => '',
                    'order_no' => create_no(),
                    'is_enable_refund' => 1,
                    'invoice_id' => 0,
                    'out_trade_no' => $relate_order_info['out_trade_no'] ?? '',
                    'relate_id' => $source_id,
                    'relate_order_id' => $relate_order_id,
                    'relate_source' => $source,
                    'activity_type' => $source,
                    'create_time' => strtotime($relate_order_info['create_time']),
                    'pay_time' => strtotime($relate_order_info['pay_time']),
                    'pay_money' => $relate_order_info['pay_money'] ?? 0,
                ];
                $order_info = (new Order())->where([
                    'relate_id' => $source_id,
                    'relate_source' => $source,
                    'relate_order_id' => $relate_order_id,
                ])->findOrEmpty();
                if (!$order_info->isEmpty()) {
                    // 修改订单
                    $shop_order_id = $order_info['order_id'];
                    Log::write("SHOP_FRIEND_HELP - friend_helpCreateOrder 订单已存在 订单ID: {$shop_order_id}");
                } else {
                    // 创建订单
                    $shop_order_id = (new Order())->insertGetId($temp_order_data);
                    Log::write("SHOP_FRIEND_HELP - friend_helpCreateOrder 创建订单成功 订单ID: {$shop_order_id}");
                }


                if (!$shop_order_id) {
                    $failed_orders[] = "第{$order_index}个订单（ID: {$relate_order_id}）创建失败";
                    continue;
                }

                // 构建订单商品数据
                $goods_info = $sku_info['goods'] ?? [];
                $goods_single_money = ($relate_order_info['goods_money'] ?? 0) / ($relate_order_info['num'] ?? 1);
                $order_goods_data = [
                    'member_id' => $relate_order_info['member_id'] ?? 0,
                    'goods_id' => $goods_id,
                    'sku_id' => $sku_id,
                    'goods_name' => $goods_info['title'] ?? '',
                    'sku_name' => $sku_info['sku_name'] ?? '',
                    'goods_image' => $goods_info['cover'] ?? '',
                    'sku_image' => $sku_info['sku_image'] ?? '',
                    'price' => $goods_single_money,
                    'original_price' => $sku_info['price_config']['original'] ?? 0,
                    'num' => $relate_order_info['num'] ?? 0,
                    'goods_money' => ($relate_order_info['goods_money'] ?? 0),
                    'goods_type' => $goods_type,
                    'order_goods_money' => $goods_single_money,
                    'order_id' => $shop_order_id,
                    'discount_money' => 0,
                    'status' => OrderGoodsDict::NORMAL,
                    'delivery_status' => OrderDeliveryDict::WAIT_DELIVERY,
                    'extend' => '',
                    'is_gift' => 0,
                    'is_enable_refund' => 1,
                ];
                $order_goods_id = (new OrderGoods())->insertGetId($order_goods_data);
                Log::write("SHOP_FRIEND_HELP - friend_helpCreateOrder 创建订单商品成功 订单商品ID: {$order_goods_id}");

                $return_order_list[$relate_order_id] = [
                    'relate_order_id' => $shop_order_id,
                    'relate_order_item_id' => $order_goods_id,
                    'is_success' => 1
                ];
                $temp_order_data['order_id'] = $shop_order_id;
                Log::write("SHOP_FRIEND_HELP - orderCreateAfter 触发事件");

                CoreOrderEventService::orderCreateAfter([
                    'order_id' => $shop_order_id,
                    'order_data' => $temp_order_data,
                    'order_goods_data' => [$order_goods_data],
                    'cart_ids' => [],
                    'basic' => [],
                    'main_type' => OrderLogDict::MEMBER,
                    'main_id' => $relate_order_info['member_id'] ?? 0,
                    'time' => time()
                ]);
                Log::write("SHOP_FRIEND_HELP - orderPayAfter 触发事件");

                CoreOrderEventService::orderPayAfter([
                    'order_id' => $shop_order_id,
                    'order_data' => $temp_order_data,
                    'order_goods_data' => [$order_goods_data],
                    'cart_ids' => [],
                    'basic' => [],
                    'main_type' => OrderLogDict::MEMBER,
                    'main_id' => $relate_order_info['member_id'] ?? 0,
                    'time' => time(),
                    'send_notice' => 0
                ]);
                $processed_orders++;
            } catch (\Exception $e) {
                $failed_orders[] = "第{$order_index}个订单（ID: {$shop_order_id}）处理异常: {$e->getMessage()}";
                Log::error("SHOP_FRIEND_HELP - friend_helpCreateOrder 好友助力订单处理失败，" . $e->getFile() . $e->getFile() . $e->getMessage() . $e->getTraceAsString());

            }
        }
        // 合并结果日志：包含统计和失败信息
        $result_log = ["总订单数: {$total_orders}", "成功处理: {$processed_orders}", "失败订单: " . count($failed_orders)];
        if (!empty($failed_orders)) {
            $result_log[] = "失败详情: " . implode('; ', $failed_orders);
        }
        Log::write("SHOP_FRIEND_HELP - friend_helpCreateOrder 好友助力订单处理完成，" . implode('; ', $result_log));

        return $return_order_list;
    }

    /**
     * 接龙订单处理
     * @param array $data
     * @return array
     */
    public function relayCreateOrder(array $data): array
    {
        // 初始化返回数据
        $return_order_list = [];

        // 提取基础参数并设置默认值
        $source_id = $data['source_id'] ?? 0;//接龙   一个订单的订单项可以来自不同的活动
        $source = $data['source'] ?? '';
        $relate_order_list = $data['order_data'] ?? [];
        $total_orders = count($relate_order_list);

        // 记录开始日志
        Log::write("SHOP_RELAY - 开始处理接龙订单，来源ID: {$source_id}，总订单数: {$total_orders}");
        Log::write("SHOP_RELAY - 订单数据: " . json_encode($relate_order_list, JSON_UNESCAPED_UNICODE));

        // 订单数据为空时直接返回
        if (empty($relate_order_list)) {
            Log::warning("SHOP_RELAY - 接收到的订单数据为空，来源ID: {$source_id}");
            return $return_order_list;
        }

        // 初始化统计变量
        $processed_orders = 0;
        $failed_orders = [];

        // 遍历处理每个订单
        foreach ($relate_order_list as $index => $relate_order_info) {

            $order_index = $index + 1;
            $relay_order_id = $relate_order_info['id'] ?? 0;

            // 过滤非商城来源订单
            if ($relate_order_info['source'] != 'shop') {
                Log::warning("SHOP_RELAY - 订单来源非商城，跳过处理，索引: {$order_index}，来源: {$relate_order_info['source']}");
                continue;
            }

            try {
                // 1. 商品校验与类型获取
                $goods_types = [];
                $all_check_res = true;
                //检测订单项 商品是否可用  主要是为了获取订单项的goods_type
                foreach ($relate_order_info['goods'] as $order_item) {
                    if ($order_item['source'] != 'shop'){
                        continue;
                    }
                    $relay_order_goods_id = $order_item['id'] ?? 0;
                    $goods_id = $order_item['sku']['source_id'] ?? 0;
                    $sku_id = $order_item['sku']['source_sku_id'] ?? 0;
                    $num = $order_item['num'] ?? 1;

                    // 获取商品类型
                    $goods_model_info = (new Goods())->where([
                        'goods_id' => $goods_id,
                    ])->field('goods_type')->findOrEmpty()->toArray();

                    $goods_type = $goods_model_info['goods_type'] ?? '';
                    $goods_types[$order_item['order_id']] = $goods_type;

                    // 商品规格校验
                    list($check_res, $reason) = $this->checkGoods($goods_id, $sku_id, $num);
                    if (!$check_res) {
                        $all_check_res = false;
                        $return_order_list[$relay_order_goods_id] = [
                            'source_order_id' => $relay_order_id,
                            'source_order_item_id' => $relay_order_goods_id,
                            'relate_order_id' => 0,
                            'relate_order_item_id' => 0,
                            'is_success' => 0,
                            'reason' => $reason,
                        ];
                    }
                }

                // 校验失败处理
                if (!$all_check_res) {
                    //有校验未通过的订单项，停止同步订单
                    break;
                }

                // 2. 构建订单数据
                $third_cost_list = array_column($relate_order_info['third_cost_list'], null, 'key');
                $has_goods_types = array_values($goods_types);
                $delivery_type = in_array(OrderDeliveryDict::EXPRESS, $has_goods_types)
                    ? OrderDeliveryDict::EXPRESS
                    : OrderDeliveryDict::VIRTUAL;

                $temp_order_data = [
                    'order_type' => OrderDict::TYPE,
                    'status' => OrderDict::WAIT_DELIVERY,
                    'body' => $relate_order_info['body'] ?? '',
                    'member_id' => $relate_order_info['member_id'] ?? 0,
                    'goods_money' => $relate_order_info['goods_money'] ?? 0,
                    'delivery_money' => $third_cost_list['delivery_money']['amount'] ?? 0,
                    'discount_money' => 0,
                    'order_from' => $relate_order_info['order_from'] ?? '',
                    'order_money' => $relate_order_info['order_money'] ?? 0,
                    'has_goods_types' => $has_goods_types,
                    'delivery_type' => $delivery_type,
                    'taker_name' => $relate_order_info['taker_name'] ?? '',
                    'taker_mobile' => $relate_order_info['taker_mobile'] ?? '',
                    'buyer_ask_delivery_time' => '',
                    'taker_province' => $relate_order_info['taker_province'] ?? 0,
                    'taker_city' => $relate_order_info['taker_city'] ?? 0,
                    'taker_district' => $relate_order_info['taker_district'] ?? 0,
                    'taker_address' => $relate_order_info['taker_address'] ?? '',
                    'taker_full_address' => $relate_order_info['taker_full_address'] ?? '',
                    'taker_longitude' => $relate_order_info['taker_longitude'] ?? '',
                    'taker_latitude' => $relate_order_info['taker_latitude'] ?? '',
                    'take_store_id' => 0,
                    'member_remark' => '',
                    'order_no' => create_no(),
                    'is_enable_refund' => 1,
                    'invoice_id' => 0,
                    'out_trade_no' => $relate_order_info['out_trade_no'] ?? '',
                    'relate_id' => $source_id,
                    'relate_order_id' => $relay_order_id,
                    'relate_source' => $source,
                    'activity_type' => $source,
                    'create_time' => strtotime($relate_order_info['create_time'] ?? ''),
                    'pay_time' => strtotime($relate_order_info['pay_time'] ?? ''),
                    'pay_money' => $relate_order_info['pay_money'] ?? 0,
                ];

                // 3. 检查订单是否已存在，存在则更新，不存在则创建
                $order_model = new Order();
                $order_info = $order_model->where([
                    'relate_id' => $source_id,
                    'relate_source' => $source,
                    'relate_order_id' => $relay_order_id,
                ])->findOrEmpty();

                if (!$order_info->isEmpty()) {
                    $shop_order_id = $order_info['order_id'];
                    Log::write("SHOP_RELAY - 订单已存在 订单ID: {$shop_order_id}");
                } else {
                    $shop_order_id = $order_model->insertGetId($temp_order_data);
                    Log::write("SHOP_RELAY - 创建订单成功 订单ID: {$shop_order_id}");
                }

                // 订单创建失败处理
                if (!$shop_order_id) {
                    $fail_msg = "第{$order_index}个订单（ID: {$relay_order_id}）创建失败";
                    $failed_orders[] = $fail_msg;
                    $return_order_list['main_order'] = [
                        'source_order_id' => $relay_order_id,
                        'source_order_item_id' => 0,
                        'relate_order_id' => $shop_order_id,
                        'relate_order_item_id' => 0,
                        'is_success' => 0,
                        'reason' => $fail_msg,
                    ];
                    continue;
                }else{
                    $return_order_list['main_order'] = [
                        'source_order_id' => $relay_order_id,
                        'source_order_item_id' => 0,
                        'relate_order_id' => $shop_order_id,
                        'relate_order_item_id' => 0,
                        'is_success' => 1,
                        'reason' => '',
                    ];
                }

                // 4. 处理订单商品
                $order_goods_data = [];
                $order_goods_ids = [];
                foreach ($relate_order_info['goods'] as $order_item) {
                    if ($order_item['source'] != 'shop'){
                        continue;
                    }
                    $relay_order_goods_id = $order_item['id'] ?? [];
                    $goods_info = $order_item['sku']['goods'] ?? [];
                    $sku_info = $order_item['sku'] ?? [];
                    $goods_single_money = ($order_item['goods_money'] ?? 0) / ($order_item['num'] ?? 1);

                    $order_goods_item = [
                        'order_id' => $shop_order_id,
                        'member_id' => $order_item['member_id'] ?? 0,
                        'goods_id' => $goods_info['source_id'] ?? 0,
                        'sku_id' => $sku_info['source_sku_id'] ?? 0,
                        'goods_name' => $goods_info['title'] ?? '',
                        'sku_name' => $sku_info['sku_name'] ?? '',
                        'goods_image' => $goods_info['cover'] ?? '',
                        'sku_image' => $sku_info['sku_image'] ?? '',
                        'price' => $goods_single_money,
                        'original_price' => $sku_info['price_config']['original'] ?? 0,
                        'num' => $order_item['num'] ?? 0,
                        'goods_money' => $order_item['goods_money'] ?? 0,
                        'goods_type' => $goods_types[$order_item['id']] ?? '',
                        'order_goods_money' => $goods_single_money,
                        'discount_money' => 0,
                        'status' => OrderGoodsDict::NORMAL,
                        'delivery_status' => OrderDeliveryDict::WAIT_DELIVERY,
                        'extend' => '',
                        'is_gift' => 0,
                        'is_enable_refund' => 1,
                    ];
                    $relate_order_goods_info = (new OrderGoods())->where([
                        'order_id' => $shop_order_id,
                        'member_id' => $order_item['member_id'] ?? 0,
                        'goods_id' => $goods_info['source_id'] ?? 0,
                        'sku_id' => $sku_info['source_sku_id'] ?? 0,
                    ])->findOrEmpty();
                    if (!$relate_order_goods_info->isEmpty()) {
                        $relate_order_goods_id = $relate_order_goods_info->order_goods_id;
                    }else{
                        $relate_order_goods_id = (new OrderGoods())->insertGetId($order_goods_item);
                    }
                    //记录成功订单项
                    $return_order_list[$relay_order_goods_id] = [
                        'source_order_id' => $relay_order_id,
                        'source_order_item_id' => $relay_order_goods_id,
                        'relate_order_id' => $shop_order_id,
                        'relate_order_item_id' => $relate_order_goods_id,
                        'is_success' => 1,
                        'reason' => '',
                    ];
                    Log::write("SHOP_RELAY - 创建订单商品成功 订单商品ID: {$relay_order_goods_id}");

                    $order_goods_ids[$order_item['id']] = $relay_order_goods_id;
                    $order_goods_data[] = $order_goods_item;
                }

                // 5. 触发订单后续事件
                $temp_order_data['order_id'] = $shop_order_id;
                Log::write("SHOP_RELAY - 触发 orderCreateAfter 事件");

                CoreOrderEventService::orderCreateAfter([
                    'order_id' => $shop_order_id,
                    'order_data' => $temp_order_data,
                    'order_goods_data' => $order_goods_data,
                    'cart_ids' => [],
                    'basic' => [],
                    'main_type' => OrderLogDict::MEMBER,
                    'main_id' => $relate_order_info['member_id'] ?? 0,
                    'time' => time()
                ]);

                Log::write("SHOP_RELAY - 触发 orderPayAfter 事件");
                CoreOrderEventService::orderPayAfter([
                    'order_id' => $shop_order_id,
                    'order_data' => $temp_order_data,
                    'order_goods_data' => $order_goods_data,
                    'cart_ids' => [],
                    'basic' => [],
                    'main_type' => OrderLogDict::MEMBER,
                    'main_id' => $relate_order_info['member_id'] ?? 0,
                    'time' => time(),
                    'send_notice' => 0
                ]);
                $processed_orders++;

            } catch (\Exception $e) {
                $failed_orders[] = "第{$order_index}个订单 处理异常: {$e->getMessage()}";
                Log::error("SHOP_RELAY - relayCreateOrder 拼团订单处理失败，" . $e->getFile() . $e->getFile() . $e->getMessage());
            }
        }

        // 记录处理结果日志
        $result_log = [
            "总订单数: {$total_orders}",
            "成功处理: {$processed_orders}",
            "失败订单: " . count($failed_orders)
        ];
        if (!empty($failed_orders)) {
            $result_log[] = "失败详情: " . implode('; ', $failed_orders);
        }
        Log::write("SHOP_RELAY - 接龙订单处理完成，" . implode('; ', $result_log));
        Log::write("SHOP_RELAY - 接龙订单处理完成， 返回数据：" . json_encode($return_order_list));

        return $return_order_list;
    }

    private function checkGoods($goods_id, $sku_id, $num = 1)
    {
        $goods_info = (new Goods())->where([
            'goods_id' => $goods_id,
        ])->findOrEmpty();
        if ($goods_info->isEmpty()) {
            return [false, get_lang('SHOP_GOODS_NOT_EXIST')];
        }
        $sku_info = (new GoodsSku())->where([
            'goods_id' => $goods_id,
            'sku_id' => $sku_id,
        ])->findOrEmpty();
        if ($sku_info->isEmpty()) {
            return [false, get_lang('SHOP_GOODS_NOT_EXIST')];
        }
        $stock = $sku_info['stock'] ?? 0;
        return [$stock >= $num, get_lang('SHOP_ORDER_GOODS_INSUFFICIENT')];
    }
}
