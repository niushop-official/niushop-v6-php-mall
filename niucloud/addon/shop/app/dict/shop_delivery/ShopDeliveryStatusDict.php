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

namespace addon\shop\app\dict\shop_delivery;


use addon\shop\app\dict\local_delivery\LocalDeliveryStatusDict;

/**
 *同城三方配送相关枚举类
 */
class ShopDeliveryStatusDict
{
    // 订单状态常量定义
    const IN_DELIVERY = 4;             // 配送中
    const COMPLETED = 5;               // 已完成
    const CANCELED = -2;                // 已取消

    /**
     * 获取商家配送订单状态映射
     * @return array
     */
    public static function getStatus()
    {
        return [
            self::IN_DELIVERY => get_lang('dict_shop_local_delivery_status.in_delivery'), //配送中,
            self::COMPLETED => get_lang('dict_shop_local_delivery_status.completed'), //已完成,
            self::CANCELED => get_lang('dict_shop_local_delivery_status.canceled'), //已取消,
        ];
    }


    /**
     * 转换配送状态
     * @param $status
     * @return int
     */
    public static function convertStatus($status)
    {
        switch ($status) {
            case self::IN_DELIVERY:
                return LocalDeliveryStatusDict::IN_DELIVERY;// 配送中
            case self::COMPLETED:
                return LocalDeliveryStatusDict::COMPLETED;//已完成
            case self::CANCELED:
                return LocalDeliveryStatusDict::CANCELED;//已取消
        }
        return 0;
    }
}
