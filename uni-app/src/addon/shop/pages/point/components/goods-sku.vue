<template>
	<view @touchmove.prevent.stop>
		<u-popup class="popup-type" :show="goodsSkuPop" @close="closeFn" mode="bottom">
			<view class="p-[32rpx] relative" v-if="goodsDetail.detail" @touchmove.prevent.stop>
				<view class="absolute right-[37rpx]  nc-iconfont nc-icon-guanbiV6xx text-[36rpx]" @click="closeFn"></view>
				<view class="flex mb-[58rpx]">

					<view class="rounded-[8rpx] overflow-hidden">
						<u--image width="180rpx" height="180rpx" :src="img(goodsDetail.detail.sku_image)" @click="imgListPreview(goodsDetail.detail.sku_image)" model="aspectFill">
							<template #error>
								<image class="w-[180rpx] h-[180rpx]" :src="img('static/resource/images/diy/shop_default.jpg')" mode="aspectFill"></image>
							</template>
						</u--image>
					</view>
					<view class="flex flex-1 flex-col ml-[20rpx] py-[10rpx]">
						<view class="w-[100%]">
							<view class="text-[var(--price-text-color)] flex items-baseline">
								<text class="text-[44rpx] leading-[62rpx] price-font" v-if="goodsDetail.point">{{goodsDetail.point}}{{t('point')}}</text>
								<text class="text-[36rpx] mb-[10rpx]" v-if="goodsDetail.point&&parseFloat(goodsDetail.price)">+</text>
								<template v-if="parseFloat(goodsDetail.price)">
									<text class="text-[28rpx] font-bold price-font">￥</text>
									<text class="text-[48rpx] price-font">{{ parseFloat(goodsDetail.price).toFixed(2).split('.')[0] }}</text>
									<text class="text-[32rpx] mr-[6rpx] price-font">.{{ parseFloat(goodsDetail.price).toFixed(2).split('.')[1] }}</text>
								</template>
								<template v-if="!goodsDetail.point&&!parseFloat(goodsDetail.price)">
									<text class="text-[28rpx] font-bold price-font">￥</text>
									<text class="text-[48rpx] price-font">{{ parseFloat(goodsDetail.price).toFixed(2).split('.')[0] }}</text>
									<text class="text-[32rpx] mr-[6rpx] price-font">.{{ parseFloat(goodsPrice).toFixed(2).split('.')[1] }}</text>
								</template>
								
							</view>
							<view class="text-[24rpx] leading-[32rpx] text-[#303133] mt-[12rpx]">库存{{goodsDetail.detail.stock}}{{ goodsDetail.goods.unit }}</view>
						</view>
						<view class="w-[100%] mt-auto"  style="max-height: calc(204rpx - 98rpx); overflow: hidden;"  v-if="goodsDetail.goodsSpec && goodsDetail.goodsSpec.length">
							<text class="text-[24rpx] leading-[30rpx] text-[#666]">已选规格：{{goodsDetail.detail.sku_spec_format}}</text>
						</view>
					</view>
				</view>
				<scroll-view class="h-[500rpx] mb-[30rpx]" scroll-y="true">
					<view :class="{'mt-[36rpx]': 0 != index }" v-for="(item,index) in goodsDetail.goodsSpec" :key="index">
						<view class="text-[26rpx] leading-[36rpx] mb-[24rpx]">{{item.spec_name}}</view>
						<view class="flex flex-wrap">
							<view class="box-border bg-[#f2f2f2] text-[24rpx] px-[44rpx] text-center h-[56rpx] leading-[56rpx] mr-[20rpx] mb-[20rpx] border-1 border-solid rounded-[50rpx] border-[#f2f2f2]"
							 :class="{'!border-[var(--primary-color)] text-[var(--primary-color)] !bg-[var(--primary-color-light)]': subItem.selected}"
							v-for="(subItem,subIndex) in item.values" :key="subIndex"
							@click="change(subItem, index)">
								{{subItem.name}}
							</view>
						</view>
					</view>
					<view class="flex justify-between items-center mt-[30rpx]">
						<view class="text-[26rpx]">购买数量</view>
						<u-number-box :min="1" :max="parseInt(goodsDetail.detail.limit_num)<goodsDetail.stock?parseInt(goodsDetail.detail.limit_num):goodsDetail.stock" integer :step="1" input-width="98rpx" v-model="buyNum" input-height="54rpx">
							<template #minus>
								<text class="text-[34rpx] nc-iconfont nc-icon-jianV6xx" :class="{ '!text-[#c8c9cc]': buyNum === 1 }"></text>
							</template>
							<template #input>
								<text class="text-[#303133] text-[24rpx] mx-[10rpx] min-w-[56rpx] h-[38rpx] leading-[40rpx] text-center border-[1rpx] border-solid border-[#ddd] rounded-[4rpx]">{{ buyNum }}</text>
							</template>
							<template #plus>
								<text class="text-[34rpx] nc-iconfont nc-icon-jiahaoV6xx" :class="{ '!text-[#c8c9cc]': buyNum === goodsDetail.stock || buyNum ==parseInt(goodsDetail.detail.limit_num)}"></text>
							</template>
						</u-number-box>
					</view>
				</scroll-view>
				<button v-if="goodsDetail.detail.stock > 0" class="!h-[72rpx] primary-btn-bg leading-[72rpx] text-[26rpx] rounded-[50rpx]" type="primary" @click="confirm">确定</button>
				<button v-else class="!h-[72rpx] leading-[72rpx] text-[26rpx] text-[#fff] bg-[#ccc] rounded-[50rpx]">已售罄</button>
			</view>
		</u-popup>
		<!-- #ifdef MP-WEIXIN -->
		<!-- 小程序隐私协议 -->
		<wx-privacy-popup ref="wxPrivacyPopupRef"></wx-privacy-popup>
		<!-- #endif -->
		<!-- 强制绑定手机号 -->
		<bind-mobile ref="bindMobileRef" /> 
	</view>
</template>

<script setup lang="ts">
	import { ref, reactive, computed } from 'vue';
	import { img, redirect, getToken } from '@/utils/common'
	import bindMobile from '@/components/bind-mobile/bind-mobile.vue';
	import { useLogin } from '@/hooks/useLogin'
	import useMemberStore from '@/stores/member'
	import { t } from '@/locale';

	const props = defineProps(['goodsDetail']);
	const goodsSkuPop = ref(false);
	const callback:any = ref(null);
	const currSpec = ref({
		skuId: "",
		name: []
	})
	const openType = ref("");
	const buyNum = ref(1)
	
	// 商品价格
	const goodsPrice = computed(() => {
        let price = "0.00";
        if (Object.keys(goodsDetail.value).length && Object.keys(goodsDetail.value.goods).length && goodsDetail.value.goods.is_discount && goodsDetail.value.sale_price != goodsDetail.value.price) {
            // 折扣价
            price = goodsDetail.value.sale_price ? goodsDetail.value.sale_price : goodsDetail.value.price;
        } else if (Object.keys(goodsDetail.value).length && Object.keys(goodsDetail.value.goods).length && goodsDetail.value.goods.member_discount && getToken() && goodsDetail.value.member_price != goodsDetail.value.price) {
            // 会员价
            price = goodsDetail.value.member_price ? goodsDetail.value.member_price : goodsDetail.value.price;
        } else {
            price = goodsDetail.value.price
        }
        return price;
    })

	// 会员信息
	const memberStore = useMemberStore()
	const userInfo = computed(() => memberStore.info)

	const open = (type="",fn = "")=>{
		openType.value = type;
		goodsSkuPop.value = true;
		callback.value = fn;
	}

	const closeFn = ()=>{
		goodsSkuPop.value = false
	}

	const goodsDetail = computed(() => {
		let data = JSON.parse(JSON.stringify(props.goodsDetail));

		// 重组数据结构
		if(Object.keys(data).length){

			if(!Object.keys(currSpec.value.name).length) currSpec.value.name = data.sku_spec_format.split(",");

			data.goodsSpec.forEach((item,index)=>{
				let specName = item.spec_values.split(",");
				item.values = [];
				specName.forEach((specItem, specIndex)=>{
					item.values[specIndex] = {};
					item.values[specIndex].name = specItem;
					item.values[specIndex].selected = false;
					item.values[specIndex].disabled = false;

					// 选中规格
					currSpec.value.name.forEach((currSpecItem, currSpecIndex)=>{
						if(currSpecIndex == index && currSpecItem == specItem){
							item.values[specIndex].selected = true;
						}
					})
				})
			})
			getSkuId();

			// 当前详情内容
			if(data.skuList && Object.keys(data.skuList).length){
				data.skuList.forEach((idItem, idIndex)=>{
					if(idItem.sku_id == currSpec.value.skuId){
						data.detail = idItem;
					}
				})
			}
		}
		return data;
	})

	const change = (data, index)=>{
		currSpec.value.name[index] = data.name;
		buyNum.value = 1
		getSkuId();
	}

	const emits = defineEmits(['change'])
	const getSkuId = ()=>{
		props.goodsDetail.skuList.forEach((skuItem, skuIndex)=>{
			if(skuItem.sku_spec_format == currSpec.value.name.toString()){
				currSpec.value.skuId = skuItem.sku_id
				emits('change',skuItem.sku_id)
			}
		})
	}
	//强制绑定手机号
	const bindMobileRef = ref(null)	//提交
	const confirm = ()=> {
        // 检测是否登录
        if (!userInfo.value) {
            useLogin().setLoginBack({url: '/addon/shop/pages/point/detail', param: {id: goodsDetail.value.exchange_id}})
            return false
        }
		// 绑定手机号
		if(uni.getStorageSync('isbindmobile')){
			bindMobileRef.value.open()
			return false
		}
        // 立即购买
        var data = {
            sku_id: goodsDetail.value.sku_id,
            num: buyNum.value
        };

        uni.setStorage({
            key: 'orderCreateData',
            data: {
                sku_data: [
                    data
                ]
            },
            success: () => {
                redirect({url: '/addon/shop/pages/point/payment'})
            }
        });

        closeFn();
    }

	//预览图片
	const imgListPreview = (item) => {
		if (item === '') return false
		var urlList = []
		urlList.push(img(item))  //push中的参数为 :src="item.img_url" 中的图片地址
		uni.previewImage({
			indicator: "number",
			loop: true,
			urls: urlList
		})
	}

	defineExpose({
		open
	})
</script>

<style lang="scss" scoped>
.popup-type {
	:deep(.u-popup__content) {
		border-top-left-radius: 16rpx;
		border-top-right-radius: 16rpx;
		overflow: hidden;
	}
}
// 防止覆盖住图片放大
.u-popup :deep(.u-transition){
	z-index: 999 !important;
}
::v-deep .u-number-box .u-number-box__slot{
	display: flex;
	align-items: center;
}
</style>

