<template>
	<view class="bg-[#F6F6F6] min-h-[100vh]" :style="themeColor()">
		<template v-if="!loading">
		<view class="w-full bg-[#F6F6F6]">
			<view class="pb-[210rpx] relative" :style="headerStyle">
				<!-- #ifdef MP-WEIXIN -->
					<top-tabbar :data="param" :scrollBool="topTabarObj.getScrollBool()" class="top-header"/>
				<!-- #endif -->
				<view class="text-[70rpx] leading-[98rpx] text-[#fff] pl-[60rpx] font-600 pt-[77rpx]">{{pointInfo.point||0}}</view>
				<view class="flex items-center pl-[60rpx]">
					<image class="h-[36rpx] w-[36rpx]" :src="img('static/resource/images/member/point/icon.png')" mode="heightFix" />
					<view class="text-[26rpx] leading-[36rpx] text-[#fff] ml-[10rpx]">我的积分</view>
				</view>
				<view class="flex items-center absolute right-0 px-[14rpx] bg-color rounded-l-[35rpx]  text-[#fff] text-[24rpx] h-[50rpx] z-10" :style="{top: topStyle}" @click="toLink('/app/pages/member/point_detail')">
				     <text class="nc-iconfont nc-icon-jifenduihuanV6xx1 text-[28rpx] text-[#fff] mr-[10rpx]"></text>
				    <text class="text-[24rpx]">积分明细</text>
				</view>
			</view>
			<view class="sidebar-marign flex flex-col mt-[-178rpx] relative">
				<view class="w-[322rpx] h-[80rpx] leading-[80rpx] text-[26rpx] text-[#333] font-bold box-border pl-[30rpx]" 
				:style="{backgroundImage: 'url(' + img('static/resource/images/member/point/top_bg.png') + ') ',backgroundSize: '100% 100%',backgroundRepeat: 'no-repeat'}">
					积分详情
				</view>
				<view class="flex items-center px-[30rpx] rounded-[16rpx] !rounded-tl-none bg-[#fff] h-[173rpx] box-border">
					<view class="w-[196rpx] flex-shrink-0 text-center">
						<view class="text-[#333] text-[42rpx] leading-[59rpx] font-bold">{{pointInfo.point_get||0}}</view>
						<view class="mt-[8rpx] text-[#666] text-[26rpx] leading-[36rpx] font-400">累计积分</view>
					</view>
					<view class="w-[1rpx] h-[50rpx] flex-shrink-0 bg-[#EBEBEB] mx-[10rpx]"></view>
					<view class="w-[196rpx] flex-shrink-0 text-center">
						<view class="text-[#333] text-[42rpx] leading-[59rpx] font-bold">{{pointInfo.use||0}}</view>
						<view class="mt-[8rpx] text-[#666] text-[26rpx] leading-[36rpx] font-400">累计消费</view>
					</view>
					<view class="w-[1rpx] h-[50rpx] flex-shrink-0 bg-[#EBEBEB] mx-[10rpx]"></view>
					<view class="w-[196rpx] min-w-[209.33rpx] flex-shrink-0 text-center">
						<view class="text-[#333] text-[42rpx] leading-[59rpx] font-bold">{{pointInfo.point||0}}</view>
						<view class="mt-[8rpx] text-[#666] text-[26rpx] leading-[36rpx] font-400">可用积分</view>
					</view>
				</view>
			</view>
		</view>
		<view class="mt-[20rpx] sidebar-marign p-[30rpx] pb-[70rpx] box-border rounded-[16rpx] bg-[#fff]">
			<view class="text-[32rpx] leading-[45rpx] font-bold">热门活动</view>
			<view class="mt-[50rpx] flex justify-between">
				<view class="w-[200rpx] h-[253rpx] box-border pt-[69rpx] relative text-center" 
				:style="{backgroundImage: 'url(' + img('static/resource/images/member/point/activity_1.png') + ') ',
				backgroundSize: '100% 100%',
				backgroundRepeat: 'no-repeat'}">
					<image class="h-[78rpx] w-[78rpx] absolute left-[65rpx] top-[-21rpx]" :src="img('static/resource/images/member/point/activity_icon_1.png')" mode="heightFix" />
					<view class="text-[28rpx] leading-[39rpx] text-[#333]">每日赚积分</view>
					<view class="mt-[10rpx] text-[24rpx] leading-[34rpx] text-[#999] font-500">每日签到</view>
					<view class="w-full flex justify-center mt-[20rpx]">
						<button class="h-[40rpx] !m-0 rounded-[40rpx] text-[26rpx] leading-[40rpx] !text-[#fff] !" shape="circle" 
						:style="{background: 'linear-gradient( 94deg, #FB7939 0%, #FE120E 99%), #EF000C'}" @click="toLink('/app/pages/member/sign_in')">去签到</button>
					</view>
				</view>
				<view class="w-[200rpx] h-[253rpx] box-border pt-[69rpx] relative text-center"
				:style="{backgroundImage: 'url(' + img('static/resource/images/member/point/activity_2.png') + ') ',
				backgroundSize: '100% 100%',
				backgroundRepeat: 'no-repeat'}">
					<image class="h-[78rpx] w-[78rpx] absolute left-[65rpx] top-[-21rpx]" :src="img('static/resource/images/member/point/activity_icon_2.png')" mode="heightFix" />
					<view class="text-[28rpx] leading-[39rpx] text-[#333]">积分当钱花</view>
					<view class="mt-[10rpx] text-[24rpx] leading-[34rpx] text-[#999] font-500">抵扣部分费用</view>
					<view class="w-full flex justify-center mt-[20rpx]">
						<button class="h-[40rpx] !m-0 rounded-[40rpx] text-[26rpx] leading-[40rpx] !text-[#fff] !" shape="circle" 
						:style="{background: 'linear-gradient( 94deg, #FB7939 0%, #FE120E 99%), #EF000C'}" @click="toLink('/addon/shop/pages/point/index')">去兑换</button>
					</view>
				</view>
				<view class="w-[200rpx] h-[253rpx] box-border pt-[69rpx] relative text-center"
				:style="{backgroundImage: 'url(' + img('static/resource/images/member/point/activity_3.png') + ') ',
				backgroundSize: '100% 100%',
				backgroundRepeat: 'no-repeat'}">
					<image class="h-[78rpx] w-[78rpx] absolute left-[65rpx] top-[-21rpx]" :src="img('static/resource/images/member/point/icon.png')" mode="heightFix" />
					<view class="text-[28rpx] leading-[39rpx] text-[#333]">购物返积分</view>
					<view class="mt-[10rpx] text-[24rpx] leading-[34rpx] text-[#999] font-500">下单得积分</view>
					<view class="w-full flex justify-center mt-[20rpx]">
						<button class="h-[40rpx] !m-0 rounded-[40rpx] text-[26rpx] leading-[40rpx] !text-[#fff] !" shape="circle" 
						:style="{background: 'linear-gradient( 94deg, #FB7939 0%, #FE120E 99%), #EF000C'}" @click="toLink('/addon/shop/pages/goods/list')">去逛逛</button>
					</view>
				</view>
			</view>
		</view>	
		
		<view class="mt-[20rpx] mx-[30rpx] p-[30rpx] box-border rounded-[16rpx] bg-[#fff]" v-if="pointList.length">
			<view class="flex justify-between items-center">
				<view class="text-[32rpx] leading-[45rpx] font-bold">做任务领积分</view>
				<!-- <view class="flex items-center text-[#666]">
					<text class=" text-[26rpx] leading-[36rpx] mr-[10rpx]">更多</text>
					<text class=" text-[18rpx] leading-[36rpx] iconfont iconxiayibu1 text-[#999]"></text>
				</view> -->
			</view>
			<block v-for="(item,index) in pointList">
				<view class="flex items-center justify-between mt-[30rpx]">
					<view class="flex items-center flex-1">
						<image class="h-[62rpx] w-[62rpx]" :src="img(item.icon||'')" mode="heightFix" />
						<view class="flex flex-col ml-[20rpx]">
							<view class="flex">
								<text class="text-[28rpx] leading-[39rpx]">{{item.title}}</text>
								<!-- <image class="h-[28rpx] w-[28rpx] ml-[10rpx] mr-[6rpx]" :src="img('static/resource/images/member/point/icon.png')" mode="heightFix" /> -->
								<!-- <text class="text-[#EF000C] text-[28rpx] leading-[39rpx]">+10</text> -->
							</view>
							<view class="mt-[10rpx] text-[#999] text-[24rpx] leading-[34rpx] font-400">{{item.desc}}</view>
						</view>
					</view>
					<button v-if="item.button" class="h-[56rpx] !m-0 rounded-[40rpx] text-[26rpx] leading-[56rpx] !text-[#fff] !" shape="circle"
					:style="{background: 'linear-gradient( 94deg, #FB7939 0%, #FE120E 99%), #EF000C'}" @click="toLink(item.button.wap_redirect)">{{item.button.text}}</button>
				</view>
			</block>
		</view>
		</template>
		<u-loading-page bg-color="rgb(248,248,248)" :loading="loading" loadingText="" fontSize="16" color="#303133"></u-loading-page>
	</view>
</template>

<script setup lang="ts">
import { computed, ref } from 'vue'
import { onLoad } from '@dcloudio/uni-app'
import { t } from '@/locale'
import { redirect, img,pxToRpx } from '@/utils/common';
import { getMemberAccountPointcount,getTaskPoint } from '@/app/api/member';
import { topTabar } from '@/utils/topTabbar'

/********* 自定义头部 - start ***********/
const topTabarObj = topTabar()
let param = topTabarObj.setTopTabbarParam({title:'我的积分'})
/********* 自定义头部 - end ***********/

// 获取系统状态栏的高度
let menuButtonInfo:any = {};
// 如果是小程序，获取右上角胶囊的尺寸信息，避免导航栏右侧内容与胶囊重叠(支付宝小程序非本API，尚未兼容)
// #ifdef MP-WEIXIN || MP-BAIDU || MP-TOUTIAO || MP-QQ
menuButtonInfo = uni.getMenuButtonBoundingClientRect();
// #endif
const headerStyle = computed(()=>{
	return { 
		backgroundImage: 'url(' + img('static/resource/images/member/point/point_bg.png') + ') ',
		backgroundSize: 'cover',
		backgroundRepeat: 'no-repeat',
		backgroundPosition: 'bottom',
		// paddingTop:Object.keys(menuButtonInfo).length?(Number(menuButtonInfo.height) * 2 + menuButtonInfo.top * 2 + 77)+'rpx':'77rpx',
	}
})
const topStyle = computed(() => {
	let style = ''
	style = Object.keys(menuButtonInfo).length?(pxToRpx(Number(menuButtonInfo.height)) + pxToRpx(menuButtonInfo.top) +  38) + 'rpx;':'38rpx'
	return style
})

//个人积分信息
const pointInfo = ref({});
//积分任务
const pointList = ref([]);
const loading = ref(true)
onLoad(async()=>{
	let pointInfoRes :any = await getMemberAccountPointcount()
	let pointListRes :any = await getTaskPoint()
	pointInfo.value = pointInfoRes.data
	pointList.value = pointListRes.data
	loading.value = false
})
const toLink=(url='',param={})=>{
	redirect({ url, param })
}
</script>

<style lang="scss" scoped>
	.bg-color{
	    background-color: rgba(102,102,102,0.4);
	}
</style>
