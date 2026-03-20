<template>
    <u-popup :show="show" @close="show = false" mode="bottom" :round="10">
        <view @touchmove.prevent.stop class="popup-common">
            <view class="title">配送记录</view>
            <scroll-view scroll-y="true" class="h-[50vh]">
                <view class="px-[20rpx] py-[40rpx]" v-if="deliveryRecord && deliveryRecord.length">

                    <view class="flex" v-for="(item,index) in deliveryRecord" :key="index">
						<view class="flex-shrink-0">
							<view class="w-[8rpx] h-[8rpx] mx-auto bg-[#999] rounded-full"></view>
							<view v-if="index + 1 != deliveryRecord.length" class="w-[2rpx] h-[60rpx] bg-[#ccc] mx-auto my-[20rpx]"></view>
						</view>
						<view class="flex-1 ml-[20rpx]">
							<view class="text-[26rpx] flex items-start justify-between mt-[-10rpx]">
								<view class="leading-[34rpx] multi-hidden">
                                    <text>{{ item.operate_desc }}</text>
                                    <text v-if="item.main_type == 'rider'">，配送员：{{ item.main_name }}</text>
                                </view>
                                <text class="flex-shrink-0 ml-[30rpx]">{{ item.create_time }}</text>
							</view>
						</view>
					</view>
                </view>
                <view  class="empty-page-popup !mt-0 empty-height" v-else>
                    <image class="img" :src="img('static/resource/images/system/empty.png')" model="aspectFit" />
                    <view class="desc">暂无配送记录</view>
                </view>
            </scroll-view>
        </view>
    </u-popup>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { img } from '@/utils/common';

const show = ref(false)
const deliveryRecord = ref<any>(null)
const activeStep = ref(0)
const open = (data: any) => {
    show.value = true;
    deliveryRecord.value = data;
    activeStep.value = deliveryRecord.value ? deliveryRecord.value.length - 1 : 0;
}

defineExpose({
    open
})
</script>

<style lang="scss" scoped>
.empty-height{
    height: calc(100% - 100rpx);
}
</style>