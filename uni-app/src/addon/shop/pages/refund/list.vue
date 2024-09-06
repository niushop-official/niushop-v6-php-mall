<template>
	<view class="bg-[#F6F6F6] min-h-screen overflow-hidden" :style="themeColor()">

		<mescroll-body ref="mescrollRef" @init="mescrollInit" :down="{ use: false }" @up="getRefundListFn">
			<view class="sidebar-marign pt-[20rpx]" v-if="list.length">
				<view class="mb-[20rpx] card-template" v-for="(item,index) in list" :key="index">
					<view @click="toLink(item)">
						<view class="flex justify-between items-center">
							<view class="text-[#626779] text-[24rpx] font-400 leading-[34rpx]">
								<text>{{ t('orderNo') }}：{{ item.order_refund_no }}</text>
								<text class="text-[#626779] text-[24rpx] font-400 nc-iconfont nc-icon-fuzhiV6xx1 ml-[11rpx]" @click.stop="copy(item.order_refund_no)"></text>
							</view>
							<view class="text-[24rpx] leading-[34rpx] text-[#333]">{{item.refund_type_name}}</view>
						</view>
						<view class="flex mt-[20rpx]">
							<view class="w-[150rpx] h-[150rpx]">
								<u--image class="rounded-[10rpx] overflow-hidden" width="150rpx" height="150rpx" :src="img(item.order_goods.goods_image_thumb_small ? item.order_goods.goods_image_thumb_small : '')" model="aspectFill">
									<template #error>
                                        <image class="rounded-[10rpx] overflow-hidden w-[150rpx] h-[150rpx]" :src="img('static/resource/images/diy/shop_default.jpg')" mode="aspectFill"></image>
									</template>
								</u--image>
							</view>
							<view class="ml-[20rpx] flex flex-1 flex-col box-border">
								<view class="flex justify-between items-baseline">
									<view class="text-[28rpx] leading-[40rpx] text-[#333] max-w-[322rpx]  truncate font-500">{{ item.order_goods.goods_name }}</view>
									<view class="text-right leading-[42rpx] ml-[10rpx]">
										<text class="text-[20rpx] price-font">￥</text>
										<text class="text-[32rpx] font-500 price-font">{{parseFloat(item.order_goods.price).toFixed(2).split('.')[0] }}</text>
										<text class="text-[22rpx] font-500 price-font">.{{parseFloat(item.order_goods.price).toFixed(2).split('.')[1] }}</text>
									</view>
								</view>
								<view class="flex justify-between items-center mt-[20rpx]">
									<view class="text-[26rpx] truncate font-400 text-[#333] leading-[36rpx] max-w-[369rpx]">{{ item.order_goods.sku_name }}</view>
									<text class="text-right text-[26rpx] font-400 w-[90rpx] leading-[36rpx]">x{{ item.order_goods.num }}</text>
								</view>
								<view class="text-[26rpx] font-400 leading-[36rpx] text-[#333] mt-[10rpx]">退款状态：{{ item.status_name }}</view>
							</view>
						</view>
					</view>
					<view class="flex justify-end items-baseline mt-[30rpx]">
						<view class="flex items-center">
							<text class="text-[26rpx] leading-[36rpx] mr-[4rpx]">{{ t('refundMoney') }}：</text>
							<view class="price-font leading-[42rpx]">
								<text class="text-[20rpx]">￥</text>
								<text class="text-[32rpx] font-500">{{ parseFloat(item.apply_money).toFixed(2).split('.')[0] }}</text>
								<text class="text-[22rpx] font-500">.{{ parseFloat(item.apply_money).toFixed(2).split('.')[1] }}</text>
							</view>
						</view>
					</view>
					<view class="mt-[20rpx] flex flex-wrap justify-end">
						<view class="text-[24rpx] text-[#626779] h-[56rpx] leading-[52rpx] box-border px-[23rpx] border-[2rpx] border-solid border-[#8288A2] rounded-full ml-[20rpx]" @click.stop="refundBtnFn(item,'cancel')" v-if="['6','7','8','-1'].indexOf(item.status) == -1">{{t('refundApply')}}</view>
						<view v-if="['3'].indexOf(item.status) != -1" class="text-[24rpx] text-[#626779] h-[56rpx] box-border  leading-[52rpx] px-[23rpx] border-[2rpx] border-solid border-[#8288A2] rounded-full ml-[20rpx]" @click.stop="refundBtnFn(item,'edit')" >编辑退款信息</view>
						<view v-if="['2'].indexOf(item.status) != -1" class=" text-[24rpx] text-[#626779] h-[56rpx]  box-border leading-[52rpx] px-[23rpx] border-[2rpx] border-solid border-[#8288A2] rounded-full ml-[20rpx]" @click.stop="refundBtnFn(item,'logistics')">填写发货物流</view>
						<view v-if="['5'].indexOf(item.status) != -1" class="text-[24rpx] text-[#626779] h-[56rpx] box-border  leading-[52rpx] px-[23rpx] border-[2rpx] border-solid border-[#8288A2] rounded-full ml-[20rpx]" @click.stop="refundBtnFn(item,'editLogistics')">编辑发货物流</view>
					</view>
				</view>
			</view>
			<view class="flex justify-center items-center w-[100%] h-[100vh]" v-if="!list.length && loading">
				<mescroll-empty :option="{tip : '暂无订单'}"></mescroll-empty>
			</view>
			
		</mescroll-body>
		<u-modal :show="cancelRefundShow" confirmColor="var(--primary-color)" :content="t('cancelRefundContent')" :showCancelButton="true" :closeOnClickOverlay="true" @cancel="refundCancel" @confirm="refundConfirm"></u-modal>
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
	import { getRefundList, closeRefund } from '@/addon/shop/api/refund';
	import MescrollBody from '@/components/mescroll/mescroll-body/mescroll-body.vue';
	import MescrollEmpty from '@/components/mescroll/mescroll-empty/mescroll-empty.vue';
	import useMescroll from '@/components/mescroll/hooks/useMescroll.js';
	import { onPageScroll, onReachBottom } from '@dcloudio/uni-app';

	const { mescrollInit, downCallback, getMescroll } = useMescroll(onPageScroll, onReachBottom);
	const list = ref<Array<Object>>([]);
	const loading = ref<boolean>(false);
	const cancelRefundShow = ref(false);

	const getRefundListFn = (mescroll) => {
		loading.value = false;
		let data : object = {
			page: mescroll.num,
			limit: mescroll.size
		};

		getRefundList(data).then((res) => {
			let newArr = (res.data.data as Array<Object>);
			//设置列表数据
			if (mescroll.num == 1) {
				list.value = []; //如果是第一页需手动制空列表
			}
			list.value = list.value.concat(newArr);
			mescroll.endSuccess(newArr.length);
			loading.value = true;
		}).catch(() => {
			loading.value = true;
			mescroll.endErr(); // 请求失败, 结束加载
		})
	}

	const toLink = (data) => {
		redirect({ url: '/addon/shop/pages/refund/detail', param: { order_refund_no : data.order_refund_no } })
	}

	let currRefundOn = "";
	const refundBtnFn = (data, type) => {
		if(type == 'cancel'){
			currRefundOn = data.order_refund_no;
			cancelRefundShow.value = true;
		}else if(type == 'edit'){
			redirect({ url: '/addon/shop/pages/refund/edit_apply', param: { order_refund_no : data.order_refund_no } })
		}else if(type == 'logistics'){
			redirect({ url: '/addon/shop/pages/refund/detail', param: { order_refund_no : data.order_refund_no, type: 'logistics' } })
		}else if(type == 'editLogistics'){
			redirect({ url: '/addon/shop/pages/refund/detail', param: { order_refund_no : data.order_refund_no, type: 'logistics', is_edit_delivery: true } })
		}
	}

	const refundConfirm = ()=>{
		closeRefund(currRefundOn).then((res) => {
			cancelRefundShow.value = false;
			getMescroll().resetUpScroll();
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
</style>
