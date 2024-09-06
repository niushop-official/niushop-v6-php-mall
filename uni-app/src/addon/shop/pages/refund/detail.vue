<template>
    <view :style="themeColor()">
        <view class="bg-[#f8f8f8] min-h-screen overflow-hidden" v-if="!loading">
            <view class="pb-[200rpx]" v-if="type != 'logistics'">
                <view class="bg-linear">
                    <!-- #ifdef MP-WEIXIN -->
                    <top-tabbar :data="topTabbarData" :scrollBool="topTabarObj.getScrollBool()" />
                    <!-- #endif -->
                    <view v-if="detail.status_name" class="flex justify-between items-center pl-[40rpx] pr-[50rpx]  h-[170rpx]">
                        <view class="text-[36rpx] font-500 leading-[42rpx] text-[#fff]">{{ detail.status_name }}</view>
                        <view>
                            <image v-if="['1','2','4','6','7'].indexOf(detail.status) != -1" class="w-[180rpx] h-[140rpx]" :src="img('addon/shop/detail/payment.png')" mode="aspectFit"/>
                            <image v-if="['8'].indexOf(detail.status) != -1" class="w-[180rpx] h-[140rpx]" :src="img('addon/shop/detail/complete.png')" mode="aspectFit" />
                            <image v-if="['3','5','-1'].indexOf(detail.status) != -1" class="w-[180rpx] h-[140rpx]" :src="img('addon/shop/detail/close.png')" mode="aspectFit" />
                        </view>
                    </view>
                </view>
                <view class="bg-[#fff] sidebar-marign p-[20rpx] rounded-[16rpx] flex justify-between flex-wrap mt-[20rpx]">
                    <view class="w-[150rpx] h-[150rpx] flex-2" @click="goodsEvent(detail.order_goods.goods_id)">
                        <u--image class="rounded-[10rpx] overflow-hidden" width="150rpx" height="150rpx" :src="img(detail.order_goods.goods_image_thumb_small ? detail.order_goods.goods_image_thumb_small : '')" model="aspectFill">
                            <template #error>
                                <image class="w-[150rpx] h-[150rpx] rounded-[10rpx] overflow-hidden" :src="img('static/resource/images/diy/shop_default.jpg')" mode="aspectFill"></image>
                            </template>
                        </u--image>
                    </view>
                    <view class="ml-[20rpx] flex flex-1 flex-col justify-between text-[#303133]">
                        <view>
                            <text class="text-[28rpx] text-item leading-[40rpx]">{{ detail.order_goods.goods_name }}</text>
                            <view class="flex" v-if="detail.order_goods.sku_name">
                                <view class="text-[24rpx] mt-[10rpx] text-[#999] truncate max-w-[450rpx] leading-[28rpx]">{{ detail.order_goods.sku_name }}</view>
                            </view>
                        </view>
                        <view class="flex justify-between items-center leading-[28rpx]">
                            <view class="price-font">
                                <text class="text-[24rpx]">￥</text>
                                <text class="text-[32rpx] font-500">{{ parseFloat(detail.order_goods.price).toFixed(2).split('.')[0] }}</text>
                                <text class="text-[22rpx] font-500">.{{ parseFloat(detail.order_goods.price).toFixed(2).split('.')[1] }}</text>
                            </view>
                            <text class="text-right text-[26rpx]">x{{ detail.order_goods.num }}</text>
                        </view>
                    </view>
                </view>
                <view class="bg-[#fff] sidebar-marign p-[20rpx] mt-[20rpx] rounded-[16rpx]">
					<view class="flex justify-between text-[28rpx] leading-[32rpx] ">
					    <view>{{t('refundMoney')}}</view>
						<view class="price-font">
							<text class="text-[24rpx]">￥</text>
							<text class="text-[32rpx] font-500">{{ parseFloat(detail.apply_money).toFixed(2).split('.')[0] }}</text>
							<text class="text-[24rpx] font-500">.{{ parseFloat(detail.apply_money).toFixed(2).split('.')[1] }}</text>
						</view>
					</view>
                    <view class="flex justify-between text-[28rpx] leading-[32rpx] mt-[30rpx]">
                        <view>{{t('refundType')}}</view>
                        <view>{{ detail.refund_type_name }}</view>
                    </view>
                    <view class="flex justify-between text-[28rpx] leading-[32rpx] mt-[30rpx]">
                        <view>{{t('refundCause')}}</view>
                        <view class="w-[400rpx] multi-hidden text-right">{{ detail.reason || '--' }}</view>
                    </view>
                    <view class="flex justify-between text-[28rpx] leading-[32rpx] mt-[30rpx]">
                        <view>{{t('refundNo')}}</view>
                        <view>{{ detail.order_refund_no }}</view>
                    </view>
                    <view class="flex justify-between text-[28rpx] leading-[32rpx] mt-[30rpx]">
                        <view>{{t('createTime')}}</view>
                        <view>{{ detail.create_time }}</view>
                    </view>
                    <view class="flex justify-between text-[28rpx] leading-[32rpx] mt-[30rpx]">
                        <view>{{t('createExplain')}}</view>
                        <view class="flex-1 ml-[20rpx] flex justify-end break-all">{{ detail.remark }}</view>
                    </view>
                    <view class="flex justify-between text-[28rpx] leading-[32rpx]  mt-[30rpx]">
                        <view>{{t('reasonRefusal')}}</view>
                        <view class="w-[400rpx] text-right">{{ detail.shop_reason || '--' }}</view>
                    </view>
                </view>

                <view class="bg-[#fff] sidebar-marign p-[20rpx] mt-[20rpx] rounded-[16rpx]">
                    <view class="flex justify-between text-[28rpx] leading-[32rpx]">
                        <view>{{t('record')}}</view>
                        <view class="flex items-center" @click="redirect({url: '/addon/shop/pages/refund/log', param: { order_refund_no: orderRefundNo }})">
                            <text>{{t('check')}}</text>
                            <text class="nc-iconfont nc-icon-youV6xx text-[30rpx] text-[#999]"></text>
                        </view>
                    </view>
                </view>

                <view class="flex tab-bar justify-between items-center bg-[#fff] fixed left-0 right-0 bottom-0 min-h-[100rpx] px-1 flex-wrap">
                    <view class="flex">
						<view class="flex w-[70rpx] mr-[20rpx] flex-col justify-center items-center" @click="redirect({ url: '/addon/shop/pages/index', mode: 'reLaunch' })">
						    <view class="nc-iconfont nc-icon-shouyeV6xx text-[36rpx]"></view>
						    <text class="text-[20rpx] mt-[10rpx]">{{t('index')}}</text>
						</view>
						<!-- #ifdef MP-WEIXIN -->
						<view>
							<nc-contact
									:send-message-title="sendMessageTitle"
									:send-message-path="sendMessagePath"
									:send-message-img="sendMessageImg">
								<view class="flex flex-col justify-center items-center">
									<text class="nc-iconfont nc-icon-kefuV6xx-1 text-[36rpx]"></text>
									<text class="text-[20rpx] mt-[10rpx]">客服</text>
								</view>
							</nc-contact>
						</view>
						<!-- #endif -->
					</view>
					
                    <view class="flex justify-end mr-[30rpx]">
                        <view class="text-[26rpx] leading-[52rpx] px-[23rpx] border-[2rpx] border-solid border-[#999] rounded-full ml-[20rpx] text-[#303133]" @click="refundBtnFn('cancel')" v-if="['6','7','8','-1'].indexOf(detail.status) == -1">{{t('refundApply')}}</view>
                        <view v-if="['3'].indexOf(detail.status) != -1" class="text-[24rpx] text-[#303133] leading-[52rpx] px-[23rpx] border-[2rpx] border-solid border-[#999] rounded-full ml-[20rpx]" @click.stop="refundBtnFn('edit')" >编辑退款信息</view>
                        <view v-if="['2'].indexOf(detail.status) != -1" class=" text-[24rpx] text-[#303133] leading-[52rpx] px-[23rpx] border-[2rpx] border-solid border-[#999] rounded-full ml-[20rpx]" @click.stop="refundBtnFn('logistics')">填写发货物流</view>
                        <view v-if="['5'].indexOf(detail.status) != -1" class="text-[24rpx] text-[#303133] leading-[52rpx] px-[23rpx] border-[2rpx] border-solid border-[#999] rounded-full ml-[20rpx]" @click.stop="refundBtnFn('editLogistics')">编辑发货物流</view>
                    </view>
                </view>
            </view>
            <view  v-else>
                <view class="bg-[#fff] sidebar-marign p-[20rpx] rounded-[16rpx] flex justify-between flex-wrap mt-[20rpx]">
                    <view class="w-[150rpx] h-[150rpx] flex-2" @click="goodsEvent(detail.order_goods.goods_id)">
                        <u--image class="rounded-[10rpx] overflow-hidden" width="150rpx" height="150rpx" :src="img(detail.order_goods.sku_image ? detail.order_goods.sku_image : '')" model="aspectFill">
                            <template #error>
                                <image class="w-[150rpx] h-[150rpx] rounded-[10rpx] overflow-hidden" :src="img('static/resource/images/diy/shop_default.jpg')" mode="aspectFill"></image>
                            </template>
                        </u--image>
                    </view>
                    <view class="ml-[20rpx] flex flex-1 flex-col justify-between">
                        <view>
                            <text class="text-[28rpx] text-item leading-[40rpx]">{{ detail.order_goods.goods_name }}</text>
                            <view class="flex" v-if="detail.order_goods.sku_name">
                                <text class="text-[24rpx] mt-[10rpx] text-[#999] truncate max-w-[450rpx] leading-[28rpx]">{{ detail.order_goods.sku_name }}</text>
                            </view>
                        </view>
                        <view class="flex justify-between items-center leading-[28rpx]">
                            <view class="price-font">
                                <text class="text-[24rpx]">￥</text>
                                <text class="text-[32rpx] font-500">{{ parseFloat(detail.order_goods.price).toFixed(2).split('.')[0] }}</text>
                                <text class="text-[22rpx] font-500">.{{ parseFloat(detail.order_goods.price).toFixed(2).split('.')[1] }}</text>
                            </view>
                            <text class="text-right text-[26rpx]">x{{ detail.order_goods.num }}</text>
                        </view>
                    </view>
                </view>
                <view class="bg-[#fff] sidebar-marign p-[20rpx] rounded-[16rpx] mt-[20rpx]">
                    <view class="flex justify-between text-[28rpx] leading-[32rpx] ">
                        <view>联系人</view>
                        <view>{{ detail.refund_address.contact_name }}</view>
                    </view>
                    <view class="flex justify-between text-[28rpx] leading-[32rpx]  mt-[30rpx]">
                        <view>手机号</view>
                        <view>{{ detail.refund_address.mobile }}</view>
                    </view>
                    <view class="flex justify-between text-[28rpx] leading-[32rpx] mt-[30rpx]">
                        <view>退货地址</view>
                        <view class="w-[460rpx] text-sm text-right" v-if="detail.refund_address">{{ detail.refund_address.full_address || '--' }}</view>
                    </view>
                </view>
                <view class="bg-[#fff] sidebar-marign p-[20rpx] rounded-[16rpx] mt-[20rpx]">
                    <u--form labelPosition="left" :model="formData" :rules="rules" ref="deliveryForm" labelWidth="140rpx" :labelStyle="{'fontSize': '28rpx'}">
                        <u-form-item label="物流公司" prop="express_company" borderBottom="true" ref="item1">
                            <u--input pl border="none" v-model="formData.express_company" placeholder="请输入物流公司" placeholderClass="text-sm" fontSize="28rpx"></u--input>
                        </u-form-item>
                        <u-form-item label="物流单号" prop="express_number" borderBottom="true" ref="item1">
                            <u--input border="none" placeholder="请输入物流单号" v-model="formData.express_number" placeholderClass="text-sm" fontSize="28rpx"></u--input>
                        </u-form-item>
                        <u-form-item label="物流说明" borderBottom ref="item1">
                            <u--input border="none" placeholder="选填" v-model="formData.remark" placeholderClass="text-sm" fontSize="28rpx"></u--input>
                        </u-form-item>
                    </u--form>
                </view>
                <view class="sidebar-marign">
                    <button class="mt-[20rpx] bg-[var(--primary-color)] text-[#fff] h-[80rpx] leading-[80rpx] rounded-[100rpx] text-[28rpx]" @click="deliverySave">提交</button>
                </view>
            </view>
            <logistics-tracking ref="materialRef"></logistics-tracking>
            <u-modal :show="cancelRefundShow" confirmColor="var(--primary-color)" :content="t('cancelRefundContent')" :showCancelButton="true" :closeOnClickOverlay="true" @cancel="refundCancel" @confirm="refundConfirm"></u-modal>
        </view>

        <u-loading-page bg-color="rgb(248,248,248)" :loading="loading" loadingText="" fontSize="16" color="#303133"></u-loading-page>

        <!-- #ifdef MP-WEIXIN -->
        <!-- 小程序隐私协议 -->
        <wx-privacy-popup ref="wxPrivacyPopupRef"></wx-privacy-popup>
        <!-- #endif -->
    </view>
</template>

<script setup lang="ts">
import { ref, reactive, computed } from 'vue';
import { onLoad } from '@dcloudio/uni-app'
import { t } from '@/locale'
import { img, redirect, copy } from '@/utils/common';
import { topTabar } from '@/utils/topTabbar'
import { getRefundDetail, refundDelivery, editRefundDelivery, closeRefund } from '@/addon/shop/api/refund';
import logisticsTracking from '@/addon/shop/pages/order/components/logistics-tracking/logistics-tracking.vue'

/********* 自定义头部 - start ***********/
const topTabarObj = topTabar()
let topTabbarData = topTabarObj.setTopTabbarParam({title:'退款详情'})
/********* 自定义头部 - end ***********/

let detail = ref<Object>({});
let loading = ref<boolean>(true);
let orderRefundNo = ref('');
let type = ref('');
let isEditDelivery = ref(false);

const sendMessageTitle = ref('')
const sendMessagePath = ref('')
const sendMessageImg = ref('')
// 物流信息
const formData = ref({
	express_number: '',
	express_company: '',
	remark: ''
})

// 物流验证
const rules = {
	'express_number': {
		type: 'string',
		required: true,
		message: '请输入物流单号',
		trigger: ['blur', 'change']
	},
	'express_company': {
		type: 'string',
		required: true,
		message: '请输入物流公司',
		trigger: ['blur', 'change']
	}
}

onLoad((option: any) => {
	orderRefundNo.value = option.order_refund_no;
	type.value = option.type;
	isEditDelivery.value = option.is_edit_delivery;

	refundDetailFn(orderRefundNo.value);
});

const refundDetailFn = (refundNo: any) => {
	loading.value = true;
	getRefundDetail(refundNo).then((res: any) => {
		detail.value = res.data;
		// 赋值物流信息
		if(isEditDelivery.value && detail.value.delivery){
			formData.value.express_number = detail.value.delivery.express_number
			formData.value.express_company = detail.value.delivery.express_company
			formData.value.remark = detail.value.delivery.remark
		}
		
		sendMessageTitle.value = detail.value.order_goods.goods_name
		sendMessageImg.value = img(detail.value.order_goods.goods_image_thumb_small || '')
		loading.value = false;
	}).catch(() => {
		loading.value = false;
	})
}

const goodsEvent = (id: number) => {
	redirect({
		url: '/addon/shop/pages/goods/detail',
		param: {
			goods_id: id
		}
	})
}

// 提交物流信息
const deliveryForm = ref()
const deliverySave = ()=>{
	deliveryForm.value.validate().then(res => {
		let obj = {delivery: formData.value,order_refund_no: detail.value.order_refund_no}
		let api = isEditDelivery.value ? editRefundDelivery(obj) : refundDelivery(obj);
		api.then((res) => {
			setTimeout(()=>{
				redirect({ url: '/addon/shop/pages/refund/list' })
			},500)
		}).catch(() => {
		})
	})
}

const refundBtnFn = (type:any) => {
    if(type == 'cancel'){
        currRefundOn = detail.value.order_refund_no;
        cancelRefundShow.value = true;
    }else if(type == 'edit'){
        redirect({ url: '/addon/shop/pages/refund/edit_apply', param: { order_refund_no : detail.value.order_refund_no } })
    }else if(type == 'logistics'){
        redirect({ url: '/addon/shop/pages/refund/detail', param: { order_refund_no : detail.value.order_refund_no, type: 'logistics' } })
    }else if(type == 'editLogistics'){
        redirect({ url: '/addon/shop/pages/refund/detail', param: { order_refund_no : detail.value.order_refund_no, type: 'logistics', is_edit_delivery: true } })
    }
}

// 撤销维权
const cancelRefundShow = ref(false);
let currRefundOn = "";

const refundConfirm = ()=>{
    closeRefund(currRefundOn).then((res) => {
        cancelRefundShow.value = false;
        refundDetailFn(orderRefundNo.value);
    }).catch(() => {
        cancelRefundShow.value = false;
    })
}

const refundCancel = ()=>{
    cancelRefundShow.value = false;
}

</script>
<style lang="scss" scoped>
.text-item {
	overflow: hidden;
	text-overflow: ellipsis;
	display: -webkit-box;
	-webkit-line-clamp: 2;
	-webkit-box-orient: vertical;
}

.bg-linear {
	background: linear-gradient( 94deg, #E73835 15%, #FE8448 87%);
}
</style>
