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

namespace addon\shop\app\model\order;

use addon\shop\app\dict\delivery\DeliveryLocalDict;
use addon\shop\app\model\delivery\Company;
use addon\shop\app\model\local_delivery\LocalDeliveryOrder;
use core\base\BaseModel;

/**
 * 发货模型
 */
class OrderDelivery extends BaseModel
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
    protected $name = 'shop_order_delivery';

    //类型
    protected $type = [

    ];

    public function company()
    {
        return $this->hasOne(Company::class, 'company_id', 'express_company_id');
    }

    public function getExpressCompanyNameAttr($value, $data)
    {
        return (new Company())->find($data['express_company_id'])->company_name ?? '';
    }

    public function orderGoods()
    {
        return $this->hasMany(OrderGoods::class, 'delivery_id', 'id');
    }
    public function getThirdDeliveryNameAttr($value, $data)
    {
        return DeliveryLocalDict::getType($data['third_delivery'])['name']  ?? "";
    }

    public function localDeliveryOrder()
    {
        return $this->hasOne(LocalDeliveryOrder::class, 'delivery_no', 'local_delivery_order_id')->append(['status_name','delivery_service_name'])->bind(['local_delivery_status' => 'status','local_delivery_status_name' => 'status_name','delivery_service','delivery_service_name','rider_name','rider_mobile']);
    }
}
