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

use addon\shop\app\dict\local_delivery\LocalDeliveryPreStatusDict;
use addon\shop\app\dict\local_delivery\LocalDeliveryStatusDict;
use addon\shop\app\dict\shop_delivery\ShopDeliveryDict;
use core\base\BaseModel;
use think\model\relation\HasOne;


/**
 * 商家配送记录模型
 * Class ShopDeliveryOrder
 * @package addon\shop\app\model\shop_delivery
 */
class ShopDeliveryOrder extends BaseModel
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
    protected $name = 'shop_delivery_shop_delivery_order';

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
        if (!empty($data['status'])) {
            return LocalDeliveryStatusDict::getStatus($data['status']);
        }
        return '';
    }

    public function getPreStatusNameAttr($value, $data)
    {
        return LocalDeliveryPreStatusDict::getStatus(ShopDeliveryDict::MERCHANT);
    }

    /**
     * 关联配送员
     * @return HasOne
     */
    public function deliver()
    {
        return $this->hasOne(Deliver::class, 'deliver_id', 'deliver_id');
    }

    /**
     * 日志
     */
    public function orderLog()
    {
        return $this->hasMany(ShopDeliveryOrderLog::class, 'order_id', 'id')->append(['main_type_name']);
    }
}
