<template>
    <view class="bg-[#f8f8f8] min-h-screen overflow-hidden" :style="themeColor()">
		<view class="fixed left-0 right-0 top-0 z-99 bg-[#fff] px-[var(--sidebar-m)]">
			<view class="py-[14rpx] flex items-center justify-between">
				<view class="flex-1 flex items-center h-[60rpx] bg-[#F8F9FD] rounded-[30rpx] px-[20rpx] mr-[30rpx]">
					<u-input class="flex-1" maxlength="50" v-model="keyword" @confirm="searchTypeFn()" placeholder="请输入搜索关键词" placeholderClass="text-[#8288A2] text-[24rpx]" fontSize="26rpx"  clearable border="none"></u-input>
					<text class="nc-iconfont nc-icon-sousuo-duanV6xx1 text-[32rpx] ml-[18rpx] !text-[#999]" @click="searchTypeFn()"></text>
				</view>
				<view class="nc-iconfont nc-icon-riliV6xx !text-[30rpx] leading-[36rpx]" @click="handleSelect"></view>
			</view>
		</view>
        <mescroll-body ref="mescrollRef" top="108rpx" @init="mescrollInit" :down="{ use: false }" @up="geVerifyRecordFn">
        	<view class="sidebar-marign">
        		<block v-for="(item,index) in list"	:key="item.id">
        			<view class="w-full flex flex-col mb-[20rpx] card-template" @click="toLink(item)">
        				<view class="flex items-center mb-[30rpx] leading-[1]">
							<view class="nc-iconfont nc-icon-hexiaotaiV6xx !text-[26rpx] pr-[10rpx]"></view>
        					<text class="truncate text-[#333333] text-[26rpx]">核销码：{{ item.code }}</text>
        				</view>
							<view class="flex flex-1" v-for="(dataItem,dataIndex) in item.value.list" :key="dataIndex">
								<u--image class="rounded-[8rpx] overflow-hidden" width="120rpx" height="120rpx" :src="img(dataItem.cover ? dataItem.cover : '')" model="aspectFill">
									<template #error>
										<image class="w-[120rpx] h-[120rpx] rounded-[8rpx] overflow-hidden" :src="img('static/resource/images/diy/shop_default.jpg')" mode="aspectFill"></image>
									</template>
								</u--image>
								<view class="flex flex-col flex-1 justify-between ml-[20rpx] py-[4rpx]" >
									<view class="leading-[1.3] multi-hidden text-[28rpx] text-[#333]">{{dataItem.name}}</view>
									<view class="self-end text-[26rpx] mt-[10rpx] text-[#626779]">x1</view>
								</view>
							</view>
							<view class="flex bg-[#F8F9FD] py-[20rpx] px-[20rpx] rounded-[12rpx] mt-[20rpx]">
								<view class="flex-1">
									<view class="text-[22rpx] text-[#8288A2] mb-[10rpx] leading-[30rpx]">核销时间</view>
									<view class="text-[26rpx] text-[#333] leading-[36rpx]">{{ item.create_time }}</view>
								</view>
								<view class="flex-1">
									<view class="text-[22rpx] text-[#8288A2] mb-[10rpx] leading-[30rpx]">核销员</view>
									<view  class="text-[26rpx] text-[#333] leading-[36rpx]">{{ item.member ? item.member.nickname : '--' }}</view>
								</view>
							</view>
        			</view>
        		</block>
        	</view>
			<view class="mx-[30rpx] rounded-[16rpx] noData flex items-center justify-center" v-if="!list.length && loading">
				<mescroll-empty  :option="{tip : '暂无核销记录'}"></mescroll-empty>
			</view>
        </mescroll-body>
        <!-- 时间选择 -->
        <select-date ref="selectDateRef" @confirm="confirmFn" />
    </view>
</template>

<script setup lang="ts">
    import { ref, reactive, computed } from 'vue'
    import MescrollBody from '@/components/mescroll/mescroll-body/mescroll-body.vue'
    import MescrollEmpty from '@/components/mescroll/mescroll-empty/mescroll-empty.vue'
    import useMescroll from '@/components/mescroll/hooks/useMescroll.js'
    import { onPageScroll, onReachBottom } from '@dcloudio/uni-app'
    import { getVerifyRecords } from '@/app/api/verify'
    import { img, redirect } from '@/utils/common'
    import selectDate from '@/components/select-date/select-date.vue';

	const keyword = ref<string>('')
	const create_time = ref([])
    const list = ref<Array<Object>>([])
    const loading = ref<boolean>(false)
    const { mescrollInit, downCallback, getMescroll } = useMescroll(onPageScroll, onReachBottom)

    const geVerifyRecordFn = (mescroll) => {
    	loading.value = false;
    	let data : object = {
    		page: mescroll.num,
    		limit: mescroll.size,
			keyword:keyword.value,
			create_time: create_time.value
    	};

    	getVerifyRecords(data).then((res) => {
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

    const toLink = (data: AnyObject)=> {
        redirect({ url: '/app/pages/verify/detail', param: { code: data.code } })
    }
	
// 关键词搜索条件搜索
const searchTypeFn = () =>{
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

<style lang="scss" scoped>
:deep(.uni-picker-view-content){
    z-index: 10;
}
:deep(.uni-picker-view-indicator::before){
    border: none !important;
}
:deep(.uni-picker-view-indicator::after){
    border: none !important;
}
.noData{
	height: calc(100vh - 132rpx - constant(safe-area-inset-bottom));
	height: calc(100vh - 132rpx - env(safe-area-inset-bottom));
 }
</style>
