<template>
    <view :style="themeColor()">
        <view class="bg-page h-screen overflow-hidden flex flex-col" v-if="!loading">
            <view v-if="!info" class="flex-1 flex flex-col justify-center bg-[#fff]">
                <image class="overflow-hidden mx-auto w-[348rpx] h-[250rpx]" :src="img('static/resource/images/system/login.png')" model="aspectFit" />
                <view class="text-[#999] text-[26rpx] font-400 text-center leading-[30rpx]">暂未登录</view>
                <button shape="circle" plain="true" class="!w-[220rpx] mt-[40rpx] text-[26rpx] !text-[#EF000C] !border-[#EF000C] leading-[62rpx] h-[66rpx] rounded-full" @click="toLogin">去登录</button>
            </view>
            <view v-else-if="!cartList.length&&!invalidList.length" class="flex-1 flex flex-col justify-center bg-[#fff]">
                <image class="overflow-hidden mx-auto w-[348rpx] h-[256rpx]" :src="img('addon/shop/cart-empty.png')" model="aspectFit" />
                <view class="text-[#999] text-[26rpx] font-400 text-center leading-[30rpx]">赶紧去逛逛, 购买心仪的商品吧</view>
                <button shape="circle" plain="true" class="!min-w-[220rpx] mt-[40rpx] text-[26rpx] h-[66rpx] leading-[62rpx] !text-[#EF000C] !border-[#EF000C] rounded-full" @click="redirect({ url: '/addon/shop/pages/goods/list' })">去逛逛</button>
            </view>
            <block v-else>
                
                <view class="flex-1 h-0">
                    <scroll-view class="scroll-height box-border" :scroll-y="true">
                        <view class="py-[20rpx] sidebar-marign">
                            <view class="bg-[#fff] pb-[10rpx] box-border rounded-[16rpx]" v-if="cartList.length">
                                <view class="flex mx-[20rpx] justify-between items-center h-[76rpx] box-border font-400 text-[24rpx] mb-[10rpx] leading-[30rpx] border-0 border-b-[2rpx] border-solid border-[#F6F6F6]">
                                    <view class="flex items-center text-[26rpx] text-[#333]">
										<text>共</text>
										<text class="text-[32rpx] mx-[2rpx] text-[var(--price-text-color)]">{{ cartList.length }}</text>
										<text>件商品</text>
									</view>
                                    <text @click="isEdit = !isEdit" class="text-[#666] text-[26rpx]">{{ isEdit ? '完成' : '管理' }}</text>
                                </view>
                                <u-swipe-action ref="swipeActive">
                                    <block v-for="(item, index) in cartList">
                                            <view v-if="item.goodsSku"  class="py-[20rpx] overflow-hidden w-full">
                                                <u-swipe-action-item :options="cartOptions" @click="swipeClick(index,item)">
                                                    <view class="flex px-[20rpx]">
                                                        <text
                                                            class="self-center iconfont text-color text-[34rpx] mr-[16rpx] w-[34rpx] h-[34rpx] rounded-[17rpx] overflow-hidden shrink-0"
                                                            :class="{ 'iconxuanze1':item.checked,'bg-[#F5F5F5]':!item.checked}"
                                                            @click="item.checked = !item.checked">
                                                        </text>
                                                        <u--image class="rounded-[8rpx] overflow-hidden" width="170rpx" height="170rpx" @click="toDetail(item)" :src="img(item.goodsSku.sku_image_thumb_mid||'')" model="aspectFill">
                                                            <template #error>
                                                                <image class="w-[170rpx] h-[170rpx] rounded-[8rpx] overflow-hidden" :src="img('static/resource/images/diy/shop_default.jpg')" mode="aspectFill"></image>
                                                            </template>
                                                        </u--image>
                                                        <view class="flex flex-1 flex-wrap ml-[20rpx] w-[410rpx]">
                                                            <view class="w-[100%] flex flex-col items-baseline">
                                                                <view class="w-[406rpx] text-[#333] text-[28rpx] max-h-[80rpx] leading-[40rpx] multi-hidden font-400">
                                                                    {{ item.goods.goods_name }}
                                                                </view>
                                                                <view class="box-border max-w-[406rpx] mt-[4rpx] px-[14rpx] h-[36rpx] leading-[36rpx] truncate text-[#666] bg-[#F5F5F5] text-[22rpx] rounded-[20rpx]" v-if="item.goodsSku && item.goodsSku.sku_spec_format">
                                                                    {{ item.goodsSku.sku_spec_format }}
                                                                </view>
                                                            </view>
                                                            <view class="flex justify-between items-end self-end w-[100%]">
                                                                <view class="flex items-end text-[var(--price-text-color)] leading-[40rpx] price-font">
                                                                    <view class="text-[var(--price-text-color)] price-font">
                                                                        <text class="text-[26rpx] font-500">￥</text>
																		<text class="text-[36rpx] font-500">{{ parseFloat(goodsPrice(item)).toFixed(2).split('.')[0] }}</text>
																		<text class="text-[24rpx] font-500">.{{ parseFloat(goodsPrice(item)).toFixed(2).split('.')[1] }}</text>
																		<image class="h-[24rpx] ml-[6rpx]" v-if="priceType(item) == 'member_price'" :src="img('addon/shop/VIP.png')" mode="heightFix" />
																		<image class="h-[24rpx] ml-[6rpx]" v-if="priceType(item) == 'discount_price'" :src="img('addon/shop/discount.png')" mode="heightFix" />
                                                                    </view>
                                                                </view>
                                                                <u-number-box v-model="item.num" :min="numLimit(item).min"
                                                                    :max="numLimit(item).max" integer :step="1" input-width="68rpx"
                                                                    input-height="52rpx" button-size="52rpx" disabledInput
                                                                    @change="numChange($event, index)">
                                                                    <template #minus>
                                                                        <text
                                                                            :class="{ 'text-[#999]': item.num === numLimit(item).min, 'text-[#303133]': item.num !== numLimit(item).min }"
                                                                            class="text-[34rpx] nc-iconfont nc-icon-jianV6xx"></text>
                                                                    </template>
                                                                    <template #input>
                                                                        <text class="text-[#303133] text-[24rpx] mx-[10rpx] min-w-[56rpx] h-[38rpx] leading-[40rpx] text-center border-[1rpx] border-solid border-[#ddd] rounded-[4rpx]">{{ item.num }}</text>
                                                                    </template>
                                                                    <template #plus>
                                                                        <text
                                                                            :class="{ 'text-[#999]': item.num === numLimit(item).max, ' text-[#303133]': item.num !== numLimit(item).max }"
                                                                            class="text-[34rpx] nc-iconfont nc-icon-jiahaoV6xx"></text>
                                                                    </template>
                                                                </u-number-box>
                                                            </view>
                                                        </view>
                                                    </view>
                                                </u-swipe-action-item>
                                            </view>
                                    </block>
                                
                                </u-swipe-action>
                            </view>
                            <view class="bg-[#fff] pb-[10rpx] box-border rounded-[16rpx] mt-[20rpx]" v-if="invalidList.length">
                                <view class="flex px-[20rpx] justify-between items-center h-[79rpx] box-border font-400 text-[#303133] text-[24rpx] mb-[10rpx] leading-[30rpx] border-0 border-b-[2rpx] border-solid border-[#f6f6f6]">
                                    <text>共<text class="text-[var(--price-text-color)]">{{ invalidList.length }}</text>件失效商品</text>
                                    <text class="text-[#666] text-[26rpx]" @click="deleteInvalidList">清空</text>
                                </view>
                                <view v-for="(item, index) in invalidList" class="py-[20rpx] overflow-hidden" >
                                    <view class="flex px-[20rpx]">
                                        <text class="self-center iconfont iconxuanze1 text-[34rpx] mr-[20rpx] text-[#F5F5F5] rounded-[50%] overflow-hidden shrink-0"></text>
                                        <view class="relative w-[168rpx] h-[168rpx]">
                                            <u--image class="rounded-[8rpx] overflow-hidden" width="168rpx" height="168rpx" :src="img(item.goodsSku.sku_image_thumb_mid)" model="aspectFill">
                                                <template #error>
                                                    <image class="w-[168rpx] h-[168rpx] rounded-[8rpx] overflow-hidden" :src="img('static/resource/images/diy/shop_default.jpg')" mode="aspectFill"></image>
                                                </template>
                                            </u--image>
                                            <view v-if="item.goodsSku.stock == 0 " class="absolute left-0 top-0  w-[168rpx] h-[168rpx]  leading-[168rpx] text-center " style="background-color: rgba(0,0,0,0.3);">
                                                <text class="text-[#fff] text-[24rpx]">已售罄</text>
                                            </view>
                                            <view v-if="item.goodsSku.stock != 0 " class="absolute left-0 top-0  w-[168rpx] h-[168rpx]  leading-[168rpx] text-center " style="background-color: rgba(0,0,0,0.3);">
                                                <text class="text-[#fff] text-[24rpx]">已失效</text>
                                            </view>
                                        </view>
                                        <view class="flex flex-1 flex-wrap ml-[20rpx] w-[410rpx]">
                                            <view class="w-[100%] flex flex-col items-baseline">
                                                <view class="w-[406rpx] text-[#333] text-[28rpx] max-h-[80rpx] leading-[40rpx] font-400 multi-hidden">
                                                    {{ item.goods.goods_name }}
                                                </view>
                                                <view class="box-border max-w-[406rpx] mt-[4rpx] px-[14rpx] h-[36rpx] leading-[36rpx] truncate text-[#666] bg-[#F5F5F5] text-[22rpx] rounded-[20rpx]" v-if="item.goodsSku && item.goodsSku.sku_spec_format">
                                                    {{ item.goodsSku.sku_spec_format }}
                                                </view>
                                            </view>
                                            <view class="flex  justify-between items-end self-end w-[100%]">
                                                <view class="flex items-end text-[var(--price-text-color)] leading-[40rpx] price-font">
                                                    <view class="text-[var(--price-text-color)] price-font">
                                                        <text class="text-[26rpx] font-500">￥</text>
                                                        <text class="text-[36rpx] font-500">{{ parseFloat(goodsPrice(item)).toFixed(2).split('.')[0] }}</text>
                                                        <text class="text-[24rpx] font-500">.{{ parseFloat(goodsPrice(item)).toFixed(2).split('.')[1] }}</text>
                                                        <image class="h-[24rpx] ml-[6rpx]" v-if="priceType(item) == 'member_price'" :src="img('addon/shop/VIP.png')" mode="heightFix" />
                                                        <image class="h-[24rpx] ml-[6rpx]" v-if="priceType(item) == 'discount_price'" :src="img('addon/shop/discount.png')" mode="heightFix" />
                                                    </view>
                                                </view>
                                            </view>
                                        </view>
                                    </view>
                                </view>
                            </view>
                        </view>
                    </scroll-view>
                </view>
            </block>
        </view>
        
        <!--  #ifdef  H5 -->
        <view v-if="cartList.length" class="flex h-[100rpx] items-center bg-[#fff] fixed left-0 right-0 bottom-[50px] box-solid mb-ios justify-between">
            <view class="flex items-center pl-[30rpx]" @click="selectAll">
                <text class="self-center iconfont text-color text-[34rpx] mr-[20rpx] w-[34rpx] h-[34rpx] rounded-[17rpx] overflow-hidden shrink-0" :class="cartList.length == checkedNum ? 'iconxuanze1' : 'bg-[#F5F5F5]'"></text>
                <text class="font-400 text-[#303133] text-[26rpx]">全选</text>
            </view>
            <view class="flex items-center">
                <view class="flex-1 flex items-center justify-between" v-if="!isEdit">
                    <view class="flex items-center mr-[67rpx] text-[var(--price-text-color)] leading-[45rpx]">
                        <view class="font-400 text-[#303133] text-[26rpx]">合计：</view>
                        <text class="text-[var(--price-text-color)] price-font">
                            <text class="text-[26rpx] font-500">￥</text>
                            <text class="text-[36rpx] font-500">{{ parseFloat(total).toFixed(2).split('.')[0] }}</text>
                            <text class="text-[24rpx] font-500">.{{ parseFloat(total).toFixed(2).split('.')[1] }}</text>
                        </text>
                    </view>
                    <button class="w-[160rpx] h-[66rpx] text-[28rpx] leading-[66rpx] !text-[#fff] m-0 !mr-[30rpx] rounded-full primary-btn-bg remove-border" @click="settlement">结算</button>
                </view>
                <view class="flex-1 flex items-center justify-end" v-else>
                    <button class="w-[160rpx] h-[66rpx] text-[28rpx] leading-[66rpx] !text-[#fff] m-0 !mr-[30rpx] rounded-full primary-btn-bg remove-border" @click="deleteCartFn">删除</button>
                </view>
            </view>
        </view>
        <!--  #endif -->
        <!--  #ifndef  H5 -->
        <view v-if="cartList.length" class="flex h-[100rpx] items-center bg-[#fff] fixed left-0 right-0 bottom-[100rpx] box-solid mb-ios justify-between">
            <view class="flex items-center pl-[30rpx]" @click="selectAll">
                <text class="self-center iconfont text-color text-[30rpx] mr-[20rpx] w-[34rpx] h-[34rpx] rounded-[17rpx] overflow-hidden shrink-0" :class="{'iconxuanze1' :cartList.length == checkedNum, 'bg-[#F5F5F5]':cartList.length != checkedNum}"></text>
                <text class="font-400 text-[#303133] text-[26rpx]">全选</text>
            </view>
            <view class="flex items-center">
                <view class="flex-1 flex items-center justify-between" v-if="!isEdit">
                    <view class="flex items-center mr-[67rpx] text-[var(--price-text-color)] leading-[45rpx]">
                        <view class="font-400 text-[#303133] text-[26rpx]">合计：</view>
                        <text class="text-[var(--price-text-color)] price-font">
                            <text class="text-[26rpx] font-500">￥</text>
                            <text class="text-[36rpx] font-500">{{ parseFloat(total).toFixed(2).split('.')[0] }}</text>
                            <text class="text-[24rpx] font-500">.{{ parseFloat(total).toFixed(2).split('.')[1] }}</text>
                        </text>
                    </view>
                    <button class="w-[160rpx] h-[66rpx] text-[28rpx] leading-[66rpx] !text-[#fff] m-0 !mr-[30rpx] rounded-full primary-btn-bg remove-border" @click="settlement">结算</button>
                </view>
                <view class="flex-1 flex items-center justify-end" v-else>
                    <button class="w-[160rpx] h-[66rpx] text-[28rpx] leading-[66rpx] !text-[#fff] m-0 !mr-[30rpx] rounded-full primary-btn-bg remove-border" @click="deleteCartFn">删除</button>
                </view>
            </view>
        </view>
        <!--  #endif -->
        <u-loading-page bg-color="rgb(248,248,248)" :loading="loading" loadingText="" fontSize="16" color="#303133"></u-loading-page>
        <tabbar />
        <!-- 强制绑定手机号 -->
		<bind-mobile ref="bindMobileRef" /> 
    </view>
</template>

<script setup lang="ts">
import { ref, computed, watch,nextTick } from 'vue'
import useMemberStore from '@/stores/member'
import { useLogin } from '@/hooks/useLogin'
import { onShow } from '@dcloudio/uni-app'
import { img, redirect, getToken } from '@/utils/common'
import useCartStore from '@/addon/shop/stores/cart'
import { getCartGoodsList } from '@/addon/shop/api/cart'
import bindMobile from '@/components/bind-mobile/bind-mobile.vue';
import {t} from "@/locale";

const memberStore = useMemberStore()
const info = computed(() => memberStore.info)
const loading = ref(true)
const optionLoading = ref(false)
const total = ref('0.00')
const cartList = ref<object[]>([])
const invalidList = ref<object[]>([]) //  失效商品：已下架、已删除
const isEdit = ref(false)
const querOne = ref(true)
const cartStore = useCartStore();

const getCartGoodsListFn = () => {
    getCartGoodsList({}).then(({ data }) => {
        cartList.value = []
        invalidList.value = []
        loading.value = false
        data.forEach(item => {
            item.checked = false
            if (item.goodsSku) {
                if (item.goods.status && item.goods.delete_time == 0) {
                    if (item.goodsSku.stock) {
                        if (item.num > item.goodsSku.stock) item.num = item.goodsSku.stock;
                        cartList.value.push(item)
                    } else {
                        // 库存为0 时，移动到售罄商品
                        invalidList.value.push(item)
                    }
                } else {
                    invalidList.value.push(item)
                }
            }
        })
        selectAll()
        if (querOne.value) querOne.value = false
    }).catch((err) => {
        if (err.code == 401) {
            cartList.value = []
            invalidList.value = []
            loading.value = false
        }
    })
}

onShow(() => {
    getCartGoodsListFn()
    cartStore.getList();
})

const checkedNum = computed(() => {
	let num = 0
	cartList.value.forEach(item => {
		item.checked && (num += 1)
	})
	return num
})

watch(() => cartList.value, () => {
    let value = 0
    cartList.value.forEach((item:any) => {
        if (item.checked && item.goodsSku) {
            let price: any = 0;
            if (item.goods.is_discount && item.goodsSku.sale_price != item.goodsSku.price) {
                price = item.goodsSku.sale_price // 折扣价
            } else if (item.goods.member_discount && getToken() && item.goodsSku.member_price != item.goodsSku.price) {
                price = item.goodsSku.member_price // 会员价
            } else {
                price = item.goodsSku.price
            }

            value += parseFloat(price) * item.num
        }
    })
    total.value = value.toFixed(2)
}, { deep: true })

const toLogin = () => {
	useLogin().setLoginBack({ url: '/addon/shop/pages/goods/cart' })
}

const toDetail = (data: any) => {
    redirect({ url: '/addon/shop/pages/goods/detail', param: { goods_id: data.goods_id } })
}

const numChange = (event, index) => {
	uni.$u.debounce((event) => {
		const data = cartList.value[index]

		cartStore.increase({
			id: data.id,
			goods_id: data.goods_id,
			sku_id: data.sku_id,
			stock: data.goodsSku.stock,
			sale_price: data.goodsSku.sale_price,
			num: data.num
		}, 0);
	}, 500)
}

const numLimit = (data) => {
	return {
		min: 1,
		max: data.goodsSku.stock || 1
	}
}

const cartOptions = ref([
    {
        text: t('delete'),
        style: {
            backgroundColor: '#F56C6C'
        }
    }
])

const swipeActive = ref()
const swipeClick = (index:any,item:any) => {
	if (optionLoading.value) return
	optionLoading.value = true
	cartStore.delete(item.id, () => {
		cartList.value.splice(index, 1) 
		nextTick(()=>{if(swipeActive.value)swipeActive.value.closeOther() })
		optionLoading.value = false
	})
}
/**
 * 全选
 */
const selectAll = () => {
	const checked = cartList.value.length == checkedNum.value ? false : true
	cartList.value.forEach(item => {
		item.checked = checked
	})
}

 //强制绑定手机号
const bindMobileRef = ref(null)
/**
 * 结算
 */
const settlement = () => {
    if(uni.getStorageSync('isbindmobile')){
        bindMobileRef.value.open()
        return false
    }
	if (!checkedNum.value) {
		uni.showToast({ title: '还没有选择商品', icon: 'none' })
		return
	}
	const ids = []
	cartList.value.forEach(item => {
		if (item.checked) ids.push(item.id)
	})

	uni.setStorage({
		key: 'orderCreateData',
		data: {
			cart_ids: ids
		},
		success() {
			redirect({ url: '/addon/shop/pages/order/payment' })
		}
	})
}

/**
 * 删除
 */
const deleteCartFn = () => {
	if (!checkedNum.value) {
		uni.showToast({ title: '还没有选择商品', icon: 'none' })
		return
	}
	if (optionLoading.value) return
	optionLoading.value = true

	const ids = []
	cartList.value.forEach(item => {
		if (item.checked) ids.push(item.id)
	})

	cartStore.delete(ids, () => {
		getCartGoodsListFn()
		optionLoading.value = false
	})
}
/**
 * 清空无效商品
 */
const deleteInvalidList = ()=>{
	if (optionLoading.value) return
	optionLoading.value = true
	const ids = invalidList.value.map((el)=>el.id)

	cartStore.delete(ids, () => {
		getCartGoodsListFn()
		optionLoading.value = false
	})
	invalidList.value = []
}

// 价格类型 
const priceType = (data:any) => {
    let type = "";
    if (data.goods.is_discount && data.goodsSku.sale_price != data.goodsSku.price) {
        type = 'discount_price'// 折扣
    } else if (data.goods.member_discount && getToken() && data.goodsSku.member_price != data.goodsSku.price) {
        type = 'member_price' // 会员价
    } else {
        type = ""
    }
    return type;
}

// 商品价格
const goodsPrice = (data:any) => {
    let price = "0.00";
    if (data.goods.is_discount && data.goodsSku.sale_price != data.goodsSku.price) {
        price = data.goodsSku.sale_price ? data.goodsSku.sale_price : data.goodsSku.price // 折扣价
    } else if (data.goods.member_discount && getToken() && data.goodsSku.member_price != data.goodsSku.price) {
        price = data.goodsSku.member_price ? data.goodsSku.member_price : data.goodsSku.price // 会员价
    } else {
        price = data.goodsSku.price
    }
    return price;
}
</script>
<style lang="scss" scoped>
.remove-border {
	&::after {
		border: none;
	}
}
:deep(uni-page) {
	background: var(--page-bg-color);
}

uni-page-body {
	height: 100%;
}

.text-color {
	color: var(--primary-color);
}

.bg-color {
	background-color: var(--primary-color);
}

:deep(.tab-bar-placeholder) {
	display: none !important;
}

:deep(.u-tabbar__placeholder) {
	display: none !important;
}

/*  #ifdef  H5  */
.scroll-height {
	height: calc(100vh - 100rpx - 50px - constant(safe-area-inset-bottom));
	height: calc(100vh - 100rpx - 50px - env(safe-area-inset-bottom));
}

/*  #endif  */
/*  #ifndef  H5  */
.scroll-height {
	height: calc(100vh - 200rpx - constant(safe-area-inset-bottom));
	height: calc(100vh - 200rpx - env(safe-area-inset-bottom));
}
/*  #endif  */

.text-ellipsis{
    display: -webkit-box;
    -webkit-box-orient: vertical;
    -webkit-line-clamp: 2;
    overflow: hidden;
}
:deep(.u-swipe-action-item__right__button__wrapper){
    padding:0 10rpx !important;
}
:deep(.u-swipe-action-item__right__button__wrapper__text){
    font-size:24rpx !important;
}
</style>
