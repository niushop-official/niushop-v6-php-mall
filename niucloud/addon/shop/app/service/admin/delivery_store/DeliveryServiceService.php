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

namespace addon\shop\app\service\admin\delivery_store;

use addon\shop\app\service\core\delivery_store\CoreDeliveryServiceService;
use core\base\BaseAdminService;

/**
 * 配送服务商服务层
 * Class DeliveryServiceService
 * @package addon\shop\app\service\admin\delivery_store
 */
class DeliveryServiceService extends BaseAdminService
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 获取配送服务商列表
     * @return array
     */
    public function deliveryServiceList(int $id)
    {
        return (new CoreDeliveryServiceService())->deliveryServiceList($id);
    }

    /**
     * 配送门店开通配送服务
     * @return true
     */
    public function deliveryServiceOpen(int $id, array $data)
    {
        return (new CoreDeliveryServiceService())->deliveryServiceOpen($id, $data);
    }

    /**
     * 配送门店品类修改
     * @return true
     */
    public function deliveryServiceEdit(int $id, array $data)
    {
        return (new CoreDeliveryServiceService())->deliveryServiceEdit($id, $data);
    }
}
