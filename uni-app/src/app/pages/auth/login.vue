<template>
    <view class="w-screen h-screen flex flex-col" :style="themeColor()">
        <view class="flex-1">
            <!-- #ifdef H5 -->
            <view class="h-[100rpx]"></view>
            <!-- #endif -->
            <view class="px-[60rpx] pt-[100rpx] mb-[100rpx]">
                <view class="font-bold text-xl">{{ t('login') }}</view>
            </view>
            <view class="px-[60rpx] text-sm flex mb-[50rpx] font-bold leading-none" v-if="loginType.length > 1">
                <block v-for="(item, index) in loginType">
                    <view :class="{'text-gray-300' : item.type != type}" @click="type = item.type">{{ item.title }}</view>
                    <view class="mx-[30rpx] border-solid border-0 border-r-[2px] border-gray-300" v-show="index == 0"></view>
                </block>
            </view>
            <view class="px-[60rpx]">
                <u-form labelPosition="left" :model="formData" errorType='toast' :rules="rules" ref="formRef">
                    <view v-show="type == 'username'">
                        <u-form-item label="" prop="username" :border-bottom="true">
                            <u-input v-model="formData.username" border="none" clearable :placeholder="t('usernamePlaceholder')" autocomplete="off" class="!bg-transparent" :disabled="real_name_input" fontSize="26rpx" placeholderClass="!text-[#8288A2] text-[26rpx]"/>
                        </u-form-item>
                        <view class="mt-[40rpx]">
                            <u-form-item label="" prop="password" :border-bottom="true">
                                <u-input v-model="formData.password" border="none" type="password" clearable :placeholder="t('passwordPlaceholder')" autocomplete="new-password" class="!bg-transparent" :disabled="real_name_input" fontSize="26rpx" placeholderClass="!text-[#8288A2] text-[26rpx]"/>
                            </u-form-item>
                        </view>
                    </view>
                    <view v-show="type == 'mobile'">
                        <u-form-item label="" prop="mobile" :border-bottom="true">
                            <u-input v-model="formData.mobile" border="none" clearable :placeholder="t('mobilePlaceholder')"  autocomplete="off" class="!bg-transparent" :disabled="real_name_input" fontSize="26rpx"  placeholderClass="!text-[#8288A2]"/>
                        </u-form-item>
                        <view class="mt-[40rpx]">
                            <u-form-item label="" prop="mobile_code" :border-bottom="true">
                                <u-input v-model="formData.mobile_code" border="none" clearable class="!bg-transparent" fontSize="26rpx"  :disabled="real_name_input" :placeholder="t('codePlaceholder')" placeholderClass="!text-[#8288A2]">
                                    <template #suffix>
                                        <sms-code :mobile="formData.mobile" type="login" v-model="formData.mobile_key"></sms-code>
                                    </template>
                                </u-input>
                            </u-form-item>
                        </view>
                    </view>
                    <view class="flex text-xs justify-between mt-[20rpx] text-[#8288A2]">
                        <view @click="redirect({ url: '/app/pages/auth/register' })">{{ t('noAccount') }}
                            <text class="text-primary">{{ t('toRegister') }}</text>
                        </view>
                        <view @click="redirect({ url: '/app/pages/auth/resetpwd' })">{{ t('resetpwd') }}</view>
                    </view>
                    <view class="mt-[80rpx]">
                        <button hover-class="none" class="bg-[var(--primary-color)] text-[#fff] h-[80rpx] leading-[80rpx] rounded-[100rpx] text-[28rpx]" :loading="loading" :loadingText="t('logining')" @click="handleLogin">{{t('login')}}</button>
                    </view>

                    <!-- #ifdef MP-WEIXIN -->
                    <view class="mt-[20rpx]" v-if="configStore.login.is_auth_register">
                        <button hover-class="none" class="text-[var(--primary-color)] bg-[#fff] h-[80rpx] leading-[80rpx] rounded-[100rpx] text-[28rpx]" :loading="loginLoading" :loadingText="t('logining')" @click="oneClickLogin">{{t('oneClicklogin')}}</button>
                    </view>
                    <view class="mt-[20rpx]" v-else-if="type == 'mobile'">
                        <u-button :customStyle="{border:'none',color:'var(--primary-color)',fontSize:'26rpx',height:'40rpx', lineHeight:'40rpx'}" :text="t('mobileQuickLogin')" open-type="getPhoneNumber" @getphonenumber="mobileAuth" @click="checkWxPrivacy"></u-button>
                    </view>
                    <!-- #endif -->
                </u-form>
            </view>
        </view>
        <view class="text-xs py-[50rpx] flex justify-center w-full" v-if="configStore.login.agreement_show">
            <text class="iconfont text-[var(--primary-color)] text-[34rpx] mr-[12rpx]" :class="isAgree ? 'iconxuanze1' : 'nc-iconfont nc-icon-yuanquanV6xx'" @click="isAgree = !isAgree"></text>
            {{ t('agreeTips') }}
            <view @click="redirect({ url: '/app/pages/auth/agreement?key=service' })">
                <text class="text-primary">{{ t('userAgreement') }}</text>
            </view>
            {{ t('and') }}
            <view @click="redirect({ url: '/app/pages/auth/agreement?key=privacy' })">
                <text class="text-primary">{{ t('privacyAgreement') }}</text>
            </view>
        </view>
        <!-- #ifdef MP-WEIXIN -->
        <!-- 小程序隐私协议 -->
        <wx-privacy-popup ref="wxPrivacyPopupRef"></wx-privacy-popup>
        <!-- #endif -->
    </view>
</template>

<script setup lang="ts">
    import { ref, reactive, computed, onMounted } from 'vue'
    import { usernameLogin, mobileLogin,bind } from '@/app/api/auth'
    import useMemberStore from '@/stores/member'
    import useConfigStore from '@/stores/config'
    import { useLogin } from '@/hooks/useLogin'
    import { t } from '@/locale'
    import { redirect,getToken } from '@/utils/common'
    import { onLoad,onShow } from '@dcloudio/uni-app';

    const formData = reactive({
        username: '',
        password: '',
        mobile: '',
        mobile_code: '',
        mobile_key: ''
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

    onLoad(async()=>{
        await configStore.getLoginConfig()
        uni.getStorageSync('openid') && (Object.assign(formData, { openid: uni.getStorageSync('openid') }))
        uni.getStorageSync('unionid') && (Object.assign(formData, { unionid: uni.getStorageSync('unionid') }))
        uni.getStorageSync('pid') && (Object.assign(formData, { pid: uni.getStorageSync('pid') }))
        if(!getToken() && !configStore.login.is_username && !configStore.login.is_mobile && !configStore.login.is_bind_mobile){
            uni.showToast({ title: '商家未开启普通账号登录注册', icon: 'none' })
            setTimeout(() => {
                redirect({ url: '/app/pages/index/index', mode: 'reLaunch' })
            }, 100)
        }
    })

    const loading = ref(false)

    const type = ref('')

    const loginType = computed(() => {
        const value = []
        if(configStore.login.is_username){
            value.push({ type: 'username', title: t('usernameLogin') })
        }
        if(configStore.login.is_bind_mobile || configStore.login.is_mobile){
            value.push({ type: 'mobile', title: t('mobileLogin') })
        }
        type.value = value[0] ? value[0].type : ''
        return value
    })

    const rules = computed(() => {
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
            'mobile': [
                {
                    type: 'string',
                    required: type.value == 'mobile',
                    message: t('mobilePlaceholder'),
                    trigger: ['blur', 'change'],
                },
                {
                    validator(rule, value) {
                        if (type.value != 'mobile') return true
                        else return uni.$u.test.mobile(value)
                    },
                    message: t('mobileError'),
                    trigger: ['change', 'blur'],
                }
            ],
            'mobile_code': {
                type: 'string',
                required: type.value == 'mobile',
                message: t('codePlaceholder'),
                trigger: ['blur', 'change']
            }
        }
    })

    const isAgree = ref(false)

    const formRef = ref(null)

    const handleLogin = () => {
        formRef.value.validate().then(() => {
            if (configStore.login.agreement_show && !isAgree.value) {
                uni.showToast({ title: t('isAgreeTips'), icon: 'none' });
                return false;
            }

            if (loading.value) return
            loading.value = true

            const login = type.value == 'username' ? usernameLogin : mobileLogin

            login(formData).then((res) => {
                memberStore.setToken(res.data.token)
                if(configStore.login.is_bind_mobile && !res.data.mobile){
                    uni.setStorageSync('isbindmobile', true)
                }
                useLogin().handleLoginBack()
            }).catch(() => {
                loading.value = false
            })
        })
    }

    // todo 一键登录
    const loginLoading = ref(false)
    const oneClickLogin = () =>{
        // 第三方平台自动登录
        if (loginLoading.value) return
        loginLoading.value =true
        const login = useLogin()
        login.getAuthCode('',false,true)
    }

    const wxPrivacyPopupRef:any = ref(null)

    // 检测是否同意隐私协议
    const checkWxPrivacy = ()=>{
        wxPrivacyPopupRef.value.proactive();
    }
    const mobileAuth = (e) => {
        if (!isAgree.value && configStore.login.agreement_show) {
            uni.showToast({
                title: `${ t('pleaceAgree') }《${ t('userAgreement') }》《${ t('privacyAgreement') }》`,
                icon: 'none'
            })
            return
        }
        
        const login = useLogin()
        login.getAuthCode('',false,true,(data) => {
            if (e.detail.errMsg == 'getPhoneNumber:ok') {
                uni.showLoading({ title: '' })

                bind({
                    openid:data.openid,
                    unionid:data.unionid,
                    mobile_code: e.detail.code
                }).then((res) => {
                    uni.hideLoading()
                    memberStore.setToken(res.data.token)
                    useLogin().handleLoginBack()
                    
                }).catch((res) => {
                    setTimeout(() => {
                        uni.hideLoading()
                    }, 2000);
                })
            }

            if (e.detail.errno == 104) {
                let msg = '用户未授权隐私权限';
                uni.showToast({ title: msg, icon: 'none' })
            }
            if (e.detail.errMsg == "getPhoneNumber:fail user deny") {
                let msg = '用户拒绝获取手机号码';
                uni.showToast({ title: msg, icon: 'none' })
            }
        })
    }
</script>

<style lang="scss">
	.u-input{
		background-color: transparent !important;
	}
</style>
