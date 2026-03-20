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

namespace addon\shop\core\local_delivery;

use core\loader\Storage;

/**
 * Class BaseLocalDelivery
 * @package addon\shop\app\service\core\local_delivery\local_delivery
 */
abstract class BaseLocalDelivery extends Storage
{
    /**
     * 初始化
     * @param array $config
     * @return void
     */
    protected function initialize(array $config = [])
    {

    }

    abstract function createOrder($data);
    abstract function reCreateOrder($data);
    abstract function calculate($data);
    abstract function createOrderAfterCalculate($delivery_third_order_no);
    abstract function queryOrderInfo($data);
    abstract function closeOrder($data);
    abstract function finishOrder($data);
    abstract function getTransporterPosition($data);
    abstract function addShop($data);
    abstract function editShop($data);
    /**
     * 回调通知
     * @param string $action
     * @param callable $params
     * @return mixed
     */
    abstract function notify(string $action, array $params);

}
