<template>
    <view :style="themeColor()">
        <view class="bg-[#f8f8f8] min-h-screen overflow-hidden" v-if="!loading">
            <view class="bg-[#fff] mx-[30rpx] p-[20rpx] mt-[30rpx] rounded-[10rpx]" v-for="(item,index) in detail.refund_log">
                <view class="text-sm">{{item.main_type_name}} {{item.main_name}}</view>
                <view class="text-xs my-[6rpx] text-[#909399]">{{item.create_time}}</view>
                <view class="text-sm">{{item.type_name || '--'}}</view>
            </view>
            <view class="pt-[140rpx]"></view>
            <view class="tab-bar bg-[#fff] fixed left-0 right-0 bottom-0 min-h-[120rpx] px-1 flex items-center">
                <button class="bg-[var(--primary-color)] text-[#fff] h-[80rpx] leading-[80rpx] rounded-[100rpx] text-[28rpx] mx-0 flex-1" @click="redirect({url: '/addon/shop/pages/refund/detail', param: { order_refund_no: orderRefundNo }})">返回详情</button>
            </view>
        </view>

        <u-loading-page bg-color="rgb(248,248,248)" :loading="loading" loadingText="" fontSize="16" color="#303133"></u-loading-page>
    </view>
</template>

<script setup lang="ts">
import { ref, reactive, computed } from 'vue';
import { onLoad } from '@dcloudio/uni-app'
import { t } from '@/locale'
import { img, redirect } from '@/utils/common';
import { getRefundDetail } from '@/addon/shop/api/refund';

const detail = ref<Object>({});
const loading = ref<boolean>(true);
const orderRefundNo = ref('')

onLoad((option: any) => {
	orderRefundNo.value = option.order_refund_no;
	refundDetailFn(option.order_refund_no);
});

const refundDetailFn = (refundNo) => {
	loading.value = true;
	getRefundDetail(refundNo).then((res) => {
		detail.value = res.data;
		loading.value = false;
	}).catch(() => {
		loading.value = false;
	})
}
</script>
<style lang="scss" scoped>
.tab-bar {
	padding-bottom: calc(constant(safe-area-inset-bottom));
	padding-bottom: calc(env(safe-area-inset-bottom));
}
</style>
