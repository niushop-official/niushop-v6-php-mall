import request from '@/utils/request'

/**
 * 营销中心
 * @param params
 * @returns
 */
export function getMarketingIndex(params: Record<string, any>) {
    return request.get(`shop/marketing`, { params })
}

/**
 * 获取商品分类列表
 * @param params
 * @returns
 */
export function getGoodsCategoryList(params: Record<string, any>) {
    return request.get(`shop/goods/coupon/init`, { params })
}

/**
 * 添加优惠券
 * @param params
 * @returns
 */
export function addCoupon(params: Record<string, any>) {
    return request.post(`shop/goods/coupon`, params, { showErrorMessage: true, showSuccessMessage: true })
}

/**
 * 获取优惠券列表
 * @param params
 * @returns
 */
export function getCouponList(params: Record<string, any>) {
    return request.get(`shop/goods/coupon`, { params })
}

/**
 * 获取优惠券领取记录
 * @param params
 * @returns
 */
export function getCouponRecords(params: Record<string, any>) {
    return request.get(`shop/goods/coupon/records`, { params });
}

/**
 * 获取优惠券详情
 * @param id
 * @returns
 */
export function getCouponInfo(id: number) {
    return request.get(`shop/goods/coupon/detail/${ id }`);
}

/**
 * 优惠券状态变更
 * @param params
 * @returns
 */
export function editCouponStatus(params: Record<string, any>) {
    return request.put(`shop/goods/coupon/setstatus/${ params.status }`, params, { showSuccessMessage: true })
}

/**
 * 编辑优惠券
 * @param params
 * @returns
 */
export function editCoupon(params: Record<string, any>) {
    return request.put(`shop/goods/coupon/edit/${ params.id }`, params, {
        showErrorMessage: true,
        showSuccessMessage: true
    })
}

/**
 * 删除优惠券
 * @param id
 * @returns
 */
export function deleteCoupon(id: number) {
    return request.delete(`shop/goods/coupon/${ id }`, { showSuccessMessage: true })
}

/**
 * 关闭优惠券
 * @param id
 * @returns
 */
export function closeCoupon(id: number) {
    return request.put(`shop/goods/coupon/invalid/${ id }`, { showSuccessMessage: true })
}

/**
 * 获取商品分类列表
 * @param params
 * @returns
 */
export function getSelectedCouponList(params: Record<string, any>) {
    return request.get(`shop/goods/coupon/selected`, { params })
}

/************ 限时折扣 ****************/
/**
 * 获取限时折扣列表
 * @param params
 * @returns
 */
export function getActiveDiscountPageList(params: Record<string, any>) {
    return request.get(`shop/active/discount`, { params })
}

/**
 * 获取限时折扣状态列表
 * @returns
 */
export function getActiveDiscountStatusList() {
    return request.get(`shop/active/status`)
}

/**
 * 获取限时折扣详情
 * @param active_id active_id
 * @returns
 */
export function getActiveDiscountInfo(active_id: number) {
    return request.get(`shop/active/discount/${ active_id }`);
}

/**
 * 添加限时折扣
 * @param params
 * @returns
 */
export function addActiveDiscount(params: Record<string, any>) {
    return request.post('shop/active/discount', params, { showErrorMessage: true, showSuccessMessage: true })
}

/**
 * 编辑限时折扣
 * @param params
 * @returns
 */
export function editActiveDiscount(params: Record<string, any>) {
    return request.put(`shop/active/discount/${ params.active_id }`, params, {
        showErrorMessage: true,
        showSuccessMessage: true
    })
}

/**
 * 关闭限时折扣
 * @param active_id
 * @returns
 */
export function closeActiveDiscount(active_id: number) {
    return request.put(`shop/active/discount/close/${ active_id }`, {}, {
        showErrorMessage: true,
        showSuccessMessage: true
    })
}

/**
 * 删除限时折扣
 * @param active_id
 * @returns
 */
export function deleteActiveDiscount(active_id: number) {
    return request.delete(`shop/active/discount/${ active_id }`, { showSuccessMessage: true })
}

/**
 * 获取参与限时折扣商品列表
 * @param params
 * @returns
 */
export function getActiveDiscountGoodsPageList(params: Record<string, any>) {
    return request.get(`shop/active/discount/goods/${ params.active_id }`, { params })
}

/**
 * 获取参与限时折扣订单列表
 * @param params
 * @returns
 */
export function getActiveDiscountOrderPageList(params: Record<string, any>) {
    return request.get(`shop/active/discount/order/${ params.active_id }`, { params })
}

/**
 * 获取参与限时折扣会员列表
 * @param params
 * @returns
 */
export function getActiveDiscountMemberPageList(params: Record<string, any>) {
    return request.get(`shop/active/discount/member/${ params.active_id }`, { params })
}

/**
 * 获取活动专题
 * @returns
 */
export function getActiveDiscountConfig() {
    return request.get(`shop/active/discount/config`);
}

/**
 * 编辑活动专题
 * @param params
 * @returns
 */
export function editActiveDiscountConfig(params: Record<string, any>) {
    return request.put(`shop/active/discount/config`, params, {
        showErrorMessage: true,
        showSuccessMessage: true
    })
}

/********** 积分商品 ***********/

/**
 * 获取积分商品列表
 * @param params
 * @returns
 */
export function getActiveExchangePageList(params: Record<string, any>) {
    return request.get(`shop/active/exchange`, { params })
}

/**
 * 获取积分商品详情
 * @param id id
 * @returns
 */
export function getActiveExchangeInfo(id: number) {
    return request.get(`shop/active/exchange/${ id }`);
}

/**
 * 添加积分商品
 * @param params
 * @returns
 */
export function addActiveExchange(params: Record<string, any>) {
    return request.post('shop/active/exchange', params, { showErrorMessage: true, showSuccessMessage: true })
}

/**
 * 编辑积分商品
 * @param params
 * @returns
 */
export function editActiveExchange(params: Record<string, any>) {
    return request.put(`shop/active/exchange/${ params.id }`, params, {
        showErrorMessage: true,
        showSuccessMessage: true
    })
}

/**
 * 编辑积分商品状态
 * @param params
 * @returns
 */
export function editActiveExchangeStatus(params: Record<string, any>) {
    return request.put(`shop/active/exchange/status/${ params.id }`, params, {
        showErrorMessage: true,
        showSuccessMessage: true
    })
}

/**
 * 删除积分商品
 * @param id
 * @returns
 */
export function deleteActiveExchange(id: number) {
    return request.delete(`shop/active/exchange/${ id }`, { showSuccessMessage: true })
}

/**
 * 获取积分商品状态列表
 * @returns
 */
export function getActiveExchangeStatus() {
    return request.get(`shop/active/exchange/status`)
}