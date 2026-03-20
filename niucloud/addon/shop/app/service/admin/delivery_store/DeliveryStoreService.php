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

use addon\shop\app\model\delivery\Store;
use addon\shop\app\dict\delivery\DeliveryDict;
use addon\shop\app\dict\delivery_store\DeliveryStoreDict;
use addon\shop\app\job\delivery\DeliveryShopEditJob;
use addon\shop\app\service\admin\local_delivery\config\LocalService;
use addon\shop\app\service\core\delivery\CoreDeliveryService;
use core\base\BaseAdminService;
use core\exception\AdminException;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;

/**
 * 提货点服务层
 * Class DeliveryStoreService
 * @package app\service\admin\delivery_store
 */
class DeliveryStoreService extends BaseAdminService
{
    public function __construct()
    {
        parent::__construct();
        $this->model = new Store();
    }

    /**
     * 获取提货点列表
     * @param array $where
     * @return array
     * @throws DbException
     */
    public function getPage(array $where = [])
    {
        $field = 'store_id,store_no,store_name,store_logo,contact_name,store_mobile,province_id,city_id,district_id,address,full_address,longitude,latitude,trade_time,time_week,trade_time_json,time_interval,support_local_delivery,support_store,status,create_time';
        $order = 'create_time desc';

        $search_model = $this->model->where([ [ 'store_id', '>', 0 ] ])->withSearch(['store_name', 'pick_up_type', 'create_time'], $where)->field($field)->append([ 'pick_up_type_name' ])->order($order);
        return $this->pageQuery($search_model);
    }

    /**
     * 获取提货点信息
     * @param int $id
     * @return array
     */
    public function getInfo(int $id)
    {
        $field = 'store_id,store_no,store_name,store_logo,contact_name,store_mobile,province_id,city_id,district_id,address,full_address,longitude,latitude,trade_time,time_week,trade_time_json,time_interval,create_time,support_local_delivery,support_store,status,area,time_is_open';
        $info = $this->model->field($field)->where([ [ 'store_id', '=', $id ] ])->append([ 'pick_up_type_name' ])->findOrEmpty()->toArray();
        $pick_up_type = [];
        if ($info[ 'support_local_delivery' ] == 1) {
            $pick_up_type[] = DeliveryDict::LOCAL_DELIVERY;
        }
        if ($info[ 'support_store' ] == 1) {
            $pick_up_type[] = DeliveryDict::STORE;
        }
        $info[ 'pick_up_type' ] = $pick_up_type;
        if (empty($info[ 'time_interval' ])) $info[ 'time_interval' ] = DeliveryDict::TIME_INTERVAL_30;
        return $info;
    }

    public function getInitInfo()
    {
        return [
            'week_list' => DeliveryDict::getWeekList(),
            'time_interval_list' => DeliveryDict::getTimeIntervalList(),
            'local_delivery_config' => (new LocalService())->getLocal()
        ];
    }

    /**
     * 添加提货点
     * @param array $data
     * @return mixed
     * @throws \Exception
     */
    public function add(array $data)
    {
        $data[ 'create_time' ] = time();
        $data[ 'update_time' ] = time();

        $store_no = create_no();//生成门店编号
        $data[ 'store_no' ] = $store_no;
        if (!empty($data['pick_up_type'])) {
            if (in_array(DeliveryDict::LOCAL_DELIVERY, $data[ 'pick_up_type' ])) {
                $data[ 'support_local_delivery' ] = 1;
            } else {
                $data[ 'support_local_delivery' ] = 0;
            }
            if (in_array(DeliveryDict::STORE, $data[ 'pick_up_type' ])) {
                $data[ 'support_store' ] = 1;
            } else {
                $data[ 'support_store' ] = 0;
            }
        } else {
            $data[ 'support_local_delivery' ] = 0;
            $data[ 'support_store' ] = 0;
        }
        unset($data['pick_up_type']);
        $res = $this->model->create($data);
        return $res->store_id;
    }

    /**
     * 提货点编辑
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function edit(int $id, array $data)
    {
        $data[ 'update_time' ] = time();
        $check = $this->storeCheck($id);
        $local_delivery_store_count = $check['local_delivery_store_count'];
        $current_local_delivery_store_count = $check['current_local_delivery_store_count'];
        $local_delivery_config = $check['local_delivery_config'];
        $pick_up_store_count = $check['pick_up_store_count'];
        $current_pick_up_store_count = $check['current_pick_up_store_count'];
        $pick_up_config = $check['pick_up_config'];

        if ($local_delivery_store_count == 1 && $current_local_delivery_store_count == 1 && !in_array(DeliveryDict::LOCAL_DELIVERY, $data[ 'pick_up_type' ]) && $local_delivery_config['status'] == 1) {
            throw new AdminException('LOCAL_DELIVERY_OPEN_AT_LEAST_ONE_LOCAL_DELIVERY_STORE');//同城配送已开启，至少需保留一个同城配送门店
        }
        if ($pick_up_store_count == 1 && $current_pick_up_store_count == 1 && !in_array(DeliveryDict::STORE, $data[ 'pick_up_type' ]) && $pick_up_config['status'] == 1) {
            throw new AdminException('STORE_OPEN_AT_LEAST_ONE_PICK_UP_STORE');//门店自提已开启，至少需保留一个自提门店
        }
        if (!empty($data['pick_up_type'])) {
            if (in_array(DeliveryDict::LOCAL_DELIVERY, $data[ 'pick_up_type' ])) {
                $data[ 'support_local_delivery' ] = 1;
            } else {
                $data[ 'support_local_delivery' ] = 0;
            }
            if (in_array(DeliveryDict::STORE, $data[ 'pick_up_type' ])) {
                $data[ 'support_store' ] = 1;
            } else {
                $data[ 'support_store' ] = 0;
            }
        } else {
            $data[ 'support_local_delivery' ] = 0;
            $data[ 'support_store' ] = 0;
        }
        unset($data['pick_up_type']);
        $this->model->where([ [ 'store_id', '=', $id ] ])->update($data);
        //批量更新服务商门店信息
        DeliveryShopEditJob::dispatch([ 'id' => $id ]);
        return true;
    }

    /**
     * 删除提货点
     * @param int $id
     * @return bool
     */
    public function del(int $id)
    {
        $check = $this->storeCheck($id);
        $local_delivery_store_count = $check['local_delivery_store_count'];
        $current_local_delivery_store_count = $check['current_local_delivery_store_count'];
        $local_delivery_config = $check['local_delivery_config'];
        $pick_up_store_count = $check['pick_up_store_count'];
        $current_pick_up_store_count = $check['current_pick_up_store_count'];
        $pick_up_config = $check['pick_up_config'];

        if ($local_delivery_store_count == 1 && $current_local_delivery_store_count == 1 && $local_delivery_config['status'] == 1) {
            throw new AdminException('LOCAL_DELIVERY_OPEN_AT_LEAST_ONE_LOCAL_DELIVERY_STORE');//同城配送已开启，至少需保留一个同城配送门店
        }
        if ($pick_up_store_count == 1 && $current_pick_up_store_count == 1 && $pick_up_config['status'] == 1) {
            throw new AdminException('STORE_OPEN_AT_LEAST_ONE_PICK_UP_STORE');//门店自提已开启，至少需保留一个自提门店
        }

        $res = $this->model->where([ [ 'store_id', '=', $id ] ])->delete();
        return $res;
    }

    /**
     * 获取提货点列表
     * @param array $where
     * @return array
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function getList(array $where = [])
    {
        $field = 'store_id,store_name,contact_name,store_mobile,province_id,city_id,district_id,address,full_address,longitude,latitude,area,trade_time,create_time';
        $order = 'create_time desc';
        return $this->model->where([ [ 'status', '=', 1 ] ])->withSearch(['keyword', 'pick_up_type'], $where)->field($field)->order($order)->select()->toArray();
    }

     /**
     * 获取提货点提货类型
     * @return array
     */
    public function getPickUpType()
    {
        return DeliveryStoreDict::getPickUpType();
    }

     /**
     * 修改提货点状态
     * @param array $data
     * @return bool
     */
    public function modifyStatus(array $data)
    {
        $this->model->where([ [ 'store_id', '=', $data[ 'store_id' ] ] ])->update([ 'status' => $data[ 'status' ] ]);
        return true;
    }

    /**
     * 提货点检测
     * @param int $id
     * @return array
     * @throws DbException
     */
    public function storeCheck(int $id)
    {
        //同城配送门店检测
        $local_delivery_store_count = $this->model->withSearch([ 'pick_up_type' ], [ 'pick_up_type' => DeliveryDict::LOCAL_DELIVERY ])->count();
        $current_local_delivery_store_count = $this->model->where([ [ 'store_id', '=', $id ] ])->withSearch([ 'pick_up_type' ], [ 'pick_up_type' => DeliveryDict::LOCAL_DELIVERY ])->count();
        $local_delivery_config = (new CoreDeliveryService())->getDeliveryConfig()['local_delivery'];

        //自提门店检测
        $pick_up_store_count = $this->model->withSearch([ 'pick_up_type' ], [ 'pick_up_type' => DeliveryDict::STORE ])->count();
        $current_pick_up_store_count = $this->model->where([ [ 'store_id', '=', $id ] ])->withSearch([ 'pick_up_type' ], [ 'pick_up_type' => DeliveryDict::STORE ])->count();
        $pick_up_config = (new CoreDeliveryService())->getDeliveryConfig()['store'];
        return [
            'local_delivery_store_count' => $local_delivery_store_count,
            'current_local_delivery_store_count' => $current_local_delivery_store_count,
            'local_delivery_config' => $local_delivery_config,
            'pick_up_store_count' => $pick_up_store_count,
            'current_pick_up_store_count' => $current_pick_up_store_count,
            'pick_up_config' => $pick_up_config,
        ];
    }

    /**
     * 修改提货点配送区域
     * @param int $id
     * @param array $area_data
     * @return bool
     */
    public function editArea(int $id, array $area_data)
    {
        $this->model->where([[ 'store_id', '=', $id ]])->update(['area' => $area_data]);
        return true;
    }

}
