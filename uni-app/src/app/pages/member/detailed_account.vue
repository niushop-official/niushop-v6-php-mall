<template>
	<view class="min-h-[100vh] bg-[#F6F6F6]  overflow-hidden" :style="themeColor()">
		<view class="fixed left-0 right-0 top-0 z-99">
            <view class="bg-[#fff] px-[var(--sidebar-m)] h-[88rpx] flex items-center">
                <view class="flex-1 flex items-center h-[60rpx] bg-[#F8F9FD] rounded-[30rpx] px-[20rpx]">
                    <u-input class="flex-1" maxlength="50" v-model="keyword" @confirm="searchTypeFn()" placeholder="请输入搜索关键词" placeholderClass="text-[#999] text-[24rpx]" fontSize="26rpx"  clearable border="none"></u-input>
                    <text class="nc-iconfont nc-icon-sousuo-duanV6xx1 text-[28rpx] ml-[32rpx] !text-[#999]" @click="searchTypeFn()"></text>
                </view>
            </view>
            <view class="tab-style-1 pt-[14rpx] bg-[#fff]">
                <view class="tab-left">
                    <view class="tab-left-item" :class="{'!text-primary class-select':fromType.from_type === item.from_type && fromType.account_data_gt === item.account_data_gt}" v-for="(item,index) in accountTypeList" :key="index" @click="fromTypeFn(item,index)">{{ item.name }}</view>
                </view>
                <view class="tab-right" @click="handleSelect">
                    <view class="tab-right-date">日期</view>
                    <view class="nc-iconfont nc-icon-riliV6xx tab-right-icon"></view>
                </view>
            </view>
        </view>
		<mescroll-body ref="mescrollRef" @init="mescrollInit" :down="{ use: false }" @up="getListFn" top="196rpx">
			<view v-for="(item,index) in list" :key="item.id" class="sidebar-marign mb-[20rpx] card-template relative">
				<view class="flex items-center justify-between mb-[10rpx]">
					<view class="text-[28rpx] font-500 text-[#333] leading-[40rpx]">{{item.from_type_name}}</view>
					<view class="absolute right-[30rpx] top-[30rpx] text-[36rpx] font-500 text-[#03B521] leading-[50rpx]" :class="{'!text-[var(--price-text-color)]':item.account_data > 0}">{{ item.account_data > 0 ? '+' + item.account_data : item.account_data }}</view>
				</view>
				<view  class="text-[24rpx] leading-[34rpx] text-[#8288A2] mb-[10rpx]" v-if="item.memo">{{item.memo}}</view>
				<view class="text-[24rpx] leading-[34rpx] text-[#8288A2]">{{item.create_time}}</view>
			</view>
			<view class="mx-[30rpx]  rounded-[16rpx] noData flex items-center justify-center" v-if="!list.length && loading">
				<mescroll-empty  :option="{tip : tip}"></mescroll-empty>
			</view>
		</mescroll-body>
        <!-- 时间选择 -->
		<select-date ref="selectDateRef" @confirm="confirmFn" />
	</view>
</template>

<script setup lang="ts">
	import { ref,nextTick, reactive } from 'vue'
	import { t } from '@/locale'
	import MescrollBody from '@/components/mescroll/mescroll-body/mescroll-body.vue';
	import MescrollEmpty from '@/components/mescroll/mescroll-empty/mescroll-empty.vue';
	import useMescroll from '@/components/mescroll/hooks/useMescroll.js';
	import { getBalanceList, getMoneyList, getCommissionList} from '@/app/api/member';
	import { onPageScroll, onReachBottom, onLoad, onShow } from '@dcloudio/uni-app';
    import selectDate from '@/components/select-date/select-date.vue';

	const { mescrollInit, downCallback, getMescroll } = useMescroll(onPageScroll, onReachBottom);

	const type = ref('')
	const tip = ref('')

	onLoad((options: any) => {
		type.value = options.type || 'balance';
        nextTick(()=>{
            setTimeout(()=>{
                if(type.value == 'balance'){
                    tip.value = t('balanceEmptyTip')
                }else if(type.value == 'money'){
                    tip.value = t('moneyEmptyTip')
                }else if(type.value == 'commission'){
                    tip.value = t('commissionEmptyTip')
                }
            },100);
        })
	});

	const keyword = ref<string>('')
	const create_time = ref([])
	//来源类型
	const fromType = ref({
		from_type:'',
		account_data_gt:''
	})
	const accountTypeList = ref([
		{name:'全部',from_type:'',account_data_gt: ''},
		{name:'佣金',from_type:'',account_data_gt: 0},
		{name:'提现',from_type:'cash_out',account_data_gt: ''},
	])
	const list = ref<Array<any>>([]),
		loading = ref<boolean>(false),
		mescrollRef = ref(null);

	interface mescrollStructure {
		num : number,
		size : number,
		endSuccess : Function,
		[propName : string] : any
	}

	const getListFn = (mescroll : mescrollStructure) => {
		loading.value = false;
		let data : Object = {
			page: mescroll.num,
			page_size: mescroll.size,
			keyword:keyword.value,
			create_time:create_time.value,
			from_type:fromType.value.from_type,
			account_data_gt: fromType.value.account_data_gt
		};
		interface acceptingDataStructure {
			data : acceptingDataItemStructure,
			msg : string,
			code : number
		}
		interface acceptingDataItemStructure {
			data : object,
			[propName : string] : number | string | object
		}

		let fnList = (params : any) => { };
		if (type.value == 'balance') fnList = getBalanceList;
		else if (type.value == 'money') fnList = getMoneyList;
		else if (type.value == 'commission') fnList = getCommissionList;

		fnList(data).then((res : acceptingDataStructure) => {
			let newArr = res.data.data;
			mescroll.endSuccess(newArr.length);
			//设置列表数据
			if (mescroll.num == 1) {
				list.value = []; //如果是第一页需手动制空列表
			}
			list.value = list.value.concat(newArr);
			loading.value = true;
		}).catch(() => {
			loading.value = true;
			mescroll.endErr(); // 请求失败, 结束加载
		})
	}

// 关键词搜索条件搜索
const searchTypeFn = () =>{
	list.value = [];
    getMescroll().resetUpScroll();
}
// 类型搜索
const fromTypeFn = (data : any ,index : number)=>{
	fromType.value.from_type = data.from_type
	fromType.value.account_data_gt = data.account_data_gt
	list.value = []
	getMescroll().resetUpScroll();
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

<style lang="scss">
.noData{
	height: calc(100vh - 210rpx - constant(safe-area-inset-bottom));
	height: calc(100vh - 210rpx - env(safe-area-inset-bottom));
 }
</style>
