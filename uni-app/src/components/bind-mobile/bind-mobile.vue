<template>
    <u-popup :show="show" @close="show = false" mode="center" :round="10" :closeable="true" :safeAreaInsetBottom="false" zIndex="10086">
        <view @touchmove.prevent.stop class="max-w-[630rpx] w-[630rpx] box-border">
            <view class="text-center py-[50rpx] text-[32rpx] font-500 leading-[46rpx]">{{t('bindMobile')}}</view>
            <view class="px-[50rpx] pb-[50rpx]">
                <u-form labelPosition="left" :model="formData" errorType='toast' :rules="rules" ref="formRef">
                    <u-form-item label="" prop="mobile" :borderBottom="true">
                        <input v-model="formData.mobile"  :placeholder="t('mobilePlaceholder')" class=" w-full h-[50rpx]  leading-[50rpx] !bg-transparent !px-[20rpx] text-[26rpx] text-[#333]"  :disabled="real_name_input"  placeholder-class="bind-mobile" />
                    </u-form-item>
                    <view class="mt-[20rpx]">
                        <u-form-item label="" prop="mobile_code"  :borderBottom="true">
                            <input v-model="formData.mobile_code"  :placeholder="t('codePlaceholder')" class=" box-border w-full h-[50rpx]  leading-[50rpx] !bg-transparent !px-[20rpx] text-[26rpx] text-[#333]" :disabled="real_name_input" placeholder-class="bind-mobile" />
                            <template #right>
                                <sms-code :mobile="formData.mobile" type="bind_mobile" v-model="formData.mobile_key"></sms-code>
                            </template> 
                        </u-form-item>
                    </view>
                    <view class="flex items-baseline mt-[20rpx] pl-[10rpx]" v-if="config.agreement_show">
                        <u-checkbox-group>
                            <u-checkbox activeColor="var(--primary-color)" :checked="isAgree" shape="shape" size="12" @change="agreeChange" :customStyle="{marginTop: '0rpx',marginBottom: '0rpx'}" :label=" t('agreeTips')" labelSize="22rpx" labelColor="#8288A2" />
                        </u-checkbox-group>
                        <view class="text-[22rpx] text-[#8288A2] leading-[30rpx]  flex flex-wrap">
                            <view @click="redirect({ url: '/app/pages/auth/agreement?key=service' })">
                                《<text class="text-primary">{{ t('userAgreement') }}</text>》
                            </view>
                            <view @click="redirect({ url: '/app/pages/auth/agreement?key=privacy' })">
                                《<text class="text-primary">{{ t('privacyAgreement') }}</text>》
                            </view>
                        </view>
                    </view>
                    <view class="mt-[120rpx]">
                        <button class="primary-btn-bg text-[26rpx] text-[#fff] !h-[80rpx] leading-[80rpx] rounded-full font-bold"   :loading="loading" :loadingText="t('binding')" @click="handleBind">{{t('bind')}}</button>
                    </view>
                    <!-- #ifdef MP-WEIXIN -->
                    <view class="mt-[20rpx]">
                        <u-button :customStyle="{border:'none',color:'var(--primary-color)',fontSize:'26rpx',height:'40rpx', lineHeight:'40rpx'}" :text="t('mobileQuickLogin')" open-type="getPhoneNumber" @getphonenumber="mobileAuth" @click="checkWxPrivacy"></u-button>
                    </view>
                    <!-- #endif -->
                </u-form>
            </view>
        </view>
        <!-- #ifdef MP-WEIXIN -->
        <!-- 小程序隐私协议 -->
        <wx-privacy-popup ref="wxPrivacyPopupRef"></wx-privacy-popup>
        <!-- #endif -->
    </u-popup>
</template>

<script setup lang="ts">
    import { ref, reactive, computed, onMounted } from 'vue'
    import { t } from '@/locale'
    import { bindMobile } from '@/app/api/member'
    import useMemberStore from '@/stores/member'
    import useConfigStore from '@/stores/config'
    import { redirect } from '@/utils/common'

    const show = ref(false)
    const memberStore = useMemberStore()
    const info = computed(() => memberStore.info)

    const config = computed(() => {
        return useConfigStore().login
    })

    const wxPrivacyPopupRef:any = ref(null)

    // 检测是否同意隐私协议
    const checkWxPrivacy = ()=>{
        wxPrivacyPopupRef.value.proactive();
    }

    const loading = ref(false)
    const isAgree = ref(false)

    const formData = reactive({
        mobile: '',
        mobile_code: '',
        mobile_key: ''
    })

	let real_name_input = ref(true);
	onMounted(() => {
		// 防止浏览器自动填充
		setTimeout(()=>{
			real_name_input.value = false;
		},800)
	});

    uni.getStorageSync('openid') && (Object.assign(formData, { openid: uni.getStorageSync('openid') }))
    uni.getStorageSync('pid') && (Object.assign(formData, { pid: uni.getStorageSync('pid') }))
    uni.getStorageSync('unionid') && (Object.assign(formData, { unionid: uni.getStorageSync('unionid') }))

    const rules = {
        'mobile': [
            {
                type: 'string',
                required: true,
                message: t('mobilePlaceholder'),
                trigger: ['blur', 'change'],
            },
            {
                validator(rule, value, callback) {
                    let mobile = /^1[3-9]\d{9}$/;
                    if (!mobile.test(value)){
                        callback(new Error('请输入正确的手机号'))
                    } else {
                        callback()
                    }
                },
                message: t('mobileError'),
                trigger: ['change', 'blur'],
            }
        ],
        'mobile_code': {
            type: 'string',
            required: true,
            message: t('codePlaceholder'),
            trigger: ['blur', 'change']
        }
    }

    const agreeChange = () => {
        isAgree.value = !isAgree.value
    }


    const formRef = ref(null)

    const handleBind = () => {
        formRef.value.validate().then(() => {
            if (loading.value) return
            loading.value = true

            bindMobile(formData).then((res) => {
                memberStore.getMemberInfo()
                if(info.value.mobile){
                    uni.removeStorageSync('isbindmobile');
                }
                show.value = false
            }).catch(() => {
                loading.value = false
            })
        })
    }

    const mobileAuth = (e) => {
        if (!isAgree.value && config.value.agreement_show) {
            uni.showToast({
                title: `${ t('pleaceAgree') }《${ t('userAgreement') }》《${ t('privacyAgreement') }》`,
                icon: 'none'
            })
            return
        }

        if (e.detail.errMsg == 'getPhoneNumber:ok') {
            uni.showLoading({ title: '' })

            bindMobile({
                mobile_code: e.detail.code
            }).then((res) => {
                uni.hideLoading()
                memberStore.getMemberInfo()
                if(res.msg == 1){
                    uni.removeStorageSync('isbindmobile');
                }
                show.value = false
            }).catch((res) => {
                setTimeout(() => {
                    show.value = false
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
    }
    const open = ()=> {
        show.value = true
    }
    
    defineExpose({
    	open
    })
</script>

<style lang="scss" scoped>
:deep(.bind-mobile){
    color:#8288A2;
    font-size: 26rpx;
}
</style>