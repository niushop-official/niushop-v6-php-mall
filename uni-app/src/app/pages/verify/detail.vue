<template>
    <view :style="themeColor()" class="bg-[#f8f8f8] min-h-[100vh] overflow-hidden">
        <block v-if="!loading">
			<view class="pt-[20rpx] sidebar-marign">
				<view class="flex flex-col card-template">
					<view class="flex" :class="{'mb-[20rpx]': verifyInfo.value.list.length-1 != index}" v-for="(item,index) in verifyInfo.value.list" :key="index">
						<image class="w-[150rpx] h-[150rpx] rounded-[8rpx]" mode="aspectFill" v-if="item.cover" :src="img(item.cover)"></image>
						<image class="w-[150rpx] h-[150rpx] rounded-[8rpx]" mode="aspectFill" v-else :src="img('addon/tourism/tourism/member/hotel.png')"></image>
						<view class="flex flex-col flex-1 ml-[20rpx] py-[4rpx]">
							<view class="leading-[1.3] text-[28rpx] multi-hidden">{{item.name}}</view>
							<view class="self-end text-[#626779] text-[26rpx] mt-[20rpx]">x1</view>
						</view>
					</view>
				</view>
				
				<view class="flex flex-col card-template mt-[20rpx]">
					<view class="text-[32rpx] text-[#333333] font-500 leading-[1.2] mb-[30rpx]">核销信息</view>
					<view class="flex justify-between h-[36rpx] items-center">
						<text class="text-[26rpx] text-[#626779]">核销类型</text>
						<view class="text-[26rpx] text-[#333]">{{verifyInfo.type_name}}</view>
					</view>
					<view class="flex justify-between h-[36rpx] items-center mt-[20rpx]">
						<text class="text-[26rpx] text-[#626779]">核销状态</text>
						<view class="text-[26rpx] text-[#333]">已核销</view>
					</view>
					<view class="flex justify-between h-[36rpx] items-center mt-[20rpx]">
						<text class="text-[26rpx] text-[#626779]">核销时间</text>
						<view class="text-[#333333] text-[26rpx]">{{verifyInfo.create_time}}</view>
					</view>
					<view class="flex justify-between h-[36rpx] items-center mt-[20rpx]">
						<text class="text-[26rpx] text-[#626779]">核销人员</text>
						<view class="text-[#333333] text-[26rpx]">{{verifyInfo.member ? verifyInfo.member.nickname : '--'}}</view>
					</view>
					<view class="flex items-center h-[36rpx] justify-between mt-[20rpx]" v-for="(item,index) in verifyInfo.value.content.fixed">
						<text class="text-[26rpx] text-[#626779]">{{item.title}}</text>
						<view class="text-[26rpx] text-[#333]">{{item.value}}</view>
					</view>
				</view>
				
				<view v-for="(item,index) in verifyInfo.value.content.diy" :key="index" class="card-template mt-[20rpx]">
					<view class="text-[32rpx] text-[#333333] font-500 leading-[1.2] mb-[30rpx]">{{item.title}}</view>
					<view class="flex items-center h-[36rpx] justify-between mt-[20rpx]" v-for="(subItem,subIndex) in item.list" :key="subIndex" :class="{'mt-30rpx' : subIndex == '0'}">
						<text class="text-[26rpx] text-[#626779]">{{subItem.title}}</text>
						<view class="text-[26rpx] text-[#333]">{{subItem.value}}</view>
					</view>
				</view>
			</view>

        </block>
        <u-loading-page :loading="loading" loading-text="" loadingColor="var(--primary-color)" iconSize="35"></u-loading-page>
    </view>
</template>

<script setup lang="ts">
    import { ref } from 'vue';
    import { onLoad,onShow } from '@dcloudio/uni-app'
	import { img,redirect, getToken } from '@/utils/common';
    import { getVerifyDetail } from '@/app/api/verify'
    import { t } from '@/locale'

    const loading = ref(true)
	const code = ref('');
    onLoad((option:any)=> {
	    if (option.code) code.value = option.code;
    })
	
	onShow(() => {
		if(getToken()){
			getVerifyDetailFn();
		}
	})
	
	const verifyInfo = ref({})
	const getVerifyDetailFn = ()=>{
		loading.value = true;
		getVerifyDetail(code.value).then((res:any) =>{
			verifyInfo.value = res.data;
			loading.value = false;
		})
	}
</script>
