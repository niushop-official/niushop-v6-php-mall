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

namespace addon\shop\app\model\local_delivery;

use addon\shop\app\dict\local_delivery\OrderLogDict;
use core\base\BaseModel;


/**
 * 配送订单日志模型
 * Class LocalDeliveryOrderLog
 * @package app\model\local_delivery
 */
class LocalDeliveryOrderLog extends BaseModel
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
    protected $name = 'shop_delivery_local_delivery_order_log';

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
     * 同城配送订单
     */
    public function localDeliveryOrder()
    {
        return $this->hasOne(LocalDeliveryOrder::class, 'id', 'order_id');
    }
}
