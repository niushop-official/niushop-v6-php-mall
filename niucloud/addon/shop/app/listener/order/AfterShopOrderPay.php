<?php
declare ( strict_types = 1 );

namespace addon\shop\app\listener\order;

use addon\shop\app\dict\goods\GoodsDict;
use addon\shop\app\dict\order\OrderDeliveryDict;
use addon\shop\app\dict\order\OrderDict;
use addon\shop\app\dict\order\OrderLogDict;
use addon\shop\app\model\goods\Goods;
use addon\shop\app\model\order\OrderGoods;
use addon\shop\app\service\admin\marketing\DiscountService;
use addon\shop\app\service\core\CoreStatService;
use addon\shop\app\service\core\order\CoreInvoiceService;
use addon\shop\app\service\core\order\CoreOrderDeliveryService;
use addon\shop\app\service\core\order\CoreOrderLogService;
use app\dict\member\MemberAccountTypeDict;
use app\model\member\Member;
use app\service\core\member\CoreMemberAccountService;
use app\service\core\member\CoreMemberConfigService;
use app\service\core\notice\NoticeService;
use app\service\core\printer\CorePrinterService;
use think\facade\Log;

class AfterShopOrderPay
{
    public function handle($data)
    {
        Log::write('订单AfterShopOrderPay' . json_encode($data));
        try {
            $order_data = $data[ 'order_data' ];
            //活动或会员购买赠送.....
            //发票改变状态........
            ( new CoreInvoiceService() )->open($order_data[ 'invoice_id' ]);
            //商城专用统计
            CoreStatService::addStat([ 'sale_money' => $order_data[ 'order_money' ] ]);
            //日志
            $main_type = $data[ 'main_type' ] ?? OrderLogDict::MEMBER;
            $main_id = $data[ 'main_id' ] ?? $order_data[ 'member_id' ];
            ( new CoreOrderLogService() )->add([
                'order_id' => $order_data[ 'order_id' ],
                'status' => OrderDict::WAIT_DELIVERY,
                'main_type' => $main_type,
                'main_id' => $main_id,
                'type' => OrderDict::ORDER_PAY_ACTION,
                'content' => ''
            ]);
            //虚拟商品自动发货
            $order_goods_list = ( new OrderGoods() )->where([ 'order_id' => $data[ 'order_id' ] ])->select();
            if (!empty($order_goods_list)) {
                $goods_column = ( new Goods() )->where([ [ 'goods_id', 'in', array_column($order_goods_list->toArray(), 'goods_id') ] ])->column('*', 'goods_id');
                $virtual_order_goods_ids = [];
                foreach ($order_goods_list as $v) {
                    //根据商品配置判断下一步配送操作
                    $temp_goods_type = $v[ 'goods_type' ];
                    //读取虚拟商品配置
                    if ($temp_goods_type == GoodsDict::VIRTUAL) {
                        $temp_goods = $goods_column[ $v[ 'goods_id' ] ] ?? [];
                        if (empty($temp_goods)) continue;
                        //是否自动发货
                        if ($temp_goods[ 'virtual_auto_delivery' ] == 1) {
                            //调用订单项发货
                            $virtual_order_goods_ids[] = $v[ 'order_goods_id' ];
                        }
                    }
                }
                if ($virtual_order_goods_ids) {
                    $delivery_data = [];
                    $delivery_data[ 'main_type' ] = OrderLogDict::SYSTEM;
                    $delivery_data[ 'main_id' ] = 0;
                    $delivery_data[ 'order_id' ] = $order_data[ 'order_id' ];
                    $delivery_data[ 'delivery_type' ] = OrderDeliveryDict::VIRTUAL;
                    $delivery_data[ 'order_goods_ids' ] = $virtual_order_goods_ids;
                    ( new CoreOrderDeliveryService() )->delivery($delivery_data);
                }

            }

            //消息发送
            ( new NoticeService() )->send('shop_order_pay', [ 'order_id' => $order_data[ 'order_id' ] ]);

            ( new DiscountService() )->orderPayAfter($order_data);

            // 分销佣金发放
            $this->distributionCommissionGrant($order_data);

            // 小票打印，订单付款之后
            return ( new CorePrinterService() )->printTicket([
                "type" => "shopGoodsOrder",
                "trigger" => "pay_after",
                'business' => [
                    'order_id' => $order_data[ 'order_id' ]
                ]
            ]);

        } catch (\Exception $e) {
            Log::write('订单AfterShopOrderPay失败' . $e->getMessage() . $e->getFile() . $e->getLine());
        }
    }

    /**
     * 分销佣金发放
     * @param array $order_data
     * @return void
     */
    private function distributionCommissionGrant(array $order_data)
    {
        try {
            $member_id = $order_data['member_id'] ?? 0;
            if ($member_id <= 0) {
                return;
            }

            $member = (new Member())->where([['member_id', '=', $member_id]])->field('pid')->findOrEmpty();
            if ($member->isEmpty() || $member['pid'] <= 0) {
                return;
            }

            $distribution_config = (new CoreMemberConfigService())->getDistributionConfig();
            $rate = intval($distribution_config['rate'] ?? 0);
            if ($rate <= 0) {
                return;
            }

            $pay_money = floatval($order_data['pay_money'] ?? 0);
            if ($pay_money <= 0) {
                return;
            }

            $commission = round($pay_money * $rate / 100, 2);
            if ($commission <= 0) {
                return;
            }

            $member_account_service = new CoreMemberAccountService();
            $memo = '分销佣金：订单金额' . $pay_money . '元 × ' . $rate . '%';
            $member_account_service->addLog(
                $member['pid'],
                MemberAccountTypeDict::BALANCE,
                $commission,
                'distribution_commission',
                $memo,
                $order_data['order_id']
            );

            Log::write('分销佣金发放成功：上级会员ID=' . $member['pid'] . ', 佣金=' . $commission . ', 订单ID=' . $order_data['order_id']);
        } catch (\Exception $e) {
            Log::write('分销佣金发放失败：' . $e->getMessage() . $e->getFile() . $e->getLine());
        }
    }
}
