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

use addon\shop\app\model\shop_delivery\ShopDeliveryOrderLog;
use core\base\BaseCoreService;

/**
 * 同城配送订单日志服务层
 * Class CoreShopDeliveryOrderLogService
 * @package addon\shop\app\service\core\shop_delivery
 */
class CoreShopDeliveryOrderLogService extends BaseCoreService
{
    public function __construct()
    {
        parent::__construct();
        $this->model = new ShopDeliveryOrderLog();
    }

    /**
     * 添加配送订单日志
     * @param $params
     * @return ShopDeliveryOrderLog|\think\Model
     */
    public function addLog($params)
    {
        $data = [
            'order_id' => $params['order_id'],
            'main_id' => $params['main_id'] ?? 0,
            'main_type' => $params['main_type'],
            'main_name' => $params['main_name'] ?? '',
            'operate' => $params['operate'],
            'operate_desc' => $params['operate_desc'],
            'status' => $params['status'],
            'remark' => $params['remark'] ?? '',
        ];
//        dd($data);
        return $this->model->create($data);
    }

}
