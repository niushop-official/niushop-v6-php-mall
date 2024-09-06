<template>
	<view @touchmove.prevent.stop>
		<u-popup :show="goodsSkuPop" @close="closeFn" mode="bottom">
			<view v-if="Object.keys(goodsDetail).length"  @touchmove.prevent.stop class="rounded-t-[20rpx] overflow-hidden bg-[#fff] p-[32rpx] relative">
				<view class="flex mb-[58rpx]">
					<view class="rounded-[8rpx] overflow-hidden">
						<u--image width="180rpx" height="180rpx" :src="img(detail.sku_image)" model="aspectFill">
							<template #error>
                                <image class="w-[180rpx] h-[180rpx]" :src="img('static/resource/images/diy/shop_default.jpg')" mode="aspectFill"></image>
							</template>
						</u--image>
					</view>

					<view class="flex flex-1 flex-col ml-[20rpx] py-[10rpx]">
						<view class="w-[100%]">
							<view class=" text-[var(--price-text-color)]  flex items-baseline">
								<text class="text-[28rpx] font-bold price-font mr-[4rpx]">￥</text>
								<text class="text-[42rpx] font-bold price-font">{{ parseFloat(goodsPrice(detail)).toFixed(2).split('.')[0] }}</text>
								<text class="text-[28rpx] font-bold price-font">.{{ parseFloat(goodsPrice(detail)).toFixed(2).split('.')[1] }}</text>
								<image class="h-[24rpx] ml-[6rpx]" v-if="priceType(detail) == 'member_price'" :src="img('addon/shop/VIP.png')" mode="heightFix" />
								<image class="h-[24rpx] ml-[6rpx]" v-if="priceType(detail) == 'discount_price'" :src="img('addon/shop/discount.png')" mode="heightFix" />
							</view>
							<view class="text-[24rpx] leading-[32rpx] text-[#303133] mt-[12rpx]">库存{{ detail.stock }}{{ goodsDetail.goods.unit }}</view>
						</view>
						<view class="w-[100%] mt-auto" style="max-height: calc(204rpx - 126rpx); overflow: hidden;" v-if="goodsDetail.goodsSpec && goodsDetail.goodsSpec.length">
							<text class="text-[24rpx] leading-[30rpx] text-[#666]">已选规格：{{ detail.sku_spec_format }}</text>
						</view>
					</view>
				</view>
				<scroll-view class="h-[500rpx] mb-[30rpx]" scroll-y="true">
					<view :class="{'mt-[36rpx]': 0 != index }" v-for="(item, index) in goodsDetail.goodsSpec" :key="index">
						<view class="text-[26rpx] leading-[36rpx] mb-[24rpx]">{{ item.spec_name }}</view>
						<view class="flex flex-wrap">
							<view
								class="box-border bg-[#f2f2f2] text-[24rpx] px-[44rpx] text-center h-[56rpx] leading-[52rpx] mr-[20rpx] mb-[20rpx] border-1 border-solid rounded-[50rpx] border-[#f2f2f2]"
								:class="{'!border-[var(--primary-color)] text-[var(--primary-color)] !bg-[var(--primary-color-light)]': subItem.selected}"
								v-for="(subItem, subIndex) in item.values" :key="subIndex" @click="change(subItem, index)">
								{{ subItem.name }}
							</view>
						</view>
					</view>
					<view class="flex justify-between items-center mt-[30rpx]">
						<view class="text-[26rpx]">购买数量</view>
						<u-number-box
							v-if="cartList['goods_' + detail.goods_id] && cartList['goods_' + detail.goods_id]['sku_' + detail.sku_id]"
							v-model="buyNum" :min="0" :max="detail.stock" integer :step="1" input-width="98rpx"
							input-height="54rpx">
							<template #minus>
								<text class="text-[34rpx] nc-iconfont nc-icon-jianV6xx" :class="{ '!text-[#c8c9cc]': buyNum === 0 }"></text>
							</template>
							<template #input>
								<text class="text-[#303133] text-[24rpx] mx-[10rpx] min-w-[56rpx] h-[38rpx] leading-[40rpx] text-center border-[1rpx] border-solid border-[#ddd] rounded-[4rpx]">{{ buyNum }}</text>
							</template>
							<template #plus>
								<text class="text-[34rpx] nc-iconfont nc-icon-jiahaoV6xx" :class="{ '!text-[#c8c9cc]': buyNum === detail.stock }"></text>
							</template>
						</u-number-box>
						<u-number-box v-else v-model="buyNum" :min="1" :max="detail.stock" integer :step="1" input-width="98rpx" input-height="54rpx">
							<template #minus>
								<text class="text-[34rpx] nc-iconfont nc-icon-jianV6xx" :class="{ '!text-[#c8c9cc]': buyNum === 1 }"></text>
							</template>
							<template #input>
								<text class="text-[#303133] text-[24rpx] mx-[10rpx] min-w-[56rpx] h-[38rpx] leading-[40rpx] text-center border-[1rpx] border-solid border-[#ddd] rounded-[4rpx]">{{ buyNum }}</text>
							</template>
							<template #plus>
								<text class="text-[34rpx] nc-iconfont nc-icon-jiahaoV6xx" :class="{ '!text-[#c8c9cc]': buyNum === detail.stock }"></text>
							</template>
						</u-number-box>

					</view>
				</scroll-view>
				<button v-if="detail.stock > 0" class="!h-[72rpx] primary-btn-bg leading-[72rpx] text-[26rpx] rounded-[50rpx]" type="primary" @click="save">确定</button>
				<button v-else class="!h-[72rpx] leading-[72rpx] text-[26rpx] text-[#fff] bg-[#ccc] rounded-[50rpx]">已售罄</button>
			</view>
		</u-popup>
	</view>
</template>

<script setup lang="ts">
import { ref, reactive, computed, watch, toRaw } from 'vue';
import { img, redirect, getToken } from '@/utils/common';
import { getGoodsSku } from '@/addon/shop/api/goods';
import useCartStore from '@/addon/shop/stores/cart'

const cartStore = useCartStore();
const cartList = computed(() => cartStore.cartList)

const goodsSkuPop = ref(false);
const currSpec = ref({
	skuId: "",
	name: []
})
const info = ref({})//获取原始数据
const detail:any = ref({})//展示数据
const buyNum = ref(1)

const getGoodsSkuFn = (sku_id: any) => {
	getGoodsSku(sku_id).then(res => {
		info.value = res.data
		// 当前详情内容
		currSpec.value.sku_id = sku_id
		if (info.value.skuList && Object.keys(info.value.skuList).length) {
			info.value.skuList.forEach((item) => {
				if (item.sku_id == sku_id) {
					detail.value = item;
					currSpec.value.name = item.sku_spec_format.split(",");
				}
			})
		}
		goodsSkuPop.value = true;

	})
}

const open = (sku_id: any) => {
	getGoodsSkuFn(sku_id)
}
const closeFn = () => {
	goodsSkuPop.value = false
}

const goodsDetail = computed(() => {
	let data = JSON.parse(JSON.stringify(info.value));

	// 重组数据结构
	if (Object.keys(data).length) {

		if (!Object.keys(currSpec.value.name).length) currSpec.value.name = data.sku_spec_format.split(",");

		data.goodsSpec.forEach((item, index) => {
			let specName = item.spec_values.split(",");
			item.values = [];
			specName.forEach((specItem, specIndex) => {
				item.values[specIndex] = {};
				item.values[specIndex].name = specItem;
				item.values[specIndex].selected = false;
				item.values[specIndex].disabled = false;

				// 选中规格
				currSpec.value.name.forEach((currSpecItem, currSpecIndex) => {
					if (currSpecIndex == index && currSpecItem == specItem) {
						item.values[specIndex].selected = true;
					}
				})
			})
		})
		getSkuId();

		// 当前详情内容
		if (data.skuList && Object.keys(data.skuList).length) {
			data.skuList.forEach((item) => {
				if (item.sku_id == currSpec.value.skuId) {
					detail.value = item;
				}
			})
		}
	}
	return data;
})

watch(
	() => detail.value,
	(newValue, oldValue) => {
		if (cartList.value['goods_' + detail.value.goods_id] && cartList.value['goods_' + detail.value.goods_id]['sku_' + detail.value.sku_id]) {
			buyNum.value = toRaw(cartList.value['goods_' + detail.value.goods_id]['sku_' + detail.value.sku_id].num);
			detail.value.cart_id = toRaw(cartList.value['goods_' + detail.value.goods_id]['sku_' + detail.value.sku_id].id)
		} else {
			buyNum.value = 1
			detail.value.cart_id = ''
		}

	}
)
const change = (data, index) => {
	currSpec.value.name[index] = data.name;
	buyNum.value = 1
	// getSkuId();
}

const getSkuId = () => {
	info.value.skuList.forEach((skuItem, skuIndex) => {
		let sku_spec_format = skuItem.sku_spec_format.split(',')
		if (currSpec.value.name.every(v => sku_spec_format.includes(v))) {
			currSpec.value.skuId = skuItem.sku_id
		}
	})
}

const save = () => {
    //删除商品
    if (buyNum.value == 0) {
        // 购物车减少数量
        cartStore.reduce({
            id: detail.value.cart_id || '',
            goods_id: detail.value.goods_id,
            sale_price: detail.value.sale_price,
            sku_id: detail.value.sku_id
        });
    } else {

        let price = 0

        if (goodsDetail.value.goods.is_discount && detail.value.sale_price != detail.value.price) {
            price = detail.value.sale_price ? detail.value.sale_price : detail.value.price // 折扣价
        } else if (goodsDetail.value.goods.member_discount && getToken() && detail.value.member_price != detail.value.price) {
            price = detail.value.member_price ? detail.value.member_price : detail.value.price // 会员价
        } else {
            price = detail.value.price
        }

        // 购物车添加数量
        cartStore.increase({
            id: detail.value.cart_id || '',
            goods_id: detail.value.goods_id,
            sku_id: detail.value.sku_id,
            stock: detail.value.stock,
            sale_price: price,
            num: buyNum.value,
        }, 0, () => {
            uni.showToast({
                title: '加入购物车成功',
                icon: 'none'
            });
        });
    }
    goodsSkuPop.value = false

}

// 商品价格
const goodsPrice = (data:any) => {
    let price = "0.00";
    if (goodsDetail.value.goods.is_discount && data.sale_price != data.price) {
        price = data.sale_price ? data.sale_price : data.price // 折扣价
    } else if (goodsDetail.value.goods.member_discount && getToken() && data.member_price != data.price) {
        price = data.member_price ? data.member_price : data.price // 会员价
    } else {
        price = data.price
    }
    return price;
}

// 价格类型 
const priceType = (data:any) => {
    let type = "";
    if (goodsDetail.value.goods.is_discount && data.sale_price != data.price) {
        type = 'discount_price'// 折扣
    } else if (goodsDetail.value.goods.member_discount && getToken() && data.member_price != data.price) {
        type = 'member_price' // 会员价
    } else {
        type = ""
    }
    return type;
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