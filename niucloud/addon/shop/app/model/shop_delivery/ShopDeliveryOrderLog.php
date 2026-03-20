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

namespace addon\shop\app\model\shop_delivery;

use addon\shop\app\dict\local_delivery\OrderLogDict;
use core\base\BaseModel;


/**
 * 商家配送订单日志模型
 * Class ShopDeliveryOrderLog
 * @package app\model\shop_delivery
 */
class ShopDeliveryOrderLog extends BaseModel
{

    /**
     * 数据表主键
     * @var string
     */
    protected $pk = 'id';

    /**
     * 模型名称
     * @var string
     */
    protected $name = 'shop_delivery_shop_delivery_order_log';

    /**
     * 来源渠道
     * @param $value
     * @param $data
     * @return mixed|string|void
     */
    public function getMainTypeNameAttr($value, $data)
    {
        if (empty($data[ 'main_type' ]))
            return '';
        return OrderLogDict::getMainType() [ $data[ 'main_type' ] ] ?? '';
    }

    /**
     * 商家配送订单
     */
    public function shopDeliveryOrder()
    {
        return $this->hasOne(ShopDeliveryOrder::class, 'id', 'order_id');
    }
}
