<template>
	<view @touchmove.prevent.stop>
		<u-overlay :show="goodsSkuPop" @click="closeFn" zIndex="490">
			<u-popup class="popup-type" :show="goodsSkuPop" @close="closeFn" mode="bottom" :overlay="false" zIndex="500">
				<view class="p-[32rpx] relative" v-if="goodsDetail.detail" @touchmove.prevent.stop>
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
									<text class="text-[28rpx] font-bold price-font">￥</text>
									<text class="text-[48rpx] price-font">{{ parseFloat(goodsPrice).toFixed(2).split('.')[0] }}</text>
									<text class="text-[32rpx] mr-[6rpx] price-font">.{{ parseFloat(goodsPrice).toFixed(2).split('.')[1] }}</text>
								</view>
								<view class="text-[24rpx] leading-[32rpx] text-[#303133] mt-[12rpx]">库存{{goodsDetail.detail.stock}}{{ goodsDetail.goods.unit }}</view>
							</view>
							<view class="w-[100%] mt-auto"  style="max-height: calc(204rpx - 98rpx); overflow: hidden; box-sizing:border-box" v-if="goodsDetail.goodsSpec && goodsDetail.goodsSpec.length">
								<text class="text-[24rpx] leading-[30rpx] text-[#666] flex items-center h-[60rpx] mt-[10rpx]">已选规格：{{goodsDetail.detail.sku_spec_format}}</text>
							</view>
	<!-- 						<view v-if="goodsDetail.goodsSpec && goodsDetail.goodsSpec.length">
								<text>已选规格：{{goodsDetail.detail.sku_spec_format}}</text>
							</view> -->
						</view>
					</view>
					<scroll-view class="h-[500rpx] mb-[30rpx]" scroll-y="true">
						<view :class="{'mt-[36rpx]': 0 != index }" v-for="(item,index) in goodsDetail.goodsSpec" :key="index">
							<view class="text-[26rpx] leading-[36rpx] mb-[24rpx]">{{item.spec_name}}</view>
							<view class="flex flex-wrap">
								<view class="box-border bg-[#f2f2f2] text-[24rpx] px-[44rpx] text-center h-[56rpx] leading-[52rpx] mr-[20rpx] mb-[20rpx] border-1 border-solid rounded-[50rpx] border-[#f2f2f2]"
								 :class="{'!border-[var(--primary-color)] text-[var(--primary-color)] !bg-[var(--primary-color-light)]': subItem.selected}"
								v-for="(subItem,subIndex) in item.values" :key="subIndex"
								@click="change(subItem, index)">
									{{subItem.name}}
								</view>
							</view>
						</view>
						<view class="flex justify-between items-center mt-[30rpx]">
							<view class="text-[26rpx]">购买数量</view>
							<u-number-box :min="1" :max="goodsDetail.stock" integer :step="1" input-width="68rpx" v-model="buyNum" input-height="52rpx">
								<template #minus>
									<text class="text-[34rpx] nc-iconfont nc-icon-jianV6xx" :class="{ '!text-[#c8c9cc]': buyNum === 1 }"></text>
								</template>
								<template #input>
									<text class="text-[#303133] text-[24rpx] mx-[10rpx] min-w-[56rpx] h-[38rpx] leading-[40rpx] text-center border-[1rpx] border-solid border-[#ddd] rounded-[4rpx]">{{ buyNum }}</text>
								</template>
								<template #plus>
									<text class="text-[34rpx] nc-iconfont nc-icon-jiahaoV6xx" :class="{ '!text-[#c8c9cc]': buyNum === goodsDetail.stock }"></text>
								</template>
							</u-number-box>
						</view>
					</scroll-view>
					<button v-if="goodsDetail.detail.stock > 0" class="!h-[72rpx] leading-[72rpx] text-[26rpx] rounded-[50rpx] primary-btn-bg" type="primary" @click="confirm">确定</button>
					<button v-else class="!h-[72rpx] leading-[72rpx] text-[26rpx] text-[#fff] bg-[#ccc] rounded-[50rpx]">已售罄</button>
				</view>
			</u-popup>
		</u-overlay>
		<!-- #ifdef MP-WEIXIN -->
		<!-- 小程序隐私协议 -->
		<wx-privacy-popup ref="wxPrivacyPopupRef"></wx-privacy-popup>
		<!-- #endif -->
		 <!-- 强制绑定手机号 -->
		<bind-mobile ref="bindMobileRef" /> 
	</view>
</template>

<script setup lang="ts">
	import { ref, reactive, computed, toRaw } from 'vue';
	import { img, redirect, getToken } from '@/utils/common'
	import useCartStore from '@/addon/shop/stores/cart'
	import { useLogin } from '@/hooks/useLogin'
	import useMemberStore from '@/stores/member'
	import bindMobile from '@/components/bind-mobile/bind-mobile.vue';

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
            price = goodsDetail.value.sale_price // 折扣价
        } else if (Object.keys(goodsDetail.value).length && Object.keys(goodsDetail.value.goods).length && goodsDetail.value.goods.member_discount && getToken() && goodsDetail.value.member_price != goodsDetail.value.price) {
            price = goodsDetail.value.member_price // 会员价
        } else {
            price = goodsDetail.value.price
        }
        return price;
    })

	// 会员信息
	const memberStore = useMemberStore()
	const userInfo = computed(() => memberStore.info)

	// 购物车数量
	const cartStore = useCartStore();
	cartStore.getList();
	const cartList = computed(() => cartStore.cartList)

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
	const bindMobileRef = ref(null)
	//提交
	const confirm = ()=>{
		// 检测是否登录
		if (!userInfo.value) {
			useLogin().setLoginBack({ url: '/addon/shop/pages/goods/detail', param: {sku_id: goodsDetail.value.sku_id} })
			return false
		}
		// 绑定手机号
		if(uni.getStorageSync('isbindmobile')){
			bindMobileRef.value.open()
			return false
		}
        // 加入购物车
		if(openType.value == 'join_cart'){
			let num = 0;
			let cartId = "";

			if(cartList.value['goods_'+goodsDetail.value.goods_id]&&cartList.value['goods_'+goodsDetail.value.goods_id]['sku_'+goodsDetail.value.sku_id]){
				num = toRaw(cartList.value['goods_'+goodsDetail.value.goods_id]['sku_'+goodsDetail.value.sku_id].num);
				cartId = toRaw(cartList.value['goods_'+goodsDetail.value.goods_id]['sku_'+goodsDetail.value.sku_id].id)
			}

			num += buyNum.value;
			cartStore.increase({
				id: cartId||'',
				goods_id: goodsDetail.value.goods_id,
				sku_id: goodsDetail.value.sku_id,
				stock: goodsDetail.value.stock,
				sale_price: goodsDetail.value.sale_price,
				num: num
			},0,()=>{
				uni.showToast({
					title: '加入购物车成功',
					icon: 'none'
				});
			});

		}else if(openType.value == 'buy_now'){
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
					redirect({ url: '/addon/shop/pages/order/payment' })
				}
			});
		}

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
	::v-deep .u-number-box .u-number-box__slot{
		display: flex;
		align-items: center;
	}
</style>
