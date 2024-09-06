<template>
    <view v-if="!loading" :style="themeColor()">
        <scroll-view scroll-y="true">
            <u-swipe-action>
                <view class="py-[30rpx] sidebar-marign">
                    <u-swipe-action-item :options="addressOptions" @click="swipeClick(key)" v-for="(item, key) in addressList">
                        <view class="border-0 !border-b !border-[#f5f5f5] border-solid pb-[30rpx] flex items-center">
                            <view class="flex-1 line-feed" @click="selectAddress(item)">
                                <view class="font-bold my-[10rpx] text-sm line-feed">{{ item.full_address }}</view>
                                <view class="text-sm flex items-center">
                                    <view>{{ item.name }}</view>
                                    <text class="text-[26rpx] text-gray-subtitle">{{ mobileHide(item.mobile) }}</text>
                                    <view class="bg-primary text-white text-xs px-[10rpx] leading-none flex items-center h-[32rpx] ml-[10rpx] rounded min-w-[50rpx]" v-if="item.is_default == 1">{{ t('default') }}</view>
                                </view>
                            </view>
                            <text class="nc-iconfont nc-icon-xiugaiV6xx shrink-0 text-[32rpx] p-[20rpx] pr-0" @click="editAddress(item.id)"></text>
                        </view>
                    </u-swipe-action-item>
                    <view v-if="!addressList.length" class="pt-[20vh]">
                        <mescroll-empty :option="{tip : '暂无收货地址'}"></mescroll-empty>
                    </view>
                </view>
            </u-swipe-action>
            <u-tabbar :fixed="true" :safeAreaInsetBottom="true" :border="false" zIndex="99">
                <view class="p-[24rpx] pt-0 w-full">
                    <button hover-class="none" class="bg-[var(--primary-color)] text-[#fff] h-[80rpx] leading-[80rpx] rounded-[100rpx] text-[28rpx]" @click="addAddress">{{t('createAddress')}}</button>
                </view>
            </u-tabbar>
        </scroll-view>
    </view>
</template>

<script setup lang="ts">
    import { ref, reactive, computed, watch } from 'vue'
    import { onLoad } from '@dcloudio/uni-app'
    import { redirect, img, mobileHide } from '@/utils/common'
    import { getAddressList, deleteAddress } from '@/app/api/member'
    import { t } from '@/locale'
    import MescrollEmpty from '@/components/mescroll/mescroll-empty/mescroll-empty.vue';

    const loading = ref(true)
    const current = ref(0)
    const addressList = ref<object[]>([])
    const locationAddressList = ref<object[]>([])
    const type = ref('')
    const source = ref('')

    onLoad((data: any) => {
        type.value = data.type || ''
        source.value = data.source || ''
        if (data.type) current.value = data.type == 'address' ? 0 : 1
		// 清空缓存，防止受待支付界面影响
		if(uni.getStorageSync('selectAddressCallback')){uni.removeStorage({ key: 'selectAddressCallback' })}
    })

    getAddressList({}).then(({ data }) => {
        const address = [], locationAddress = []
        data.forEach(item => {
            item.type == 'address' ? address.push(item) : locationAddress.push(item)
        })
		if(!source.value){ //地址管理使用
			addressList.value = data;
		}else{ // 区分同城配送地址和快递地址
			addressList.value = current.value == 0 ? address : locationAddress;
		}
        loading.value = false
    }).catch(() => {
        loading.value = false
    })

    const addAddress = ()=> {
        const url = `/app/pages/member/address_edit`
        redirect({ url, param: { source : source.value } })
    }

    const editAddress = (id: number)=> {
        const url = `/app/pages/member/address_edit`
        redirect({ url, param: { id, source : source.value} })
    }

    const addressOptions = ref([
        {
            text: t('delete'),
            style: {
                backgroundColor: '#F56C6C'
            }
        }
    ])

    const selectAddress = (data: object) => {
        const selectAddress = uni.getStorageSync('selectAddressCallback')
        if (selectAddress) {
            selectAddress.address_id = data.id

            uni.setStorage({
                key: 'selectAddressCallback',
                data: selectAddress,
                success() {
                    redirect({url: selectAddress.back, mode: 'redirectTo'})
                }
            })
        }
    }

    const swipeClick = (index: any) => {
        const list = current.value ? locationAddressList : addressList
        const data = list.value[index]

        deleteAddress(data.id).then(() => {
            list.value.splice(index, 1)
        }).catch()
    }
	
</script>

<style lang="scss" scoped>
    :deep(.u-tabs__wrapper__nav__line) {
        bottom: 0;
        background: var(--primary-color) !important;
    }
    .line-feed{
        word-wrap:break-word;
        word-break: break-all;
    }
</style>
