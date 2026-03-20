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

use core\base\BaseModel;


/**
 * 同城配送服务商模型
 * Class LocalDeliveryService
 * @package addon\shop\app\model\local_delivery
 */
class LocalDeliveryService extends BaseModel
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
    protected $name = 'shop_delivery_local_delivery_service';

    // 设置json类型字段
    protected $json = [ 'config' ];

    // 设置JSON数据返回数组
    protected $jsonAssoc = true;

    /**
     * 搜索器:三方配送名称
     * @param $query
     * @param $value
     */
    public function searchNameAttr($query, $value)
    {
        if ($value != '') {
            $query->where('name', 'like', '%' . $value . '%');
        }
    }
}
