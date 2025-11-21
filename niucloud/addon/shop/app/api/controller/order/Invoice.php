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

namespace addon\shop\app\api\controller\order;


use addon\shop\app\service\api\order\InvoiceService;
use addon\shop\app\service\api\order\OrderService;
use core\base\BaseApiController;

class Invoice extends BaseApiController
{
    /**
     * 发票列表
     * @return \think\Response
     */
    public function list()
    {
        $data = $this->request->params([
            ['status', ''],
        ]);
        $data = (new InvoiceService())->pages($data);
        return success($data);
    }

    /**
     * 发票列表
     * @return \think\Response
     */
    public function info($id)
    {
        $data = (new InvoiceService())->detail($id);
        return success($data);
    }

    /**
     * 申请开发票
     * @return \think\Response
     */
    public function apply()
    {
        $data = $this->request->params([
            ['trade_ids', []],
            ['header_type', ''],
            ['header_name', ''],
            ['type', ''],
            ['name', ''],
            ['tax_number', ''],
            ['mobile', ''],
            ['email', ''],
            ['telephone', ''],
            ['address', ''],
            ['bank_name', ''],
            ['bank_card_number', ''],
            ['money', ''],
            ['pay_voucher', ''],
        ]);
        (new InvoiceService())->apply($data);
        return success("SUCCESS");
    }

    /**
     * 计算发票金额
     * @param $trade_ids
     * @return \think\Response
     */
    public function calculate($trade_ids)
    {
        $info = (new OrderService())->calculateInvoiceMoney($trade_ids);
        return success("SUCCESS", $info);
    }

    /**
     * 发票列表
     * @return \think\Response
     */
    public function edit($id)
    {
        $data = $this->request->params([
            ['id', $id],
            ['header_type', ''],
            ['header_name', ''],
            ['type', ''],
            ['name', ''],
            ['tax_number', ''],
            ['mobile', ''],
            ['email', ''],
            ['telephone', ''],
            ['address', ''],
            ['bank_name', ''],
            ['bank_card_number', ''],
            ['money', ''],
            ['pay_voucher', ''],
        ]);
        (new InvoiceService())->edit($data);
        return success('EDIT_SUCCESS');
    }

    /**
     * 取消发票
     * @return \think\Response
     */
    public function cancel($id)
    {
        (new InvoiceService())->cancel($id);
        return success('SUCCESS');
    }
}