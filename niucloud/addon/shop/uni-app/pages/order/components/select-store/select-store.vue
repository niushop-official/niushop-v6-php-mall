<template>
    <u-popup :show="show" @close="show = false" mode="bottom" :round="10">
        <view class="popup-common">
            <view class="title">请选择门店</view>
            <scroll-view scroll-y="true" class="h-[50vh]">
                <view class="p-[var(--popup-sidebar-m)] pt-0 text-sm">
                    <view class="card-template mt-[var(--top-m)] border-1 border-[#eee] border-solid  mb-[var(--top-m)]" :class="{'!border-primary bg-[var(--primary-color-light2)]': store && store.store_id == item.store_id}"
                        v-for="item in storeList" @click="selectStore(item)">
                        <view class="flex items-center mb-[20rpx]">
                            <view class="flex-1 using-hidden text-[30rpx] text-[#333]">{{ item.store_name }} </view>
                            <view v-if="item.hasOwnProperty('distance')" class="flex-shrink-0 ml-[20rpx] text-[#999] text-[24rpx] font-normal">{{ distanceVal(item) }}</view>
                        </view>
                        <view class="flex items-center justify-between">
                            <view class="flex-1">
                                <view class=" text-[22rpx] leading-[1.4] text-[#666]">
                                    <text class="nc-iconfont nc-icon-dizhiguanliV6xx text-[26rpx] mr-[12rpx]"></text>
                                    <text class="flex-shrink-0">门店地址：</text>
                                    <text>{{ item.full_address }}</text>
                                </view>
                                <view class="mt-[12rpx] text-[22rpx] text-[#666]">
                                    <text class="nc-iconfont nc-icon-shijianV6xx text-[26rpx] mr-[12rpx]"></text>
                                    <text>营业时间：{{ item.trade_time }}</text>
                                </view>
                            </view>
                            <view class="w-[1rpx] h-[42rpx] bg-[#ddd] mx-[24rpx]"></view>
                            <view class="flex-shrink-0">
                                <image @click="callFn(item.store_mobile)" class="w-[42rpx] h-[42rpx] mr-[20rpx]" :src="img('addon/shop/payment/phone.png')" mode="heightFix" />
                                <image @click="getAddress(item)" class="w-[42rpx] h-[42rpx]" :src="img('addon/shop/payment/navigation.png')" mode="heightFix" />
                            </view>
                        </view>
                    </view>
                </view>
                <view class="h-[50vh] flex items-center flex-col justify-center" v-if="loading">
                    <u-loading-icon :vertical="true"></u-loading-icon>
                </view>
                <view class="h-[95%] flex items-center flex-col justify-center" v-if="!loading && !storeList.length">
                    <u-empty text="没有可选择的门店" width="214" :icon="img('static/resource/images/empty.png')" />
                </view>
            </scroll-view>
            <view class="btn-wrap">
                <button class="primary-btn-bg btn" @click="confirm">确认</button>
            </view>
        </view>
    </u-popup>
</template>

<script setup lang="ts">
import { ref, reactive, computed } from 'vue'
import { getStoreList } from '@/addon/shop/api/order'
import { img, isWeixinBrowser } from '@/utils/common'
import wechat from '@/utils/wechat'

const show = ref(false)
const loaded = ref(false)
const loading = ref(true)
const storeList = ref<object[]>([])
const store = ref<null | object>(null)
const latlng = reactive({
    lat: 0,
    lng: 0
})
const distanceFn = (distance: string | number) => {
	const dist = typeof distance === 'string' ? parseFloat(distance) : distance;
	if (isNaN(dist)) return distance.toString();
	return dist < 1 ? parseInt((dist * 1000).toString()) + 'm' : dist.toFixed(1) + 'km'
}

const distanceVal = (item:any)=>{
    if(item?.distance){
        return distanceFn(item.distance)
    }
    return ''
}

// 位置
// #ifdef H5
if (isWeixinBrowser()) {
    wechat.init();
}
// #endif
const getAddress = (data: any) => {
    // #ifdef H5
    if (isWeixinBrowser()) {
        wechat.openLocation({
            latitude: Number(data.latitude),
            longitude: Number(data.longitude),
            name: data.full_address,
            address: data.full_address
        })
    }
    // #endif
    // #ifdef MP
	uni.openLocation({
		latitude: Number(data.latitude),
		longitude: Number(data.longitude),
        name: data.full_address,
        address: data.full_address,
		success: function () { }
	});
    // #endif
}

const callFn = (data: any) => {
    uni.makePhoneCall({
        phoneNumber: data,
        success: (res: any) => {
        },
        fail: (res: any) => {
        }
    })
}

const open = (data: any) => {
    storeList.value = data.store_list || []
    store.value = data.take_store || storeList.value[0] || null
    loading.value = false
    show.value = true
}

const getStoreListFn = (callback: any) => {
    if (!loaded.value) {
        loaded.value = true

        if (uni.getStorageSync('location_address')) {
            let location_address = uni.getStorageSync('location_address');
            latlng.lat = location_address.latitude;
            latlng.lng = location_address.longitude;
        } else {
            uni.getLocation({
                type: 'gcj02',
                success: (res) => {
                    latlng.lat = res.latitude
                    latlng.lng = res.longitude
                },
                fail: (res) => {
                    if (res.errno) {
                        if (res.errno == 104) {
                            let msg = '用户未授权隐私权限，获取位置失败';
                            uni.showToast({ title: msg, icon: 'none' })
                        } else if (res.errno == 112) {
                            let msg = '隐私协议中未声明，获取位置失败';
                            uni.showToast({ title: msg, icon: 'none' })
                        }
                    }
                    if (res.errMsg) {
                        if (res.errMsg.indexOf('getLocation:fail') != -1 || res.errMsg.indexOf('deny') != -1 || res.errMsg.indexOf('denied') != -1) {
                            let msg = '用户未授权获取位置权限，将无法提供距离最近的门店';
                            uni.showToast({ title: msg, icon: 'none' })
                        } else {
                            uni.showToast({ title: res.errMsg, icon: 'none' })
                        }
                    }
                }
            });
        }

        setTimeout(() => {
            getStoreList({ latlng }).then(({ data }) => {
                storeList.value = data
                if (data.length) {
                    selectStore(data[0]) // 默认选择第一个
                }
                if (typeof callback == 'function') {
                    callback(data);
                }
                loading.value = false
            }).catch(() => {
                loading.value = false
            })
        }, 1500)
    }
}


const selectStore = (data: object) => {
    if (store.value) {
        store.value = store.value.store_id != data.store_id ? data : null
    } else {
        store.value = data
    }
}

const emits = defineEmits(['confirm'])

const confirm = () => {
    if (!store.value) {
        uni.showToast({ title: '请选择自提点', icon: 'none' })
        return
    }
    emits('confirm', store.value)
    store.value = null
    show.value = false
}



defineExpose({
    open,
    getData: getStoreListFn
})
</script>

<style lang="scss" scoped></style>
