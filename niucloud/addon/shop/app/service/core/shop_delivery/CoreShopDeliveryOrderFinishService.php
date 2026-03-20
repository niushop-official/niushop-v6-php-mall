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

use addon\shop\app\dict\local_delivery\OrderLogDict;
use addon\shop\app\dict\shop_delivery\ShopDeliveryDict;
use addon\shop\app\dict\shop_delivery\ShopDeliveryStatusDict;
use addon\shop\app\model\shop_delivery\ShopDeliveryOrder;
use addon\shop\app\service\core\local_delivery\CoreLocalDeliveryOrderNotifyService;
use core\base\BaseCoreService;
use core\exception\CommonException;

/**
 * 同城配送服务层
 * Class CoreShopDeliveryOrderService
 * @package app\service\core\delivery
 */
class CoreShopDeliveryOrderFinishService extends BaseCoreService
{
    public function __construct()
    {
        parent::__construct();
        $this->model = new ShopDeliveryOrder();
    }

    /**
     * 完成订单
     * @param array $data
     * @return bool
     */
    public function finishOrder(array $data)
    {
        $id = $data['id'] ?? 0;
        $out_delivery_no = $data['out_delivery_no'] ?? '';
        if (empty($id) && empty($out_delivery_no)) {
            throw new CommonException('DELIVERY_ORDER_NOT_EXIT');
        }
        $condition = [];
        if (!empty($id)) {
            $condition[] = ['id', '=', $id];
        } else {
            $condition[] = ['out_delivery_no', '=', $out_delivery_no];
        }
        $info = $this->model->where($condition)->findOrEmpty();
        if ($info->isEmpty()) throw new CommonException('DELIVERY_ORDER_NOT_EXIT');
        $res = $info->save([
            'status' => ShopDeliveryStatusDict::COMPLETED,
            'finish_time' => time()
        ]);
        if ($res) {
            // 订单状态回调
            $notify_data = [
                'status' => ShopDeliveryStatusDict::COMPLETED,
                'finish_time' => time(),
                'out_delivery_no' => $data['out_delivery_no'] ?? $info['out_delivery_no'],
                'main_id' => $data['main_id'] ?? 0,
                'main_type' => $data['main_type'] ?? '',
                'main_name' => $data['main_name'] ?? '',
            ];
            (new CoreLocalDeliveryOrderNotifyService())->notify(ShopDeliveryDict::MERCHANT, $info['out_delivery_no'], 'order', $notify_data);
            //添加配送订单日志
            $operate = OrderLogDict::getOperate(ShopDeliveryStatusDict::convertStatus(ShopDeliveryStatusDict::COMPLETED));
            (new CoreShopDeliveryOrderLogService())->addLog([
                'order_id' => $info['id'],
                'main_id' => $data['main_id'] ?? 0,
                'main_type' => OrderLogDict::STORE,
                'main_name' => $data['main_name'] ?? '',
                'operate' => $operate['operate'],
                'operate_desc' => $operate['operate_desc'],
                'status' => ShopDeliveryStatusDict::COMPLETED,
                'remark' => $operate['operate_desc'],
            ]);
        }
        return $res;
    }
}
