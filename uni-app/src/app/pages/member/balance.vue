<template>
    <view class="min-h-[100vh] !bg-[#F6F6F6]" :style="themeColor()" v-if="memberStore.info">
			<view class="fixed w-full z-2  !bg-[#F6F6F6]">
				<view class="pb-[203rpx] text-[#fff] w-full" :style="headerStyle">
					<!-- #ifdef MP-WEIXIN -->
					<top-tabbar :data="param" class="top-header"/>
					<!-- #endif -->
				    <view class="leading-[39rpx] text-[28rpx] pl-[53rpx] pt-[79rpx]">{{t('accountBalance')}}</view>
					<view class="flex items-baseline pl-[53rpx]">
						<text class="text-[40rpx] leading-[56rpx]">￥</text>
						<text class="text-[70rpx] leading-[98rpx]">{{ memberStore.info ? moneyFormat((parseFloat(memberStore.info.balance) + parseFloat(memberStore.info.money)).toString()) : '0.00' }}</text>
					</view>
				</view>
				<view class="sidebar-marign py-[30rpx] bg-[#fff] rounded-[16rpx] px-[40rpx] box-border w-[calc(100% - 60rpx)] mt-[-112rpx]">
					<view class="flex flex-col items-center w-full" @click="redirect({ url: '/app/pages/member/detailed_account', param: { type : 'money' } })"  :class="{'pt-[12rpx]': !Object.keys(cashOutConfigObj).length || (Object.keys(cashOutConfigObj).length && !systemStore.siteAddons.includes('recharge') && cashOutConfigObj.is_open != 1)}">
						<view class=" text-[#999] text-[24rpx] leading-[34rpx] font-400">{{t('money')}}</view>
						<view class="flex items-baseline text-[#333]">
							<text class="text-[26rpx] leading-[36rpx]">￥</text>
							<text class="text-[44rpx] leading-[62rpx] font-bold">{{ moneyFormat(memberStore.info?.money)|| '0.00' }}</text>
						</view>
						
					</view>
					<view class="mt-[50rpx] flex justify-between" v-if="Object.keys(cashOutConfigObj).length && (systemStore.siteAddons.includes('recharge') || cashOutConfigObj.is_open == 1)">
						<button :class="{'!w-[630rpx]': cashOutConfigObj.is_open != 1}" class="w-[280rpx] h-[66rpx] rounded-[40rpx] text-[30rpx] !bg-[#fff] !text-[var(--primary-color)] leading-[64rpx] !m-0 border-[2rpx] border-[var(--primary-color)] border-solid box-border" shape="circle" v-if="systemStore.siteAddons.includes('recharge')"
                    @click="redirect({url: '/addon/recharge/pages/recharge'})">充值</button>
						<view v-if="cashOutConfigObj.is_open == 1" :class="{'!w-[630rpx]': !systemStore.siteAddons.includes('recharge')}" class="text-center w-[280rpx] h-[66rpx] rounded-[40rpx] text-[30rpx] !text-[#fff] leading-[66rpx] !m-0"
						style="background: linear-gradient( 94deg, #FB7939 0%, #FE120E 99%), #EF000C;" @click="applyCashOut">{{t('cashOut')}}</view>
					</view>
				</view>
				<view class="px-[var(--sidebar-m)] box-border mt-[20rpx] flex justify-between items-center">
					<scroll-view :scroll-x="true" scroll-with-animation :scroll-into-view="'id' + (subActive>3 ? subActive - 2 : 0)" class="!h-[100%] flex-1">
						<view class="flex whitespace-nowrap">
							<view :id="'id' + index" class="text-[26rpx] leading-[70rpx] text-[#666] font-400" :class="{ 'class-select': fromType === item.key,'ml-30rpx':index}" @click="fromTypeFn(item.key,index)" v-for="(item, index) in accountTypeList">{{ item.name }}</view>
						</view>
					</scroll-view>
					<view class="flex items-center" @click="handleSelect">
						<view class="text-[26rpx] text-[#333] mr-[10rpx]">日期</view>
						<view class="nc-iconfont nc-icon-riliV6xx !text-[28rpx] leading-[36rpx]"></view>
					</view>
				</view>
			</view>
            <mescroll-body ref="mescrollRef" @init="mescrollInit" :down="{ use: false }" height="auto" @up="getListFn" :top="mescrollTop">
            	<view class="sidebar-marign pt-[20rpx] body-bottom" v-if="list.length">
					<view  v-for="(item,index) in list" :key="item.id" class="w-full h-[120rpx] flex justify-between items-center bg-[#fff] box-border p-[20rpx] rounded-[16rpx]" :class="{'mt-[20rpx]':index>0}">
						<view class="flex items-center">
							<view class="w-[80rpx] h-[80rpx] text-center rounded-[40rpx] text-[40rpx] font-bold leading-[80rpx] text-[#fff]"
							:class="{'bg-[#EF000C]' :item.account_data > 0&&item.account_type!='money', 'bg-[#03B521]':item.account_data <= 0&&item.account_type!='money','bg-[#1379FF]':item.account_type=='money'}">
								{{item.account_type=='money'?'提':item.account_data > 0?'收':'支'}}
							</view>
							<view class="flex flex-col ml-[20rpx]">
								<view class="text-[#000] text-[26rpx] leading-[36rpx]">{{item.from_type_name}}</view>
								<view class="text-[#999] text-[24rpx] leading-[34rpx] mt-[4rpx] font-400">{{item.create_time}}</view>
							</view>
						</view>
						<view class="text-right">
							<view class="text-[36rpx] leading-[50rpx]"
							:class="{'text-[#EF000C]' :item.account_data > 0&&item.account_type!='money', 'text-[#03B521]':item.account_data <= 0&&item.account_type!='money'}">{{ item.account_data > 0 ? '+' + item.account_data : item.account_data }}</view>
							<view class="text-[#999] text-[24rpx] leading-[34rpx] mt-[4rpx] font-400">
								<text class="mr-[15rpx]">剩余余额</text>
								<text>{{item.account_sum}}</text>
							</view>
						</view>
					</view>
				</view>
				<view class="box-border pt-[20rpx] px-[30rpx]  body-bottom" :style="{'height':'calc(100vh - '+mescrollTop +')'}" v-if="!list.length && !loading &&!listLoading">
					<view class="h-full  rounded-[16rpx] flex items-center justify-center">
						<mescroll-empty></mescroll-empty>
					</view>
				</view>

            </mescroll-body>
			<u-loading-page bg-color="rgb(248,248,248)" :loading="loading" loadingText="" fontSize="16" color="#303133"></u-loading-page>
			<!-- <pay ref="payRef" @close="rechargeLoading = false"></pay> -->
			<!-- 时间选择 -->
			<select-date ref="selectDateRef" @confirm="confirmFn" />
    </view>
</template>

<script setup lang="ts">
	import { ref, reactive,computed } from 'vue'
	import { t } from '@/locale'
	import { moneyFormat, redirect, img,pxToRpx } from '@/utils/common';
	import { cashOutConfig,getBalanceListAll } from '@/app/api/member';
	import MescrollBody from '@/components/mescroll/mescroll-body/mescroll-body.vue';
	import MescrollEmpty from '@/components/mescroll/mescroll-empty/mescroll-empty.vue';
	import useMescroll from '@/components/mescroll/hooks/useMescroll.js';
	import { onShow, onPageScroll, onReachBottom } from '@dcloudio/uni-app';
	import useMemberStore from '@/stores/member'
    import useSystemStore from '@/stores/system'
    import { topTabar } from '@/utils/topTabbar'
	import selectDate from '@/components/select-date/select-date.vue';

	const { downCallback,mescrollInit, getMescroll } = useMescroll(onPageScroll, onReachBottom);
	const memberStore = useMemberStore()
    const systemStore = useSystemStore()
    
    /********* 自定义头部 - start ***********/
    const topTabarObj = topTabar()
    let param = topTabarObj.setTopTabbarParam({title:'我的余额'})
    /********* 自定义头部 - end ***********/

	const cashOutConfigObj = reactive({})
	onShow(() => {
        cashOutConfig().then((res) => {
            for (let key in res.data) {
                cashOutConfigObj[key] = res.data[key];
            }

        })
    })

	// 获取系统状态栏的高度
	let menuButtonInfo:any = {};
	// 如果是小程序，获取右上角胶囊的尺寸信息，避免导航栏右侧内容与胶囊重叠(支付宝小程序非本API，尚未兼容)
	// #ifdef MP-WEIXIN || MP-BAIDU || MP-TOUTIAO || MP-QQ
	menuButtonInfo = uni.getMenuButtonBoundingClientRect();
	// #endif
	const headerStyle = computed(()=>{
		return {
			backgroundImage: 'url(' + img('static/resource/images/member/balance_bg.png') + ') ',
			backgroundSize: 'cover',
			backgroundRepeat: 'no-repeat',
			backgroundPosition: 'bottom',
		}
	})

	const mescrollTop = computed(()=>{
		if(Object.keys(cashOutConfigObj).length && (systemStore.siteAddons.includes('recharge') || cashOutConfigObj.is_open == 1)){
			if(Object.keys(menuButtonInfo).length){
				return (pxToRpx(Number(menuButtonInfo.height)) + pxToRpx(menuButtonInfo.top) +pxToRpx(8)+669)+'rpx'
			}else{
				return '669rpx'
			}
		}else {
			if(Object.keys(menuButtonInfo).length){
				return (pxToRpx(Number(menuButtonInfo.height)) + pxToRpx(menuButtonInfo.top) +pxToRpx(8)+566)+'rpx'
			}else{
				return '566rpx'
			}
		}
	})

	//获取数据来源类型
	const accountTypeList = ref([
		{name:'全部',key:''},
		{name:'收入',key:'income'},
		{name:'支出',key:'disburse'},
		{name:'提现',key:'cash_out'},
	])
	const fromType = ref('')
	const create_time = ref([])
	//来源类型
	const subActive = ref(0)
	const fromTypeFn = (key,index)=>{
		fromType.value = key
		subActive.value = index
		getMescroll().resetUpScroll();
	}

	const applyCashOut = () => {
		uni.setStorageSync('cashOutAccountType', 'money')
		redirect({ url: '/app/pages/member/apply_cash_out' })
	}

	const list = ref<Array<any>>([]),
		loading = ref<boolean>(true),
		listLoading = ref<boolean>(true),
		mescrollRef = ref(null);

	interface mescrollStructure {
		num : number,
		size : number,
		endSuccess : Function,
		[propName : string] : any
	}

	const getListFn = (mescroll : mescrollStructure) => {
		listLoading.value = true;
		let data : Object = {
			page: mescroll.num,
			limit: mescroll.size,
			trade_type:fromType.value,
			create_time: create_time.value

		};
		interface acceptingDataStructure {
			data : acceptingDataItemStructure,
			msg : string,
			code : number
		}
		interface acceptingDataItemStructure {
			data : [],
			[propName : string] : number | string | object
		}

		getBalanceListAll(data).then((res : acceptingDataStructure) => {
			let newArr = res.data.data;
			mescroll.endSuccess(newArr.length);
			//设置列表数据
			if (mescroll.num == 1) {
				list.value = []; //如果是第一页需手动制空列表
			}
			list.value = list.value.concat(newArr);
			listLoading.value = false;
			loading.value = false;
		}).catch(() => {
			listLoading.value = false;
			loading.value = false;
			mescroll.endErr(); // 请求失败, 结束加载
		})
	}
	//日期筛选
	const selectDateRef = ref()
	const handleSelect = () =>{
		selectDateRef.value.show = true
	}
	// 确定时间筛选
	const confirmFn = (data) =>{
		create_time.value = data;
		list.value = []
		getMescroll().resetUpScroll();
	}
</script>

<style lang="scss" scoped>
	.class-select {
		position: relative;
		font-weight: 500;
		color: var(--primary-color);
		&::before {
			content: "";
			position: absolute;
			bottom: 0;
			height: 4rpx;
			border-radius: 4rpx;
			background-color: var(--primary-color);
			width: 40rpx;
			left: 50%;
			transform: translateX(-50%);
		}
	}
	:deep(.uni-scroll-view){
		overflow: auto hidden !important;
	}
	.body-bottom{
		padding-bottom:  calc(20rpx + constant(safe-area-inset-bottom));
		padding-bottom:  calc(20rpx + env(safe-area-inset-bottom));
	}
	.pl-20rpx{
		padding-left: 20rpx;
	}

</style>
