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

namespace addon\shop\app\service\admin\local_delivery\service;

use addon\shop\app\dict\local_delivery\LocalDeliveryDict;
use addon\shop\app\model\delivery\Store;
use addon\shop\app\model\local_delivery\LocalDeliveryService;
use app\dict\common\CommonDict;
use core\base\BaseAdminService;
use core\exception\AdminException;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;

/**
 * 配送服务商服务层
 * Class LocalDeliveryServiceService
 * @package app\service\admin\local_delivery\site\service
 */
class LocalDeliveryServiceService extends BaseAdminService
{

    public function __construct()
    {
        parent::__construct();
        $this->model = new LocalDeliveryService();
    }

    /**
     * 获取配送服务商列表
     */
    public function getList()
    {
        $delivery_type_list = LocalDeliveryDict::getType();
        $list = [];
        foreach($delivery_type_list as $k => $v)
        {
            $info = $this->getConfig($k);
            $data = [];
            $data['delivery_type'] = $k;
            $data['name'] = $v['name'];
            $data['is_merchant'] = $v['is_merchant'];
            $data['is_use'] = $info['is_use'];
            foreach ($v['params'] as $k_param => $v_param)
            {
                $data['params'][$k_param] = [
                    'name' => $v_param,
                    'value' => $info ? $info['config'][$k_param] ?? '' : ''
                ];
            }
            $data['component'] = $v['component'] ?? '';
            $list[] = $data;
        }
        return $list;
    }

    /**
     * 设置服务商配置
     * @param $delivery_type
     * @param $data
     * @return true
     */
    public function setConfig($delivery_type, $data)
    {
        $delivery_type_list = LocalDeliveryDict::getType();
        if(!array_key_exists($delivery_type, $delivery_type_list)) throw new AdminException('LOCAL_DELIVERY_TYPE_NOT_EXIST');
        $db_config = $this->getConfig($delivery_type);
        $config = [];
        foreach ($delivery_type_list[$delivery_type]['params'] as $k_param => $v_param)
        {
            if ($data[$k_param]  == CommonDict::ENCRYPT_STR){
                $config[$k_param] = $db_config['params'][$k_param]['value'];
            }else{
                $config[$k_param] = $data[$k_param] ?? '';
            }

        }
        $third_delivery = $this->model->where([['key', '=', $delivery_type]])->findOrEmpty();
        if ($third_delivery->isEmpty()) {
            $this->model->create([
                'key' => $delivery_type,
                'name' => $delivery_type_list[$delivery_type]['name'],
                'config' => $config,
                'is_use' => $data['is_use'] ?? 0,
            ]);
        } else {
            $third_delivery->save([
                'config' => $config,
                'is_use' => $data['is_use'] ?? 0,
            ]);
        }
        return true;
    }

    /**
     * 获取服务商配置
     * @return array
     */
    public function getConfig($delivery_type,$is_encrypt = false)
    {
        $delivery_type_list = LocalDeliveryDict::getType();
        if(!array_key_exists($delivery_type, $delivery_type_list)) throw new AdminException('LOCAL_DELIVERY_TYPE_NOT_EXIST');
        $info = $this->model->where([['key', '=', $delivery_type]])->findOrEmpty()->toArray();
        $data = [
            'delivery_type' => $delivery_type,
            'name' => $delivery_type_list[$delivery_type]['name'],
            'is_merchant' => $delivery_type_list[$delivery_type]['is_merchant'],
            'is_use' => $info ? $info['is_use'] : ($delivery_type_list[$delivery_type]['is_merchant'] ? 1 : 0)
        ];
        foreach ($delivery_type_list[$delivery_type]['params'] as $k_param => $v_param)
        {
            $data['params'][$k_param] = [
                'name' => $v_param,
                'value' => $info ? ($is_encrypt ? CommonDict::ENCRYPT_STR :$info['config'][$k_param]) ?? '' : ''
            ];
        }
        return $data;
    }

    /**
     * 获取已启用配送服务商列表
     * @param array $data
     * @return array
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function getInUseList(array $data)
    {
        $support_delivery_list = (new Store())->where([['store_id', '=', $data['store_id']], ['status', '=', 1]])->value('support_delivery') ?? [];
        $list = $this->model->field('key,name')->where([['is_use', '=', 1]])->select()->toArray();
        foreach ($list as &$v) {
            if (in_array($v['key'], array_keys($support_delivery_list))) {
                $v['open_status'] = $support_delivery_list[$v['key']]['open_status'];
            } else {
                $v['open_status'] = LocalDeliveryDict::OPEN_REFUND;
            }
        }
        return $list;
    }
}
