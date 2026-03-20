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

namespace addon\shop\app\listener\local_delivery\dada\order;


use addon\shop\app\dict\local_delivery\dada\DadaDeliveryDict;

/**
 * 支付方式
 * @package addon\mall\app\listener\diy
 */
class OrderCancelReasonListener
{

    public function handle($params)
    {
        // dada 订单取消原因
        if (!empty($params['service']) && $params['service'] == DadaDeliveryDict::DADA) {
            return [
                1 => get_lang('dict_shop_local_delivery_order_cancel.no_rider'), //没有配送员接单,
                2 => get_lang('dict_shop_local_delivery_order_cancel.no_rider_pickup'), //配送员没来取货,
                3 => get_lang('dict_shop_local_delivery_order_cancel.rider_bad_attitude'), //配送员态度太差,
                4 => get_lang('dict_shop_local_delivery_order_cancel.customer_cancel'), //顾客取消订单,
                5 => get_lang('dict_shop_local_delivery_order_cancel.order_write_error'), //订单填写错误,
                34 => get_lang('dict_shop_local_delivery_order_cancel.rider_cancel'), //配送员让我取消此单,
                35 => get_lang('dict_shop_local_delivery_order_cancel.rider_unwill'), //配送员不愿上门取货,
                36 => get_lang('dict_shop_local_delivery_order_cancel.rider_unnecessary'), //我不需要配送了,
                37 => get_lang('dict_shop_local_delivery_order_cancel.rider_cannot_complete'), //配送员以各种理由表示无法完成订单,
                10000 => get_lang('dict_shop_local_delivery_order_cancel.other_reason'), //其他,
            ];
        }
    }
}
