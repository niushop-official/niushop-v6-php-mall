import request from '@/utils/request'

// 发票列表
export function getInvoiceList(params: Record<string, any>) {
    return request.get(`shop/order/invoice/list`,params)
}

// 发票详情
export function getInvoiceInfo(id: number) {
    return request.get(`shop/order/invoice/${id}`)
}

// 计算发票金额
export function calculateInvoice(params: Record<string, any>) {
    return request.post(`shop/order/invoice/calculate`,params)
}

// 添加发票
export function addInvoice(params: Record<string, any>) {
    return request.post(`shop/order/invoice/apply`, params)
}

// 编辑发票
export function editInvoice(id:number, params: Record<string, any>) {
    return request.post(`shop/order/invoice/edit/${id}`, params)
}

// 取消申请
export function cancelInvoice(id: number) {
    return request.delete(`shop/order/invoice/cancel/${id}`)
}