<template>
    <view class="discount bg-[#f6f6f6] min-h-[100vh]" :style="themeColor()">
	    
        <view class="fixed top-0 left-0 w-full z-10 text-[0]">
			<!-- #ifdef MP-WEIXIN -->
			<view class="absolute top-0 left-0 right-0 z-999">
				<top-tabbar :data="param" :scrollBool="topTabarObj.getScrollBool()" class="top-header"/>
			</view>
			<!-- #endif -->
			<u-swiper v-if="bannerList.length" :list="imgList" :indicator="bannerList.length" :indicatorStyle="{'bottom': '46rpx',}" :autoplay="true" :height="headStyle" @click="toRedirect"></u-swiper>
			<image v-if="!bannerListLoading&&!bannerList.length" :src="img('addon/shop/discount/discount_banner.png')" mode="scaleToFill" class="w-full" :style="{height: headStyle}" :show-menu-by-longpress="true"/>
			<view class="relative w-full h-[110rpx] mt-[-40rpx] z-5" v-if="discountList.length">
                <view class="bg-[#f24f3d] w-[750rpx] rounded-tl-[24rpx] rounded-tr-[24rpx] h-[96rpx] absolute left-0 bottom-0"></view>
                <scroll-view :scroll-x="true" class="h-[110rpx] absolute left-0 bottom-0 z-5">
                    <view class="flex items-end h-[100%]" :style="{'width':187.5*discountList.length+'rpx'}" >
                        <view class="w-[187.5rpx] h-[96rpx] relative flex-shrink-0" v-for="(item,index) in discountList" @click="navClick(item)">
                            <view class="w-full absolute left-0 top-0 z-10 text-[#fff] text-center pt-[10rpx]">
                                <view class="text-[28rpx] leading-[39rpx] font-500 px-[10rpx] h-[39rpx] overflow-hidden" :class="{'!text-[#333]':active==item.active_id}">{{item.active_desc}}</view>
                                <view class="flex justify-center w-full">
                                    <text class="text-[22rpx] leading-[34rpx] font-400 mt-[5rpx]" :class="{'active flex items-center justify-center':active==item.active_id}">{{item.active_status=='not_active'?'预告':item.active_status_name}}</text>
                                </view>
                            </view>
                            <template v-if="active==item.active_id">
                                <image v-if="discountList.length<4" class="absolute bottom-0 z-5 h-[110rpx] z-5" :class="{'left-0 w-[230rpx]':index==0,'left-[-41.25rpx] w-[270rpx]':index!=0}" :src="img(index==0?'addon/shop/discount/nav-left.png':'addon/shop/discount/nav-center.png')" />
                                <image v-if="discountList.length>=4" class="absolute bottom-0 z-5 h-[110rpx] z-5" :class="{'left-0 w-[230rpx]':index==0,'left-[-41.25rpx] w-[270rpx]':index!=0&&index!=discountList.length-1 ,'right-0 w-[230rpx]':index==discountList.length-1}" :src="img(index==0?'addon/shop/discount/nav-left.png':index==discountList.length-1?'addon/shop/discount/nav-right.png':'addon/shop/discount/nav-center.png')" />
                            </template>
                        </view>
                    </view>
                </scroll-view>
            </view>

        </view>

	    <mescroll-body v-if="discountList.length" ref="mescrollRef" :top="mescrollTop" @init="mescrollInit" :down="{ use: false }" @up="getActiveDiscountGoodsListFn">
            <view class="sidebar-marign bg-[#F4F6F8]">
				<block v-for="(item,index) in list" :key="index">
					<view class="bg-[#fff] p-[20rpx] flex rounded-[16rpx]" :class="{'mb-[20rpx]':index<list.length-1,'mb-[30rpx]':index==list.length-1}" @click="toLink(item)">
						<view class="w-[240rpx] h-[240rpx]">
							<u--image width="240rpx" height="240rpx" radius="16rpx" :src="img(item.goods_cover_thumb_mid ? item.goods_cover_thumb_mid : '')" model="aspectFill">
								<template #error>
									<image class="rounded-[16rpx] overflow-hidden w-[240rpx] h-[240rpx]" :src="img('static/resource/images/diy/shop_default.jpg')" mode="aspectFill"></image>
								</template>
							</u--image>
						</view>

						<view class="ml-[20rpx] flex-1 flex flex-col justify-between">
							<view>
								<view class=" text-[28rpx] font-500 leading-[39rpx] multi-hidden">
									{{ item.goods_name }}
								</view>
								<view v-if="item.goods_label_name&&item.goods_label_name.length" class=" mt-[10rpx] text-[#999999] text-[24rpx] font-400 multi-hidden">
									{{item.goods_label_name.map(el=>el.label_name).join(' | ')}}
								</view>
							</view>

							<view class="w-full h-[80rpx] bg-[#FFECEC] flex justify-between rounded-[40rpx]">
								<view class="mr-[20rpx] pl-[20rpx] flex-1 flex flex-col justify-center">
									<view class="text-[#EF000C] flex items-baseline">
										<text class="text-[20rpx] leading-[26rpx] font-400 price-font">￥</text>
										<text class="text-[32rpx] leading-[40rpx] font-500 price-font">{{parseFloat(item.goodsSku.active_discount_price).toFixed(2).split('.')[0]}}.</text>
										<text class="text-[22rpx] leading-[28rpx] font-500 price-font">{{parseFloat(item.goodsSku.active_discount_price).toFixed(2).split('.')[1]}}</text>
										<view v-if="item.goodsSku.active_discount_rate<10" class="px-[4rpx] border-[1rpx] border-[#EF000C] border-solid  text-[18rpx] ml-[6rpx] rounded-[4rpx] leading-[24rpx] transform translate-y-[-4rpx]">{{item.goodsSku.active_discount_rate}}折</view>
									</view>
									<view class="flex items-center mt-[2rpx]">
										<view class="w-[20rpx] h-[20rpx] mr-[4rpx] rounded-[12.5rpx] text-[#fff] bg-[#EF000C] flex items-center justify-center" :class="{'!bg-[#FF8540]':item.activeGoods.active_goods_status!='active'}">
											<text class="text-[10rpx] iconzhekou iconfont"></text>
										</view>
										<view class="text-[18rpx] font-400 text-[#EF000C] leading-[24rpx]" :class="{'!text-[#FF8540]':item.activeGoods.active_goods_status!='active'}">已省{{item.goodsSku.active_reduce_money}}元</view>
									</view>
								</view>
								<view class="active-button w-[106rpx] h-[78rpx] box-border pl-[25rpx] leading-[78rpx] text-[#fff]" :class="{'!w-[116rpx]':item.activeGoods.active_goods_status!='active'}" :style="{'background-image':'url(' + img(item.activeGoods.active_goods_status!='active'?'addon/shop/discount/no-active.png':'addon/shop/discount/active.png') + ')'}">
									<text v-if="item.activeGoods.active_goods_status!='active'" class="text-[26rpx] font-500">{{item.activeGoods.active_goods_status_name}}</text>
									<view v-else class="h-full flex items-center justify-between">
										<text class="text-[40rpx]">抢</text>
										<text class="text-[20rpx] iconxiayibu iconfont mr-[16rpx]"></text>
									</view>
								</view>
							</view>
						</view>
					</view>
                </block>
				<view class="flex justify-center w-[100%] items-center "  :class="{'h-[calc(100vh-620rpx)]':discountList.length,'h-[calc(100vh-550rpx)]':!discountList.length}" v-if="!list.length && !loading">
                    <mescroll-empty :option="{tip : '暂无商品，请看看其他商品吧！'}"></mescroll-empty>
                </view>
            </view>
        </mescroll-body>
		<view class="flex flex-col justify-center items-center h-[calc(100vh-550rpx)] fixed left-[30rpx] right-[30rpx]" :style="{'top':mescrollTop}" v-if="!discountList.length && !loading">
			<u-empty text="暂无活动，请看看其他商品吧！" width="347rpx" height="265rpx" :icon="img('static/resource/images/system/empty.png')"/>
			<button shape="circle" plain="true" class="w-[220rpx] mt-[42rpx] text-[28rpx] h-[66rpx] leading-[62rpx] !text-[#EF000C] !border-[#EF000C] border-[2rpx] rounded-full" @click="redirect({ url: '/addon/shop/pages/goods/list' })">去逛逛</button>
		</view>
        <u-loading-page bg-color="rgb(248,248,248)" :loading="loading" loadingText="" fontSize="16" color="#303133"></u-loading-page>
    </view>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import { useLogin } from '@/hooks/useLogin'
import { img, redirect, getToken, currRoute, diyRedirect,pxToRpx } from '@/utils/common';
import { getActiveDiscountConfig,getActiveDiscountList,getActiveDiscountGoodsList } from '@/addon/shop/api/discount'
import MescrollBody from '@/components/mescroll/mescroll-body/mescroll-body.vue'
import MescrollEmpty from '@/components/mescroll/mescroll-empty/mescroll-empty.vue'
import useMescroll from '@/components/mescroll/hooks/useMescroll.js'
import { onPageScroll, onReachBottom } from '@dcloudio/uni-app'
import { topTabar } from '@/utils/topTabbar'

let menuButtonInfo = {};
// 如果是小程序，获取右上角胶囊的尺寸信息，避免导航栏右侧内容与胶囊重叠(支付宝小程序非本API，尚未兼容)
// #ifdef MP-WEIXIN || MP-BAIDU || MP-TOUTIAO || MP-QQ
menuButtonInfo = uni.getMenuButtonBoundingClientRect();
// #endif

const headStyle = computed(() => {
	let style = Object.keys(menuButtonInfo).length ? (pxToRpx(Number(menuButtonInfo.height)) + pxToRpx(menuButtonInfo.top) + pxToRpx(8) + 368) + 'rpx' : '490rpx'
	return style
})

const { mescrollInit, downCallback, getMescroll } = useMescroll(onPageScroll, onReachBottom)
const bannerList = ref<Array<Object>>([])
const discountList = ref<Array<Object>>([])
const active = ref<number>(0)
const active_name = ref<String>('')
const list = ref<Array<Object>>([]);
const loading = ref<boolean>(true);
const bannerListLoading = ref<boolean>(true);

/********* 自定义头部 - start ***********/
const topTabarObj = topTabar()
let param = topTabarObj.setTopTabbarParam({title:'限时折扣'})
/********* 自定义头部 - end ***********/

const imgList:any = ref([])

const getActiveDiscountConfigFn = ()=>{
	bannerListLoading.value = true
    getActiveDiscountConfig().then((res:any)=>{
        bannerList.value = res.data
		imgList.value = bannerList.value.map((el)=>img(el.imageUrl))
		bannerListLoading.value = false
    }).catch(()=>{
		bannerListLoading.value = false
	})
}

getActiveDiscountConfigFn()
const getActiveDiscountListFn = ()=>{
    getActiveDiscountList({limit:4}).then((res:any)=>{
        discountList.value = res.data
		calculateHeight();
		if(discountList.value&&discountList.value.length){
			navClick(res.data[0]);
		}else{
			loading.value = false
		}
        
    })
}
getActiveDiscountListFn()
const navClick = (item:any)=>{
    active.value = item.active_id
    active_name.value = item.active_status_name
    getMescroll()?.resetUpScroll();
	uni.pageScrollTo({
	    scrollTop: 0, //距离页面顶部的距离
	    duration: 1
	});
}
const getActiveDiscountGoodsListFn = (mescroll) => {
	if(discountList.value.length == 0) return;
	
	loading.value = true;
	let data: object = {
		page: mescroll.num,
		limit: mescroll.size,
        active_id:active.value
	};

	getActiveDiscountGoodsList(data).then((res) => {
		let newArr = (res.data.data as Array<Object>).map((el: any) => {return el})
		//设置列表数据
		if (mescroll.num == 1) {
			list.value = []; //如果是第一页需手动制空列表
		}
		list.value = list.value.concat(newArr);
		mescroll.endSuccess(newArr.length);
		loading.value = false;
	}).catch(() => {
		loading.value = false;
		mescroll.endErr(); // 请求失败, 结束加载
	})
}
const toRedirect =(index:any)=>{
	let data = bannerList.value[index].toLink
	if (Object.keys(data).length) {
		if (!data.name) return;
		if (currRoute() == 'app/pages/member/index' && !getToken()) {
			useLogin().setLoginBack({ url: data.url })
			return;
		}
		diyRedirect(data);
	}
}

const toLink = (item : any) => {
	if(item.activeGoods.active_goods_status!='active'){
		uni.showToast({ title: `活动${item.activeGoods.active_goods_status_name}`, icon: 'none' })
		return;
	}
	redirect({ url: '/addon/shop/pages/goods/detail', param: { sku_id :item.goodsSku.sku_id } })
}

const mescrollTop = ref('')
const calculateHeight = ()=>{
	mescrollTop.value = discountList.value.length ? 590 : 520;
	mescrollTop.value = mescrollTop.value + 'rpx'
}
</script>

<style lang="scss" scoped>
.swiper.ns-indicator-dots :deep(.uni-swiper-dots-horizontal) {
	bottom:40rpx
}
.swiper.ns-indicator-dots :deep(.uni-swiper-dot) {
	background-color: #fff;
	opacity: 0.5;
}
.swiper.ns-indicator-dots :deep(.uni-swiper-dot-active) {
    background-color: #fff;
    opacity: 1;
}
.active{
    padding: 0 17rpx;
    line-height: 34rpx;
    border-radius: 29rpx;
    background: linear-gradient( 94deg, #FB7939 0%, #FE120E 99%), #EF000C;
}
.active-button{
    background-size: cover;
    background-repeat: no-repeat;
}
:deep(.u-swiper-indicator__wrapper--line__bar){
	height: 5rpx !important;
}
:deep(.u-swiper-indicator__wrapper--line){
	height: 5rpx !important;
}
</style>