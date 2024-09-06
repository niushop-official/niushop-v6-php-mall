<template>
    <view class="w-screen h-screen flex flex-col" :style="themeColor()" >
        <view class="flex-1">
            <!-- #ifdef H5 -->
            <view class="h-[100rpx]"></view>
            <!-- #endif -->
            <view class="px-[60rpx] pt-[100rpx] mb-[100rpx]">
                <view class="font-bold text-xl">{{ t('register') }}</view>
            </view>
            <view class="px-[60rpx] text-sm flex mb-[50rpx] font-bold leading-none" v-if="registerType.length > 1">
                <block v-for="(item, index) in registerType">
                    <view :class="{'text-gray-300' : item.type != type}" @click="type = item.type">{{ item.title }}</view>
                    <view class="mx-[30rpx] border-solid border-0 border-r-[2px] border-gray-300" v-show="index == 0"></view>
                </block>
            </view>
            <view class="px-[60rpx]">
                <u-form labelPosition="left" :model="formData" errorType='toast' :rules="rules" ref="formRef">
                    <view v-show="type == 'username'">
                        <view class="mt-[30rpx]">
                            <u-form-item label="" prop="username" :border-bottom="true">
                                <u-input v-model="formData.username" border="none" clearable :placeholder="t('usernamePlaceholder')" class="!bg-transparent" :disabled="real_name_input" fontSize="26rpx" placeholderClass="!text-[#8288A2]"/>
                            </u-form-item>
                        </view>
                        <view class="mt-[30rpx]">
                            <u-form-item label="" prop="password" :border-bottom="true">
                                <u-input v-model="formData.password" border="none" type="password" clearable :placeholder="t('passwordPlaceholder')" class="!bg-transparent" :disabled="real_name_input" fontSize="26rpx" placeholderClass="!text-[#8288A2]"/>
                            </u-form-item>
                        </view>
                        <view class="mt-[30rpx]">
                            <u-form-item label="" prop="confirm_password" :border-bottom="true">
                                <u-input v-model="formData.confirm_password" border="none" type="password" clearable :placeholder="t('confirmPasswordPlaceholder')" class="!bg-transparent" :disabled="real_name_input" fontSize="26rpx" placeholderClass="!text-[#8288A2]"/>
                            </u-form-item>
                        </view>
                    </view>
                    <view v-show="type == 'mobile' || configStore.login.is_bind_mobile">
                        <view class="mt-[30rpx]">
                            <u-form-item label="" prop="mobile" :border-bottom="true">
                                <u-input v-model="formData.mobile" border="none" clearable :placeholder="t('mobilePlaceholder')" class="!bg-transparent" :disabled="real_name_input" fontSize="26rpx" placeholderClass="!text-[#8288A2]"/>
                            </u-form-item>
                        </view>
                        <view class="mt-[30rpx]">
                            <u-form-item label="" prop="mobile_code" :border-bottom="true">
                                <u-input v-model="formData.mobile_code" border="none" clearable :placeholder="t('codePlaceholder')" class="!bg-transparent" :disabled="real_name_input" fontSize="26rpx" placeholderClass="!text-[#8288A2]">
                                    <template #suffix>
                                        <sms-code v-show="type" :mobile="formData.mobile" type="register" v-model="formData.mobile_key"></sms-code>
                                    </template>
                                </u-input>
                            </u-form-item>
                        </view>
                    </view>
                    <view v-show="type == 'username'">
                        <view class="mt-[30rpx]">
                            <u-form-item label="" prop="captcha_code" :border-bottom="true">
                                <u-input v-model="formData.captcha_code" border="none" clearable :placeholder="t('captchaPlaceholder')" class="!bg-transparent" :disabled="real_name_input" fontSize="26rpx" placeholderClass="!text-[#8288A2]">
                                    <template #suffix>
                                        <image :src="captcha.image.value" class="h-[48rpx] w-[60rpx] ml-[20rpx]" mode="heightFix" @click="captcha.refresh()"></image>
                                    </template>
                                </u-input>
                            </u-form-item>
                        </view>
                    </view>
                    <view class="flex text-xs justify-between mt-[20rpx] text-[#8288A2]">
                        <view @click="redirect({ url: '/app/pages/auth/login' })">{{ t('haveAccount') }}，<text class="text-primary">{{ t('toLogin') }}</text></view>
                    </view>
                    <view class="mt-[80rpx]">
                        <button hover-class="none" class="bg-[var(--primary-color)] text-[#fff] h-[80rpx] leading-[80rpx] rounded-[100rpx] text-[28rpx]" :loading="loading" :loadingText="t('registering')" @click="handleRegister">{{t('register')}}</button>
                    </view>
                </u-form>
            </view>
        </view>
        <view class="text-xs py-[50rpx] flex justify-center w-full" v-if="configStore.login.agreement_show">
            <text class="iconfont text-[var(--primary-color)] text-[34rpx] mr-[12rpx]" :class="isAgree ? 'iconxuanze1' : 'nc-iconfont nc-icon-yuanquanV6xx'" @click="isAgree = !isAgree"></text>
            {{ t('registerAgreeTips') }}
            <view @click="redirect({ url: '/app/pages/auth/agreement?key=service' })">
                <text class="text-primary">{{ t('userAgreement') }}</text>
            </view>
            {{ t('and') }}
            <view @click="redirect({ url: '/app/pages/auth/agreement?key=privacy' })">
                <text class="text-primary">{{ t('privacyAgreement') }}</text>
            </view>
        </view>
    </view>
</template>

<script setup lang="ts">
    import { ref, reactive, computed, onMounted } from 'vue'
    import { usernameRegister, mobileRegister } from '@/app/api/auth'
    import useMemberStore from '@/stores/member'
    import useConfigStore from '@/stores/config'
    import { useLogin } from '@/hooks/useLogin'
    import { useCaptcha } from '@/hooks/useCaptcha'
    import { t } from '@/locale'
    import { redirect, getToken } from '@/utils/common'
    import { onLoad } from '@dcloudio/uni-app';

    const formData = reactive({
        username: '',
        password: '',
        confirm_password: '',
        mobile: '',
        mobile_code: '',
        mobile_key: '',
        captcha_key: '',
        captcha_code: ''
    })

	const real_name_input = ref(true);
	onMounted(() => {
		// 防止浏览器自动填充
		setTimeout(()=>{
			real_name_input.value = false;
		},800)
	});

    const memberStore = useMemberStore()
    const configStore = useConfigStore()

    onLoad(async() =>{
        await configStore.getLoginConfig()
        if (!uni.getStorageSync('autoLoginLock')) {
            uni.getStorageSync('openid') && (Object.assign(formData, {openid: uni.getStorageSync('openid')}))
            uni.getStorageSync('pid') && (Object.assign(formData, {pid: uni.getStorageSync('pid')}))
        }
        uni.getStorageSync('unionid') && (Object.assign(formData, { unionid: uni.getStorageSync('unionid') }))
        if(!getToken() && !configStore.login.is_username && !configStore.login.is_mobile && !configStore.login.is_bind_mobile){
            uni.showToast({ title: '商家未开启普通账号登录注册', icon: 'none' })
            setTimeout(() => {
                redirect({ url: '/app/pages/index/index', mode: 'reLaunch' })
            }, 100)
        }
    })

    const captcha = useCaptcha(formData)
    captcha.refresh()

    const loading = ref(false)

    const type = ref('')

    const registerType = computed(()=> {
        const value = []
        configStore.login.is_username && (value.push({ type: 'username', title: t('usernameRegister') }))
        configStore.login.is_mobile && !configStore.login.is_bind_mobile && (value.push({ type: 'mobile', title: t('mobileRegister') }))
        type.value = value[0] ? value[0].type : ''
        return value
    })

    const rules = computed(()=>{
        return {
            'username': {
                type: 'string',
                required: type.value == 'username',
                message: t('usernamePlaceholder'),
                trigger: ['blur', 'change'],
            },
            'password': {
                type: 'string',
                required: type.value == 'username',
                message: t('passwordPlaceholder'),
                trigger: ['blur', 'change']
            },
            'confirm_password': [
                {
                    type: 'string',
                    required: type.value == 'username',
                    message: t('confirmPasswordPlaceholder'),
                    trigger: ['blur', 'change']
                },
                {
                    validator(rule, value){
                        return value == formData.password
                    },
                    message: t('confirmPasswordError'),
                    trigger: ['change','blur'],
                }
            ],
            'mobile': [
                {
                    type: 'string',
                    required: type.value == 'mobile' || configStore.login.is_bind_mobile,
                    message: t('mobilePlaceholder'),
                    trigger: ['blur', 'change'],
                },
                {
                    validator(rule, value){
                        if (type.value != 'mobile' && !configStore.login.is_bind_mobile) return true
                        else return uni.$u.test.mobile(value)
                    },
                    message: t('mobileError'),
                    trigger: ['change','blur'],
                }
            ],
            'mobile_code': {
                type: 'string',
                required: type.value == 'mobile' || configStore.login.is_bind_mobile,
                message: t('codePlaceholder'),
                trigger: ['blur', 'change']
            },
            'captcha_code': {
                type: 'string',
                required: type.value == 'username',
                message: t('captchaPlaceholder'),
                trigger: ['blur', 'change'],
            }
        }
    })

    const isAgree = ref(false)

    const formRef = ref(null)

    const handleRegister = () => {
        formRef.value.validate().then(() => {
            if (configStore.login.agreement_show && !isAgree.value) {
                uni.showToast({ title: t('isAgreeTips'), icon: 'none' });
                return false;
            }
            if (loading.value) return
            loading.value = true

            const register = type.value == 'username' ? usernameRegister : mobileRegister

            register(formData).then((res: responseResult) => {
                memberStore.setToken(res.data.token)
                useLogin().handleLoginBack()
            }).catch(() => {
                loading.value = false
                captcha.refresh()
            })
        })
    }
</script>

<style lang="scss">
	.u-input{
		background-color: transparent !important;
	}
</style>
