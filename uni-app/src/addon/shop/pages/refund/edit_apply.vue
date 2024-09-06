<template>
    <view :style="themeColor()">
        <swiper :indicator-dots="false" :autoplay="false" :disable-touch="true" :current="step" class="h-screen" :duration="300" v-if="detail">
            <swiper-item>
                <scroll-view scroll-y="true" class="bg-page h-screen">
                    <view class="m-[24rpx] px-[24rpx] rounded-md bg-white">
                        <view class="flex py-[30rpx] border-0 !border-b !border-[#f5f5f5] border-solid">
                            <u--image width="120rpx" height="120rpx" :src="img(orderDetail.sku_image)" model="aspectFill">
                                <template #error>
                                    <image class="w-[120rpx] h-[120rpx]" :src="img('static/resource/images/diy/shop_default.jpg')" mode="aspectFill"></image>
                                </template>
                            </u--image>
                            <view class="flex flex-1 w-0 flex-col justify-between ml-[20rpx]">
                                <view>
                                    <view class="text-ellipsis text-[#303133] text-sm leading-normal">{{orderDetail.goods_name}}</view>
                                    <view class="mt-[10rpx] text-[26rpx] text-gray-subtitle">{{ orderDetail.sku_name }}</view>
                                </view>
                            </view>
                        </view>
                    </view>

                    <view class="m-[24rpx] px-[24rpx] rounded-md bg-white">
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
                    <view class="m-[24rpx] px-[24rpx] rounded-md bg-white">
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
                    <view class="m-[24rpx] px-[24rpx] rounded-md bg-white">
                        <view class="py-[24rpx] flex items-center">
                            <view class="text-sm">退款金额</view>
                            <view class="flex-1 text-right">
                                <view class="flex justify-end items-center">
                                    <text class="font-bold text-sm leading-none">￥</text>
                                    <input type="digit" v-model.number="formData.apply_money" class="font-bold text-sm leading-none" :style="{ width: inputWidth(formData.apply_money) }">
                                </view>
                                <view class="text-xs text-gray-subtitle mt-[10rpx]">
                                    <text>最多可输入金额￥{{ refundMoney.refund_money }}</text>
                                    <text v-if="refundMoney.is_refund_delivery === 1 && Number(refundMoney.refund_delivery_money) > 0" class="ml-[10rpx]">(包含运费￥{{ refundMoney.refund_delivery_money }})</text>
                                </view>
                            </view>
                        </view>
                    </view>
                    <view class="m-[24rpx] px-[24rpx] rounded-md bg-white">
                        <view class="py-[24rpx]">
                            <view class="text-sm">上传凭证<text class="text-xs text-gray-subtitle ml-[10rpx]">选填</text></view>
                            <view class="p-[20rpx] bg-[#f5f5f5] rounded mt-[20rpx]">
                                <u-upload :fileList="voucherListPreview" @afterRead="afterRead" @delete="deletePic" multiple :maxCount="9"/>
                            </view>
                        </view>
                    </view>
                    <view class="m-[24rpx] px-[24rpx] rounded-md bg-white">
                        <view class="py-[24rpx]">
                            <view class="text-sm">补充描述<text class="text-xs text-gray-subtitle ml-[10rpx]">选填</text></view>
                            <view class="p-[20rpx] bg-[#f5f5f5] rounded mt-[20rpx] h-[200rpx]">
                                <textarea v-model="formData.remark" cols="30" rows="5" placeholder="补充描述,有助于更好的处理售后问题" placeholder-class="text-sm"></textarea>
                            </view>
                        </view>
                    </view>
                    <view class="mt-[40rpx] m-[24rpx]">
                        <button class="bg-[var(--primary-color)] text-[#fff] h-[80rpx] leading-[80rpx] rounded-[100rpx] text-[28rpx]" @click="save" :loading="operateLoading">提交</button>
                    </view>

                    <!-- 退款原因 -->
                    <u-popup :show="refundCausePopup" @close="close" @open="open">
                        <view class="px-[30rpx] pb-[30rpx]" @touchmove.prevent.stop>
                            <view class="flex items-center h-[90rpx] justify-between">
                                <text>退款原因</text>
                                <text class="nc-iconfont nc-icon-guanbiV6xx" @click="refundCausePopup = false"></text>
                            </view>
                            <scroll-view scroll-y="true" class="h-[450rpx] mt-[20rpx]">
                                <u-radio-group v-model="currReasonName" placement="column">
                                    <u-radio activeColor="var(--primary-color)" :customStyle="{marginBottom: '8px'}" v-for="(item, index) in reason" :key="index" :label="item" :name="item"></u-radio>
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
    import { getRefundReason, editRefund, getRefundDetail,getRefundMoneyAgain } from '@/addon/shop/api/refund'
    import { uploadImage } from '@/app/api/system'

    const detail = ref(null)
	const orderDetail = ref({})
    const orderGoodsId = ref(0)
    const step = ref(0)
	const refundCausePopup = ref(false)
    const formData = ref({
        order_id: detail.value?.order_id,
        order_goods_id: orderGoodsId.value,
        order_refund_no:'',
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

    onLoad((option: any) => {
        orderGoodsId.value = option.order_goods_id || 0
		getRefundDetail(option.order_refund_no).then(({ data }) => {
		    detail.value = data
			orderDetail.value = data.order_goods;

			// 初始化信息
			formData.value.order_goods_id = data.order_goods_id;
			formData.value.order_id = data.order_id;
            formData.value.order_refund_no = data.order_refund_no;
			formData.value.remark = data.remark;
			formData.value.reason = data.reason;
			currReasonName.value = data.reason;
			formData.value.voucher = data.voucher;
		})
        getRefundMoneyAgain({order_refund_no: option.order_refund_no}).then(res =>{
            refundMoney.value = res.data
            formData.value.apply_money = moneyFormat(refundMoney.value.refund_money)
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
            }).catch(() => {
            })
        })
    }

    const deletePic = (event)=> {
        formData.value.voucher.splice(event.index, 1)
    }

    const operateLoading = ref(false)
    const save = ()=> {
        if (!formData.value.reason) {
            uni.showToast({
                title: '请选择退款原因',
                icon: 'none'
            });
            return false;
        }

        if ((Number(formData.value.apply_money).toFixed(2)) < 0) {
            uni.showToast({
                title: '退款金额不能为0,保留两位小数',
                icon: 'none'
            });
            return false;
        }

        if (Number(formData.value.apply_money) > Number(refundMoney.value.refund_money)) {
            uni.showToast({
                title: '退款金额不能大于可退款总额',
                icon: 'none'
            });
            return false;
        }

        if (operateLoading.value) return
        operateLoading.value = true

        editRefund(formData.value).then((res) => {
            operateLoading.value = false
            setTimeout(() => {
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
