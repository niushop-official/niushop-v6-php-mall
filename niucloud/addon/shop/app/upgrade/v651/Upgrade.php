<?php

namespace addon\shop\app\upgrade\v651;


use addon\shop\app\model\order\OrderGoods;
use addon\shop\app\model\order\OrderRefund;

class Upgrade
{

    public function handle()
    {
        $this->handleOrderRefundData();
    }

    /**
     * 处理门店数据
     * @return true
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function handleOrderRefundData()
    {
        $order_ids = (new OrderGoods())->where("order_goods_id",">",0)->field('order_goods_id,order_id')->select();
        foreach ($order_ids as $datum) {
            (new OrderRefund())->where('order_goods_id',$datum['order_goods_id'])->update([
                'order_id'=>$datum['order_id'],
            ]);
        }
    }
}
