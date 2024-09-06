<template>
	<view :style="themeColor()">
		<view
				v-if="Object.keys(detail).length&&!loading"
				class="overflow-hidden min-h-screen bg-style relative"
				:style="{ 'background': 'url(' + img('addon/shop/coupon/coupon_bg.png') + ') no-repeat' }">
			<!-- #ifdef MP -->
			<top-tabbar :data="topTabbarData" :scrollBool="topTabarObj.getScrollBool()" />
			<!-- #endif -->
			<view class="relative mt-[236rpx] w-[100%] h-[932rpx]" :style="{ 'background': 'url(' + img('addon/shop/coupon/coupon_bg_02.png') + ') center / contain no-repeat' }">
				<view v-if="detail.limit_count"
				      :style="{ 'background': 'url(' + img('addon/shop/coupon/top_tab.png') + ') center / cover no-repeat', 'transform': 'translateX(-50%)'}"
				      class="text-[32rpx] leading-[56rpx] top-[2rpx] left-[50%] px-[30rpx] box-border justify-center absolute min-w-[196rpx] h-[56rpx] flex items-center text-[#FFF9DD]">
					限领{{ detail.limit_count }}张
				</view>
				<view class="flex justify-center pt-[90rpx]">
					<text class="max-w-[380rpx] text-[var(--price-text-color)] text-[140rpx] truncate price-font">{{ detail.coupon_price || 0.00 }}</text>
					<text class="flex items-center justify-center text-[44rpx] mt-[54rpx] ml-[8rpx] text-[#F7D894] bg-[var(--price-text-color)] rounded-full w-[70rpx] h-[70rpx]">元</text>
				</view>
				<view class="h-[64rpx] leading-[64rpx] text-[42rpx] text-[#E22D17] mt-[10rpx] text-center">
					<text v-if="detail.min_condition_money === '0.00'">无门槛</text>
					<text v-else>满{{ detail.coupon_min_price }}元可用</text>
				</view>
				<view class="text-[26rpx] h-[36rpx] text-[#E22D17] mt-[44rpx] text-center flex justify-center items-center">
					<block v-if="detail.valid_type == 1">
						<text>领取之日起</text>
						<text>{{ detail.length}}</text>
						<text>天内有效</text>
					</block>
					<block v-else>
						<text>有效期至</text>
						<text>{{ detail.valid_end_time ? detail.valid_end_time.slice(0, 10) : '' }}</text>
					</block>
				</view>
				<view class="flex justify-center items-center mt-[20rpx]">
					<text v-if="detail.btnType === 'collected'"
					      class="!leading-[100rpx] text-center text-[#fff] text-[46rpx] min-w-[240rpx] h-[106rpx]"
					      :style="{ 'background': 'url(' + img('addon/shop/coupon/coupon_btn_02.png') + ')  center / contain no-repeat' }"
					>已领完
					</text>
					<text v-if="detail.btnType === 'collecting'"
					      class="!leading-[100rpx] text-center text-[#E22D17] text-[46rpx] min-w-[240rpx] h-[106rpx]"
					      :style="{ 'background': 'url(' + img('addon/shop/coupon/coupon_btn.png') + ') center / contain no-repeat' }"
					      @click="collecting(detail.id)">领取
					</text>
					<text v-if="detail.btnType === 'using'"
					      class="!leading-[100rpx] text-center text-[#E22D17] text-[46rpx] min-w-[240rpx] h-[106rpx]"
					      :style="{ 'background': 'url(' + img('addon/shop/coupon/coupon_btn.png') + ') center / contain no-repeat' }"
					      @click="toLink(detail.id)">去使用
					</text>
				</view>
				<view class="w-[230rpx] h-[230rpx] box-border p-[14rpx] bg-[#fff] mx-[auto] mt-[50rpx]">
					<image class="w-[200rpx] h-[200rpx]" :src="codeUrl" mode="aspectFill" />
				</view>
				<view class="text-[24rpx] text-[rgba(255,255,255,0.7)] mt-[30rpx] text-center">注:扫描二维码或点击右上角进行分享</view>
			</view>

		</view>
		<u-loading-page bg-color="rgb(248,248,248)" :loading="loading" loadingText="" fontSize="16" color="#303133"></u-loading-page>
        <!-- 强制绑定手机号 -->
		<bind-mobile ref="bindMobileRef" /> 
	</view>
</template>
<script lang="ts" setup>
    import { ref, computed } from 'vue'
    import { onLoad } from '@dcloudio/uni-app'
    import { img, redirect, handleOnloadParams } from '@/utils/common'
    import QRCode from "qrcode";
    import { topTabar } from '@/utils/topTabbar'
    import { getShopCouponInfo, getCoupon, getShopCouponQrocde } from '@/addon/shop/api/coupon'
    import bindMobile from '@/components/bind-mobile/bind-mobile.vue';
    import useMemberStore from '@/stores/member'
    import { useLogin } from '@/hooks/useLogin'

    const loading = ref(false)
    const codeUrl = ref('')
    const detail = ref({})
    const memberStore = useMemberStore()
    const userInfo = computed(() => memberStore.info)

    /********* 自定义头部 - start ***********/
    const topTabarObj = topTabar()
    let topTabbarData = topTabarObj.setTopTabbarParam({title:'优惠券详情'})
    /********* 自定义头部 - end ***********/

    onLoad((option) => {

        // #ifdef MP-WEIXIN
        // 处理小程序场景值参数
        option = handleOnloadParams(option);
        // #endif

        if (!option.coupon_id) {
            uni.showToast({ title: '优惠券不存在', icon: 'none' })
            setTimeout(() => {
                redirect({ url: '/addon/shop/pages/index', mode: 'reLaunch' })
            }, 600)
            return false
        } else {
            getShopCouponInfoFn(option.coupon_id)
            getShopCouponQrocdeFn(option.coupon_id)
        }

    })

    const getShopCouponInfoFn = (id: number) => {
        loading.value = true
        getShopCouponInfo(id).then((res: any) => {
            detail.value = res.data
            if (detail.value.sum_count != -1 && detail.value.receive_count === detail.value.sum_count) {
                detail.value.btnType = 'collected'//已领完
            }
            if (!userInfo.value) {
                detail.value.btnType = 'collecting'//领用
            } else {
                if (detail.value.is_receive && detail.value.limit_count === detail.value.member_receive_count) {
                    detail.value.btnType = 'using'//去使用
                } else {
                    detail.value.btnType = 'collecting'//领用
                }
            }
            loading.value = false

        }).catch(() => {
            loading.value = false
            detail.value = {}
            setTimeout(() => {
                redirect({ url: '/addon/shop/pages/index', mode: 'reLaunch' })
            }, 600)
        })
    }

    const getShopCouponQrocdeFn = (id) => {
        // #ifdef H5
        QRCode.toDataURL(window.location.href, { errorCorrectionLevel: 'L', margin: 0, width: 100 }).then(url => {
            codeUrl.value = url
        });
        // #endif

        // #ifdef MP-WEIXIN
        getShopCouponQrocde(id).then((res: any) => {
            if (res.data) {
                codeUrl.value = img(res.data);
            }
        })
        // #endif
    }

    //强制绑定手机号
    const bindMobileRef = ref(null)
    const collecting = (coupon_id: any) => {
        if (!userInfo.value) {
            useLogin().setLoginBack({ url: '/addon/shop/pages/coupon/list' })
            return false
        }
        if(uni.getStorageSync('isbindmobile')){
            bindMobileRef.value.open()
            return false
        }
        getCoupon({ coupon_id, number: 1 }).then(res => {
            detail.value.btnType = 'using'
        })
    }
    const toLink = (coupon_id: any) => {
        redirect({ url: '/addon/shop/pages/goods/list', param: { coupon_id } })
    }
</script>
<style lang="scss" scoped>
	.border-color {
		border-color: var(--primary-color) !important;
	}

	.text-color {
		color: var(--primary-color) !important;
	}

	.bg-color {
		background-color: var(--primary-color) !important;
	}

	.bg-style {
		background-size: 100% 100% !important;
	}

	.code {
		box-shadow: 0 0 20px -1px;
	}
</style>
