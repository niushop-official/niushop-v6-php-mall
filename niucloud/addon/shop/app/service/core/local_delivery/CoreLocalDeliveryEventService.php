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

use addon\shop\core\local_delivery\LocalDeliveryLoader;
use core\base\BaseCoreService;
use Exception;
use Psr\Http\Message\ResponseInterface;
use think\Response;

/**
 * 同城配送事件服务层
 * Class CoreLocalDeliveryEventService
 * @package addon\shop\app\service\core\local_delivery
 */
class CoreLocalDeliveryEventService extends BaseCoreService
{
    private $type = '';//配送类型
    private $order_no = '';//配送单号
    private $config = [];//本地配送配置

    public function __construct()
    {
        parent::__construct();
    }


    /**
     * 支付引擎外置触点初始化
     * @param string $type
     * @param string $order_no
     * @param array $config
     * @return $this
     */
    public function init(string $type = '', string $order_no = '', array $config = [])
    {
        $this->type = $type;
        $this->order_no = $order_no;
        $this->config = $config;
        return $this;
    }

    /**
     * 获取实例化应用
     * @param string $action
     * @return LocalDeliveryLoader
     * @throws Exception
     */
    public function app(string $action = 'order')
    {
        $notify_url = (string)url("/api/delivery/notify/$this->type/$this->order_no/$action", [], '', true);//异步回调通知地址
        $this->config['notify_url'] = $notify_url;
        return new LocalDeliveryLoader($this->type, $this->config);
    }

    /**
     * 直接下单
     * @param array $params
     * @return mixed
     * @throws Exception
     */
    public function createOrder(array $params = [])
    {
        return $this->app()->createOrder($params);
    }

    /**
     * 查询运费
     * @param array $params
     * @return mixed
     * @throws Exception
     */
    public function calculate(array $params = [])
    {
        return $this->app()->calculate($params);
    }

    /**
     * 取消订单
     * @param array $params
     * @return mixed
     * @throws Exception
     */
    public function closeOrder(array $params = [])
    {
        return $this->app()->closeOrder($params);
    }

    /**
     * 完成订单
     * @param array $params
     * @return mixed
     * @throws Exception
     */
    public function finishOrder(array $params = [])
    {
        return $this->app()->finishOrder($params);
    }

    /**
     * 查询订单
     * @param array $params
     * @return mixed
     * @throws Exception
     */
    public function queryOrderInfo(array $params = [])
    {
        return $this->app()->queryOrderInfo($params);
    }

    /**
     * 添加门店
     * @param array $params
     * @return mixed
     * @throws Exception
     */
    public function addShop(array $params = [])
    {
        return $this->app()->addShop($params);
    }

    /**
     * 编辑门店
     * @param array $params
     * @return mixed
     * @throws Exception
     */
    public function editShop(array $params = [])
    {
        return $this->app()->editShop($params);
    }

    /**
     * 支付异步通知
     * @param string $action
     * @param array $params
     * @return ResponseInterface|Response
     * @throws Exception
     */
    public function notify(string $action, array $params = [])
    {
        return $this->app()->notify($action, $params);
    }
}
