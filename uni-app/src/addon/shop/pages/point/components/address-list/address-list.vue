<template>
    <u-popup :show="show" @close="show = false" mode="bottom" :round="10" :closeable="true">
        <view @touchmove.prevent.stop>
			<view class="text-center p-[30rpx]">{{t('selectAddress')}}</view>
			<scroll-view scroll-y="true" class="max-h-[50vh]">
				<view v-for="(item,index) in addressList" :key="item.id" class="flex items-center px-[30rpx]" :class="{'mb-[30rpx]': addressList.length-1 != index, 'text-[var(--primary-color)]': item.id == currAddressId}" @click="selectAddress(index)">
					<text class="nc-iconfont nc-icon-dingweiV6xx-1 text-[36rpx]"></text>
					<view class="flex flex-col mx-[30rpx] w-[480rpx]">
						<view class="flex items-center truncate leading-[1.5]">
							<text class="mr-[8rpx] text-[28rpx] truncate max-w-[300rpx]">{{item.name}}</text>
							<text class="text-[28rpx]">{{item.mobile}}</text>
						</view>
						<view class="truncate text-[28rpx] leading-[1.5]">{{item.full_address}}</view>
					</view>
					<text class="nc-iconfont nc-icon-xiugaiV6xx text-[32rpx] ml-auto" @click="editAddress(item)"></text>
				</view>
				<view v-if="!addressList || addressList && !addressList.length" class="text-[#666] text-[28rpx] text-center">{{t('emptyAddress')}}</view>
			</scroll-view>
			
			<view class="p-[30rpx] pt-[40rpx]">
                <button class="bg-[var(--primary-color)] text-[#fff] h-[80rpx] leading-[80rpx] rounded-[100rpx] text-[28rpx]" @click="addAddress">{{t('addAddress')}}</button>
			</view>
		</view>
    </u-popup>
</template>

<script setup lang="ts">
    import { ref } from 'vue'
    import { getAddressList } from '@/app/api/member'
    import { t } from '@/locale'
    import { redirect } from '@/utils/common'

    const show = ref(false)
	const loading = ref(false)
	const currAddressId = ref('') // 选中的索引
	const propData = ref({})
	const addressList = ref([])
	
	const emits = defineEmits(['confirm'])
	
    const open = (data: Object) => {
        show.value = true;
		propData.value = data;
		currAddressId.value = data.id;
    }

	getAddressList({}).then(({ data }) => {
		addressList.value = data;
	    loading.value = false
	})
	
	const selectAddress = (index: number) => {
		let data = addressList.value[index];
		if(propData.value.delivery == 'local_delivery' && !data.lat && !data.lng){
			// 待支付订单-同城配送，选择的地址没有经纬度的情况，会直接跳转到地图界面进行选择
			// 参数二，表示是否直接跳转到地图界面, 1跳转到地图界面，2表示不跳
			editAddress(data,1);
		}else{
			let obj:any = {}
			obj.address_id = addressList.value[index].id
			obj.delivery = propData.value.delivery
			emits('confirm', obj)
		}
		show.value = false;
	}
	
	const editAddress = (data: Object, isSelectMap: number = 2)=>{
		uni.setStorage({
		    key: 'selectAddressCallback',
		    data: {
		        back: '/addon/shop/pages/point/payment',
		        delivery: propData.value.delivery
		    },
		    success() {
		        redirect({ url: '/app/pages/member/address_edit', param: { id: data.id, source: 'shop_order_payment', isSelectMap } })
		    }
		})
	}

	const addAddress = () =>{
		uni.setStorage({
		    key: 'selectAddressCallback',
		    data: {
		        back: '/addon/shop/pages/point/payment',
		        delivery: propData.value.delivery
		    },
		    success() {
		        redirect({ url: '/app/pages/member/address_edit', param: { source: 'shop_order_payment' } })
		    }
		})
	}
	
    defineExpose({
        open
    })
</script>

<style lang="scss" scoped>

</style>
