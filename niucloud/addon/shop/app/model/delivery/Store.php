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

namespace addon\shop\app\model\delivery;

use addon\shop\app\dict\delivery\DeliveryDict;
use addon\shop\app\dict\delivery_store\DeliveryStoreDict;
use core\base\BaseModel;
use think\db\Query;


/**
 * 自提门店模型
 * Class Store
 * @package addon\shop\app\model\delivery
 */
class Store extends BaseModel
{

    /**
     * 数据表主键
     * @var string
     */
    protected $pk = 'store_id';

    /**
     * 模型名称
     * @var string
     */
    protected $name = 'shop_store';
    protected $type = [
        'create_time' => 'timestamp',
        'update_time' => 'timestamp',
    ];
    protected $json = ['time_week','trade_time_json','area','support_delivery', 'extend_data'];
    protected $jsonAssoc = true;

    /**
     * 搜索器:关键字
     * @param $query
     * @param $value
     * @param $data
     */
    public function searchKeywordAttr($query, $value, $data)
    {
        if ($value != '') {
            $query->where('store_name|mobile|full_address', 'like', '%' . $value . '%');
        }
    }

    /**
     * 搜索器:自提门店门店名称
     * @param $value
     * @param $data
     */
    public function searchStoreNameAttr($query, $value, $data)
    {
        if ($value != '') {
            $query->where("store_name", "like", "%" . $value . "%");
        }
    }

    /**
     * 搜索器:支持的提货类型
     * @param $query
     * @param $value
     * @param $data
     */
    public function searchPickUpTypeAttr($query, $value, $data)
    {
        if (!empty($data['pick_up_type'])) {
            switch ($data['pick_up_type']) {
                case DeliveryDict::LOCAL_DELIVERY:
                    $query->where('support_local_delivery', '=', 1);
                    break;
                case DeliveryDict::STORE:
                    $query->where('support_store', '=', 1);
                    break;
            }
        }
    }

    /**
     * 搜索器:提货点状态
     * @param $query
     * @param $value
     * @param $data
     */
    public function searchStatusAttr($query, $value, $data)
    {
        if ($value != '') {
            $query->where('status', $value);
        }
    }

    /**
     * 创建时间搜索器
     * @param $value
     */
    public function searchCreateTimeAttr(Query $query, $value, $data)
    {
        $start_time = empty($value[ 0 ]) ? 0 : strtotime($value[ 0 ]);
        $end_time = empty($value[ 1 ]) ? 0 : strtotime($value[ 1 ]);
        if ($start_time > 0 && $end_time > 0) {
            $query->whereBetweenTime('create_time', $start_time, $end_time);
        } else if ($start_time > 0 && $end_time == 0) {
            $query->where([ [ 'create_time', '>=', $start_time ] ]);
        } else if ($start_time == 0 && $end_time > 0) {
            $query->where([ [ 'create_time', '<=', $end_time ] ]);
        }
    }

    /**
     * 提货类型
     * @param $value
     * @param $data
     * @return array
     */
    public function getPickUpTypeNameAttr($value, $data)
    {
        $pick_up_type = [];
        if (isset($data[ 'support_local_delivery' ]) && $data[ 'support_local_delivery' ] == 1) {
            $pick_up_type[] = DeliveryStoreDict::getPickUpType(DeliveryDict::LOCAL_DELIVERY);
        }
        if (isset($data[ 'support_store' ]) && $data[ 'support_store' ] == 1) {
            $pick_up_type[] = DeliveryStoreDict::getPickUpType(DeliveryDict::STORE);
        }
        return $pick_up_type;
    }
}
