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

namespace addon\shop\app\service\core\shop_delivery;

use addon\shop\app\model\shop_delivery\ShopDeliveryOrder;
use core\base\BaseCoreService;

/**
 * 同城配送服务层
 * Class CoreShopDeliveryOrderService
 * @package addon\shop\app\service\core\shop_delivery
 */
class CoreShopDeliveryOrderService extends BaseCoreService
{
    public function __construct()
    {
        parent::__construct();
        $this->model = new ShopDeliveryOrder();
    }

    public function getOrderInfo($data)
    {
        return $this->model->where($data)->findOrEmpty()->toArray();
    }

}
