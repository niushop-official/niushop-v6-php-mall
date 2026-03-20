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

namespace addon\shop\app\service\core\local_delivery;

use addon\shop\app\model\local_delivery\LocalDeliveryOrder;
use core\base\BaseCoreService;
use core\exception\CommonException;
use think\facade\Log;

/**
 * 同城配送服务层
 * Class CoreExpressService
 * @package addon\shop\app\service\admin\delivery
 */
class CoreLocalDeliveryOrderNotifyService extends BaseCoreService
{
    protected $local_delivery_event;
    public function __construct()
    {
        parent::__construct();
        $this->model = new LocalDeliveryOrder();
        $this->local_delivery_event = new CoreLocalDeliveryEventService();
    }

    public function notify($type, $delivery_no, $action, $params = [])
    {
        Log::write('同城配送订单状态回调参数：' . json_encode($params));
        try {
            return $this->local_delivery_event->init($type, $delivery_no)->notify($action, $params);
        } catch ( CommonException $e ) {
            Log::write('同城配送订单状态回调失败：'.json_encode(func_get_args()) .$e->getMessage(), 'error');
            return false;
        }
    }

}
