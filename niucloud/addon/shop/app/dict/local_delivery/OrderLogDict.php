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

namespace addon\shop\app\dict\local_delivery;


/**
 * 订单日志枚举类
 */
class OrderLogDict
{

    const STORE = 'store';
    const RIDER = 'rider';
    const MEMBER = 'member';
    const SYSTEM = 'system';


    /**
     * 获取操作人类型
     * @param string $type
     * @return array|array[]|string
     */
    public static function getMainType(string $type = '')
    {
        $data = [
            self::STORE => get_lang('dict_shop_local_delivery_order_log.store'),//商家
            self::RIDER => get_lang('dict_shop_local_delivery_order_log.rider'),//骑手
            self::MEMBER => get_lang('dict_shop_local_delivery_order_log.member'),//会员
            self::SYSTEM => get_lang('dict_shop_local_delivery_order_log.system'),//系统
        ];
        if (!$type) {
            return $data;
        }
        return $data[$type] ?? '';
    }

    const ORDER_ACCEPTED = 'order_accepted';//商家已接单
    const ORDER_CALL = 'order_call';//商家呼叫配送
    const DISPATCH_ORDER = 'dispatch_order';//商家指派骑手
    const RIDER_ACCEPTED = 'rider_accepted';//骑手已接单
    const RIDER_ARRIVED = 'rider_arrived';//骑手已到店
    const RIDER_PICKED_UP = 'rider_picked_up';//骑手已取货
    const ORDER_DELIVERED = 'order_delivered';//订单已送达
    const ORDER_CANCELED = 'order_canceled';//订单已取消
    const RETURN_APPLIED = 'return_applied';//物品返回申请
    const RETURN_COMPLETED = 'return_completed';//物品返回完成

    /**
     * 获取操作
     * @return array
     */
    public static function getOperate($status)
    {
        switch ($status) {
            case LocalDeliveryStatusDict::ORDER_ACCEPTED:
                return [
                    'operate' => self::ORDER_ACCEPTED,
                    'operate_desc' => get_lang('dict_shop_local_delivery_order_log.order_accepted')
                ];// 商家已接单
            case LocalDeliveryStatusDict::PENDING_ACCEPTANCE:
                return [
                    'operate' => self::ORDER_CALL,
                    'operate_desc' => get_lang('dict_shop_local_delivery_order_log.order_call')
                ];// 商家呼叫配送
            case LocalDeliveryStatusDict::DISPATCH_ORDER:
                return [
                    'operate' => self::DISPATCH_ORDER,
                    'operate_desc' => get_lang('dict_shop_local_delivery_order_log.dispatch_order')
                ];// 商家指派骑手
            case LocalDeliveryStatusDict::PENDING_PICKUP:
                return [
                    'operate' => self::RIDER_ACCEPTED,
                    'operate_desc' => get_lang('dict_shop_local_delivery_order_log.rider_accepted')
                ];// 骑手已接单
            case LocalDeliveryStatusDict::RIDER_ARRIVED:
                return [
                    'operate' => self::RIDER_ARRIVED,
                    'operate_desc' => get_lang('dict_shop_local_delivery_order_log.rider_arrived')
                ];// 骑手已到店
            case LocalDeliveryStatusDict::IN_DELIVERY:
                return [
                    'operate' => self::RIDER_PICKED_UP,
                    'operate_desc' => get_lang('dict_shop_local_delivery_order_log.rider_picked_up')
                ];// 骑手已取货
            case LocalDeliveryStatusDict::COMPLETED:
                return [
                    'operate' => self::ORDER_DELIVERED,
                    'operate_desc' => get_lang('dict_shop_local_delivery_order_log.order_delivered')
                ];// 订单已送达
            case LocalDeliveryStatusDict::CANCELED:
                return [
                    'operate' => self::ORDER_CANCELED,
                    'operate_desc' => get_lang('dict_shop_local_delivery_order_log.order_canceled')
                ];// 订单已取消
            case LocalDeliveryStatusDict::RETURN_IN_PROGRESS:
                return [
                    'operate' => self::RETURN_APPLIED,
                    'operate_desc' => get_lang('dict_shop_local_delivery_order_log.return_applied')
                ];// 物品返回申请
            case LocalDeliveryStatusDict::RETURN_COMPLETED:
                return [
                    'operate' => self::RETURN_COMPLETED,
                    'operate_desc' => get_lang('dict_shop_local_delivery_order_log.return_completed')
                ];// 物品返回完成
        }
        return [
            'operate' => '',
            'operate_desc' => ''
        ];
    }

}
