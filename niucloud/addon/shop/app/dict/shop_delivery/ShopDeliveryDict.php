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

/**
 * 商家配送配送枚举类
 * Class LocalDeliveryDict
 * @package app\dict\sys
 */
class ShopDeliveryDict
{
    //商家配送
    const MERCHANT = 'merchant';

    public static function getType()
    {
        $system = [
            self::MERCHANT => [
                'name' => '商家配送',
                'key' => self::MERCHANT,
                //配置参数
                'params' => [],
                'component' => '',
                'business_list' => '',
                'extend_data' => [],
                //商家配送标识
                'is_merchant' => 1
            ],
        ];
        return $system;
    }

}