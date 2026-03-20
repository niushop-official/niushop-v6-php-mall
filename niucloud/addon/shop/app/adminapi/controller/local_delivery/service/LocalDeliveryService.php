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

namespace addon\shop\app\adminapi\controller\local_delivery\service;

use addon\shop\app\dict\local_delivery\LocalDeliveryDict;
use addon\shop\app\service\admin\local_delivery\service\LocalDeliveryServiceService;
use core\base\BaseAdminController;
use core\exception\AdminException;
use think\Response;


/**
 * 配送服务商
 * Class LocalDeliveryService
 * @package addon\shop\app\adminapi\controller\local_delivery\service
 */
class LocalDeliveryService extends BaseAdminController
{
    /**
     * 获取配送服务商列表
     */
    public function lists()
    {
        $res = (new LocalDeliveryServiceService())->getList();
        return success($res);
    }

    /**
     * 设置配置
     * @return Response
     */
    public function setConfig($delivery_type)
    {
        //参数获取
        $delivery_type_list = LocalDeliveryDict::getType();
        if (!array_key_exists($delivery_type, $delivery_type_list)) throw new AdminException('LOCAL_DELIVERY_TYPE_NOT_EXIST');
        //数据验证
        $data = [
            ['is_use', 0]
        ];
        foreach ($delivery_type_list[$delivery_type]['params'] as $k_param => $v_param) {
            $data[] = [$k_param, ''];
        }
        $request_data = $this->request->params($data);
        (new LocalDeliveryServiceService())->setConfig($delivery_type, $request_data);
        return success('SUCCESS');

    }

    /**
     * 获取配置
     * @return Response
     */
    public function getConfig($delivery_type)
    {
        return success((new LocalDeliveryServiceService())->getConfig($delivery_type,true));
    }

    /**
     * 获取已启用配送服务商列表
     * @return Response
     */
    public function getInUseList()
    {
        $data = $this->request->params([
            ['store_id', 0],
        ]);
        return success((new LocalDeliveryServiceService())->getInUseList($data));
    }
}