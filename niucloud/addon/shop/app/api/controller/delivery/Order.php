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

namespace addon\shop\app\api\controller\delivery;

use addon\shop\app\service\api\delivery\OrderService;
use addon\shop\app\service\core\local_delivery\CoreLocalDeliveryOrderNotifyService;
use core\base\BaseApiController;
use think\Response;

class Order extends BaseApiController
{
    /**
     * 订单状态回调
     * @param $type
     * @param $order_no
     * @param $action
     * @return Response
     */
    public function notify($type, $order_no, $action)
    {
        $params = $this->request->all();
        $params['type'] = $type;
        $params['order_no'] = $order_no;
        return (new CoreLocalDeliveryOrderNotifyService())->notify($type, $order_no, $action, $params);
    }

    /**
     * 获取配送轨迹
     * @return Response
     */
    public function getTrackOfLocalDeliveryOrder()
    {
        $data = $this->request->params([
            [ 'trade_no', '' ],
        ]);
        return success(( new OrderService() )->getTrackOfLocalDeliveryOrder($data));
    }
}
