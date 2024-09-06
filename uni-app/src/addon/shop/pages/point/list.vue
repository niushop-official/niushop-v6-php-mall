<template>
	<view class="bg-gray-100 min-h-[100vh]" :style="themeColor()">
		<view class="fixed left-0 right-0 top-0 product-warp bg-[#fff] px-[30rpx] z-10">
			<view class="py-[14rpx] flex items-center justify-between">
				<view class="flex-1 flex items-center h-[64rpx] bg-[#F6F8F8] rounded-[33rpx] px-[32rpx]">
					<u-input class="flex-1" maxlength="50" v-model="goods_name" @confirm="searchTypeFn('total_order_num')" placeholder="请搜索您想要的商品" placeholderClass="text-[#a5a6a6] text-[26rpx]" fontSize="26rpx"  clearable border="none"></u-input>
					<text class="nc-iconfont nc-icon-sousuo-duanV6xx1 text-[32rpx] ml-[18rpx] !text-[#999]" @click="searchTypeFn('total_order_num')"></text>
				</view>
				<!-- <view :class="['nc-iconfont  text-[44rpx]', listType ? 'nc-icon-yingyongzhongxinV6xx' : 'nc-icon-yingyongliebiaoV6xx']" @click="listIconBtn"></view> -->
			</view>
			<view class="flex justify-between tems-center py-[22rpx] px-[20rpx]">
				<view class=" flex items-center justify-between text-[24rpx] text-[#666] flex-1">
					<text  :class="{ 'text-[#303133] ': searchType == 'total_order_num' }" @click="searchTypeFn('total_order_num')">综合排序</text>
					<view class="flex items-center" :class="{ 'text-[#303133]': searchType == 'total_exchange_num' }" @click="searchTypeFn('total_exchange_num')">
						<text class="mr-[4rpx]">销量</text>
						<text v-if="(sale_num != 'asc') && ( sale_num != 'desc')" class="text-[30rpx] text-[#666] nc-iconfont nc-icon-shangxiaxuanzeV6xx"></text>
						<text v-else-if="sale_num == 'asc'" class="text-[18rpx] w-[30rpx] h-[30rpx] flex items-center justify-center text-[#666] nc-iconfont font-bold nc-icon-shangV6xx-1 font-bold"></text>
						<text v-else-if="sale_num == 'desc'" class="text-[18rpx] w-[30rpx] h-[30rpx] flex items-center justify-center text-[#666] font-bold nc-iconfont nc-icon-xiaV6xx"></text>
					</view>
					<view class="flex items-center" :class="{'text-[#303133]': searchType == 'price' }" @click="searchTypeFn('price')">
						<text class=" mr-[4rpx]">价格</text>
						<text v-if="(price != 'asc') && ( price != 'desc')" class="text-[30rpx] text-[#666] nc-iconfont nc-icon-shangxiaxuanzeV6xx"></text>
						<text v-else-if="price == 'asc'" class="text-[18rpx] w-[30rpx] h-[30rpx] flex items-center justify-center font-bold text-[#666] nc-iconfont nc-icon-shangV6xx-1 font-bold"></text>
						<text v-else-if="price == 'desc'" class="text-[18rpx] w-[30rpx] h-[30rpx] flex items-center justify-center font-bold text-[#666] nc-iconfont nc-icon-xiaV6xx"></text>
					</view>
				</view>
			</view>
		</view>

		<mescroll-body ref="mescrollRef" top="160rpx" bottom="50px" @init="mescrollInit" :down="{ use: false }" @up="getAllAppListFn">
			<view v-if="goodsList.length" class="sidebar-marign flex justify-between flex-wrap">
				<template v-for="(item, index) in goodsList">
					<view class="goods-item-style-two flex flex-col bg-[#fff] box-border rounded-[16rpx] overflow-hidden mt-[20rpx]" @click="toDetail(item.id)">
						<u--image width="100%" height="350rpx" :src="img(item.goods_cover_thumb_mid ? item.goods_cover_thumb_mid : '')" model="aspectFill">
							<template #error>
								<image class="w-[100%] h-[350rpx]" :src="img('static/resource/images/diy/shop_default.jpg')" mode="aspectFill"></image>
							</template>
						</u--image>
						<view class="px-[16rpx] flex-1 pt-[10rpx] pb-[20rpx] flex flex-col justify-between">
                            <view class="text-[#333] leading-[40rpx] text-[28rpx] multi-hidden">{{item.names}}</view>
                            <view class="text-[24rpx] font-400 leading-[34rpx] mt-[10rpx] text-[#999]">已兑{{item.total_exchange_num}}人</view>
                            <view class="flex justify-between flex-wrap items-center mt-[16rpx]" >
                                <view class="flex items-baseline">
                                    <image class="h-[28rpx] self-center" :src="img('addon/shop/exchange/point-icon.png')" mode="heightFix" />
                                    <text class="text-[var(--price-text-color)] price-font text-[32rpx] ml-[2rpx]">{{ item.point  }}</text>
                                    <text class="text-[#333] font-400 text-[24rpx]" v-if="item.price&&item.price>0">+</text>
                                    <text class="text-[var(--price-text-color)] font-400 text-[22rpx]" v-if="item.price&&item.price>0">￥{{ parseFloat(item.price).toFixed(2)  }}</text>
                                </view>
                                <view class="w-[80rpx] h-[46rpx] text-[28rpx] leading-[46rpx] !text-[#fff] m-0 rounded-full !bg-[var(--primary-color)] font-400 remove-border text-center" shape="circle">兑换</view>
                            </view>
                        </view>
					</view>
				</template>
			</view>
			<view class="mx-[30rpx] mt-[20rpx]  rounded-[16rpx] noData flex items-center justify-center" v-if="!goodsList.length && loading">
				<mescroll-empty :option="{tip : '暂无商品'}"></mescroll-empty>
			</view>
		</mescroll-body>

		<tabbar />
	</view>
</template>

<script setup lang="ts">
import { reactive, ref, onMounted } from 'vue'
import { t } from '@/locale'
import { redirect, img } from '@/utils/common';
import { getExchangeGoodsList } from '@/addon/shop/api/point';
import MescrollBody from '@/components/mescroll/mescroll-body/mescroll-body.vue';
import MescrollEmpty from '@/components/mescroll/mescroll-empty/mescroll-empty.vue';
import useMescroll from '@/components/mescroll/hooks/useMescroll.js';
import { onShow, onPageScroll, onReachBottom } from '@dcloudio/uni-app';

const { mescrollInit, downCallback, getMescroll } = useMescroll(onPageScroll, onReachBottom);

const goodsList = ref<Array<any>>([]);
const coupon_id = ref<number | string>('');
const currGoodsCategory = ref<number | string>('');
const mescrollRef = ref(null);
const loading = ref<boolean>(false);
const goods_name = ref("");
const price = ref("");
const sale_num = ref("");
const searchType = ref('total_order_num');
interface mescrollStructure {
	num: number,
	size: number,
	endSuccess: Function,
	[propName: string]: any
}

const getAllAppListFn = (mescroll: mescrollStructure) => {
	loading.value = false;
	let data: object = {
		goods_category: currGoodsCategory.value,
		page: mescroll.num,
		limit: mescroll.size,
		names: goods_name.value,
		coupon_id: coupon_id.value,
		order: searchType.value === 'total_order_num' ? '' : searchType.value,
		sort: searchType.value == 'price' ? price.value : sale_num.value
	};
	getExchangeGoodsList(data).then((res: any) => {
		let newArr = (res.data.data as Array<Object>);
		//设置列表数据
		if (Number(mescroll.num) === 1) {
			goodsList.value = []; //如果是第一页需手动制空列表
		}
		goodsList.value = goodsList.value.concat(newArr);
		mescroll.endSuccess(newArr.length);
		loading.value = true;
	}).catch(() => {
		loading.value = true;
		mescroll.endErr(); // 请求失败, 结束加载
	})
}

// 搜索
const searchTypeFn = (type:string) => {
	searchType.value = type;
    if ( type == 'total_order_num') {
		sale_num.value = '';
		price.value = '';
	}
	if ( type == 'price') {
		sale_num.value = '';
		if(price.value){
			price.value = price.value == 'asc' ? 'desc' : 'asc';
		}else{
			price.value = 'asc';
		}
	}
	if ( type == 'total_exchange_num') {
		price.value = '';
		if(sale_num.value){
			sale_num.value = sale_num.value == 'asc' ? 'desc' : 'asc';
		}else{
			sale_num.value = 'asc';
		}
	}
	goodsList.value = [];
    getMescroll().resetUpScroll();
}
const toDetail = (id: string | number) => {
	redirect({ url: '/addon/shop/pages/point/detail', param: { id: id }, mode: 'navigateTo' })
}
onMounted(() => {
	setTimeout(() => {
		getMescroll().optUp.textNoMore = t("end");
	}, 500)
});
</script>

<style lang="scss" scoped>
.bg-color{
	background: linear-gradient( 180deg, #EF000C 16%, rgba(239,0,12,0) 92%);
}
.nav-item.active {
	color: $u-primary;
}

.scroll-view-wrap {
	word-break: keep-all;
}

.text-color {
	color: var(--primary-color);
}

.label-select {
	color: var(--primary-color);
	border-color: var(--primary-color);
	background-color: var(--primary-color-light);
}

:deep(.u-popup .u-transition) {
	top: 156rpx !important;
}

.product-warp {
	z-index: 99999;
}

:deep(.tab-bar-placeholder) {
	display: none !important;
}

:deep(.u-tabbar__placeholder) {
	display: none !important;
}
:deep(.u-input__content__clear){
	width: 28rpx;
	height: 28rpx;
	font-size: 28rpx;
	background-color: #999;
}
.noData{
	height: calc(100vh - 200rpx - 50px - constant(safe-area-inset-bottom));
	height: calc(100vh - 200rpx - 50px - env(safe-area-inset-bottom));
 }
.goods-item-style-two{
	width: calc(50% - 10rpx);
}
</style>