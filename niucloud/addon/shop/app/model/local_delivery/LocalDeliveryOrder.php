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

use addon\shop\app\dict\local_delivery\LocalDeliveryDict;
use addon\shop\app\dict\local_delivery\LocalDeliveryPreStatusDict;
use addon\shop\app\dict\local_delivery\LocalDeliveryStatusDict;
//use addon\shop\app\dict\trade\TradeDict;
use core\base\BaseModel;
use think\model\relation\HasOne;


/**
 * 配送记录模型
 * Class LocalDeliveryService
 * @package app\model\delivery
 */
class LocalDeliveryOrder extends BaseModel
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
    protected $name = 'shop_delivery_local_delivery_order';

    //类型
    protected $type = [
        'finish_time' => 'timestamp',
        'cancel_time' => 'timestamp'
    ];

    /**
     * 搜索器:交易单号
     * @param $query
     * @param $value
     */
    public function searchTradeNoAttr($query, $value)
    {
        if ($value != '') {
            $query->where('trade_no', 'like', '%' . $value . '%');
        }
    }

    /**
     * 搜索器:交易类型
     * @param $query
     * @param $value
     */
    public function searchTradeTypeAttr($query, $value)
    {
        if ($value != '') {
            $query->where('trade_type', $value);
        }
    }

    /**
     * 搜索器:配送单号
     * @param $query
     * @param $value
     */
    public function searchDeliveryNoAttr($query, $value)
    {
        if ($value != '') {
            $query->where('delivery_no', 'like', '%' . $value . '%');
        }
    }

    /**
     * 搜索器:配送状态
     * @param $query
     * @param $value
     */
    public function searchStatusAttr($query, $value)
    {
        if ($value != '') {
            $query->where('status', $value);
        }
    }

    /**
     * 搜索器:创建时间
     * @param $query
     * @param $value
     * @param $data
     */
    public function searchCreateTimeAttr($query, $value, $data)
    {
        $start_time = empty($value[0]) ? 0 : strtotime($value[0]);
        $end_time = empty($value[1]) ? 0 : strtotime($value[1]);
        if ($start_time > 0 && $end_time > 0) {
            $query->whereBetweenTime('create_time', $start_time, $end_time);
        } elseif ($start_time > 0 && $end_time == 0) {
            $query->where([['create_time', '>=', $start_time]]);
        } elseif ($start_time == 0 && $end_time > 0) {
            $query->where([['create_time', '<=', $end_time]]);
        }
    }

    /**
     * 转化配送状态
     */
    public function getStatusNameAttr($value, $data)
    {
        if ($data['status'] == '') return '';
        return LocalDeliveryStatusDict::getStatus($data['status']);
    }

    public function getPreStatusNameAttr($value, $data)
    {
        if (!empty($data['delivery_service'])) {
            return LocalDeliveryPreStatusDict::getStatus($data['delivery_service']);
        }
        return '';
    }

    /**
     * 转化配送服务商名称
     */
    public function getDeliveryServiceNameAttr($value, $data)
    {
        if (!empty($data['delivery_service'])) {
            return LocalDeliveryDict::getType($data['delivery_service']);
        }
        return '';
    }

    /**
     * 转化配送服务商名称
     */
//    public function getTradeTypeNameAttr($value, $data)
//    {
//        if (!empty($data['trade_type'])) {
//            return TradeDict::getTradeType()[$data['trade_type']] ?? '';
//        }
//        return '';
//    }

    /**
     * 日志
     */
    public function orderLog()
    {
        return $this->hasMany(LocalDeliveryOrderLog::class, 'order_id', 'id')->append(['main_type_name']);
    }
}
