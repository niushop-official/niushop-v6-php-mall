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

namespace addon\shop\app\service\admin\local_delivery\config;

use addon\shop\app\model\local_delivery\Local;
use addon\shop\app\service\admin\delivery_store\DeliveryStoreService;
use core\base\BaseAdminService;

/**
 * 同城配送服务层
 * Class LocalService
 * @package addon\shop\app\service\admin\delivery
 */
class LocalService extends BaseAdminService
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 设置同城配送
     * @param array $data
     * @return mixed
     */
    public function setLocal(array $data)
    {
        (new Local())->where([['local_id', '>', 0]])->delete();

        $create_res = (new Local())->create([
            'fee_type' => $data['fee_type'],
            'base_dist' => $data['base_dist'],
            'base_price' => $data['base_price'],
            'grad_dist' => $data['grad_dist'],
            'grad_price' => $data['grad_price'],
            'weight_start' => $data['weight_start'],
            'weight_unit' => $data['weight_unit'],
            'weight_price' => $data['weight_price'],

            'time_is_open' => $data['time_is_open'],
            'time_type' => $data['time_type'],
            'time_week' => $data['time_week'],
            'time_interval' => $data['time_interval'],
            'advance_day' => 0,
            'most_day' => 7,
            'start_time' => $data['start_time'],
            'end_time' => $data['end_time'],
            'delivery_time' => $data['delivery_time'],
            'area' => []
        ]);

        try {
            if (!empty($data['area_data'])) {
                foreach ($data['area_data'] as $store_id => $area) {
                    (new DeliveryStoreService())->editArea($store_id, $area);
                }
            }
        } catch (\Exception $e) {
        }
        return $create_res->local_id;
    }

    /**
     * 获取同城配送设置
     * @return array
     */
    public function getLocal($field = 'fee_type,base_dist,base_price,grad_dist,grad_price,weight_start,weight_unit,weight_price')
    {
        return (new Local())->where([['local_id', '>', 0]])->field($field)->findOrEmpty()->toArray();
    }
}
