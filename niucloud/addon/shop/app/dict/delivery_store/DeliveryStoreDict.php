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

namespace addon\shop\app\dict\delivery_store;

/**
 * 提货点枚举类
 * Class LocalDeliveryDict
 * @package app\dict\sys
 */
class DeliveryStoreDict
{

    //同城配送
    const LOCAL_DELIVERY = 'local_delivery';
    //门店自提
    const STORE = 'store';

    /**
     * 获取提货类型
     * @param string $type
     * @return array|string
     */
    public static function getPickUpType($type = '')
    {
        $pick_up_type = [
            self::LOCAL_DELIVERY => get_lang('dict_shop_delivery_store_pick_up_type.local_delivery'),
            self::STORE => get_lang('dict_shop_delivery_store_pick_up_type.store'),
        ];
        if (!empty($type)) {
            return $pick_up_type[$type] ?? '';
        }
        return $pick_up_type;
    }

}