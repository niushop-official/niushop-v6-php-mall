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
 *订单取消原因相关枚举类
 */
class OrderCancelReasonDict
{
    /**
     * 获取订单取消原因枚举列表
     * @return array|mixed|string
     */
    public static function getType($type = '', string $service = '')
    {
        $data = array_filter(event('OrderCancelReason', ['service' => $service]))[0];
        if ($type == '') {
            return $data;
        }
        return $data[$type] ?? '';
    }

}
