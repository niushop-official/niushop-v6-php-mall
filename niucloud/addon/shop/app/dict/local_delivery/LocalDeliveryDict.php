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
 * 同城配送枚举类
 * Class LocalDeliveryDict
 * @package addon\shop\app\dict\local_delivery
 */
class LocalDeliveryDict
{

    //商家配送
    const MERCHANT = 'merchant';

    public static function getType($type = '')
    {
        $list = [
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

        $extend = event('LocalDeliveryType');
        $type_list = array_merge($list, ...$extend);
        if ($type == '') return $type_list;
        return $type_list[$type]['name'] ?? '';
    }


    //门店开通状态
    const OPEN_PASS = 1;//通过
    const OPEN_REFUND = 2;//未通过

    //门店编辑状态
    const EDIT_PASS = 1;//编辑审核通过
    const EDIT_REFUND = 2;//编辑审核驳回

}