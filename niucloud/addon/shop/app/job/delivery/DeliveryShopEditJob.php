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

namespace addon\shop\app\job\delivery;

use addon\shop\app\service\core\delivery_store\CoreDeliveryServiceService;
use core\base\BaseJob;

/**
 * 门店批量修改
 */
class DeliveryShopEditJob extends BaseJob
{
    public function doJob($id)
    {
        (new CoreDeliveryServiceService())->batchDeliveryShopEdit($id);
        return true;
    }
}
