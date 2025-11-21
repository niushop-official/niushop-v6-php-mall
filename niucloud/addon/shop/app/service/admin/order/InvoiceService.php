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

namespace addon\shop\app\service\admin\order;

use addon\shop\app\dict\order\InvoiceDict;
use addon\shop\app\model\order\Invoice;
use addon\shop\app\service\core\order\CoreInvoiceService;
use addon\shop\app\service\core\order\CoreOrderService;
use core\base\BaseAdminService;
use core\exception\AdminException;
use core\exception\CommonException;

/**
 * 发票
 * Class InvoiceService
 * @package app\service\admin
 */
class InvoiceService extends BaseAdminService
{
    public function __construct()
    {
        parent::__construct();
        $this->model = new Invoice();
    }

    public function getPage(array $where)
    {
        $order = 'id desc';
        $search_model = $this->model
            ->withSearch(["is_invoice", "header_type", "header_name", "create_time", "invoice_time"], $where)
            ->where([['status', '=', InvoiceDict::OPEN]])
            ->append(['header_type_name', 'type_name', 'invoice_type_name','pay_voucher_thumb_mid','pay_voucher_thumb_small','pay_voucher_thumb_big','invoice_voucher_thumb_mid','invoice_voucher_thumb_small','invoice_voucher_thumb_big'])
            ->field('*')->order($order);
        $list = $this->pageQuery($search_model);
        foreach ($list['data'] as &$item) {
            $item['is_can_jump'] = 1;
            if (count(explode(',', $item['trade_id'])) > 1) {
                $item['is_can_jump'] = 0;
            }
        }
        return $list;
    }

    public function getInfo(int $id)
    {
        $detail = $this->model->where([['id', '=', $id], ['status', '=', InvoiceDict::OPEN]])->field('*')->append(['header_type_name', 'type_name'])->findOrEmpty()->toArray();
        return $detail;
    }

    /**
     * 开票
     * @param int $id
     * @param array $data
     * @return true
     */
    public function invoicing(int $id, array $data)
    {
        $invoice = $this->model->where([['id', '=', $id], ['status', '=', 2]])->findOrEmpty();
        if ($invoice->isEmpty()) throw new CommonException('INVOICE_NOT_EXIST');
        if ($invoice['is_invoice']) throw new CommonException('INVOICED');

        $invoice->is_invoice = 1;
        $invoice->invoice_number = $data['invoice_number'];
        $invoice->invoice_voucher = $data['invoice_voucher'];
        $invoice->remark = $data['remark'];
        $invoice->invoice_time = time();

        $invoice->save();

        return true;
    }

    /**
     * 开票
     * @param int $id
     * @param array $data
     * @return true
     */
    public function audit($data)
    {
        (new CoreInvoiceService())->audit($data['ids']);
        return true;
    }

    public function add($data)
    {
        $order_info = (new OrderService())->getInfoByOrderNo($data['order_no']);
        if (empty($order_info)) {
            throw new AdminException('SHOP_ORDER_NOT_FOUND');
        }
        $trade_id = $order_info['order_id'];
        $member_id = $order_info['member_id'];
        $invoice_info = $this->model->where([
            'trade_id' => $trade_id,
            'member_id' => $member_id
        ])->findOrEmpty();
        if (!$invoice_info->isEmpty()) {
            throw new CommonException('INVOICE_IS_EXIST');
        }
        $insert = [
            'member_id' => $member_id,
            'trade_type' => InvoiceDict::TYPE,
            'trade_id' => $trade_id,
            'header_type' => $data['header_type'],
            'header_name' => $data['header_name'],
            'type' => $data['type'],
            'name' => $data['name'],
            'pay_voucher' => $data['pay_voucher'],
            'tax_number' => $data['tax_number'],
            'mobile' => $data['mobile'],
            'email' => $data['email'],
            'telephone' => $data['telephone'],
            'address' => $data['address'],
            'bank_name' => $data['bank_name'],
            'bank_card_number' => $data['bank_card_number'],
            'money' => $data['money'],
            'is_invoice' => InvoiceDict::WAIT_INVOICE,
            'create_time' => time(),
            'status' => InvoiceDict::OPEN,
        ];
        $invoice_id = (new CoreInvoiceService())->add($insert);
        (new CoreOrderService())->setInvoiceIdByOrderId([$trade_id], $invoice_id);
    }

}
