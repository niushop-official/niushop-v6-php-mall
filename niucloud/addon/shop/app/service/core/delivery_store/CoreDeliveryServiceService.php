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

namespace addon\shop\app\service\core\delivery_store;

use addon\shop\app\dict\local_delivery\LocalDeliveryDict;
use addon\shop\app\model\delivery\Store;
use addon\shop\app\model\local_delivery\LocalDeliveryService;
use addon\shop\app\service\core\local_delivery\CoreLocalDeliveryEventService;
use addon\shop\app\service\core\local_delivery\CoreLocalDeliveryService;
use core\base\BaseCoreService;
use core\exception\CommonException;
use think\facade\Log;

/**
 * 配送服务商服务层
 * Class CoreDeliveryServiceService
 * @package addon\shop\app\service\core\delivery_store
 */
class CoreDeliveryServiceService extends BaseCoreService
{
    protected $local_delivery_event;
    public function __construct()
    {
        parent::__construct();
        $this->model = new Store();
        $this->local_delivery_event = new CoreLocalDeliveryEventService();
    }

    /**
     * 获取配送门店信息
     * @param int $id
     * @return array
     */
    public function getInfo(int $id)
    {
        return $this->model->where([ [ 'store_id', '=', $id ] ])->findOrEmpty()->toArray();
    }

    /**
     * 获取配送服务商列表
     * @return array
     */
    public function deliveryServiceList(int $id)
    {
        $info = $this->getInfo($id);
        if (empty($info)) throw new CommonException('DELIVERY_STORE_NOT_EXIST');
        $list = LocalDeliveryDict::getType();
        $keys = (new LocalDeliveryService())->where([['key', 'in', array_keys($list)], ['is_use', '=', 1]])->column('key');
        $support_delivery_array = $info['support_delivery'] ?? [];
        foreach ($list as $k => &$v) {
            if ($v['is_merchant']) {
                $v['open_status'] = LocalDeliveryDict::OPEN_PASS;
                $v['business'] = '';
                $v['edit_status'] = '';
                $v['reason'] = '';
            } else {
                if (in_array($k, $keys)) {
                    $v['open_status'] = empty($support_delivery_array[$k]) ? '' : $support_delivery_array[$k]['open_status'];
                    $v['business'] = empty($support_delivery_array[$k]) ? '' : $support_delivery_array[$k]['business'];
                    $v['edit_status'] = empty($support_delivery_array[$k]) ? '' : $support_delivery_array[$k]['edit_status'];
                    $v['reason'] = empty($support_delivery_array[$k]) ? '' : $support_delivery_array[$k]['reason'];
                } else {
                    unset($list[$k]);
                }
            }
        }
        return array_values($list);
    }

    /**
     * 配送门店开通配送服务
     * @param int $id
     * @param array $data
     * @return true
     * @throws \Exception
     */
    public function deliveryServiceOpen(int $id, array $data)
    {
        $info = $this->getInfo($id);
        if (empty($info)) throw new CommonException('DELIVERY_STORE_NOT_EXIST');
        $delivery_type = $data['delivery_type'];
        $config = (new CoreLocalDeliveryService())->getConfig($delivery_type);
        $request_data = [
            'delivery_type' => $delivery_type,
            'business' => $data['business'],
            'delivery_store' => $info,
        ];
        $response_data = $this->local_delivery_event->init(type: $delivery_type, config: $config)->addShop($request_data);
        if (!empty($response_data)) {
            $update_data['support_delivery'] = array_merge($info['support_delivery'] ?? [], $response_data['support_delivery']);
            $update_data['extend_data'] = array_merge($info['extend_data'] ?? [], $data['extend_data']);
            $this->model->where([ [ 'store_id', '=', $id ] ])->update($update_data);
        }
        return true;
    }

    /**
     * 配送门店品类修改
     * @param int $id
     * @param array $data
     * @return true
     * @throws \Exception
     */
    public function deliveryServiceEdit(int $id, array $data)
    {
        $info = $this->getInfo($id);
        if (empty($info)) throw new CommonException('DELIVERY_STORE_NOT_EXIST');
        $delivery_type = $data['delivery_type'];
        $config = (new CoreLocalDeliveryService())->getConfig($delivery_type);
        $request_data = [
            'delivery_type' => $delivery_type,
            'business' => $data['business'],
            'delivery_store' => $info,
        ];
        $response_data = $this->local_delivery_event->init(type: $delivery_type, config: $config)->editShop($request_data);
        if (!empty($response_data)) {
            $update_data['support_delivery'] = $response_data['support_delivery'];
            $this->model->where([ [ 'store_id', '=', $id ] ])->update($update_data);
        }
        return true;
    }

    /**
     * 配送门店信息批量修改
     * @return true
     */
    public function batchDeliveryShopEdit(int $id)
    {
        try {
            $info = $this->getInfo($id);
            if (empty($info)) throw new CommonException('DELIVERY_STORE_NOT_EXIST');
            $support_delivery_array = $info['support_delivery'];
            $delivery_type_list = array_keys(LocalDeliveryDict::getType());
            foreach ($delivery_type_list as $delivery_type) {
                if ($delivery_type != LocalDeliveryDict::MERCHANT && !empty($support_delivery_array[$delivery_type]) && $support_delivery_array[$delivery_type]['open_status'] == LocalDeliveryDict::OPEN_PASS) {
                    $config = (new CoreLocalDeliveryService())->getConfig($delivery_type);
                    //三方配送更新门店
                    $request_data = [
                        'delivery_type' => $delivery_type,
                        'business' => $support_delivery_array[$delivery_type]['business'],
                        'delivery_store' => $info,
                    ];
                    $response_data = $this->local_delivery_event->init(type: $delivery_type, config: $config)->editShop($request_data);
                    if (!empty($response_data)) {
                        $support_delivery_array[$delivery_type]['edit_status'] = $response_data['support_delivery'][$delivery_type]['edit_status'];
                        $support_delivery_array[$delivery_type]['reason'] = $response_data['support_delivery'][$delivery_type]['reason'];
                    }
                }
            }
            $update_data['support_delivery'] = $support_delivery_array;
            $this->model->where([['store_id', '=', $id]])->update($update_data);
        } catch (\Exception $e) {
            Log::write('配送门店信息批量修改：'.$e->getMessage());
        }
        return true;
    }
}
