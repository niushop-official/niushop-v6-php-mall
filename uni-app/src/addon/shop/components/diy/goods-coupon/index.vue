<template>
	<x-skeleton :type="skeleton.type" :loading="skeleton.loading" :config="skeleton.config" v-if="couponList.length > 0 && Object.keys(couponList).length > 0">
		<view :style="warpCss" class="overflow-hidden">

			<view v-if="diyComponent.style == 'style-1'" class="coupon-wrap style-1 relative">
				<scroll-view scroll-x="true" class="coupon-list" :style="{'background-image':'url(' + img('addon/shop/diy/goods_coupon/style1_bg2.png') + ')','background-size':'100%','background-repeat':'no-repeat'}">
					<view class="coupon-class">
						<block v-if="couponList.length > 1">
							<view v-for="(item,index) in couponList" :key="index" class="box-border pt-[14rpx] inline-flex flex-col items-center relative w-[150rpx] h-[130rpx]" :class="{'mr-[20rpx]': index != couponList.length-1}" :style="{'background-image':'url(' + img('addon/shop/diy/goods_coupon/coupon_item_bg.png') + ')','background-size':'100%','background-repeat':'no-repeat'}">
								<view class="truncate w-full flex items-baseline justify-center price-font text-[var(--price-text-color)]">
									<text class="text-[26rpx] font-500">￥</text>
									<text class="text-[36rpx] truncate font-500">{{ parseFloat(item.price) }}</text>
								</view>
								<view class="text-[#303133] text-[20rpx] mt-[12rpx]">{{ item.min_condition_money == '0.00' ? '无门槛' : ('满'+parseFloat(item.min_condition_money)+'元可用') }}</view>
								<view class="mt-[auto] rounded-b-[12rpx] text-[#f2333c] text-[20rpx] w-[100%] h-[36rpx] flex items-center justify-center bg-[#fff5f2]">{{item.type_name}}</view>
							</view>
						</block>
						<block v-else>
							<view v-for="(item,index) in couponList" :key="index" class="box-border pt-[14rpx] pl-[44rpx] pr-[44rpx] inline-flex items-center justify-between relative w-[100%] h-[130rpx]" :style="{'background-image':'url(' + img('addon/shop/diy/goods_coupon/style1_bg4.png') + ')','background-size':'100%','background-repeat':'no-repeat'}">
								<view class="flex price-font text-[var(--price-text-color)] items-baseline">
									<text class="text-[36rpx] mt-[16rpx] mr-[4rpx]">￥</text>
									<text class="text-[85rpx] font-bold max-w-[170rpx] truncate">{{parseFloat(item.price)}}</text>
								</view>
								<view class="border-0 border-dashed border-r-[2rpx] border-[#FF323C] absolute left-[40%] top-[46rpx] bottom-0 w-[2rpx]"></view>
								<view class="w-[270rpx]">
									<view class="flex items-center mt-[auto]">
										<text class="rounded-[4rpx] bg-[#fff3f0] text-[#f2333c] border-[2rpx] border-solid border-[#f2333c] text-[22rpx] px-[6rpx] pb-[4rpx] pt-[6rpx] flex items-center justify-center whitespace-nowrap">{{item.type_name}}</text>
										<text class="ml-[4rpx] text-[#f2333c] max-w-[184rpx] truncate">{{item.title}}</text>
									</view>
									<view class="text-[#f2333c] text-[32rpx] font-bold mt-[10rpx] w-[270rpx] truncate">{{item.min_condition_money == '0.00' ? '无门槛' : ('消费满'+parseFloat(item.min_condition_money)+'元可用') }}</view>
								</view>
							</view>
						</block>
					</view>
				</scroll-view>
				<view class="w-[100%] h-[130rpx] pt-[24rpx] px-[26rpx] box-border flex items-center justify-between absolute left-0 right-0 bottom-0" :style="{'background-image':'url(' + img('addon/shop/diy/goods_coupon/style1_bg.png') + ')','background-size':'100% 130rpx','background-repeat':'no-repeat'}">
					<view class="flex flex-col">
						<text class="text-[32rpx] text-[#fff] font-bold">{{ diyComponent.couponTitle }}</text>
						<text class="text-[22rpx] text-[#fff] mt-[10rpx]">{{ diyComponent.couponSubTitle }}</text>
					</view>
					<text v-if="diyComponent.btnText" @click="toLink('/addon/shop/pages/coupon/list')" class="bg-[#fff] flex leading-[52rpx] justify-center text-[#FF4142] text-[24rpx] min-w-[100rpx] px-[24rpx] box-border h-[48rpx] coupon-buy-btn">{{diyComponent.btnText}}</text>
				</view>
			</view>

			<view v-else-if="diyComponent.style == 'style-2'" class="coupon-wrap style-2 relative">
				<scroll-view scroll-x="true" class="coupon-list">
					<view v-for="(item,index) in couponList" :key="index" class="box-border pt-[14rpx] inline-flex flex-col items-center relative w-[140rpx] h-[130rpx] rounded-[10rpx]" :class="{'mr-[20rpx]': index != couponList.length-1, 'mr-[290rpx]': index == couponList.length-1}" :style="{'background-image':'url(' + img('addon/shop/diy/goods_coupon/coupon_item_bg.png') + ')','background-size':'100%','background-repeat':'no-repeat'}">
						<view class="flex items-baseline justify-center w-full truncate price-font text-[var(--price-text-color)]">
							<text class="text-[24rpx]">￥</text>
							<text class="text-[38rpx] font-bold truncate">{{parseFloat(item.price)}}</text>
						</view>
						<view class="text-[#303133] text-[20rpx] truncate max-w-[120rpx] mt-[12rpx]">{{item.min_condition_money == '0.00' ? '无门槛' : ('满'+parseFloat(item.min_condition_money)+'元可用') }}</view>
						<view class="mt-[auto] rounded-b-[12rpx] text-[#f2333c] text-[20rpx] w-[100%] h-[36rpx] flex items-center justify-center bg-[#fff5f2]">{{item.type_name}}</view>
					</view>
				</scroll-view>
				<view class="w-[290rpx] h-[170rpx] py-[20rpx] pl-[30rpx] box-border flex flex-col items-center justify-between absolute right-0 bottom-0" :style="{'background-image':'url(' + img('addon/shop/diy/goods_coupon/style2_bg.png') + ')','background-size':'290rpx 170rpx','background-repeat':'no-repeat'}">
					<text class="text-[32rpx] text-[#fff] font-bold">{{ diyComponent.couponTitle }}</text>
					<text class="text-[22rpx] text-[#fff] mt-[14rpx]">{{ diyComponent.couponSubTitle }}</text>
					<text v-if="diyComponent.btnText" @click="toLink('/addon/shop/pages/coupon/list')" class="bg-[#fff] text-[#FF4142] text-[24rpx] min-w-[100rpx] px-[24rpx] box-border h-[48rpx] leading-[52rpx] text-center coupon-buy-btn mt-auto">{{diyComponent.btnText}}</text>
				</view>
			</view>

		</view>
	</x-skeleton>
</template>

<script setup lang="ts">
	// 优惠券组件
	import { ref, reactive, computed, watch, onMounted } from 'vue';
	import { img, redirect } from '@/utils/common';
	import useDiyStore from '@/app/stores/diy';

    import { getShopCouponComponents } from '@/addon/shop/api/coupon'

	const props = defineProps(['component', 'index', 'pullDownRefreshCount']);
	const diyStore = useDiyStore();

    const skeleton = reactive({
        type: 'banner',
        loading: diyStore.mode == 'decorate' ? false : true,
        config: {
            gridRows: 1,
            gridRowsGap: '0rpx',
            headHeight: '170rpx'
        }
    })

	const diyComponent = computed(() => {
		if (diyStore.mode == 'decorate') {
			return diyStore.value[props.index];
		} else {
			return props.component;
		}
	})

	const warpCss = computed(() => {
		var style = '';
		if (diyComponent.value.topRounded) style += 'border-top-left-radius:' + diyComponent.value.topRounded * 2 + 'rpx;';
		if (diyComponent.value.topRounded) style += 'border-top-right-radius:' + diyComponent.value.topRounded * 2 + 'rpx;';
		if (diyComponent.value.bottomRounded) style += 'border-bottom-left-radius:' + diyComponent.value.bottomRounded * 2 + 'rpx;';
		if (diyComponent.value.bottomRounded) style += 'border-bottom-right-radius:' + diyComponent.value.bottomRounded * 2 + 'rpx;';
		return style;
	})

	watch(
		() => props.pullDownRefreshCount,
		(newValue, oldValue) => {
			// 处理下拉刷新业务
		}
	)

    const toLink = (url: any)=>{
        if (diyStore.mode == 'decorate') return;
        redirect({ url })
    }

    const couponList: any = ref([])
    const getShopCouponListFn = () => {
        let data: object = {
            num: diyComponent.value.source == 'all' ? diyComponent.value.num : '',
            coupon_ids: diyComponent.value.source == 'custom' ? diyComponent.value.couponIds : '',
        };
        getShopCouponComponents(data).then((res: any) => {
            couponList.value = res.data
            skeleton.loading = false;
        }).catch(() => {
        })
    }

    onMounted(() => {
        refresh();
    });

    const refresh = () => {

        // 装修模式下设置默认图
        if (diyStore.mode == 'decorate') {
            let obj = {
                title: '满减券',
                type_name: '通用券',
                price: 100,
                min_condition_money: 0,
            };
            for (let i = 0; i < 4; i++) {
                couponList.value.push(obj);
            }
        } else {
            getShopCouponListFn();
        }
    }

</script>

<style lang="scss" scoped>
	.coupon-wrap{
		&.style-1{
			.coupon-list{
				position: relative;
				height: 270rpx;
				width: 100%;
				white-space: nowrap;
				padding: 30rpx 48rpx 0;
				box-sizing: border-box;
				&::before{
					content: "";
					position: absolute;
					top: 0;
					left: 26rpx;
					right: 26rpx;
					bottom: 0;
					background: linear-gradient(#FEF9EC, #FCD9A5);
					border-radius: 20rpx;
				}
			}
			.coupon-buy-btn{
				border-radius: 50rpx;
			}
		}
		&.style-2{
			.coupon-list{
				position: relative;
				height: 170rpx;
				width: 100%;
				white-space: nowrap;
				padding: 20rpx 0 20rpx 20rpx;
				box-sizing: border-box;
				border-radius: 12rpx;
				overflow: hidden;
				background: linear-gradient(#EE3928, #EF3F30);
			}
			.coupon-buy-btn{
				border-radius: 50rpx;
			}
		}
	}
</style>
