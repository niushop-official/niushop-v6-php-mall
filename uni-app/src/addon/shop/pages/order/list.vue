<template>
	<view class="bg-[#F6F6F6] min-h-screen overflow-hidden order-list" :style="themeColor()">
		<view class="fixed left-0 top-0 right-0 z-10" v-if="statusLoading">
			<scroll-view :scroll-x="true" class="box-border px-[30rpx] bg-white">
				<view class="flex whitespace-nowrap justify-between py-[24rpx]">
					<view :class="['text-[28rpx] leading-[40rpx]', { 'class-select': orderState === item.status.toString() }]" @click="orderStateFn(item.status)" v-for="(item, index) in orderStateList">{{ item.name }}</view>
				</view>
			</scroll-view>
		</view>

		<mescroll-body ref="mescrollRef" top="88rpx" @init="mescrollInit" :down="{ use: false }" @up="getShopOrderFn">
			<view class="sidebar-marign mt-[20rpx]" v-if="list.length">
				<template v-for="(item, index) in list" :key="index">
					<view class="mb-[20rpx] card-template">
						<view @click.stop="toLink(item)">
							<view class="flex justify-between items-center">
								<view class="text-[#626779] text-[24rpx] font-400 leading-[34rpx]">
									<text>{{ t('orderNo') }}：{{ item.order_no }}</text>
									<text class="text-[#626779] text-[24rpx] font-400 nc-iconfont nc-icon-fuzhiV6xx1 ml-[11rpx]" @click.stop="copy(item.order_no)"></text>
								</view>
								<view class="text-[#333] text-[24rpx] leading-[34rpx]" :class="{'text-primary': item.status  == 1,'text-[#626779]' :item.status  == 5 || item.status  == -1}">{{ item.status_name.name }}</view>
							</view>
							<view class="flex box-border mt-[20rpx]" v-for="(subitem, index) in item.order_goods" :key="index">
								<view class="w-[150rpx] h-[150rpx]">
									<u--image class="rounded-[10rpx] overflow-hidden" radius="10rpx" width="150rpx" height="150rpx" :src="img(subitem.goods_image_thumb_small ? subitem.goods_image_thumb_small : '')" mode="aspectFill">
										<template #error>
											<image class="w-[150rpx] h-[150rpx] rounded-[10rpx] overflow-hidden" :src="img('static/resource/images/diy/shop_default.jpg')" mode="aspectFill"></image>
										</template>
									</u--image>
								</view>
								<view class="ml-[20rpx] flex flex-1 flex-col box-border">
									<view class="flex justify-between items-baseline">
										<view class="max-w-[322rpx] text-[28rpx] leading-[40rpx] font-500 truncate text-[#333]">{{ subitem.goods_name }}</view>
										<block v-if="item.activity_type == 'exchange'">
											<view class="text-right  ml-[10rpx] leading-[42rpx]" v-if="parseFloat(subitem.price)">
												<text class="text-[20rpx] price-font">￥</text>
												<text class="text-[32rpx] font-500 price-font">{{parseFloat(subitem.price).toFixed(2).split('.')[0] }}</text>
												<text class="text-[22rpx] font-500 price-font">.{{parseFloat(subitem.price).toFixed(2).split('.')[1] }}</text>
											</view>
										</block>
										<block v-else>
											<view class="text-right leading-[42rpx] ml-[10rpx]">
												<text class="text-[20rpx] price-font">￥</text>
												<text class="text-[32rpx] font-500 price-font">{{parseFloat(subitem.price).toFixed(2).split('.')[0] }}</text>
												<text class="text-[22rpx] font-500 price-font">.{{parseFloat(subitem.price).toFixed(2).split('.')[1] }}</text>
											</view>
										</block>
									</view>
									<view class="flex justify-between items-center text-[#333] mt-[20rpx]">
										<view class="text-[26rpx] text-[#333] font-400 truncate leading-[36rpx] max-w-[369rpx]">{{ subitem.sku_name }}</view>
										<text class="text-right text-[26rpx] font-400 w-[90rpx] leading-[36rpx]">x{{ subitem.num }}</text>
									</view>
									<view class="text-[26rpx] font-400 leading-[36rpx] text-[#333] mt-[10rpx]" v-if="item.delivery_type != 'virtual'">
										{{t('deliveryType') }} ： {{item.delivery_type_name}}
									</view>
									<view class="text-[26rpx] font-400 leading-[36rpx] text-[#333] mt-[10rpx]" v-else>{{t('createTime') }} ：{{item.create_time}}</view>
								</view>
							</view>
						</view>
						<view class="flex justify-end items-center mt-[30rpx]">
							<view class="flex items-baseline">
								<view class="text-[22rpx] font-400 text-[#8288A2] leading-[30rpx] mr-[6rpx]" v-if="parseFloat(item.delivery_money)">{{ t('service') }}</view>
								<view class="text-[22rpx] font-400 leading-[30rpx] ">{{ t('actualPayment') }}：</view>
								<view class="leading-[1]">
									<block v-if="item.activity_type == 'exchange'">
										<text class="text-[32rpx] mr-[2rpx] leading-[40rpx] price-font">{{ item.point }}</text>
										<text  class="text-[20rpx] leading-[28rpx]">{{ t('point') }}</text>
										<block v-if="parseFloat(item.order_money)">
											<text class="text-[20rpx] mx-[4rpx] leading-[28rpx]">+</text>
											<text class="text-[32rpx] font-500 leading-[40rpx] price-font">{{ parseFloat(item.order_money).toFixed(2).split('.')[0]  }}</text>
											<text class="text-[22rpx] font-500 leading-[28rpx] price-font">.{{ parseFloat(item.order_money).toFixed(2).split('.')[1]  }}</text>
											<text class="text-[20rpx] font-500 leading-[28rpx] ml-[2rpx]">{{ t('money') }}</text>
										</block>
									</block>
									<block v-else>
										<text class="text-[20rpx] leading-[26rpx] price-font">￥</text>
										<text class="text-[32rpx] font-500 leading-[40rpx] price-font">{{ parseFloat(item.order_money).toFixed(2).split('.')[0]  }}</text>
										<text class="text-[22rpx] font-500 leading-[28rpx] price-font">.{{ parseFloat(item.order_money).toFixed(2).split('.')[1]  }}</text>
									</block>
								</view>
							</view>
						</view>
						<view class="flex justify-end text-[28rpx] mt-[20rpx] items-center" v-if="(item.status == 1) || (item.status == 3) || (item.status == 5 && evaluateConfig.is_evaluate == 1)">
							<view class="text-[26rpx] font-400 leading-[56rpx] h-[56rpx] min-w-[150rpx] text-center border-[2rpx] border-solid border-[#8288A2] rounded-full text-[#626779] box-border" v-if="item.status == 1" @click.stop="orderBtnFn(item, 'close')">{{ t('orderClose') }}</view>
							<view class="text-[26rpx] font-400 leading-[56rpx] h-[56rpx] min-w-[150rpx] text-center border-[2rpx] border-solid text-[#fff] border-primary bg-primary rounded-full ml-[20rpx] box-border" @click.stop="orderBtnFn(item, 'pay')" v-if="item.status == 1">{{ t('topay') }}</view>
							<view class="text-[26rpx] font-400 leading-[56rpx] h-[56rpx] min-w-[150rpx] text-center border-[2rpx] border-solid text-[#fff] border-primary bg-primary rounded-full ml-[20rpx] box-border" @click.stop="orderBtnFn(item, 'finish')" v-if="item.status == 3">{{ t('orderFinish') }}</view>
							<view class="text-[26rpx] font-400 leading-[56rpx] h-[56rpx] min-w-[150rpx] text-center border-[2rpx] border-solid border-[#8288A2] rounded-full ml-[20rpx]  text-[#626779] box-border" v-if="item.status == 5 && evaluateConfig.is_evaluate == 1" @click.stop="orderBtnFn(item, 'evaluate')">{{ item.is_evaluate == 1 ? t('selectedEvaluate') : t('evaluate') }}</view>
						</view>
					</view>
				</template>
			</view>
			<view class="mx-[30rpx] mt-[20rpx] rounded-[16rpx] flex items-center justify-center noData" v-if="!list.length && loading">
				<mescroll-empty :option="{tip : '暂无订单'}"></mescroll-empty>
			</view>
		</mescroll-body>
		<pay ref="payRef" @close="payClose"></pay>
		<!-- #ifdef MP-WEIXIN -->
		<!-- 小程序隐私协议 -->
		<wx-privacy-popup ref="wxPrivacyPopupRef"></wx-privacy-popup>
		<!-- #endif -->
	</view>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { t } from '@/locale'
import { img, redirect,copy } from '@/utils/common'
import { getShopOrderStatus, getShopOrder, orderClose, orderFinish } from '@/addon/shop/api/order';
import { getEvaluateConfig } from '@/addon/shop/api/shop';
import MescrollBody from '@/components/mescroll/mescroll-body/mescroll-body.vue';
import MescrollEmpty from '@/components/mescroll/mescroll-empty/mescroll-empty.vue';
import useMescroll from '@/components/mescroll/hooks/useMescroll.js';
import { onLoad, onPageScroll, onReachBottom } from '@dcloudio/uni-app';
import useConfigStore from "@/stores/config";
import { hideMenuItems } from 'weixin-js-sdk';

const { mescrollInit, downCallback, getMescroll } = useMescroll(onPageScroll, onReachBottom);
const list = ref<Array<Object>>([]);
const loading = ref<boolean>(false);
const statusLoading = ref<boolean>(false);
const orderState = ref('')
const orderStateList = ref([]);
const evaluateConfig = ref("")

const mch_id = ref('')
const isTradeManaged = ref(false)

onLoad((option) => {
	orderState.value = option.status || "";
	evaluateEvent()
	getShopOrderStatusFn();
});

const evaluateEvent = () => {
	getEvaluateConfig().then((data) => {
		evaluateConfig.value = data.data
	})
}

const getShopOrderFn = (mescroll) => {
	loading.value = false;
	let data: object = {
		page: mescroll.num,
		limit: mescroll.size,
		status: orderState.value
	};

	getShopOrder(data).then((res) => {
		let newArr = (res.data.data as Array<Object>);
		//设置列表数据
		if (mescroll.num == 1) {
			list.value = []; //如果是第一页需手动制空列表
		}
		list.value = list.value.concat(newArr);
		mescroll.endSuccess(newArr.length);
		
        mch_id.value = res.data.mch_id;
        isTradeManaged.value = res.data.is_trade_managed;
		loading.value = true;
	}).catch(() => {
		loading.value = true;
		mescroll.endErr(); // 请求失败, 结束加载
	})
}

const getShopOrderStatusFn = () => {
	statusLoading.value = false;
	orderStateList.value = [];
	let obj = { name: '全部', status: '' };
	orderStateList.value.push(obj);

	getShopOrderStatus().then((res) => {
		Object.values(res.data).forEach((item, index) => {
			orderStateList.value.push(item);
		});
		statusLoading.value = true;
	}).catch(() => {
		statusLoading.value = true;
	})
}

const orderStateFn = (status) => {
	orderState.value = status.toString();
	list.value = [];
	getMescroll().resetUpScroll();
};

const toLink = (data: any) => {
	redirect({ url: '/addon/shop/pages/order/detail', param: { order_id: data.order_id } })
}

// 支付
const payRef = ref(null)
const orderBtnFn = (data, type = '') => {
	if (type == 'pay')
		payRef.value?.open(data.order_type, data.order_id, `/addon/shop/pages/order/detail?order_id=${data.order_id}`);
	else if (type == 'close') {
		close(data);
	} else if (type == 'finish') {
		finish(data);
	} else if (type == 'evaluate') {
		if (!data.is_evaluate) {
			redirect({ url: '/addon/shop/pages/evaluate/order_evaluate', param: { order_id: data.order_id } })
		} else {
			redirect({ url: '/addon/shop/pages/evaluate/order_evaluate_view', param: { order_id: data.order_id } })
		}
	}
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
					getMescroll().resetUpScroll();
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
					getMescroll().resetUpScroll();
				})
			}
		}
	})
    // #endif

    // #ifdef MP-WEIXIN
    // 检测微信小程序是否已开通发货信息管理服务
    if (item.pay && item.pay.type == 'wechatpay' && isTradeManaged.value && wx.openBusinessView) {
        wx.openBusinessView({
            businessType: 'weappOrderConfirm',
            extraData: {
                merchant_id: mch_id.value,
                merchant_trade_no: item.out_trade_no
            },
            success: (res:any) => {
                orderFinish(item.order_id).then((data) => {
                    getMescroll().resetUpScroll();
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
                        getMescroll().resetUpScroll();
                    })
                }
            }
        })
    }
    // #endif
}
</script>
<style>
.order-list .mescroll-body {
	padding-bottom: constant(safe-area-inset-bottom) !important;
	padding-bottom: env(safe-area-inset-bottom) !important;
}
.order-list :deep(.u-count-down__text){
	font-size: 24rpx !important;
	color:#EF000C !important;
}
</style>
<style lang="scss" scoped>
.text-color {
	color: var(--primary-color);
}

.bg-color {
	background-color: var(--primary-color);
}

.class-select {
	position: relative;
	font-weight: 500;
	color: var(--primary-color);
	&::before {
		content: "";
		position: absolute;
		bottom: -14rpx;
		height: 6rpx;
		border-radius: 4rpx;
		background-color: var(--primary-color);
		width: 40rpx;
		left: 50%;
		transform: translateX(-50%);
	}
}
.noData{
	height: calc(100vh - 130rpx - constant(safe-area-inset-bottom));
	height: calc(100vh - 130rpx - env(safe-area-inset-bottom));
 }
</style>
