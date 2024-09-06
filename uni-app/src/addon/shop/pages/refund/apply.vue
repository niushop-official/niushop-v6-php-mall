<template>
    <view :style="themeColor()">
        <swiper :indicator-dots="false" :autoplay="false" :disable-touch="true" :current="step" class="h-screen" :duration="300" v-if="detail">
            <swiper-item>
                <scroll-view scroll-y="true" class="bg-page h-screen">
                    <view class="my-[24rpx] sidebar-marign px-[24rpx] rounded-md bg-white">
                        <view class="flex py-[30rpx] border-0 !border-b !border-[#f5f5f5] border-solid">
                            <view class="w-[120rpx] h-[120rpx] flex items-center justify-center">
                            	<u--image radius="10rpx" width="120rpx" height="120rpx" :src="img(orderDetail.sku_image)" model="aspectFill">
                            	    <template #error>
                            	        <image class="w-[120rpx] h-[120rpx] rounded-[10rpx] overflow-hidden" :src="img('static/resource/images/diy/shop_default.jpg')" mode="aspectFill"></image>
                            	    </template>
                            	</u--image>
                            </view>
                            <view class="flex flex-1 w-0 flex-col justify-between ml-[20rpx]">
                                <view>
                                    <view class="text-ellipsis text-[#303133] text-sm leading-normal">{{orderDetail.goods_name}}</view>
                                    <view class="mt-[10rpx] text-[26rpx] leading-[1.3] text-gray-subtitle">{{ orderDetail.sku_name }}</view>
                                </view>
                            </view>
                        </view>
                    </view>

                    <view class="my-[24rpx] sidebar-marign px-[24rpx] rounded-md bg-white">
                        <view class="py-[24rpx] flex items-center" @click="selectRefundType(1)">
                            <view class="flex-1">
                                <view class="text-sm">仅退款</view>
                                <view class="text-xs mt-[10rpx] text-gray-subtitle" v-if="orderDetail.goods_type == 'real'">未收到货，或与商家协商一致不用退货只退款</view>
                                <view class="text-xs mt-[10rpx] text-gray-subtitle" v-else-if="orderDetail.goods_type == 'virtual'">与商家协商一致不用退货只退款</view>
                            </view>
                            <text class="nc-iconfont nc-icon-youV6xx text-[30rpx] text-[#999]"></text>
                        </view>
                        <view class="py-[24rpx] flex items-center border-0 !border-t !border-[#f5f5f5] border-solid" v-if="orderDetail.goods_type == 'real' && (!orderDetail.delivery_status || orderDetail.delivery_status != 'wait_delivery')" @click="selectRefundType(2)">
                            <view class="flex-1">
                                <view class="text-sm">退货退款</view>
                                <view class="text-xs mt-[10rpx] text-gray-subtitle">已收到货，需退还收到的货物</view>
                            </view>
                            <text class="nc-iconfont nc-icon-youV6xx text-[30rpx] text-[#999]"></text>
                        </view>
                    </view>
                </scroll-view>
            </swiper-item>
            <swiper-item>
                <scroll-view scroll-y="true" class="bg-page h-screen">
                    <view class="my-[24rpx] sidebar-marign px-[24rpx] rounded-md bg-white">
                        <view class="py-[24rpx] flex justify-between items-center">
                            <view class="text-sm">退款原因</view>
                            <view class="flex items-center" @click="refundCausePopup = true">
                                <view class="flex-1 text-right">
                                    <view class="text-xs text-gray-subtitle truncate w-[460rpx]">{{ formData.reason || '请选择' }}</view>
                                </view>
                                <text class="nc-iconfont nc-icon-youV6xx text-[30rpx] text-[#999]"></text>
                            </view>
                        </view>
                    </view>
                    <view class="my-[24rpx] sidebar-marign px-[24rpx] rounded-md bg-white">
                        <view class="py-[24rpx] flex items-center">
                            <view class="text-sm">退款金额</view>
                            <view class="flex-1 text-right">
                                <view class="flex justify-end items-center">
                                    <text class="font-bold text-sm leading-none">￥</text>
                                    <input type="digit" v-model.number="formData.apply_money" class="font-bold text-sm leading-none" :style="{ width: inputWidth(formData.apply_money) }" @blur="handleInput">
                                </view>
                                <view class="text-xs text-gray-subtitle mt-[10rpx]">
                                    <text>最多可输入金额￥{{ refundMoney.refund_money }}</text>
                                    <text v-if="refundMoney.is_refund_delivery === 1 && Number(refundMoney.refund_delivery_money) > 0" class="ml-[10rpx]">(包含运费￥{{ refundMoney.refund_delivery_money }})</text>
                                </view>
                            </view>
                        </view>
                    </view>
                    <view class="my-[24rpx] sidebar-marign px-[24rpx] rounded-md bg-white">
                        <view class="py-[24rpx]">
                            <view class="text-sm">上传凭证<text class="text-xs text-gray-subtitle ml-[10rpx]">选填</text></view>
                            <view class="p-[20rpx] bg-[#f5f5f5] rounded mt-[20rpx]">
                                <u-upload :fileList="voucherListPreview" @afterRead="afterRead" @delete="deletePic" multiple :maxCount="9"/>
                            </view>
                        </view>
                    </view>
                    <view class="my-[24rpx] sidebar-marign px-[24rpx] rounded-md bg-white">
                        <view class="py-[24rpx]">
                            <view class="text-sm">补充描述<text class="text-xs text-gray-subtitle ml-[10rpx]">选填</text></view>
                            <view class="p-[20rpx] bg-[#f5f5f5] rounded mt-[20rpx] h-[200rpx]">
                                <textarea class="h-[100%]" v-model="formData.remark" cols="30" rows="5" placeholder="补充描述,有助于更好的处理售后问题" placeholder-class="text-sm"></textarea>
                            </view>
                        </view>
                    </view>
                    <view class="mt-[40rpx] my-[24rpx] sidebar-marign">
                        <button class="bg-[var(--primary-color)] text-[#fff] h-[80rpx] leading-[80rpx] rounded-[100rpx] text-[28rpx]" :loading="operateLoading" @click="save">提交</button>
                    </view>

                    <!-- 退款原因 -->
                    <u-popup :show="refundCausePopup" @close="refundCausePopup = false" :closeable="true">
                        <view class="px-[30rpx] pb-[30rpx]" @touchmove.prevent.stop>
							<view class="text-center p-[30rpx]">退款原因</view>
                            <scroll-view scroll-y="true" class="h-[450rpx] mt-[20rpx]">
                                <u-radio-group v-model="currReasonName" placement="column" iconPlacement="right">
                                    <u-radio activeColor="var(--primary-color)" :customStyle="{marginBottom: '30rpx'}" v-for="(item, index) in reason" :key="index" :label="item" :name="item"></u-radio>
                                </u-radio-group>
                            </scroll-view>
                            <button class="mt-[40rpx] bg-[var(--primary-color)] text-[#fff] h-[80rpx] leading-[80rpx] rounded-[100rpx] text-[28rpx]" @click="refundCausePopupFn">确定</button>
                        </view>
                    </u-popup>
                </scroll-view>
            </swiper-item>
        </swiper>
    </view>
</template>

<script setup lang="ts">
    import { ref, computed } from 'vue'
    import { onLoad } from '@dcloudio/uni-app'
    import { redirect, img, moneyFormat } from '@/utils/common'
    import { t } from '@/locale'
    import { getShopOrderDetail } from '@/addon/shop/api/order'
    import { getRefundReason, applyRefund,getRefundMoney } from '@/addon/shop/api/refund'
    import { uploadImage } from '@/app/api/system'
    import {useSubscribeMessage} from "@/hooks/useSubscribeMessage";

    const detail = ref(null)
	const orderDetail = ref({})
    const orderGoodsId = ref(0)
    const step = ref(0)
	const refundCausePopup = ref(false)
    const formData = ref({
        order_id: detail.value?.order_id,
        order_goods_id: orderGoodsId.value,
        refund_type: '',
        apply_money: '',
        reason: '',
        remark: '',
        voucher: []
    })
    const refundMoney = ref<any>({})
    const reason = ref<string[]>([])
    const currReasonName = ref('')

    getRefundReason().then(({ data }) => {
        reason.value = data
		if(reason.value && reason.value.length) currReasonName.value = reason.value[0];
    })

    onLoad((data) => {
        orderGoodsId.value = data.order_goods_id || 0
        formData.value.order_goods_id = orderGoodsId.value
        formData.value.order_id = data.order_id || 0
        getShopOrderDetail(data.order_id).then(({ data }) => {
            detail.value = data
			detail.value.order_goods.forEach((item,index)=>{
				if(orderGoodsId.value == item.order_goods_id){
					orderDetail.value = item;
				}
			})
            formData.value.apply_money = moneyFormat(refundMoney.value.refund_money)
        })

        // 获取可退款金额
        getRefundMoney({order_goods_id: data.order_goods_id}).then(res =>{
            refundMoney.value = res.data
        })
    })

    const inputWidth = computed((value) => {
        return function (value) {
            if (value == '' || value == 0) {
                return '70rpx';
            } else {
                return String(value).length * 18 + 'rpx';
            }
        };
    })

    const selectRefundType = (type : number) => {
        formData.value.refund_type = type
        step.value = 1
    }

    const voucherListPreview = computed(() => {
        return formData.value.voucher.map(item => {
            return {url: img(item)}
        })
    })

    const afterRead = (event) => {
        event.file.forEach(item => {
            uploadImage({
                filePath: item.url,
                name: 'file'
            }).then(res => {
                if (formData.value.voucher.length < 9 ) {
                    formData.value.voucher.push(res.data.url)
                }
            })
        })
    }

    const deletePic = (event)=> {
        formData.value.voucher.splice(event.index, 1)
    }

    const operateLoading = ref(false)
    const save = ()=> {

		if(!formData.value.reason){
			uni.showToast({
				title: '请选择退款原因',
				icon: 'none'
			});
			return false;
		}

		if((Number(formData.value.apply_money).toFixed(2)) < 0 ){
            uni.showToast({
                title: '退款金额不能为0,保留两位小数',
                icon: 'none'
            });
            return false;
		}

		if(Number(formData.value.apply_money)>Number(refundMoney.value.refund_money)) {
            uni.showToast({
                title: '退款金额不能大于可退款总额',
                icon: 'none'
            });
            return false;
        }

		if (operateLoading.value) return
		operateLoading.value = true

        useSubscribeMessage().request('shop_refund_agree,shop_refund_refuse')

        applyRefund(formData.value).then((res) => {
            operateLoading.value = false
            setTimeout(()=> {
                redirect({ url: '/addon/shop/pages/order/detail', param: { order_id: formData.value.order_id } })
            }, 1000)
        }).catch(() => {
            operateLoading.value = false
        })
    }

	const refundCausePopupFn = ()=>{
		formData.value.reason = currReasonName.value;
		refundCausePopup.value = false;
	}

    const handleInput = (event:any) =>{
       if(Number(event.detail.value) > Number(refundMoney.value.refund_money)){
            uni.showToast({
				title: '退款金额不能大于可退款总额',
				icon: 'none'
			});
       }
    }
</script>
<style lang="scss" scoped>
    :deep(.u-upload__button) {
        width: 70px!important;
        height: 70px!important;
        border: 1px dashed #ddd;
    }
    :deep(.u-upload__wrap__preview__image) {
        width: 70px!important;
        height: 70px!important;
    }
</style>
