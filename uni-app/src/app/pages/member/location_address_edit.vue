<template>
    <view :style="themeColor()">
        <scroll-view scroll-y="true" class="bg-page h-screen">
            <!-- <view class="h-[88rpx]">
                    <u-navbar title="添加地址" @rightClick="rightClick" :autoBack="true"></u-navbar>
            </view> -->
            <view class="h-[30rpx]"></view>
            <view class="m-[30rpx] mt-0 p-[30rpx] pt-[10rpx] rounded-md bg-white">
                <u-form labelPosition="left" :model="formData" labelWidth="200rpx" errorType='toast' :rules="rules" ref="formRef">
                    <view class="mt-[10rpx]">
                        <u-form-item :label="t('name')" prop="name" :border-bottom="true">
                            <u-input v-model.trim="formData.name" border="none" clearable maxlength="25" :placeholder="t('namePlaceholder')"/>
                        </u-form-item>
                    </view>
                    <view class="mt-[10rpx]">
                        <u-form-item :label="t('mobile')" prop="mobile" :border-bottom="true">
                            <u-input v-model="formData.mobile" border="none" clearable :placeholder="t('mobilePlaceholder')"/>
                        </u-form-item>
                    </view>
                    <view class="mt-[10rpx]">
                        <u-form-item :label="t('deliveryAddress')" prop="address_name" :border-bottom="true">
                            <view class="flex justify-between flex-1" @click="chooseLocation">
                                <view  class="text-[15px]" :class="{ 'text-[#303133]': formData.area,'text-[#c3c4d5]':!formData.area  }">{{formData.area ? formData.address_name : t('selectAddressPlaceholder')}}</view>
                                <u-icon name="arrow-right" color="#c3c4d5"></u-icon>
                            </view>
                        </u-form-item>
                    </view>
                    <view class="mt-[10rpx]">
                        <u-form-item :label="t('address')" prop="address" :border-bottom="true">
                            <u-input v-model.trim="formData.address" border="none" clearable maxlength="120" :placeholder="t('addressPlaceholder')"/>
                        </u-form-item>
                    </view>
                    <view class="mt-[10rpx]">
                        <u-form-item :label="t('defaultAddress')" prop="name" :border-bottom="true" >
                            <u-switch v-model="formData.is_default" size="20" :activeValue="1" :inactiveValue="0" activeColor="var(--primary-color)"/>
                        </u-form-item>
                    </view>
                    <view class="mt-[40rpx]">
                        <button class="!bg-[var(--primary-color)] !text-[#fff] h-[80rpx] leading-[80rpx] rounded-[100rpx] text-[28rpx]" :class="{'opacity-50': btnDisabled}"  @click="save" :disabled="btnDisabled" :loading="operateLoading">{{t('save')}}</button>
                    </view>
                </u-form>
            </view>
        </scroll-view>

        <!-- #ifdef MP-WEIXIN -->
        <!-- 小程序隐私协议 -->
        <wx-privacy-popup ref="wxPrivacyPopupRef"></wx-privacy-popup>
        <!-- #endif -->
    </view>
</template>

<script setup lang="ts">
    import { ref, computed } from 'vue'
    import { onLoad } from '@dcloudio/uni-app'
    import { redirect } from '@/utils/common'
    import { t } from '@/locale'
    import { addAddress, editAddress, getAddressInfo } from '@/app/api/member'
    import manifestJson from '@/manifest.json'
    import { getAddressByLatlng } from '@/app/api/system'

    const type = ref('')
    const source = ref('')
    const btnDisabled = ref(false)

    const formData = ref({
        id: 0,
        name: '',
        mobile: '',
        province_id: 0,
        city_id: 0,
        district_id: 0,
        lat: '',
        lng: '',
        address: '',
        address_name: '',
        full_address: '',
        is_default: 0,
        area: '',
        type: 'location_address'
    })

    onLoad((option:any) => {
        if (option.id) {
            getAddressInfo(option.id).then(({ data }) => {
                if (data) {
                    Object.assign(formData.value, data)
                    formData.value.area = formData.value.full_address.replace(formData.value.address, '').replace(formData.value.address_name, '')
                }
            }).catch()
        } else if (option.name) {
            if (uni.getStorageSync('addressInfo')) {
                Object.assign(formData.value, uni.getStorageSync('addressInfo'))
            }
            formData.value.address = option.name;
            getAddress(option.latng);
            var tempArr = getQueryVariable('latng').split(',');
            formData.value.lat = tempArr[0];
            formData.value.lng = tempArr[1];
        }

        type.value = option.type || ''
        source.value = option.source || ''
    })

    const formRef = ref(null)

    const rules = computed(() => {
        return {
            'address': {
                type: 'string',
                required: true,
                message: t('addressError'),
                trigger: ['blur', 'change'],
            },
            'name': {
                type: 'string',
                required: true,
                message: t('namePlaceholder'),
                trigger: ['blur', 'change'],
            },
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
                            callback(new Error(t('mobileError')))
                        } else {
                            callback()
                        }
                    }
                }
            ]
        }
    })

    const getQueryVariable = (variable:any)=> {
        var query = window.location.search.substring(1);
        var vars = query.split('&');
        for (var i = 0; i < vars.length; i++) {
            var pair = vars[i].split('=');
            if (pair[0] == variable) {
                return pair[1];
            }
        }
        return false;
    }

    //获取详细地址
    const getAddress = (latlng:any)=> {
        getAddressByLatlng({latlng}).then((res: any) => {
            if (res.data) {
                formData.value.full_address = '';
                formData.value.full_address += res.data.province != undefined ? res.data.province : '';
                formData.value.full_address += res.data.city != undefined ? '' + res.data.city : '';
                formData.value.full_address += res.data.district != undefined ? '' + res.data.district : '';

                formData.value.address_name = formData.value.full_address.replace(/-/g,'');
                formData.value.area = res.data.full_address;

                formData.value.province_id = res.data.province_id != undefined ? res.data.province_id : 0;
                formData.value.city_id = res.data.city_id != undefined ? res.data.city_id : 0;
                formData.value.district_id = res.data.district_id != undefined ? res.data.district_id : 0;

            } else {
                uni.showToast({title: res.msg, icon: 'none'})
            }
        })

    }

    const operateLoading = ref(false)
    const save = ()=> {
        if (uni.$u.test.isEmpty(formData.value.area)) {
            uni.showToast({title: t('selectAddressPlaceholder'), icon: 'none'})
            return
        }

        const save = formData.value.id ? editAddress : addAddress

        formRef.value.validate().then(() => {
            if (operateLoading.value) return
            operateLoading.value = true

            btnDisabled.value = true

            formData.value.full_address = `${formData.value.area}${formData.value.address_name}${formData.value.address}`

            save(formData.value).then((res) => {
                operateLoading.value = false
                uni.removeStorageSync('addressInfo');
                setTimeout(() => {
                    btnDisabled.value = false
                    redirect({
                        url: '/app/pages/member/address',
                        mode: 'redirectTo',
                        param: {type: type.value, source: source.value}
                    })
                }, 1000)
            }).catch(() => {
                operateLoading.value = false
                btnDisabled.value = false
            })
        })
    }

    const chooseLocation = ()=> {
        // #ifdef MP
        uni.chooseLocation({
        	success: (res) => {
                res.latitude && (formData.value.lat = res.latitude)
                res.longitude && (formData.value.lng = res.longitude)
                res.address && (formData.value.area = res.address)
                res.name && (formData.value.address_name = res.name)
        	},
            fail: (res)=>{
                // 在隐私协议中没有声明chooseLocation:fail api作用域
                if(res.errMsg && res.errno) {
                    if(res.errno == 104){
                        let msg = '用户未授权隐私权限，选择位置失败';
                        uni.showToast({title: msg, icon: 'none'})
                    }else if(res.errno == 112){
                        let msg = '隐私协议中未声明，打开地图选择位置失败';
                        uni.showToast({title: msg, icon: 'none'})
                    }else {
                        uni.showToast({title: res.errMsg, icon: 'none'})
                    }
                }
            }
        });
        // #endif

        // #ifdef H5
        var urlencode = formData.value;
        uni.setStorageSync('addressInfo', urlencode);
        let backurl = location.origin +  location.pathname + '?type=' + type.value + '&source=' + source.value;
        window.location.href = 'https://apis.map.qq.com/tools/locpicker?search=1&type=0&backurl=' + encodeURIComponent(backurl) + '&key=' + manifestJson.h5.sdkConfigs.maps.qqmap.key + '&referer=myapp';
        // #endif
    }
</script>

<style lang="scss" scoped></style>