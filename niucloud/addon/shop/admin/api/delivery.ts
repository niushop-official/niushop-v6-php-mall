import request from '@/utils/request'

/*********************************  物流公司  ***************************************/
/**
 * 获取物流公司分页列表
 * @param params
 * @returns
 */
export function getCompanyPageList(params: Record<string, any>) {
    return request.get(`shop/delivery/company`, { params })
}

/**
 * 获取物流公司列表
 * @param params
 * @returns
 */
export function getCompanyList(params: Record<string, any>) {
    return request.get(`shop/delivery/company/list`, { params })
}

/**
 * 获取物流公司详情
 * @param company_id 物流公司company_id
 * @returns
 */
export function getCompanyInfo(company_id: number) {
    return request.get(`shop/delivery/company/${ company_id }`);
}

/**
 * 添加物流公司
 * @param params
 * @returns
 */
export function addCompany(params: Record<string, any>) {
    return request.post('shop/delivery/company', params, { showErrorMessage: true, showSuccessMessage: true })
}

/**
 * 编辑物流公司
 * @param params
 * @returns
 */
export function editCompany(params: Record<string, any>) {
    return request.put(`shop/delivery/company/${ params.company_id }`, params, {
        showErrorMessage: true,
        showSuccessMessage: true
    })
}

/**
 * 删除物流公司
 * @param company_id
 * @returns
 */
export function deleteCompany(company_id: number) {
    return request.delete(`shop/delivery/company/${ company_id }`, { showErrorMessage: true, showSuccessMessage: true })
}

/*********************************  运费模版  ***************************************/
/**
 * 获取运费模版分页列表
 * @param params
 * @returns
 */
export function getShippingTemplatePageList(params: Record<string, any>) {
    return request.get(`shop/shipping/template`, { params })
}

/**
 * 获取运费模版列表
 * @param params
 * @returns
 */
export function getShippingTemplateList(params: Record<string, any>) {
    return request.get(`shop/shipping/template/list`, { params })
}

/**
 * 获取物运费模版详情
 * @param template_id 运费模版template_id
 * @returns
 */
export function getShippingTemplateInfo(template_id: number) {
    return request.get(`shop/shipping/template/${ template_id }`);
}

/**
 * 添加运费模版
 * @param params
 * @returns
 */
export function addShippingTemplate(params: Record<string, any>) {
    return request.post('shop/shipping/template', params, { showErrorMessage: true, showSuccessMessage: true })
}

/**
 * 编辑运费模版
 * @param params
 * @returns
 */
export function editShippingTemplate(params: Record<string, any>) {
    return request.put(`shop/shipping/template/${ params.template_id }`, params, {
        showErrorMessage: true,
        showSuccessMessage: true
    })
}

/**
 * 删除运费模版
 * @param template_id
 * @returns
 */
export function deleteShippingTemplate(template_id: number) {
    return request.delete(`shop/shipping/template/${ template_id }`, {
        showErrorMessage: true,
        showSuccessMessage: true
    })
}

/*********************************  自提门店  ***************************************/
/**
 * 获取自提门店列表
 * @param params
 * @returns
 */
export function getStoreList(params: Record<string, any>) {
    return request.get(`shop/delivery/store`, { params })
}

/**
 * 获取自提门店详情
 * @param store_id 自提门店store_id
 * @returns
 */
export function getStoreInfo(store_id: number) {
    return request.get(`shop/delivery/store/${ store_id }`);
}

/**
 * 获取初始化页面信息
 * @returns
 */
export function getStoreInit() {
    return request.get(`shop/delivery/store/init`)
}

/**
 * 添加自提门店
 * @param params
 * @returns
 */
export function addStore(params: Record<string, any>) {
    return request.post('shop/delivery/store', params, { showErrorMessage: true, showSuccessMessage: true })
}

/**
 * 编辑自提门店
 * @param params
 * @returns
 */
export function editStore(params: Record<string, any>) {
    return request.put(`shop/delivery/store/${ params.store_id }`, params, {
        showErrorMessage: true,
        showSuccessMessage: true
    })
}

/**
 * 删除自提门店
 * @param store_id
 * @returns
 */
export function deleteStore(store_id: number) {
    return request.delete(`shop/delivery/store/${ store_id }`, { showErrorMessage: true, showSuccessMessage: true })
}

/*********************************  物流查询  ***************************************/
/**
 * 设置 物流查询配置
 * @param params
 * @returns
 */
export function setDeliverySearch(params: Record<string, any>) {
    return request.post('shop/delivery/search', params, { showErrorMessage: true, showSuccessMessage: true })
}

/**
 * 获取 物流查询配置
 * @returns
 */
export function getDeliverySearch() {
    return request.get('shop/delivery/search')
}

/**
 * 获取 物流管理配置
 * @returns
 */
export function getShopDeliveryList() {
    return request.get('shop/delivery/deliveryList')
}

/**
 * 物流管理设置
 * @param params
 * @returns
 */
export function setShopDeliveryConfig(params: Record<string, any>) {
    return request.put(`shop/delivery/setConfig`, params, { showSuccessMessage: true })
}

/*********************************  配送员  ***************************************/
/**
 * 获取 配送员列表
 * @param params
 * @returns
 */
export function getShopDelivery(params: Record<string, any>) {
    return request.get('shop/shop_delivery/staff', { params })
}

/**
 * 获取 配送员列表
 * @param params
 * @returns
 */
export function getShopDeliverList(params: Record<string, any>) {
    return request.get('shop/shop_delivery/staff/list', { params })
}

/**
 * 获取配送员信息
 * @param staff_id
 * @returns
 */
export function getShopDeliverInfo(staff_id: number) {
    return request.get(`shop/shop_delivery/staff/${ staff_id }`);
}

/**
 * 添加配送员
 * @param params
 * @returns
 */
export function addShopDeliver(params: Record<string, any>) {
    return request.post('shop/shop_delivery/staff', params, { showSuccessMessage: true })
}

/**
 * 编辑配送员
 * @param params
 * @returns
 */
export function editShopDeliver(params: Record<string, any>) {
    return request.put(`shop/shop_delivery/staff/${ params.deliver_id }`, params, { showSuccessMessage: true })
}

/**
 * 删除配送员
 * @param staff_id
 * @returns
 */
export function deleteShopDeliver(staff_id: number) {
    return request.delete(`shop/shop_delivery/staff/${ staff_id }`)
}


/*********************************  配送服务商  ***************************************/

/**
 * 获取配送服务商列表
 * @param params
 * @returns
 */
export function getDeliveryServiceList(id: number) {
    return request.get(`shop/delivery_store/delivery_service_list/${id}`)
}

/**
 * 服务商开通门店
 * @param params
 * @returns
 */
export function deliveryShopOpen(params: Record<string, any>) {
    return request.put(`shop/delivery_store/delivery_shop_open/${params.id}`, params, { showErrorMessage: true, showSuccessMessage: true })
}

/**
 * 服务商门店品类修改
 * @param params
 * @returns
 */
export function deliveryShopEdit(params: Record<string, any>) {
    return request.put(`shop/delivery_store/delivery_shop_edit/${params.id}`, params, { showErrorMessage: true, showSuccessMessage: true })
}


/*********************************  同城配送设置  ***************************************/

/**
 * 获取同城配送设置
 * @returns
 */
export function getLocal() {
    return request.get('shop/local_delivery/config');
}

/**
 * 设置同城配送
 * @param params
 * @returns
 */
export function setLocal(params: Record<string, any>) {
    return request.put('shop/local_delivery/config', params, { showSuccessMessage: true })
}

/**
 * 获取三方配送列表
 * @param params
 * @returns
 */
export function getInUseLocalDeliveryList(params: Record<string, any>) {
    return request.get('shop/local_delivery/list', {params})
}

/**
 * 获取同城配送平台列表
 */
export function getLocalDeliveryServiceList() {
    return request.get(`shop/local_delivery/service`)
}


/**
 * 同城配送平台设置
 * @param params
 * @returns
 */
export function setLocalDeliveryService(params: Record<string, any>) {
    return request.put(`shop/local_delivery/service/${params.delivery_type}`, params, {showSuccessMessage: true})
}

/**
 * 同城配送平台详情
 * @param delivery_type
 * @returns
 */
export function getLocalDeliveryServiceInfo(delivery_type: string) {
    return request.get(`shop/local_delivery/service/${delivery_type}`,)
}

/**
 * 三方配送
 * @returns
 */
export function getThird() {
    return request.get('shop/third/init')
}




/*********************************  同城配送记录  ***************************************/

/**
 * 获取配送记录状态
 * @param params
 * @returns
 */
export function getLocalDeliveryStatus() {
    return request.get('shop/local_delivery/order/status')
}

/**
 * 获取配送记录分页列表
 * @param params
 * @returns
 */
export function getLocalDeliveryList(params: Record<string, any>) {
    return request.get(`shop/local_delivery/order`, {params})
}

/**
 * 获取同城配送订单详情
 * @param id
 * @returns
 */
export function getLocalDeliveryInfo(id: number) {
    return request.get(`shop/local_delivery/order/${ id }`);
}

/**
 * 获取同城配送订单取消原因
 * @param params
 * @returns
 */
export function getLocalDeliveryCancelReason(params: Record<string, any>) {
    return request.get('shop/local_delivery/order/cancel_reason', {params})
}

/**
 * 获取同城配送订单同步
 * @param staff_id
 * @returns
 */
export function getLocalDeliverySync(id: number) {
    return request.put(`shop/local_delivery/order/sync/${ id }`);
}

/**
 * 同城配送订单取消
 * @param params
 * @returns
 */
export function setLocalDeliveryCancel(params: Record<string, any>) {
   return request.put(`shop/local_delivery/order/cancel/${ params.id }`, params, { showSuccessMessage: true })
}


/*********************************  商家配送记录  ***************************************/
/**
 * 获取配送记录状态
 * @param params
 * @returns
 */
export function getShopDeliveryStatus() {
    return request.get('shop/shop_delivery/order/status')
}

/**
 * 获取商家配送记录分页列表
 * @param params
 * @returns
 */
export function getShopDeliveryOrderList(params: Record<string, any>) {
    return request.get(`shop/shop_delivery/order`, {params})
}

/**
 * 获取同城配送订单详情
 * @param id
 * @returns
 */
export function getShopDeliveryInfo(id: number) {
    return request.get(`shop/shop_delivery/order/${ id }`);
}

/**
 * 获取商家配送记录订单完成
 * @param staff_id
 * @returns
 */
export function getShopDeliveryFinish(id: number) {
    return request.put(`shop/shop_delivery/order/finish/${ id }`);
}

/**
 * 获取商家配送记录订单完成
 * @param staff_id
 * @returns
 */
export function getDeliveryFinish(id: number) {
    return request.put(`shop/local_delivery/order/finish/${ id }`);
}

/**
 * 商家配送记录订单取消
 * @param params
 * @returns
 */
export function setShopDeliveryCancel(params: Record<string, any>) {
   return request.put(`shop/shop_delivery/order/cancel/${ params.id }`, params, { showSuccessMessage: true })
}

/**
 * 获取配送方式
 * @param id
 * @returns
 */
export function getShopDeliveryType() {
    return request.get(`shop/delivery/type`);
}


/*********************************  门店自提  ***************************************/

/**
 * 获取门店自提分页列表
 * @param params
 * @returns
 */
export function getDeliveryStoreList(params: Record<string, any>) {
    return request.get(`shop/delivery/store`, {params})
}

/**
 * 获取门店选择列表
 * @param params
 * @returns
 */
export function getDeliveryStoreListAll(params: Record<string, any>) {
    return request.get(`shop/delivery/store/list`, {params})
}

/**
 * 获取提货方式
 * @returns
 */
export function getStorePickUpType() {
    return request.get(`shop/delivery/store/pick_up_type`);
}

/**
 * 获取配送门店详情
 * @returns
 */
export function getDeliveryStoreDetail(id: number) {
    return request.get(`shop/delivery/store/${ id }`);
}

/**
 * 添加配送门店
 * @param params
 * @returns
 */
export function addDeliveryStore(params: Record<string, any>) {
    return request.post('shop/delivery/store', params, {showErrorMessage: true, showSuccessMessage: true})
}

/**
 * 编辑配送门店
 * @param params
 * @returns
 */
export function editDeliveryStore(params: Record<string, any>) {
    return request.put(`shop/delivery/store/${params.store_id}`, params, { showErrorMessage: true, showSuccessMessage: true })
}
/**
 * 删除运费模版
 * @param template_id
 * @returns
 */
export function deleteDeliveryStore(store_id: number) {
    return request.delete(`shop/delivery/store/${store_id}`, {showErrorMessage: true, showSuccessMessage: true})
}

/**
 * 编辑配送门店状态
 * @param params
 * @returns
 */
export function editDeliveryStoreStatus(params: Record<string, any>) {
    return request.put(`shop/delivery/store/modify_status`, params, { showErrorMessage: true, showSuccessMessage: true })
}

/**
 * 获取距离配置
 * @returns
 */
export function getDeliveryConfig() {
    return request.get('shop/local_delivery/base_config')
}


/**
 * 编辑距离配置
 * @param params
 * @returns
 */
export function setDeliveryConfig(params: Record<string, any>) {
    return request.put('shop/local_delivery/base_config', params, {showSuccessMessage: true})
}

