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

namespace addon\shop\app\service\api\delivery;

use addon\shop\app\service\core\local_delivery\CoreLocalDeliveryOrderService;
use core\base\BaseApiService;

/**
 * 配送订单服务层
 * Class OrderService
 * @package addon\shop\app\service\api\delivery
 */
class OrderService extends BaseApiService
{
    public function __construct()
    {
        parent::__construct();

    }

    /**
     *  获取同城配送订单轨迹
     * @param $data
     * @return array|array[]
     */
    public function getTrackOfLocalDeliveryOrder($data){
        $data['member_id'] = $this->member_id;
        return (new CoreLocalDeliveryOrderService())->getTrackInfo($data);
    }
}
