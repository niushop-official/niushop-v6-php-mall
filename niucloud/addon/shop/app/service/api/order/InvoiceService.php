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

namespace addon\shop\app\service\api\order;

use addon\shop\app\dict\order\InvoiceDict;
use addon\shop\app\model\order\Invoice;
use addon\shop\app\service\core\order\CoreInvoiceService;
use addon\shop\app\service\core\order\CoreOrderService;
use core\base\BaseApiService;
use core\exception\ApiException;
use think\facade\Db;

/**
 * 发票
 */
class InvoiceService extends BaseApiService
{
    public function __construct()
    {
        parent::__construct();
        $this->model = new Invoice();
    }

    public function pages($data)
    {
        $where[] = ['member_id', '=', $this->member_id];
        if ($data['status'] != '') {
            if ($data['status'] == InvoiceDict::WAIT_INVOICE) {//未开票为待开票和待审核
                $where[] = ['is_invoice', 'in', [InvoiceDict::WAIT_AUDIT, InvoiceDict::WAIT_INVOICE]];
            } else {
                $where[] = ['is_invoice', '=', $data['status']];
            }
        }
        $query = $this->model->field('id,member_id,header_type,header_name,type,name,tax_number,mobile,email,telephone,address,bank_name,bank_card_number,money,is_invoice,invoice_number,invoice_voucher,remark,create_time,invoice_time,pay_voucher')
            ->where($where)
            ->with([
                'member' => function ($q) {
                    $q->field('member_id,nickname,headimg');
                }
            ])
            ->append(['header_type_name', 'type_name', 'invoice_type_name', 'pay_voucher_thumb_mid', 'pay_voucher_thumb_small', 'pay_voucher_thumb_big', 'invoice_voucher_thumb_mid', 'invoice_voucher_thumb_small', 'invoice_voucher_thumb_big'])
            ->order('create_time desc');
        return $this->pageQuery($query);
    }

    public function detail($id)
    {
        $where[] = ['member_id', '=', $this->member_id];
        $where[] = ['id', '=', $id];
        $info = $this->model->field('id,member_id,header_type,header_name,type,name,tax_number,mobile,email,telephone,address,bank_name,bank_card_number,money,is_invoice,invoice_number,invoice_voucher,remark,create_time,invoice_time,pay_voucher')->where($where)->with(['member' => function ($q) {
            $q->field('member_id,nickname,headimg');
        }])->with([
            'order_list' => function ($q) {
                $q->field('invoice_id,order_id,order_no,order_money')->with([
                    'order_goods' => function ($query) {
                        $query->field('extend,order_goods_id, order_id, member_id, goods_id, sku_id, goods_name, sku_name, goods_image, sku_image, price, num, goods_money, is_enable_refund, delivery_id,is_enable_refund, status, is_gift')
                            ->with([
                                'order_delivery' => function ($query) {
                                    $query->field('id, express_company_id, express_number')->with('company');
                                }
                            ])
                            ->append(['goods_image_thumb_small']);
                    },
                ]);
            }
        ])
            ->append(['header_type_name', 'type_name', 'invoice_type_name', 'pay_voucher_thumb_mid', 'pay_voucher_thumb_small', 'pay_voucher_thumb_big', 'invoice_voucher_thumb_mid', 'invoice_voucher_thumb_small', 'invoice_voucher_thumb_big'])
            ->findOrEmpty();
        if ($info->isEmpty()) {
            throw new ApiException('INVOICE_NOT_EXIST');
        }
        return $info->toArray();
    }

    /**
     * 用户申请补开发票
     * @param $data
     * @return void
     */
    public function apply($data)
    {
        $trade_ids = implode(',', $data['trade_ids']);
        $is_apply = $this->model->where([
            'member_id' =>  $this->member_id,
            'trade_type' => InvoiceDict::TYPE,
        ])->whereIn('trade_id',$trade_ids)->count() > 0;
        if ($is_apply) {
            throw new ApiException('INVOICE_HAS_ORDER_APPLY');
        }
        $member_id = $this->member_id;
        $insert = [
            'member_id' => $member_id,
            'trade_type' => InvoiceDict::TYPE,
            'trade_id' => $trade_ids,
            'pay_voucher' => $data['pay_voucher'],
            'header_type' => $data['header_type'],
            'header_name' => $data['header_name'],
            'type' => $data['type'],
            'name' => $data['name'],
            'tax_number' => $data['tax_number'],
            'mobile' => $data['mobile'],
            'email' => $data['email'],
            'telephone' => $data['telephone'],
            'address' => $data['address'],
            'bank_name' => $data['bank_name'],
            'bank_card_number' => $data['bank_card_number'],
            'money' => $data['money'],
            'create_time' => time(),
            'status' => InvoiceDict::OPEN,//生效
            'is_invoice' => InvoiceDict::WAIT_AUDIT,//待审核
        ];
        $invoice_id = (new CoreInvoiceService())->add($insert);
        (new CoreOrderService())->setInvoiceIdByOrderId(explode(',', $trade_ids), $invoice_id);
    }

    /**
     * 取消发票申请
     * @param $id
     * @return void
     */
    public function cancel($id)
    {
        $info = $this->model->where(['id' => $id, 'member_id' => $this->member_id])->findOrEmpty();
        if ($info->isEmpty()) {
            throw new ApiException('INVOICE_NOT_EXIST');
        }
        if ($info->is_invoice == InvoiceDict::INVOICED) {
            throw new ApiException('INVOICED');
        }
        Db::startTrans();
        try {
            $this->model->where(['id' => $id, 'member_id' => $this->member_id])->delete();
            (new CoreOrderService())->cancelInvoiceId($id);
            Db::commit();
        } catch (ApiException $e) {
            Db::rollback();
            throw new ApiException('INVOICE_CANCEL_ERROR');
        }
    }

    /**
     * 编辑发票信息
     * @param $data
     * @return void
     */
    public function edit($data)
    {
        $id = $data['id'];
        $invoice_info = $this->model->where(['id' => $id, 'member_id' => $this->member_id])->findOrEmpty();
        if ($invoice_info->isEmpty()) {
            throw new ApiException('INVOICE_NOT_EXIST');
        }
        if ($invoice_info->is_invoice == InvoiceDict::INVOICED) {
            throw new ApiException('INVOICED');
        }
        $this->model->where(['id' => $id, 'member_id' => $this->member_id])->update([
            'trade_type' => InvoiceDict::TYPE,
            'pay_voucher' => $data['pay_voucher'],
            'header_type' => $data['header_type'],
            'header_name' => $data['header_name'],
            'type' => $data['type'],
            'name' => $data['name'],
            'tax_number' => $data['tax_number'],
            'mobile' => $data['mobile'],
            'email' => $data['email'],
            'telephone' => $data['telephone'],
            'address' => $data['address'],
            'bank_name' => $data['bank_name'],
            'bank_card_number' => $data['bank_card_number'],
            'money' => $data['money'],
        ]);
    }

}
