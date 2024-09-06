<template>
    <view :style="themeColor()">   
        <view class="bg-[#f8f8f8] min-h-screen overflow-hidden" v-if="!loading">
            <view v-if="!loading" class="pb-20rpx">
                <view v-if="detail.status_name" class="pl-[40rpx] pr-[50rpx] bg-linear pb-[100rpx]">
                    <!-- #ifdef MP-WEIXIN -->
                    <top-tabbar :data="topTabbarData" :scrollBool="topTabarObj.getScrollBool()" />
                    <!-- #endif -->
                    <view class="flex justify-between items-center pt-[40rpx]">
						<view class="text-[#fff] text-[36rpx] font-500 leading-[42rpx]">{{ detail.status_name.name }}</view>
						<image v-if="detail.status == 1" class="w-[180rpx] h-[140rpx]" :src="img('addon/shop/detail/payment.png')" mode="aspectFit" />
						<image v-if="detail.status == 2" class="w-[180rpx] h-[140rpx]" :src="img('addon/shop/detail/deliver_goods.png')" mode="aspectFit" />
						<image v-if="detail.status == 3" class="w-[180rpx] h-[140rpx]" :src="img('addon/shop/detail/receive.png')" mode="aspectFit" />
						<image v-if="detail.status == 5" class="w-[180rpx] h-[140rpx]" :src="img('addon/shop/detail/complete.png')" mode="aspectFit" />
						<image v-if="detail.status == -1" class="w-[180rpx] h-[140rpx]" :src="img('addon/shop/detail/close.png')" mode="aspectFit" />
                    </view>
                </view>
                <view class="bg-[#fff] sidebar-marign p-[30rpx] mt-[-86rpx] rounded-[16rpx]" v-if="detail.delivery_type != 'virtual'">
                    <view v-if="detail.delivery_type == 'express'">
                        <view class="text-[#303133]">
							<view class="flex items-center">
								<text class="nc-iconfont nc-icon-dizhiguanliV6xx text-[30rpx] mr-[6rpx]"></text>
								<view class="text-[30rpx] font-500 leading-[38rpx] overflow-hidden">{{detail.taker_address}}</view>
							</view>
							<view class="mt-[12rpx] text-[22rpx] leading-[26rpx]">{{detail.taker_full_address.split(detail.taker_address)[0]}}</view>
                            <view class="mt-[16rpx] text-[24rpx] leading-[28rpx] ">
                                <text>{{ detail.taker_name }}</text>
                                <text class="ml-[15rpx]">{{ detail.taker_mobile }}</text>
                            </view>
                        </view>
                    </view>
                    <view v-if="detail.delivery_type == 'store'">
                        <view class="flex">
                            <view>
                                <u--image class="overflow-hidden" radius="10rpx" width="100rpx" height="100rpx" :src="img(detail.store.store_logo ? detail.store.store_logo : '')" model="aspectFill">
                                    <template #error>
                                        <u-icon name="photo" color="#999" size="50"></u-icon>
                                    </template>
                                </u--image>
                            </view>
                            <view class="flex flex-col ml-[20rpx]">
                                <text class="text-[24rpx]">{{ detail.store.store_name }}</text>
                                <text class="text-[24rpx] mt-[15rpx]">{{ detail.store.trade_time }}</text>
                                <text class="text-[24rpx] mt-[10rpx] leading-[35rpx]">{{ detail.store.full_address }}</text>
                            </view>
                        </view>
                    </view>
                    <view class="flex" v-if="detail.delivery_type == 'local_delivery'">
                        <view @click="getAddress" class="min-w-[100rpx] flex items-center justify-center">
                            <u-icon name="map" size="25"></u-icon>
                        </view>
                        <view class="flex flex-col ml-[20rpx]">
                            <text class="text-[24rpx]">{{ detail.taker_name }}</text>
                            <text class="text-[24rpx] mt-[15rpx]">{{ detail.taker_mobile }}</text>
                            <text class="text-[24rpx] mt-[10rpx] leading-[35rpx]">{{ detail.taker_full_address }}</text>
                        </view>
                    </view>
                </view>
                <view class="bg-[#fff] sidebar-marign p-[20rpx] rounded-[16rpx]" :style="detail.delivery_type == 'virtual' ? 'margin-top: -70rpx' : 'margin-top: 20rpx'">
                    <view class="order-goods-item flex justify-between flex-wrap mt-[30rpx]" v-for="(goodsItem, goodsIndex) in detail.order_goods" :key="goodsIndex">
                        <view class="w-[150rpx] h-[150rpx] flex-2" @click="goodsEvent(goodsItem.goods_id)">
                            <u--image class="overflow-hidden" radius="10rpx" width="150rpx" height="150rpx" :src="img(goodsItem.goods_image_thumb_small ? goodsItem.goods_image_thumb_small : '')" model="aspectFill">
                                <template #error>
									<image class="w-[150rpx] h-[150rpx] rounded-[10rpx] overflow-hidden" :src="img('static/resource/images/diy/shop_default.jpg')" mode="aspectFill"></image>
                                </template>
                            </u--image>
                        </view>
						
                        <view class="ml-[20rpx] flex flex-1 flex-col justify-between">
                            <view>
                                <text class="text-[28rpx] text-item leading-[40rpx]">{{ goodsItem.goods_name }}</text>
                                <view v-if="goodsItem.sku_name">
                                    <view class="text-[24rpx] mt-[10rpx] text-[#999] truncate max-w-[450rpx] leading-[28rpx]">{{ goodsItem.sku_name }}</view>
                                </view>
                            </view>
                            <view class="flex justify-between items-center leading-[28rpx]">
                                <view class="price-font">
									<text v-if="goodsItem.extend && parseFloat(goodsItem.extend.point) > 0" class="text-[32rpx]">{{goodsItem.extend.point}}积分</text>
									<text v-if="parseFloat(goodsItem.price) && goodsItem.extend && parseFloat(goodsItem.extend.point) > 0" class="mx-[4rpx] text-[32rpx]">+</text>
									<block v-if="parseFloat(goodsItem.price)">
										<text class="text-[32rpx]">￥</text>
										<text class="text-[32rpx] font-500">{{ parseFloat(goodsItem.price).toFixed(2).split('.')[0] }}</text>
										<text class="text-[32rpx] font-500">.{{ parseFloat(goodsItem.price).toFixed(2).split('.')[1] }}</text>
									</block>
                                </view>
                                <text class="text-right text-[26rpx]">x{{ goodsItem.num }}</text>
                            </view>
                        </view>
                        <view class="flex justify-end  self-end w-[100%] mt-[10rpx]" v-if="(goodsItem.status != '1') || (goodsItem.is_enable_refund == 1)">	
                            <view v-if="goodsItem.status != '1'"
                                class="text-[26rpx] text-[#303133] leading-[46rpx] px-[10rpx] border-[2rpx] border-solid border-[#999] rounded-[23rpx]"
                                @click="redirect({ url: '/addon/shop/pages/refund/detail', param: { order_refund_no : goodsItem.order_refund_no } })">查看退款</view>
                            <view v-else-if="goodsItem.is_enable_refund == 1"
                                class="text-[26rpx] text-[#303133]  leading-[46rpx] px-[10rpx] border-[2rpx] border-solid border-[#999] rounded-[23rpx] ml-[20rpx]"
                                @click="applyRefund(goodsItem.order_goods_id)">申请退款</view>
                        </view>
                    </view>
                </view>
                <view class="bg-[#fff] sidebar-marign p-[20rpx] mt-[20rpx] rounded-[16rpx]">
                    <view class="flex justify-between text-[28rpx] leading-[32rpx]">
                        <view>{{ t('orderNo') }}</view>
                        <view class="flex items-center">
                            <text>{{ detail.order_no }}</text>
                            <text class="w-[2rpx] h-[20rpx] bg-[#999] mx-[10rpx]"></text>
                            <text class="text-[#EF900A]" @click="copy(detail.order_no)">复制</text>
                        </view>
                    </view>
                    <view v-if="detail.out_trade_no" class="flex justify-between text-[28rpx] leading-[32rpx] mt-[30rpx]">
                        <view>{{ t('orderTradeNo') }}</view>
                        <view>{{ detail.out_trade_no }}</view>
                    </view>
                    <view class="flex justify-between text-[28rpx] leading-[32rpx] mt-[30rpx]">
                        <view>{{ t('createTime') }}</view>
                        <view>{{ detail.create_time }}</view>
                    </view>
                    <view class="flex justify-between text-[28rpx] leading-[32rpx] mt-[30rpx]">
                        <view>{{ t('deliveryType') }}</view>
                        <view>{{ detail.delivery_type_name }}</view>
                    </view>
                    <view v-if="detail.pay" class="flex justify-between text-[28rpx] leading-[32rpx] mt-[30rpx]">
                        <view>{{ t('payTypeName') }}</view>
                        <view>{{ detail.pay.type_name }}</view>
                    </view>
                    <view v-if="detail.pay" class="flex justify-between text-[28rpx] leading-[32rpx] mt-[30rpx]">
                        <view>{{ t('payTime') }}</view>
                        <view>{{ detail.pay.pay_time }}</view>
                    </view>
                </view>
				<!-- 核销码 -->
				<block v-if="isShowVerify">
					<view class="bg-[#fff] sidebar-marign p-[20rpx] mt-[20rpx] rounded-[16rpx]" v-if="verifyInfo && verifyInfo.length">
						<swiper class="h-[450rpx]" circular indicator-dots="true" v-if="verifyInfo.length > 1">
							<swiper-item v-for="(item,index) in verifyInfo" :key="index">
								<view class="flex flex-col items-center justify-center">
									<image :src="item.qrcode" class="w-[300rpx]" mode="widthFix"></image>
								</view>
								<view class="w-full my-[20rpx] border-0 border-t-[2rpx] border-dashed border-[#e5e5e5] "></view>
								<view class="flex items-center justify-center">
									<text class="text-[28rpx] font-bold">{{item.code}}</text>
									<text class="text-[#666] text-[24rpx] ml-[10rpx] border-[2rpx] border-solid border-[#666] bg-[#f7f7f7] px-[12rpx] py-[6rpx] rounded" @click="copy(item.code)">复制</text>
								</view>
							</swiper-item>
						</swiper>
						<block v-else>
							<view class="flex flex-col items-center justify-center">
								<image :src="verifyInfo[0].qrcode" class="w-[300rpx]" mode="widthFix"></image>
							</view>
							<view class="w-full my-[20rpx] border-0 border-t-[2rpx] border-dashed border-[#e5e5e5] "></view>
							<view class="flex items-center justify-center">
								<text class="text-[28rpx] font-bold">{{verifyInfo[0].code}}</text>
								<text class="text-[#666] text-[24rpx] ml-[10rpx] border-[2rpx] border-solid border-[#666] bg-[#f7f7f7] px-[12rpx] py-[6rpx] rounded" @click="copy(verifyInfo[0].code)">复制</text>
							</view>
						</block>
						
					</view>
					<view class="bg-[#fff] sidebar-marign p-[20rpx] mt-[20rpx] rounded-[16rpx]">
						<view class="pb-[20rpx] pt-[10rpx] border-0 border-b-[2rpx] border-dashed border-[#e5e5e5]">核销信息</view>
						<view class="flex mt-[30rpx] justify-between leading-[32rpx]">
							<view class="text-[28rpx]">核销次数</view>
							<view class="price-font font-500 text-[28rpx]">
								{{'剩余'+(verifyGoodsData.num - verifyGoodsData.verify_count)+'次'}}/{{'共'+verifyGoodsData.num+'次'}}
							</view>
						</view>
						<view class="flex mt-[30rpx] justify-between leading-[32rpx]">
							<view class="text-[28rpx]">有效期</view>
							<view class="price-font font-500 text-[28rpx]">
								{{verifyGoodsData.verify_expire_time ? verifyGoodsData.verify_expire_time : '永久'}}
							</view>
						</view>
					</view>
				</block>
                <view class="bg-[#fff] sidebar-marign p-[20rpx] mt-[20rpx] rounded-[16rpx]">
                    <view class="flex justify-between leading-[32rpx]">
                        <view class="text-[28rpx]">{{ t('goodsMoney') }}</view>
                        <view class="price-font font-500">
							<text v-if="parseFloat(detail.point) > 0" class="text-[28rpx]">{{detail.point}}积分</text>
							<text v-if="parseFloat(detail.point) > 0 && parseFloat(detail.goods_money)" class="mx-[4rpx] text-[28rpx]">+</text>
							<block v-if="parseFloat(detail.goods_money) || !parseFloat(detail.point)">
								<text class="text-[28rpx]">￥</text>
								<text class="text-[28rpx]">{{ parseFloat(detail.goods_money).toFixed(2).split('.')[0] }}</text>
								<text class="text-[28rpx]">.{{ parseFloat(detail.goods_money).toFixed(2).split('.')[1] }}</text>
							</block>
                        </view>
                    </view>
                    <view class="flex justify-between text-[28rpx] leading-[32rpx]  mt-[30rpx]">
                        <view class="text-[28rpx]">{{ t('discountMoney') }}</view>
                        <view class="price-font font-500">
                            <text class="text-[24rpx]">-￥</text>
                            <text class="text-[28rpx]">{{ parseFloat(detail.discount_money).toFixed(2).split('.')[0] }}</text>
                            <text class="text-[22rpx]">.{{ parseFloat(detail.discount_money).toFixed(2).split('.')[1] }}</text>
                        </view>
                    </view>
                    <view class="flex justify-between text-[28rpx] leading-[32rpx] mt-[30rpx]">
                        <view class="text-[28rpx]">{{ t('deliveryMoney') }}</view>
                        <view class="price-font font-500">
                            <text class="text-[24rpx]">￥</text>
                            <text class="text-[28rpx]">{{ parseFloat(detail.delivery_money).toFixed(2).split('.')[0] }}</text>
                            <text class="text-[22rpx]">.{{ parseFloat(detail.delivery_money).toFixed(2).split('.')[1] }}</text>
                        </view>
                    </view>
                    <view class="flex justify-end text-[28rpx] leading-[32rpx] mt-[30rpx] items-baseline">
                        <view class="text-[28rpx]">{{ t('orderMoney') }}：</view>
                        <view class="text-[var(--price-text-color)] price-font font-500">
							<text v-if="parseFloat(detail.point) > 0" class="text-[28rpx]">{{detail.point}}积分</text>
							<text v-if="parseFloat(detail.point) > 0 && parseFloat(detail.order_money)" class="mx-[4rpx] text-[28rpx]">+</text>
							<block v-if="parseFloat(detail.order_money) || !parseFloat(detail.point)">
								<text class="text-[28rpx]">￥</text>
								<text class="text-[28rpx]">{{ parseFloat(detail.order_money).toFixed(2).split('.')[0] }}</text>
								<text class="text-[28rpx]">.{{ parseFloat(detail.order_money).toFixed(2).split('.')[1] }}</text>
							</block>
                        </view>
                    </view>
                </view>

                <view class="flex z-2 justify-between items-center bg-[#fff] fixed left-0 right-0 bottom-0 min-h-[100rpx] px-1 flex-wrap  pb-ios">
					<view class="flex">
						<view class="flex w-[70rpx] mr-[20rpx] flex-col justify-center items-center" @click="orderBtnFn('index')">
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
                        <view class="text-[26rpx] leading-[58rpx] px-[23rpx] border-[2rpx] border-solid border-[#999] rounded-full ml-[20rpx] text-[#666]" v-if="showLogistics(detail)" @click="orderBtnFn('logistics')">
                            {{ t('logisticsTracking') }}
                        </view>
                        <view class="text-[26rpx] leading-[58rpx] px-[23rpx] border-[2rpx] border-solid border-[#999] rounded-full ml-[20rpx] text-[#666]" v-if="detail.status == 1" @click="orderBtnFn('close')">{{ t('orderClose') }}</view>
                        <view class="text-[26rpx] leading-[58rpx] px-[23rpx] text-[#fff] primary-btn-bg rounded-full ml-[20rpx]" @click="orderBtnFn('pay')" v-if="detail.status == 1">{{ t('topay') }}</view>
                        <view class="text-[26rpx] leading-[58rpx] px-[23rpx] text-[#fff] primary-btn-bg rounded-full ml-[20rpx]" v-if="detail.status == 3" @click="orderBtnFn('finish')" >{{ t('orderFinish') }}</view>
                        <block v-if="detail.status == 5">
                            <view v-if="detail.is_evaluate == 1 || (detail.is_evaluate != 1 && evaluateConfig.is_evaluate == 1)"
                                class="text-[26rpx] leading-[52rpx] px-[23rpx] border-[2rpx] border-solid border-[#999] rounded-full ml-[20rpx] !text-[#666]"
                                @click="orderBtnFn('evaluate')">{{ detail.is_evaluate == 1 ? t('selectedEvaluate') : t('evaluate') }}</view>
                        </block>
                    </view>
                </view>
            </view>
            <view class="tab-bar-placeholder"></view>
            <pay ref="payRef" @close="payClose"></pay>
            <logistics-tracking ref="materialRef"></logistics-tracking>
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
import { getShopOrderDetail, orderClose, orderFinish } from '@/addon/shop/api/order';
import { getEvaluateConfig } from '@/addon/shop/api/shop';
import { getVerifyCode } from '@/app/api/verify';
import logisticsTracking from '@/addon/shop/pages/order/components/logistics-tracking/logistics-tracking.vue'
import useConfigStore from "@/stores/config";
import { topTabar } from '@/utils/topTabbar'

/********* 自定义头部 - start ***********/
const topTabarObj = topTabar()
let topTabbarData = topTabarObj.setTopTabbarParam({title:'订单详情'})
/********* 自定义头部 - end ***********/

const detail = ref<Object>({});
const loading = ref<boolean>(true);
const type = ref('')
const orderId = ref('')
const orderStepsShow = ref(false)
const evaluateConfig = ref<Object>({});

const sendMessageTitle = ref('')
const sendMessagePath = ref('')
const sendMessageImg = ref('')

onLoad((option) => {
	orderId.value = option.order_id;
	orderDetailFn(orderId.value);
});

getEvaluateConfig().then(({ data }) => {
    evaluateConfig.value = data
})

const orderDetailFn = (id) => {
	loading.value = true;
	getShopOrderDetail(id).then((res) => {
		detail.value = res.data;
		if(res.data.order_goods && res.data.order_goods.length && isShowVerify.value){
			let obj = {};
			obj.order_goods_id = res.data.order_goods[0].order_goods_id
			getVerifyCodeFn(obj);
		}

        sendMessageTitle.value = detail.value.order_goods[0].goods_name
		sendMessageImg.value = img(detail.value.order_goods[0].goods_image_thumb_small || '')

		loading.value = false;
	}).catch(() => {
		loading.value = false;
	})
}

//关闭订单
const close = (item: any) => {
	uni.showModal({
		title: '提示',
		content: '您确定要关闭该订单吗？',
        confirmColor: useConfigStore().themeColor['--primary-color'],
		success: res => {
			if (res.confirm) {
				orderClose(item.order_id).then((data) => {
					orderDetailFn(item.order_id);
				})
			}
		}
	})
}

//订单完成
const finish = (item: any) => {
    // 如果不在微信小程序中
    // #ifndef MP-WEIXIN
    uni.showModal({
        title: '提示',
        content: '您确定物品已收到吗？',
        confirmColor: useConfigStore().themeColor['--primary-color'],
        success: res => {
            if (res.confirm) {
                orderFinish(item.order_id).then((data) => {
                    orderDetailFn(item.order_id);
                })
            }
        }
    })
    // #endif

    // #ifdef MP-WEIXIN
    // 检测微信小程序是否已开通发货信息管理服务
    if (item.pay && item.pay.type == 'wechatpay' && item.is_trade_managed && wx.openBusinessView) {
        wx.openBusinessView({
            businessType: 'weappOrderConfirm',
            extraData: {
                merchant_id: item.mch_id,
                merchant_trade_no: item.out_trade_no
            },
            success: (res:any) => {
                orderFinish(item.order_id).then((data) => {
                    orderDetailFn(item.order_id);
                })
            },
            fail: (res:any) => {
                console.log('小程序确认收货组件打开失败 fail', res);
            }
        })
    }else{
        uni.showModal({
            title: '提示',
            content: '您确定物品已收到吗？',
            confirmColor: useConfigStore().themeColor['--primary-color'],
            success: res => {
                if (res.confirm) {
                    orderFinish(item.order_id).then((data) => {
                        orderDetailFn(item.order_id);
                    })
                }
            }
        })
    }
    // #endif
}

const goodsEvent = (id: number) => {
	if(detail.value.activity_type=='exchange') {
		redirect({
			url: '/addon/shop/pages/point/detail',
			param: {
				id: detail.value.relate_id
			}
		})
	}else{
		redirect({
			url: '/addon/shop/pages/goods/detail',
			param: {
				goods_id: id
			}
		})
	}

}

const payRef = ref(null)
const materialRef = ref(null)
const orderBtnFn = (type = '') => {
	if (type == 'pay')
		payRef.value?.open(detail.value.order_type, detail.value.order_id, `/addon/shop/pages/order/detail?order_id=${detail.value.order_id}`);
	else if (type == 'close') {
		close(detail.value);
	} else if (type == 'finish') {
		finish(detail.value);
	} else if (type == 'index') {
		redirect({
			url: '/addon/shop/pages/index',
            mode: 'reLaunch'
		});
	} else if (type == 'logistics') {
		if (detail.value.order_delivery.length > 0) {
			let params = {
				id: detail.value.order_delivery[0].id,
				mobile: detail.value.taker_mobile
			}
			let list:any = []
			detail.value.order_delivery.forEach((item: any, index: number) => {
                item.name = `包裹${index + 1}`
                list.push(item)
			})

			materialRef.value.open(params);
			materialRef.value.packageList = list
		}
	} else if (type == 'evaluate') {
		if (!detail.value.is_evaluate) {
			redirect({ url: '/addon/shop/pages/evaluate/order_evaluate', param: { order_id: detail.value.order_id } })
		} else {
			redirect({ url: '/addon/shop/pages/evaluate/order_evaluate_view', param: { order_id: detail.value.order_id } })
		}
	}
}
const dateFormat = (res, type) => {
	let data;
	if (res.indexOf('/') != -1) {
		data = res.split("/");
	} else if (res.indexOf('-') != -1) {
		data = res.split("-");
	}

	let time;
	const index = new Date(res).getDay();
	const week = ['周日', '周一', '周二', '周三', '周四', '周五', '周六']
    if (type == "yearMonthDay") {
        time = data[0] + '年' + data[1] + '月' + data[2] + '日';
    } else if (type == "yearMonthDayWeek") {
        time = data[0] + '年' + data[1] + '月' + data[2] + '日 ' + week[index];
    } else if (type == "monthDayWeek") {
        time = data[1] + '月' + data[2] + '日 ' + week[index];
    } else {
        time = data[1] + '月' + data[2] + '日';
    }

	return time;
}

const applyRefund = (orderGoodsId: number) => {
	redirect({
		url: '/addon/shop/pages/refund/apply',
		param: {
			order_id: detail.value.order_id,
			order_goods_id: orderGoodsId
		}
	})
}

const getAddress = () => {
	uni.openLocation({
		latitude: Number(detail.value.taker_latitude),
		longitude: Number(detail.value.taker_longitude),
		success: function () { }
	});
}

const showLogistics = (data: any) => {
    let status = false
    if (data.delivery_type != 'express') return false;
    for (let i = 0; i < data.order_delivery.length; i++) {
        let item = data.order_delivery[i];
        if (item.sub_delivery_type === 'express' && data.status === '3') {
            status = true;
            break;
        } else if (item.sub_delivery_type === 'express' && data.status === '5') {
            status = true;
            break;
        } else {
            status = false
        }
    }
    return status
}

/************ 虚拟商品核销-start ***************/ 
const verifyGoodsData = ref({}) //虚拟商品
const isShowVerify = computed(() => {
	let bool =  false;
	if(detail.value.order_goods.length == 1){
		verifyGoodsData.value = detail.value.order_goods[0]
		
		let data = detail.value.order_goods[0];
		bool = data.is_verify == 1 && data.goods_type == 'virtual' && data.delivery_status == 'delivery_finish' && detail.value.status == 3 ? true : false;
	}
	return bool
})
const verifyInfo = ref([])
const getVerifyCodeFn = (data:any) => {
	verifyInfo.value = [];
	
	getVerifyCode('shopVirtualGoods', data).then((res) => {
		verifyInfo.value = res.data;
	})
}
/************ 虚拟商品核销-end ***************/ 

</script>
<style lang="scss" scoped>
.text-item {
	overflow: hidden;
	text-overflow: ellipsis;
	display: -webkit-box;
	-webkit-line-clamp: 2;
	-webkit-box-orient: vertical;
}

.order-goods-item:nth-child(1) {
	margin-top: 0rpx;
}

.text-color {
	color: $u-primary;
}

.bg-color {
	background-color: $u-primary;
}

.bg-linear {
	background: linear-gradient( 94deg, #E73835 15%, #FE8448 87%);
}

.triangle {
	@apply relative;

	&::before {
		content: "";
		@apply absolute;
		width: 0;
		height: 0;
		position: absolute;
		bottom: -40rpx;
		border: 20rpx solid #EEF3FF;
		border-left-color: transparent;
		border-right-color: transparent;
		border-bottom-color: transparent;
	}
}

.tab-bar-placeholder {
	padding-bottom: calc(constant(safe-area-inset-bottom) + 100rpx);
	padding-bottom: calc(env(safe-area-inset-bottom) + 100rpx);
}
</style>