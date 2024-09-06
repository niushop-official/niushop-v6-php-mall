<template>
	<view :style="themeColor()">
		<view class="bg-[#f7f7f7] min-h-[100vh] relative" v-if="Object.keys(goodsDetail).length">
			<!-- 自定义头部 -->
			<view class="flex items-center fixed left-0 right-0 z-10 bg-transparent detail-head" :class="{'!bg-[#fff]' :detailHeadBgChange}" :style="navbarInnerStyle">
				<text class="nc-iconfont nc-icon-zuoV6xx" :style="navbarInnerArrowStyle" @click="goback()"></text>
				<view class="ml-auto !pt-[12rpx] !pb-[8rpx] p-[10rpx] bg-[rgba(255,255,255,.4)] rounded-full border-[2rpx] border-solid border-transparent box-border nc-iconfont nc-icon-fenxiangV6xx font-bold text-[#303133] text-[36rpx]" :class="{'border-[#d8d8d8]': detailHeadBgChange}"  @click="openShareFn"></view>
			</view>

			<view class="swiper-box">
				<u-swiper :list="goodsDetail.goods.goods_image" :indicator="goodsDetail.goods.goods_image.length" :indicatorStyle="{'bottom': '50rpx'}" :autoplay="true" height="100vw" @click="swiperClick"></u-swiper>
			</view>
			<view v-if="priceType == 'discount_price'" class="-mt-[26rpx] relative flex items-center justify-between !bg-cover box-border pb-[26rpx] h-[136rpx] px-[30rpx]" :style="{ background: 'url(' + img('addon/shop/detail/discount_price_bg.png') + ') no-repeat'}">
				<view class="flex text-[#fff]">
					<text class="text-[26rpx] mt-[18rpx] mr-[8rpx]">折扣价</text>
					<view class="flex">
						<text class="text-[26rpx] mt-[18rpx] font-medium price-font">￥</text>
						<text class="text-[50rpx] price-font">{{ parseFloat(goodsPrice).toFixed(2).split('.')[0] }}</text>
						<text class="text-[50rpx] price-font">.{{ parseFloat(goodsPrice).toFixed(2).split('.')[1] }}</text>
						<text class="text-[26rpx] mt-[18rpx] ml-[10rpx] line-through price-font" v-if="goodsDetail.market_price && parseFloat(goodsDetail.market_price)">
							￥{{ goodsDetail.market_price }}
						</text>
					</view> 
				</view>
				<view class="flex flex-col text-[#fff] items-end">
					<image class="h-[28rpx] mr-[2rpx]" :src="img('addon/shop/detail/discount_price.png')" mode="heightFix"></image>
					<view class="flex items-center text-[24rpx] -mb-[10rpx]">
						<text class="mr-[4rpx]">距结束</text>
						<up-count-down class="text-[#fff] text-[28rpx]" :time="discountTime" format="HH:mm:ss"></up-count-down>
					</view>
				</view>
			</view>
			<view class="bg-[#f6f6f6] rounded-[16rpx] -mt-[26rpx] relative">
				<view class="relative mx-[30rpx] rounded-[16rpx]" :class="{'pt-[30rpx]': priceType == 'discount_price','pt-[20rpx]': priceType != 'discount_price'}">
					<view class="text-[var(--price-text-color)] flex items-baseline mb-[16rpx]" v-if="priceType != 'discount_price'">
						<text class="text-[32rpx] font-medium price-font">￥</text>
						<text class="text-[48rpx] price-font">{{ parseFloat(goodsPrice).toFixed(2).split('.')[0] }}</text>
						<text class="text-[32rpx] mr-[10rpx] price-font">.{{ parseFloat(goodsPrice).toFixed(2).split('.')[1] }}</text>
						<image  v-if="priceType == 'member_price'" class="h-[34rpx] mr-[12rpx] w-[80rpx]" :src="img('addon/shop/VIP.png')" mode="heightFix" />
						<text class="text-[26rpx] text-[#999] line-through price-font" v-if="goodsDetail.market_price && parseFloat(goodsDetail.market_price)">
							￥{{ goodsDetail.market_price }}
						</text>
					</view>
					<view class="font-medium text-[32rpx] multi-hidden leading-[40rpx]">
						{{ goodsDetail.goods.goods_name }}
					</view>
					<view class="flex items-start mt-[12rpx] pb-[10rpx]">
						<view class="flex flex-wrap" v-if="goodsDetail.label_info && goodsDetail.label_info.length">
							<view v-for="item in goodsDetail.label_info" :key="item.label_id"
								class="text-[#FA6400] mb-[10rpx] leading-[36rpx] text-[22rpx] h-[40rpx] px-[10rpx] border-[2rpx] border-solid border-[#FA6400] rounded-[4rpx] mr-[15rpx] box-border truncate">
								{{ item.label_name }}
							</view>
						</view>
						<view class="text-[26rpx] mt-[6rpx] text-[#666] flex items-baseline ml-auto">
							<text class="whitespace-nowrap">销量</text>
							<text class="mx-[2rpx]">{{ goodsDetail.goods.sale_num }}</text>
							<text>{{ goodsDetail.goods.unit }}</text>
						</view>
					</view>
				</view>

				<view class="mt-[20rpx] bg-white sidebar-marign rounded-[16rpx] px-[20rpx] py-[16rpx]" v-if="isGoodsPropertyTemp">
					<view @click="servicesDataShow = !servicesDataShow" v-if="goodsDetail.service && goodsDetail.service.length" class="flex items-center h-[64rpx]">
						<text class="text-[#666] text-[26rpx] leading-[30rpx] font-400 shrink-0">服务</text>
						<view class="text-[#343434] text-[26rpx] leading-[30rpx] font-400 truncate ml-auto">
							{{ goodsDetail.service[0].service_name }}
						</view>
						<text class="nc-iconfont nc-icon-youV6xx text-[26rpx] text-[#666]"></text>
					</view>
					<view @click="buyFn" v-if="goodsDetail.goodsSpec && goodsDetail.goodsSpec.length" class="flex items-center h-[64rpx]">
						<text class="text-[#666] text-[26rpx] leading-[30rpx] font-400 shrink-0 mr-[20rpx]">已选</text>
						<view class="ml-auto text-right truncate flex-1 text-[#343434] text-[26rpx] leading-[30rpx] font-400">
							{{ goodsDetail.sku_spec_format }}
						</view>
						<text class="nc-iconfont nc-icon-youV6xx text-[26rpx] text-[#666]"></text>
					</view>
					<view class="flex items-center h-[64rpx]" @click="distributionDataOpen"
						v-if="goodsDetail.goods.goods_type == 'real'&&goodsDetail.delivery_type_list&&goodsDetail.delivery_type_list.length">
						<text class="text-[#666] text-[26rpx] leading-[30rpx] font-400 shrink-0">配送</text>
						<view class="ml-auto flex items-center text-[#343434] text-[26rpx] leading-[30rpx] font-400">
							{{goodsDetail.delivery_type_list[selectDeliveryType]}}
						</view>
						<text class="nc-iconfont nc-icon-youV6xx text-[26rpx] text-[#666]"></text>
					</view>
					<view @click="couponListShow = true" v-if="couponList.length" class="flex items-center h-[64rpx]">
						<text class="text-[#666] text-[26rpx] leading-[30rpx] font-400 shrink-0 mr-[20rpx]">领券</text>
						<view class="ml-auto flex-1 flex-nowrap flex items-center overflow-hidden h-[44rpx] content-between">
							<block v-for="(item, index) in couponList" :key="index">
								<text v-if="index < 3" class="text-xs whitespace-nowrap rounded-sm border-[2rpx] px-[6rpx] py-[2rpx] border-solid border-[var(--primary-color)] text-[var(--primary-color)] mt-[4rpx]" :class="{'mr-[8rpx]': couponList.length != (index+1) && index < 2, 'ml-auto': index == 0}">
									{{ item.title }}
								</text>
							</block>
						</view>
						<text class="nc-iconfont nc-icon-youV6xx text-[26rpx] text-[#666]"></text>
					</view>

				</view>

				<view class="mt-[20rpx] bg-white sidebar-marign rounded-[16rpx] px-[20rpx]">
					<view class="flex items-center justify-between h-[80rpx]">
						<text class="text-[28rpx] text-[#303133] font-bold">宝贝评价({{ evaluate.count }})</text>
						<view v-if="evaluate.count" class="h-[80rpx] flex items-center" @click="toLink(goodsDetail.goods_id)">
							<text class="text-[24rpx] text-[#666]">查看全部</text>
							<text class="nc-iconfont nc-icon-youV6xx text-[26rpx] text-[#666]"></text>
						</view>
						<text v-if="!evaluate.count" class="text-[24rpx] text-[#666]">暂无评价</text>
					</view>
					<view>
						<view class="pb-[20rpx]" v-for="(item, index) in evaluate.list" :key="index">
							<view class="flex items-center w-full">
								<u-avatar :default-url="img('static/resource/images/default_headimg.png')" :src="img(item.member_head)" :size="'30rpx'" leftIcon="none"></u-avatar>
								<text class="ml-[10rpx] text-[22rpx] text-[#999]">{{ item.member_name }}</text>
							</view>
							<view class="flex justify-between w-full mt-[10rpx]">
								<view class="flex-1 w-[540rpx] text-[26rpx] text-[#303133] max-h-[72rpx] leading-[36rpx] multi-hidden mr-[50rpx]">{{ item.content }}</view>
								<view class="w-[80rpx] shrink-0">
									<u--image v-if="item.image_mid && item.image_mid.length" width="80rpx" height="80rpx" radius="8rpx" :src="img(item.image_mid[0])" model="aspectFill" @click="imgListPreview(item.images[0])">
										<template #error>
											<u-icon name="photo" color="#999" size="50"></u-icon>
										</template>
									</u--image>
								</view>
							</view>
						</view>
					</view>
				</view>

				<view class="my-[20rpx] bg-white sidebar-marign rounded-[16rpx] px-[20rpx] pb-[20rpx]" v-if="goodsDetail.goods && goodsDetail.goods.attr_format && Object.keys(goodsDetail.goods.attr_format).length">
					<view class="text-[28rpx] h-[80rpx] leading-[80rpx] font-bold">商品属性</view>
					<view class="border-[2rpx] border-solid border-[#f1f1f1] border-b-0">
						<block v-for="(item,index) in goodsDetail.goods.attr_format" :key="index">
							<view v-if="index < 4 || isAttrFormatShow" class="flex border-0 border-solid border-[#f1f1f1] border-b-[2rpx]">
								<view class="w-[30%] px-[18rpx] py-[8rpx] text-[24rpx] min-h-[70rpx] break-all box-border flex items-center border-0 border-solid border-[#f1f1f1] border-r-[2rpx] bg-[#fbfafa]">{{item.attr_value_name}}</view>
								<view class="w-[70%] px-[18rpx] py-[8rpx] text-[24rpx] min-h-[70rpx] break-all flex items-center box-border">{{Array.isArray(item.attr_child_value_name) ? item.attr_child_value_name.join(',') : item.attr_child_value_name }}</view>
							</view>
						</block>
						<view v-if="goodsDetail.goods.attr_format.length > 4" class="flex items-center justify-center h-[70rpx] border-0 border-solid border-[#f1f1f1] border-b-[2rpx]" @click="isAttrFormatShow = !isAttrFormatShow">
							<text class="text-[24rpx] mr-[10rpx]">{{!isAttrFormatShow ? '展开' : '收起'}}</text>
							<text class="nc-iconfont !text-[22rpx]" :class="{'nc-icon-xiaV6xx': !isAttrFormatShow, 'nc-icon-shangV6xx-1': isAttrFormatShow}"></text>
						</view>
					</view>
				</view>

				<view class="my-[20rpx] bg-white sidebar-marign rounded-[16rpx] px-[20rpx] pb-[20rpx]">
					<view class="text-[28rpx] h-[80rpx] leading-[80rpx] font-bold">商品详情</view>
					<view class="u-content">
						<u-parse :content="goodsDetail.goods.goods_desc" :tagStyle="{img: 'vertical-align: top;',p:'overflow: hidden;word-break:break-word;' }"></u-parse>
					</view>
				</view>

				<view class="tab-bar-placeholder"></view>
				<view class="w-[100%] flex justify-between px-[27rpx] bg-[#fff] box-border fixed left-0 bottom-0 tab-bar z-1 items-center">
					<view class="flex items-center ml-[10rpx]">
						<view class="flex flex-col justify-center items-center mr-[39rpx]" @click="redirect({ url: '/addon/shop/pages/index', mode: 'reLaunch' })">
							<view class="nc-iconfont nc-icon-shouyeV6xx text-[36rpx]"></view>
							<text class="text-[20rpx] mt-[10rpx]">首页</text>
						</view>
						<view class="flex flex-col justify-center items-center mr-[39rpx]" @click="openShareFn">
							<view class="nc-iconfont nc-icon-fenxiangV6xx text-[36rpx]"></view>
							<text class="text-[20rpx] mt-[10rpx]">分享</text>
						</view>

						<!-- #ifdef H5 -->
						<view class="flex flex-col justify-center items-center" @click="collectFn">
							<text class="nc-iconfont text-[36rpx]" :class="{'text-[#ff0000] nc-icon-xihuanV6mm': isCollect, 'text-[#303133] nc-icon-guanzhuV6xx' : !isCollect}"></text>
							<text class="text-[20rpx] mt-[10rpx]">收藏</text>
						</view>
						<!-- #endif -->

						<!-- #ifdef MP-WEIXIN -->
						<view>
							<nc-contact
									:send-message-title="sendMessageTitle"
									:send-message-path="sendMessagePath"
									:send-message-img="sendMessageImg">
								<view class="flex flex-col justify-center items-center">
									<text class="nc-iconfont nc-icon-kefuV6xx-1 text-[36rpx]"></text>
									<text class="text-[20rpx] mt-[10rpx]">客服</text>
								</view>
							</nc-contact>
						</view>
						<!-- #endif -->
					</view>
					<view class="flex" v-if="goodsDetail.goods.status == 1">
						<button
							v-if="goodsDetail.goods.goods_type == 'real' || (goodsDetail.goods.goods_type == 'virtual' && goodsDetail.goods.virtual_receive_type != 'verify')"
							class="!w-[200rpx] !h-[72rpx] text-[26rpx] !text-[#fff] !m-0 !mr-[20rpx] leading-[72rpx] rounded-full remove-border"
							style="background: linear-gradient(127deg, #FFB000 0%, #FFA029 100%);" @click="buyFn('join_cart')">
							加入购物车</button>
						<button
							v-if="isShowSingleSku"
							:style="{ width : (goodsDetail.goods.goods_type == 'real' || (goodsDetail.goods.goods_type == 'virtual' && goodsDetail.goods.virtual_receive_type != 'verify')) ?  '200rpx' : '420rpx' + '!important'  }"
							class="!h-[72rpx] text-[26rpx] !text-[#fff] primary-btn-bg !m-0 leading-[72rpx] rounded-full remove-border"
							@click="buyFn('buy_now')">立即购买</button>
						<button
							v-else
							:style="{ width : (goodsDetail.goods.goods_type == 'real' || (goodsDetail.goods.goods_type == 'virtual' && goodsDetail.goods.virtual_receive_type != 'verify')) ?  '200rpx' : '420rpx' + '!important'  }"
							class="!h-[72rpx] text-[26rpx] !text-[#fff] !bg-[#ccc] !m-0 leading-[72rpx] rounded-full remove-border"
							>已售罄</button>
					</view>
					<view class="flex flex-1 ml-[40rpx]" v-else>
						<button class="w-[100%] !h-[72rpx] text-[26rpx] !text-[#fff] !bg-[#ccc] !m-0 leading-[72rpx] rounded-full remove-border">该商品已下架</button>
					</view>
				</view>
			</view>
			<!-- 服务 -->
			<view @touchmove.prevent.stop>
				<u-popup class="popup-type" :show="servicesDataShow" @close="servicesDataShow = false">
					<view class="min-h-[480rpx]" @touchmove.prevent.stop>
						<view class="flex items-center justify-center py-[34rpx] relative">
							<text class="text-[32rpx] leading-[36rpx] font-500">商品服务</text>
						</view>
						<scroll-view class="h-[520rpx]" scroll-y="true">
							<view class="pl-[22rpx] py-[28rpx] pr-[37rpx]">
								<view class="flex mb-[28rpx]" v-for="(item, index) in goodsDetail.service">
									<image class="max-w-[34rpx] max-h-[34rpx] mr-[14rpx]" :src="img(item.image || 'addon/shop/icon_service.png')" mode="aspectFit" />
									<view class="flex-1">
										<view class="text-[26rpx] leading-[36rpx] text-[#222] mb-[4rpx] w-[643rpx]">{{ item.service_name }}</view>
										<view class="text-[22rpx] leading-[36rpx] text-[#888] w-[643rpx]">{{ item.desc }}</view>
									</view>
								</view>
							</view>
						</scroll-view>
					</view>
				</u-popup>
			</view>
			<!-- 配送 -->
			<view @touchmove.prevent.stop>
				<u-popup class="popup-type" :show="distributionDataShow" @close="distributionDataShow = false">
					<view class="min-h-[360rpx]" @touchmove.prevent.stop>
						<view class="flex items-center justify-center py-[34rpx] relative">
							<text class="text-[32rpx] leading-[36rpx] font-500">配送方式</text>
						</view>
						<scroll-view class="h-[520rpx]" scroll-y="true">
							<view class="pl-[22rpx] pt-[28rpx] pr-[37rpx]">
								<view class="flex mb-[28rpx]" v-for="(item, index) in goodsDetail.delivery_type_list" @click="distributionListFn(item,index)">
									<image class="w-[34rpx] h-[34rpx] mr-[14rpx]" :src="img('addon/shop/icon_service.png')" mode="aspectFit" />
									<view class="flex-1">
										<view class="text-[26rpx] leading-[36rpx] text-[#222] mb-[4rpx]">{{ item }}</view>
										<view class="text-[22rpx] leading-[36rpx] text-[#888]">{{ item }}</view>
									</view>
								</view>
							</view>
						</scroll-view>
					</view>
				</u-popup>
			</view>
			<!-- 优惠券 -->
			<view @touchmove.prevent.stop>
				<u-popup class="popup-type" :show="couponListShow" @close="couponListShow = false">
					<view class="min-h-[480rpx]" @touchmove.prevent.stop>
						<view class="flex items-center justify-center py-[34rpx] relative">
							<text class="text-[32rpx] leading-[36rpx] font-500">优惠券</text>
						</view>
						<scroll-view class="h-[520rpx]" scroll-y="true">
							<view class="px-[20rpx]">
								<view
									class="mb-[30rpx] flex items-center border-[2rpx] border-solid border-[rgba(0,0,0,.1)] rounded"
									v-for="(item, index) in couponList" :key="index">
									<view class="flex flex-col items-center py-[20rpx] w-[240rpx] border-0 border-r-[2rpx] border-dashed border-[rgba(0,0,0,.1)]">
										<view class="text-xs price-font">
											<text class="text-[28rpx]">￥</text>
											<text class="text-[48rpx]">{{ item.price }}</text>
										</view>
										<text class="text-xs mt-[12rpx]">{{ Number(item.min_condition_money) ? ('满' + item.min_condition_money + '元可以使用') : '无门槛优惠券' }}</text>
									</view>
									<view class="ml-[20rpx] flex-1 flex flex-col py-[20rpx]">
										<text class="text-xs">{{ item.title }}</text>
										<text class="text-xs text-[#ABABAB] mt-[12rpx]">{{ item.valid_type == 1 &&
											('领取之日起' + item.length + '天内有效') || item.valid_type == 2 &&
											('领取之日起至' + item.valid_end_time) }}</text>
									</view>
									<text v-if="item.btnType === 'collecting'"
										class="bg-[var(--primary-color)] rounded-2xl text-[#fff] text-xs mr-[20rpx] py-[8rpx] px-[16rpx]"
										@click="getCouponFn(item, index)">领取</text>
									<text v-else class="!bg-[#fff] rounded-2xl text-[#ABABAB] text-xs mr-[20rpx] py-[8rpx] px-[16rpx]">{{ item.btnType === 'collected' ? '已领完' : '已领取' }}</text>
								</view>
							</view>
						</scroll-view>
						<view class="px-[32rpx] pb-[67rpx] pt-[42rpx]">
							<button
								class="!w-[100%] !h-[72rpx] text-[26rpx] !bg-[var(--primary-color)] !text-[#fff] !m-0 rounded-full leading-[72rpx]"
								@click="couponListShow = false">确定</button>
						</view>
					</view>
				</u-popup>
			</view>
			<ns-goods-sku ref="goodsSkuRef" :goods-detail="goodsDetail" @change="specSelectFn"></ns-goods-sku>
			<share-poster ref="sharePosterRef" posterType="shop_goods" :posterId="goodsDetail.goods.poster_id" :posterParam="posterParam" :copyUrlParam="copyUrlParam" />
		</view>
		<u-loading-page bg-color="rgb(248,248,248)" :loading="loading" loadingText="" fontSize="16" color="#303133"></u-loading-page>

		<!-- #ifdef MP-WEIXIN -->
		<!-- 小程序隐私协议 -->
		<wx-privacy-popup ref="wxPrivacyPopupRef"></wx-privacy-popup>
		<!-- #endif -->
		<!-- 强制绑定手机号 -->
		<bind-mobile ref="bindMobileRef" /> 
	</view>
</template>

<script setup lang="ts">
import { ref, computed, getCurrentInstance, nextTick } from 'vue';
import { onLoad, onShow, onUnload,onPageScroll } from '@dcloudio/uni-app'
import { img, redirect,handleOnloadParams, getToken, deepClone } from '@/utils/common';
import { t } from '@/locale';
import { getGoodsDetail, collect, cancelCollect, getEvaluateList } from '@/addon/shop/api/goods';
import { getShopCouponList, getCoupon } from '@/addon/shop/api/coupon';
import nsGoodsSku from '@/addon/shop/components/ns-goods-sku/ns-goods-sku.vue';
import bindMobile from '@/components/bind-mobile/bind-mobile.vue';
import useCartStore from '@/addon/shop/stores/cart'
import { useLogin } from '@/hooks/useLogin'
import useMemberStore from '@/stores/member'
import { useShare }from '@/hooks/useShare'
import sharePoster from '@/components/share-poster/share-poster.vue'

// 分享
const{setShare} = useShare()

// 会员信息
const memberStore = useMemberStore()
const userInfo = computed(() => memberStore.info)

// 购物车数量
const cartStore = useCartStore();
const cartTotalNum = computed(() => cartStore.totalNum)

const goodsSkuRef = ref(null);
const goodsDetail = ref({});

const isAttrFormatShow = ref(false); //控制属性是否展开

const loading = ref<boolean>(false);
const servicesDataShow = ref<boolean>(false)
const distributionDataShow =  ref<boolean>(false) //配送
const couponListShow = ref<boolean>(false) //优惠券
const discountTime = ref(0)

const sendMessageTitle = ref('')
const sendMessagePath = ref('')
const sendMessageImg = ref('')

onLoad((option) => {

    // #ifdef MP-WEIXIN
	// 处理小程序场景值参数
    option = handleOnloadParams(option);
    // #endif

	getGoodsDetail({
		goods_id: option.goods_id || '',
		sku_id: option.sku_id || '',
	}).then(res => {
		if (!res.data.goods || JSON.stringify(res.data) === '[]') {
			uni.showToast({ title: '找不到该商品', icon: 'none' })
			setTimeout(() => {
				redirect({ url: '/addon/shop/pages/index', mode: 'reLaunch' })
			}, 600)
			return false
		}
		
		goodsDetail.value = deepClone(res.data);
		isCollect.value = goodsDetail.value.goods.is_collect;
		goodsDetail.value.delivery_type_list = goodsDetail.value.goods.delivery_type_list ? Object.values(goodsDetail.value.goods.delivery_type_list).map(el => el.name) : [];
		goodsDetail.value.goods.goods_image = goodsDetail.value.goods.goods_image_thumb_big;
		goodsDetail.value.goods.goods_image.forEach((item, index) => {
			goodsDetail.value.goods.goods_image[index] = img(item);
		})
		
		let data = deepClone(res.data);
		goodsDetail.value.goods.attr_format = []
		if(data.goods && data.goods.attr_format){
			let attrFormatArr = deepClone(JSON.parse(data.goods.attr_format));
			attrFormatArr.forEach((item,index)=>{
				if((item.attr_child_value_name && !(item.attr_child_value_name instanceof Array)) || ((item.attr_child_value_name instanceof Array) && item.attr_child_value_name.length)){
					goodsDetail.value.goods.attr_format.push(item);
				}
			})
		}

        sendMessageTitle.value = goodsDetail.value.goods.goods_name
        sendMessagePath.value = '/addon/shop/pages/goods/detail?sku_id=' + goodsDetail.value.sku_id;
        sendMessageImg.value = img(goodsDetail.value.goods.goods_cover_thumb_mid)

		// 分享 - start
		let share = {
			title: goodsDetail.value.goods.goods_name,
			desc: goodsDetail.value.goods.sub_title,
			url: goodsDetail.value.goods.goods_cover_thumb_mid
		}
		uni.setNavigationBarTitle({
			title: goodsDetail.value.goods.goods_name
		})
		setShare({
			wechat:{
				...share
			},
			weapp:{
				...share
			}
		});
		// 分享 - end
		
		// 折扣信息
		if(Object.keys(goodsDetail.value.goods).length && goodsDetail.value.goods.is_discount && Object.keys(goodsDetail.value.discount_info).length){
			let now = new Date();
			let timestamp = now.getTime();
			discountTime.value = goodsDetail.value.discount_info.active.end_time*1000 - timestamp.toFixed(0)
		}

		// 获取优惠券列表
		getShopCouponListFn();

		// 获取评价
		getEvaluateListFn();

		copyUrlFn();

        nextTick(() => {
            setTimeout(()=>{
                const query = uni.createSelectorQuery().in(instance);
                query.select('.swiper-box').boundingClientRect(data => {
                    swiperHeight = data ? data.height : 0;
                }).exec();
                query.select('.detail-head').boundingClientRect(data => {
                    if(data) {
                        detailHead = data.height ? data.height : 0;
                    }
                }).exec();
            }, 400)
        })
	})
})

onShow(() => {
	// 删除配送方式
	uni.removeStorageSync('distributionType');
	cartStore.getList();
})

const specSelectFn = (id) => {
	goodsDetail.value.skuList.forEach((item, index) => {
		if (item.sku_id == id) {
			Object.assign(goodsDetail.value, item);
		}
	})
}

// 判断单规格库存是否为0
const isShowSingleSku = computed(() => {
	let isSingleSpec = false // 是否为单规格，true：多规格，false：单规格
	goodsDetail.value.skuList.forEach((item,index)=>{
		if(item.sku_spec_format){
			isSingleSpec = true
		}
	})

	// 单规格，库存为0，显示已售罄
	if(!isSingleSpec && goodsDetail.value.stock <= 0){
	    return false;
	}else if(!isSingleSpec && goodsDetail.value.stock > 0){
	    // 单规格，库存大于0，可以购买
        return true;
    }
	return true;
})

// 判断商品属性模块是否展示
const isGoodsPropertyTemp = computed(() => {
	let bool = false;
	if(goodsDetail.value.service && goodsDetail.value.service.length || 
	goodsDetail.value.goodsSpec && goodsDetail.value.goodsSpec.length ||
	goodsDetail.value.goods.goods_type == 'real'&&goodsDetail.value.delivery_type_list&&goodsDetail.value.delivery_type_list.length ||
	couponList.value.length){
		bool = true;
	}
	return bool;
})

const buyFn = (type) => {
	goodsSkuRef.value.open(type)
}

//强制绑定手机号
const bindMobileRef = ref(null)
// 收藏
const isCollect = ref(0);
const collectFn = () => {
	// 检测是否登录
	if (!userInfo.value) {
		useLogin().setLoginBack({ url: '/addon/shop/pages/goods/detail', param: { sku_id: goodsDetail.value.sku_id } })
		return false
	}
	// 绑定手机号
	if(uni.getStorageSync('isbindmobile')){
        bindMobileRef.value.open()
        return false
    }
	let api = isCollect.value ? cancelCollect(goodsDetail.value.goods_id) : collect(goodsDetail.value.goods_id);
	api.then(res => {
		isCollect.value = !isCollect.value;
		if (isCollect.value) {
			uni.showToast({
				title: '收藏成功',
				icon: 'none'
			});
		} else {
			uni.showToast({
				title: '取消收藏',
				icon: 'none'
			});
		}
	})
}

// 优惠券
const couponList = ref([]);
const getShopCouponListFn = () => {
	getShopCouponList({
		category_id: goodsDetail.value.goods.goods_category || '',
		goods_id: goodsDetail.value.goods_id || ''
	}).then(res => {
		couponList.value = res.data.data.map((el: any) => {
			if (el.sum_count != -1 && el.receive_count === el.sum_count) {
				el.btnType = 'collected'//已领完
			}
			if (!userInfo.value) {
				el.btnType = 'collecting'//领用
			} else {
				if (el.is_receive && el.limit_count === el.member_receive_count) {
					el.btnType = 'using'//去使用
				} else {
					el.btnType = 'collecting'//领用
				}
			}
			return el
		});
	})
}

// 领取优惠券
const getCouponFn = (data, index) => {
	// 检测是否登录
	if (!userInfo.value) {
		useLogin().setLoginBack({ url: '/addon/shop/pages/goods/detail', param: { sku_id: goodsDetail.value.sku_id } })
		return false
	}
	// 绑定手机号
	if(uni.getStorageSync('isbindmobile')){
        bindMobileRef.value.open()
        return false
    }
	getCoupon({
		coupon_id: data.id || '',
		number: 1,
	}).then(res => {
		// couponList.value[index].btnType = 'using'
		getShopCouponListFn();
	})
}

// 获取评价
const evaluate = ref({
	count : 0
})

const getEvaluateListFn = () => {
	getEvaluateList(goodsDetail.value.goods_id).then(res => {
		evaluate.value = res.data
	})
}

//进入评论
const toLink = () => {
	redirect({ url: '/addon/shop/pages/evaluate/list', param: { goods_id: goodsDetail.value.goods_id } })
}

//预览图片
const imgListPreview = (item:any,index:any) => {
	if(Array.isArray(item)){
		if (!item.length) return false
		var urlList =item;
		uni.previewImage({
			indicator: "number",
			current:index,
			loop: true,
			urls: urlList
		})
	}else{
		if (item === '') return false
		var urlList = []
		urlList.push(img(item))  //push中的参数为 :src="item.img_url" 中的图片地址
		uni.previewImage({
			indicator: "number",
			loop: true,
			urls: urlList
		})
	}
	
}

// 返回上一页
const goback=()=> {
	if(getCurrentPages().length > 1){
		uni.navigateBack({
			delta: 1
		});
	}else{
        redirect({
			url: '/addon/shop/pages/index',
            mode: 'reLaunch'
		});
	}
}

/************ 选择配送方式-start ****************/
const selectDeliveryType = ref(0);
const distributionDataOpen = (()=>{
	distributionDataShow.value = true;
});
const distributionListFn = ((data,index)=>{
	selectDeliveryType.value =  index;
	distributionDataShow.value = false;
	uni.setStorageSync('distributionType', data);
});
/************ 选择配送方式-end ****************/


/************ 自定义头部-start ****************/
// 获取系统状态栏的高度
let systemInfo = uni.getSystemInfoSync();
let platform = systemInfo.platform;
let menuButtonInfo = {};
// 如果是小程序，获取右上角胶囊的尺寸信息，避免导航栏右侧内容与胶囊重叠(支付宝小程序非本API，尚未兼容)
// #ifdef MP-WEIXIN || MP-BAIDU || MP-TOUTIAO || MP-QQ
menuButtonInfo = uni.getMenuButtonBoundingClientRect();
// #endif

// 导航栏内部盒子的样式
const navbarInnerStyle = computed(() => {
	let style = '';
	// 导航栏宽度，如果在小程序下，导航栏宽度为胶囊的左边到屏幕左边的距离
	// #ifdef MP
	let rightButtonWidth = menuButtonInfo.width ? menuButtonInfo.width * 2 + 'rpx' : '70rpx';
	style += 'height:' + menuButtonInfo.height + 'px;';
	style += 'padding-right:calc(' + rightButtonWidth + ' + 30rpx);';
	style += 'padding-left:calc(' + rightButtonWidth + ' + 30rpx);';
	style += 'padding-top:' + menuButtonInfo.top + 'px;';
	style += 'padding-bottom: 8px;';

	style += 'font-size: 32rpx;';
	if (platform === 'ios') {
		// 苹果(iOS)设备
		style += 'font-weight: 500;';
	} else if (platform === 'android') {
	  // 安卓(Android)设备
		style += 'font-size: 36rpx;';
	}
	// #endif

	// #ifdef H5
	style += 'height: 100rpx;';
	style += 'padding-right: 30rpx;';
	style += 'padding-left: 30rpx;';

	style += 'font-size: 32rpx;';
	if (platform === 'ios') {
		// 苹果(iOS)设备
		style += 'font-weight: 500;';
	} else if (platform === 'android') {
	  // 安卓(Android)设备
		style += 'font-size: 36rpx;';
	}
	// #endif
	return style;
})

// 导航栏内部盒子的样式
const navbarInnerArrowStyle = computed(() => {
	let style = '';
	// 导航栏宽度，如果在小程序下，导航栏宽度为胶囊的左边到屏幕左边的距离
	// #ifdef MP
	style += "padding-left: 10rpx;"
	style += "padding-right: 10rpx;"
	style += 'position: absolute;';
	style += 'left:calc( 100vw - ' + menuButtonInfo.right + 'px);';
	style += 'font-size: 26px;';
	if (platform === 'ios') {
		// 苹果(iOS)设备
		style += 'font-weight: 700;';
	} else if (platform === 'android') {
	  // 安卓(Android)设备
	}
	// #endif
	// #ifdef H5
	style += 'font-size: 26px;';
	// #endif
	return style;
})

// 头部滚动
const instance = getCurrentInstance();
let swiperHeight = 0
let detailHead = 0

const detailHeadBgChange = ref(false)
onPageScroll((e)=> {
    if (swiperHeight == 0 || detailHead == 0) return;
    let height = swiperHeight - detailHead - 20;
    detailHeadBgChange.value = false;
    if (e.scrollTop >= height) {
        detailHeadBgChange.value = true;
    }
})
/************ 自定义头部-end ****************/

const swiperClick = (index)=>{
	if(typeof index == 'number')
		imgListPreview(goodsDetail.value.goods.goods_image,index)
}

/************* 分享海报-start **************/
const sharePosterRef = ref(null);
const copyUrlParam = ref('');
let posterParam = {};

// 分享海报链接
const copyUrlFn = ()=>{
	copyUrlParam.value = '?sku_id='+goodsDetail.value.sku_id;
	if (userInfo.value && userInfo.value.member_id) copyUrlParam.value += '&mid=' + userInfo.value.member_id;
}

const openShareFn = ()=>{
    posterParam.sku_id = goodsDetail.value.sku_id;
    if (userInfo.value && userInfo.value.member_id) posterParam.member_id = userInfo.value.member_id;
	sharePosterRef.value.openShare()
}
/************* 分享海报-end **************/

// 价格类型
const priceType = ref('') //''=>原价，discount_price=>折扣价，member_price=>会员价

// 商品价格
const goodsPrice = computed(() => {
    let price = "0.00";
    if (Object.keys(goodsDetail.value).length && Object.keys(goodsDetail.value.goods).length && goodsDetail.value.goods.is_discount && goodsDetail.value.sale_price != goodsDetail.value.price) {
        // 折扣价
        price = goodsDetail.value.sale_price ? goodsDetail.value.sale_price : goodsDetail.value.price;
        priceType.value = 'discount_price'
    } else if (Object.keys(goodsDetail.value).length && Object.keys(goodsDetail.value.goods).length && goodsDetail.value.goods.member_discount && getToken() && goodsDetail.value.member_price != goodsDetail.value.price) {
        // 会员价
        price = goodsDetail.value.member_price ? goodsDetail.value.member_price : goodsDetail.value.price;
        priceType.value = 'member_price'
    } else {
        price = goodsDetail.value.price
        priceType.value = ''
    }
    return price;
})

// 关闭预览图片
onUnload(()=>{
	// #ifdef  H5 || APP
	uni.closePreviewImage()
	// #endif
})
</script>
<style lang="scss" scoped>
	.remove-border {
		&::after {
			border: none;
		}
	}
:deep(.u-cell-group__wrapper) {
	.u-cell__body {
		padding: 23rpx 32rpx;
	}
}

.tab-bar-placeholder {
	padding-bottom: calc(constant(safe-area-inset-bottom) + 100rpx);
	padding-bottom: calc(env(safe-area-inset-bottom) + 100rpx);
}

.tab-bar {
	padding-top: 16rpx;
	padding-bottom: calc(constant(safe-area-inset-bottom) + 16rpx);
	padding-bottom: calc(env(safe-area-inset-bottom) + 16rpx);
}
:deep(.u-count-down) .u-count-down__text{
	color: #fff;
	font-size: 28rpx;
}
:deep(.u-swiper-indicator__wrapper--line__bar){
	height: 5rpx !important;
}
:deep(.u-swiper-indicator__wrapper--line){
	height: 5rpx !important;
}
</style>
