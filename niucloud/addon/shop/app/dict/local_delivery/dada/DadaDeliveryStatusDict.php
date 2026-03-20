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

namespace addon\shop\app\dict\local_delivery\dada;

use addon\shop\app\dict\local_delivery\LocalDeliveryStatusDict;

/**
 *同城三方配送相关枚举类
 */
class DadaDeliveryStatusDict
{
    // 订单状态常量定义

    const WAIT_RELEASE = 0;      // 预约待发布
    const PENDING_ACCEPTANCE = 1;      // 待接单
    const PENDING_PICKUP = 2;          // 待取货
    const RIDER_ARRIVED = 100;         // 骑士到店
    const IN_DELIVERY = 3;             // 配送中
    const COMPLETED = 4;               // 已完成
    const DISPATCH_ORDER = 8;          // 指派单（已追加待接单）
    const RETURN_IN_PROGRESS = 9;      // 妥投异常之物品返回中
    const RETURN_COMPLETED = 10;        // 妥投异常之物品返回完成
    const RETURN_RIDER_ARRIVED = 6;    // 售后取件单送达门店
    const CREATE_ORDER_FAILED = 1000; // 创建运单失败
    const CANCELED = 5;                // 已取消

    /**
     * 转换配送状态
     * @param $status
     * @return int
     */
    public static function convertStatus($status)
    {
        switch ($status) {
            case self::WAIT_RELEASE:
                return LocalDeliveryStatusDict::WAIT_RELEASE;// 待接单
            case self::PENDING_ACCEPTANCE:
                return LocalDeliveryStatusDict::PENDING_ACCEPTANCE;// 待接单
            case self::PENDING_PICKUP:
                return LocalDeliveryStatusDict::PENDING_PICKUP;// 待取货
            case self::RIDER_ARRIVED:
                return LocalDeliveryStatusDict::RIDER_ARRIVED;// 骑士到店
            case self::IN_DELIVERY:
                return LocalDeliveryStatusDict::IN_DELIVERY;// 配送中
            case self::COMPLETED:
                return LocalDeliveryStatusDict::COMPLETED;// 已完成
            case self::CANCELED:
                return LocalDeliveryStatusDict::CANCELED;// 已取消
            case self::DISPATCH_ORDER:
                return LocalDeliveryStatusDict::DISPATCH_ORDER;// 指派单（已追加待接单）
            case self::RETURN_IN_PROGRESS:
                return LocalDeliveryStatusDict::RETURN_IN_PROGRESS;// 妥投异常之物品返回中
            case self::RETURN_COMPLETED:
                return LocalDeliveryStatusDict::RETURN_COMPLETED;// 妥投异常之物品返回完成
            case self::RETURN_RIDER_ARRIVED:
                return LocalDeliveryStatusDict::RETURN_RIDER_ARRIVED;// 售后取件单送达门店
            case self::CREATE_ORDER_FAILED:
                return LocalDeliveryStatusDict::CREATE_ORDER_FAILED;// 创建运单失败
        }
        return 0;
    }

}
