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
 *同城配送相关枚举类
 */
class LocalDeliveryStatusDict
{

    const WAIT_RELEASE = -100; // 预约待发布
    // 订单状态常量定义
    const ORDER_ACCEPTED = 0; // 商家已接单
    const PENDING_ACCEPTANCE = 1;      // 待接单
    const PENDING_PICKUP = 2;          // 待取货
    const RIDER_ARRIVED = 3;         // 骑士到店
    const IN_DELIVERY = 4;             // 配送中
    const COMPLETED = 5;               // 已完成
    const DISPATCH_ORDER = 6;          // 指派单（已追加待接单）
    const RETURN_IN_PROGRESS = 7;      // 妥投异常之物品返回中
    const RETURN_COMPLETED = 8;        // 妥投异常之物品返回完成
    const RETURN_RIDER_ARRIVED = 9;    // 售后取件单送达门店
    const CREATE_ORDER_FAILED = -1; // 创建运单失败
    const CANCELED = -2;                // 已取消

    /**
     * 获取所有订单状态映射
     * @return array|mixed|string
     */
    public static function getStatus(string $status = '')
    {
        $data = [
            self::WAIT_RELEASE => get_lang('dict_shop_local_delivery_status.wait_release'), //预约待发布,
            self::ORDER_ACCEPTED => get_lang('dict_shop_local_delivery_status.order_accepted'), //商家已接单,
            self::PENDING_ACCEPTANCE => get_lang('dict_shop_local_delivery_status.pending_acceptance'), //待接单,
            self::PENDING_PICKUP => get_lang('dict_shop_local_delivery_status.pending_pickup'), //待取货,
            self::RIDER_ARRIVED => get_lang('dict_shop_local_delivery_status.rider_arrived'), //骑士到店,
            self::IN_DELIVERY => get_lang('dict_shop_local_delivery_status.in_delivery'), //配送中,
            self::COMPLETED => get_lang('dict_shop_local_delivery_status.completed'), //已完成,
            self::DISPATCH_ORDER => get_lang('dict_shop_local_delivery_status.dispatch_order'), //指派单,
            self::RETURN_IN_PROGRESS => get_lang('dict_shop_local_delivery_status.return_in_progress'), //妥投异常之物品返回中,
            self::RETURN_COMPLETED => get_lang('dict_shop_local_delivery_status.return_completed'), //妥投异常之物品返回完成,
            self::RETURN_RIDER_ARRIVED => get_lang('dict_shop_local_delivery_status.return_rider_arrived'), //售后取件单送达门店,
            self::CREATE_ORDER_FAILED => get_lang('dict_shop_local_delivery_status.create_order_failed'), //创建运单失败,
            self::CANCELED => get_lang('dict_shop_local_delivery_status.canceled'), //已取消,
        ];
        if ($status == '' && $status !== 0) {
            return $data;
        }
        return $data[$status] ?? '';
    }

}
