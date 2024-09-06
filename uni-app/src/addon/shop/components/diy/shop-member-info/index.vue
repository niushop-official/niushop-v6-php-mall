<template>
	<view :style="warpCss">
		<view class="px-[30rpx] pt-[30rpx] pb-[120rpx] box-border">
			<!-- #ifdef MP-WEIXIN -->
			<view :style="navbarInnerStyle"></view>
			<!-- #endif -->
			<view v-if="info" class="flex items-center">
				<!-- 唤起获取微信 -->
				<u-avatar :src="img(info.headimg)" :size="'100rpx'" leftIcon="none" :default-url="img('static/resource/images/default_headimg.png')" @click="clickAvatar"></u-avatar>
				<view class="ml-[20rpx] flex-1">
					<view class="text-[#ffffff] flex items-center flex-wrap" :style="{ color : diyComponent.textColor }">
						<view class="text-[32rpx] truncate max-w-[320rpx] font-bold leading-[38rpx]">{{ info.nickname }}</view>
						<view class="text-[24rpx] font-400 leading-[28rpx] ml-[10rpx]" v-if="info.mobile">{{info.mobile.replace(info.mobile.substring(3,7), "****")}}</view>
						<view v-else-if="!info.mobile" @click="bindMobileFn" class="text-[22rpx] text-[#626779] ml-[10rpx] px-[6rpx] border-[1rpx] border-solid border-[#E3E4E9] rounded-[8rpx] leading-[30rpx]">{{ t('bindMobile') }}</view>
					</view>
					<view class="text-[#ffffff] text-[24rpx] font-400 leading-[28rpx] mt-[6rpx]" :style="{ color : diyComponent.textColor }">UID：{{ info.member_no }}</view>
				</view>
				<view @click="redirect({ url: '/app/pages/setting/index' })">
					<text class="nc-iconfont nc-icon-shezhiV6xx1 text-[38rpx] ml-[10rpx]" :style="{ color : diyComponent.textColor }"></text>
				</view>
			</view>
			<view v-else class="flex items-center" @click="toLogin">
				<u-avatar src="" :size="'100rpx'" :default-url="img('static/resource/images/default_headimg.png')"></u-avatar>
				<view class="ml-[20rpx] flex-1">
					<view class="text-[32rpx] font-bold leading-[38rpx]" :style="{ color : diyComponent.textColor }">
						{{ t('login') }}/{{ t('register') }}
					</view>
				</view>
				<view @click="redirect({ url: '/app/pages/setting/index' })">
					<text class="nc-iconfont nc-icon-shezhiV6xx1 text-[38rpx] ml-[10rpx]" :style="{ color : diyComponent.textColor }"></text>
				</view>
			</view>

			<view class="flex mt-[30rpx] items-center">
				<view class="text-center w-[33.333%] flex-shrink-0">
					<view class="text-[36rpx] leading-[42rpx] h-[50rpx] font-500">
						<view @click="redirect({url: '/app/pages/member/balance'})" :style="{ color : diyComponent.textColor }">{{ money }}</view>
					</view>
					<view class="text-[22rpx] leading-[26rpx] h-[31rpx] font-400">
						<view @click="redirect({url: '/app/pages/member/balance'})" :style="{ color : diyComponent.textColor }">{{ t('balance') }}</view>
					</view>
				</view>
				<view class="text-center w-[33.333%] flex-shrink-0">
					<view class="text-[36rpx] leading-[42rpx] h-[50rpx] font-500">
						<view @click="redirect({url: '/app/pages/member/point'})" :style="{ color : diyComponent.textColor }">{{ parseInt(info?.point) || 0 }}</view>
					</view>
					<view class="text-[22rpx] leading-[26rpx] h-[31rpx] font-400">
						<view @click="redirect({url: '/app/pages/member/point'})" :style="{ color : diyComponent.textColor }">{{ t('point') }}</view>
					</view>
				</view>
				<view class="text-center w-[33.333%] flex-shrink-0" @click="redirect({ url: '/addon/shop/pages/member/my_coupon' })">
					<view class="text-[36rpx] leading-[42rpx] h-[50rpx] font-500">
						<view :style="{ color : diyComponent.textColor }">{{ couponCount }}</view>
					</view>
					<view class="text-[22rpx] leading-[26rpx] h-[31rpx] font-400">
						<view  :style="{ color : diyComponent.textColor }">{{ t('coupon') }}</view>
					</view>
				</view>
			</view>
		</view>

		<!-- #ifdef MP-WEIXIN -->
		<information-filling ref="infoFill"></information-filling>
		<!-- #endif -->
		<!-- 强制绑定手机号 -->
		<bind-mobile ref="bindMobileRef" /> 
		
		<!-- #ifdef MP-WEIXIN -->
		<!-- 小程序隐私协议 -->
		<wx-privacy-popup ref="wxPrivacyPopupRef"></wx-privacy-popup>
		<!-- #endif -->
	</view>
</template>

<script lang="ts" setup>
	import { computed, ref, watch } from 'vue'
	import useMemberStore from '@/stores/member'
	import { useLogin } from '@/hooks/useLogin'
	import { getMyCouponCount } from '@/addon/shop/api/coupon'
	import { img, isWeixinBrowser, redirect, urlDeconstruction, moneyFormat } from '@/utils/common'
	import { t } from '@/locale'
	import { wechatSync } from '@/app/api/system'
	import useDiyStore from '@/app/stores/diy'
    import useConfigStore from '@/stores/config'
	import bindMobile from '@/components/bind-mobile/bind-mobile.vue';

	const props = defineProps(['component', 'index', 'pullDownRefreshCount','global']);

	const configStore = useConfigStore()
	const diyStore = useDiyStore();

	const diyComponent = computed(() => {
		if (diyStore.mode == 'decorate') {
			return diyStore.value[props.index];
		} else {
			return props.component;
		}
	})

	const warpCss = computed(() => {
		var style = '';
        if(diyComponent.value.componentStartBgColor) {
            if (diyComponent.value.componentStartBgColor && diyComponent.value.componentEndBgColor) style += `background:linear-gradient(${diyComponent.value.componentGradientAngle},${diyComponent.value.componentStartBgColor},${diyComponent.value.componentEndBgColor});`;
            else style += 'background-color:' + diyComponent.value.componentStartBgColor + ';';
        }
		if (diyComponent.value.bgUrl) {
			style += 'background-image:url(' + img(diyComponent.value.bgUrl) + ');';
			style += 'background-size: 100%;';
			style += 'background-repeat: no-repeat;';
		}
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

	const memberStore = useMemberStore()

	// #ifdef H5
	const { query } = urlDeconstruction(location.href)
	if (query.code && isWeixinBrowser()) {
		setTimeout(() =>{
			wechatSync({ code: query.code }).then(res => {
				memberStore.getMemberInfo()
			})
		},1500)
	}
	// #endif

	const info = computed(() => {
		// 装修模式
		if (diyStore.mode == 'decorate') {
			return {
                headimg: '',
                nickname: '昵称',
                member_level_name: '普通会员',
                balance: 0,
                point: 0,
                money: 0,
                mobile: '155****0549',
                member_no: 'NIU0000021'
            }
		} else {
			getMyCouponCountFn()
			return memberStore.info;
		}
	})

	const money = computed(() => {
		if (info.value) {
			let m = parseFloat(info.value.balance) + parseFloat(info.value.money)
			return moneyFormat(m.toString());
		} else {
			return 0;
		}
	})

	const toLogin = () => {
		if(configStore.login.is_username || configStore.login.is_mobile || configStore.login.is_bind_mobile){
			useLogin().setLoginBack({ url: '/app/pages/member/index' })
		}else if(!configStore.login.is_username && !configStore.login.is_mobile && !configStore.login.is_bind_mobile){
			uni.showToast({ title: '商家未开启普通账号登录注册', icon: 'none' })
		}else if(configStore.login.is_auth_register){  // 判断是否开启第三方自动注册登录
           // 第三方平台自动登录
                // #ifdef MP
                useLogin().getAuthCode()
                // #endif
                // #ifdef H5
                if (isWeixinBrowser()) {
                    useLogin().getAuthCode('snsapi_userinfo')
                }
                // #endif
		}
	}

	const infoFill = ref(false)
	const clickAvatar = () => {
		// #ifdef MP-WEIXIN
		infoFill.value.show = true
		// #endif

		// #ifdef H5
		if (isWeixinBrowser()) {
			useLogin().getAuthCode('snsapi_userinfo')
		} else {
			redirect({ url: '/app/pages/member/personal' })
		}
		// #endif
	}

	const couponCount = ref(0)
	const getMyCouponCountFn= async()=>{
		try {
			const res = await getMyCouponCount({status:1})
			couponCount.value = res.data
		} catch (e){
			couponCount.value = 0
		}

	}

	//强制绑定手机号
    const bindMobileRef = ref(null)
	const bindMobileFn = () =>{
		bindMobileRef.value.open()
	}

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
	    if (props.global.topStatusBar.isShow == false) {
	        style += 'height:' + menuButtonInfo.height + 'px;';
	        style += 'padding-top:' + menuButtonInfo.top + 'px;';
	    }
		// #endif
		return style;
	})
</script>

<style lang="scss" scoped>
</style>
