<template>
    <view :style="themeColor()" class="bg-[#f8f8f8] min-h-[100vh] overflow-hidden">
        <block v-if="!loading && verifyInfo && verifyInfo.value">
			<view class="w-full bg-[#fff] flex justify-center">
				<view class="text-[var(--primary-color)] absolute top-[40rpx] right-[30rpx] flex items-center" @click="redirect({url:'/app/pages/verify/record'})">
					<image class="w-[26rpx] h-[28rpx]" :src="img('static/resource/images/verify/history.png')"/>
					<text class="text-[26rpx] ml-[10rpx]">核销记录</text>
				</view>
				<view class="flex pt-[120rpx] pb-[30rpx] items-center">
					<view class="flex justify-center items-center flex-col pr-[30rpx] w-[130rpx]">
						<image class="w-[100rpx] h-[100rpx]" :src="img('static/resource/images/verify/yanzhenghexiaoma.png')"/>
						<view class="text-[26rpx] mt-[12rpx] h-[36rpx] leading-[36rpx]">验证核销码</view>
					</view>
					<image class="w-[74rpx] h-[12rpx] mb-[50rpx]" :src="img('static/resource/images/verify/youjiantou.png')"/>
					<view class="flex justify-center items-center flex-col pl-[30rpx] w-[130rpx]">
						<image class="w-[100rpx] h-[100rpx]" :src="img('static/resource/images/verify/hexiao.png')"/>
						<view class="text-[26rpx] mt-[12rpx] h-[36rpx] leading-[36rpx]">确定核销</view>
					</view>
				</view>
			</view>
			<view class="card-template mt-[20rpx] sidebar-marign">
				<view class="flex" :class="{'mb-[20rpx]': (verifyInfo.value.list.length - 1 != index)}" v-for="(item,index) in verifyInfo.value.list" :key="index">
					<image class="w-[150rpx] h-[150rpx] rounded-[8rpx]" mode="aspectFill" v-if="item.cover" :src="img(item.cover)"></image>
					<image class="w-[150rpx] h-[150rpx] rounded-[8rpx]" mode="aspectFill" v-else :src="img('addon/tourism/tourism/member/hotel.png')"></image>
					<view class="flex flex-col flex-1 ml-[20rpx] py-[4rpx]">
						<view class="leading-[1.3] multi-hidden">{{item.name}}</view>
						<view class="self-end text-[#626779] text-[28rpx] mt-[10rpx]">x1</view>
					</view>
				</view>
			</view>
			
			<view class="card-template mt-[20rpx] sidebar-marign">
				<view class="text-[32rpx] font-500 leading-[1.3]">核销信息</view>
				<view class="flex pt-[30rpx] items-center justify-between min-h-[36rpx]">
					<text class="text-[26rpx] text-[#626779]">核销类型</text>
					<view class="text-[26rpx] text-[#333333]">{{verifyInfo.type_name}}</view>
				</view>
				<view class="flex pt-[20rpx] items-center justify-between min-h-[36rpx]" v-for="(item,index) in verifyInfo.value.content.fixed">
					<text class="text-[26rpx] text-[#626779]">{{item.title}}</text>
					<view class="text-[26rpx] text-[#333333]">{{item.value}}</view>
				</view>
			</view>
			
			<view v-for="(item,index) in verifyInfo.value.content.diy" :key="index" class="card-template mt-[20rpx] sidebar-marign">
				<view class="text-[32rpx] font-500 leading-[1.3]">{{item.title}}</view>
				<view class="flex items-center justify-between min-h-[36rpx]" :class="{'pt-[30rpx]': subIndex==0, 'pt-[20rpx]': subIndex!=0}" v-for="(subItem,subIndex) in item.list" :key="subIndex">
					<text class="text-[26rpx] text-[#626779]">{{subItem.title}}</text>
					<text class="text-[26rpx] text-[#333333]">{{ subItem.value }}</text>
				</view>
			</view>
			
			<text class=" min-w-[630rpx] fixed bottom-[60rpx] confirmBtn text-[#fff] flex items-center justify-center !text-[32rpx] rounded-[50rpx] h-[88rpx] ml-[60rpx] mr-[60rpx]" @click="verifyFn">确定</text>
        </block>
        <u-loading-page :loading="loading" loading-text="" loadingColor="var(--primary-color)" iconSize="35"></u-loading-page>
    </view>
</template>

<script setup lang="ts">
    import { ref } from 'vue';
    import { onLoad ,onShow} from '@dcloudio/uni-app'
	import { img,redirect, getToken } from '@/utils/common';
    import { getVerifierInfo, getCheckVerifier, verify } from '@/app/api/verify'
    import { t } from '@/locale'

    const loading = ref(true)
	const code = ref('');
    onLoad((option:any)=> {
	    if (option.code) code.value = option.code;
	    // 小程序扫码进入
	    if (option.scene) {
		    let sceneParams: any = decodeURIComponent(option.scene).split('&');
		    if (sceneParams.length) {
			    sceneParams.forEach((item: any) => {
				    if (item.indexOf('code') != -1) code.value = item.split('-')[1];
			    });
		    }
	    }
    })
	
	onShow(() => {
		if(getToken()){
			checkIsVerifier();
			getVerifierInfoFn();
		}
	})
	
	// 检测是否是核销员
	const checkIsVerifier = () => {
		getCheckVerifier().then((res:any) =>{
			if(!res.data){
				uni.showToast({
					title: '非核销员无此权限',
					icon: 'none'
				});
				setTimeout(() => {
					if(getCurrentPages().length > 1){
                        uni.navigateBack({
                            delta: 1
                        });
                    }else{
                        redirect({
                            url: '/app/pages/member/index',
                            mode: 'reLaunch'
                        });
                    }
				}, 1000);
			}else{
				loading.value = false;
			}
		})
	}
	
	const verifyInfo = ref({})
	const getVerifierInfoFn = ()=>{
		loading.value = true;
		getVerifierInfo(code.value).then((res:any) =>{
			verifyInfo.value = res.data;
			loading.value = false;
		}).catch(() => {
            setTimeout(() => {
            	loading.value = false;
            	redirect({ url: '/app/pages/verify/index', param: {}, mode: 'redirectTo' })
            }, 1000);
        })
	}
	let isLoading = false;
	const verifyFn = ()=>{
		if(isLoading) return false;
		isLoading = true;
		
		verify(code.value).then((res:any) =>{
			uni.showToast({
				title: '核销成功',
				icon: 'none'
			});
			setTimeout(() => {
				isLoading = false;
				redirect({ url: '/app/pages/verify/index', param: {}, mode: 'redirectTo' })
			}, 1000);
		}).catch(() => {
            isLoading = false;
        })
	}
</script>

<style lang="scss" scoped>
	.confirmBtn{
		background: linear-gradient( 94deg, #FB7939 0%, #FE120E 99%), #EF000C;
	}
</style>