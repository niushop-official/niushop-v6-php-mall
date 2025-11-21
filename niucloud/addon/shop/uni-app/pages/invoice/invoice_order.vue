<template>
    <view class="bg-[var(--page-bg-color)] min-h-screen overflow-hidden" :style="themeColor()">
        <view class="sidebar-margin pt-[var(--top-m)]" v-if="list.length && !loading">
            <template v-for="(item, index) in list" :key="index">
                    <view class="mb-[var(--top-m)] card-template flex-1">
                            <view @click.stop="toLink(item)">
                                <view class="flex justify-between items-center">
                                    <view class="text-[#303133] text-[24rpx] font-400 leading-[36rpx] flex items-center">
                                        <!-- <view v-if="item.activity_type_name" class="text-primary text-[18rpx] border-primary border-[2rpx] border-solid rounded-[4rpx] leading-[24rpx] px-[3rpx]">{{ item.activity_type_name }}</view> -->
                                        <view class="ml-[10rpx] text-[24rpx] font-400 text-[#303133]">订单号：{{ item.order_no }}</view>
                                    </view>
                                    <!-- <view v-if="item.status  == -1" class="text-[#303133] text-[26rpx] max-w-[150px] leading-[34rpx] truncate" :class="{'text-primary': item.status  == 1,'!text-[var(--text-color-light9)]' :item.status  == 5 || item.status  == -1}">{{ item.close_type_name }}</view> -->
                                    <!-- // <view v-else class="text-[#303133] text-[26rpx] leading-[34rpx]" :class="{'text-primary': item.status  == 1,'!text-[var(--text-color-light9)]' :item.status  == 5 || item.status  == -1}">{{ item.status_name.name }}</view> -->
                                </view>
                                <template v-for="(subItem, index) in item.order_goods" :key="index">
                                    <view class="flex box-border mt-[20rpx]">
                                        <up-image width="150rpx" height="150rpx" :radius="'var(--goods-rounded-big)'" :src="img(subItem.goods_image_thumb_small ? subItem.goods_image_thumb_small : '')" mode="aspectFill">
                                            <template #error>
                                                <image class="w-[150rpx] h-[150rpx] rounded-[var(--goods-rounded-big)] overflow-hidden" :src="img('static/resource/images/diy/shop_default.jpg')" mode="aspectFill" />
                                            </template>
                                        </up-image>
                                        <view class="ml-[20rpx] flex flex-1 flex-col box-border">
                                            <view class="flex justify-between items-baseline">
                                                <view class="max-w-[322rpx] text-[28rpx] leading-[40rpx] font-400 truncate text-[#303133]">{{ subItem.goods_name }}</view>
                                                    <view class="text-right leading-[42rpx] ml-[10rpx]">
                                                        <text class="text-[22rpx] price-font">￥</text>
                                                        <text class="text-[36rpx] font-500 price-font">{{ parseFloat(subItem.price).toFixed(2).split('.')[0] }}</text>
                                                        <text class="text-[22rpx] font-500 price-font">.{{ parseFloat(subItem.price).toFixed(2).split('.')[1] }}</text>
                                                    </view>
                                            </view>
                                            <view class="flex justify-between items-baseline text-[#303133] mt-[14rpx]">
                                                <view>
                                                    <view class="text-[24rpx] text-[var(--text-color-light6)] font-400 truncate leading-[34rpx] max-w-[369rpx] mb-[10rpx]" v-if="subItem.sku_name">{{ subItem.sku_name }}</view>
                                                    <!-- <view class="text-[24rpx] font-400 leading-[34rpx] text-[var(--text-color-light6)]" v-if="item.delivery_type != 'virtual'">{{ t('deliveryType') }} ： {{ item.delivery_type_name }}</view> -->
                                                </view>
                                                <text class="text-right text-[26rpx] font-400 w-[90rpx] leading-[36rpx]">x{{ subItem.num }}</text>
                                            </view>
                                        </view>
                                    </view>
                                    <view class="flex items-center box-border mt-[8rpx]" v-if="subItem.extend && subItem.extend.is_newcomer && subItem.num > 1">
                                        <image class="h-[24rpx] w-[56rpx]" :src="img('addon/shop/newcomer.png')" mode="heightFix" />
                                        <view class="text-[24rpx] text-[#FFB000] leading-[34rpx] ml-[8rpx]">第1{{ subItem.unit }}，￥{{ parseFloat(subItem.extend.newcomer_price).toFixed(2) }}/{{ subItem.unit }}；第{{ subItem.num > 2 ? '2~' + subItem.num : '2' }}{{ subItem.unit }}，￥{{ parseFloat(subItem.price).toFixed(2) }}/{{ subItem.unit }}</view>
                                    </view>
                                </template>
                            </view>
                            <view class="flex justify-end items-center mt-[20rpx]">
                                <view class="flex items-baseline">
                                    <view class="text-[22rpx] font-400 leading-[30rpx] text-[#303133]">实付款：</view>
                                    <view class="leading-[1] text-[var(--price-text-color)]">
                                        <text class="text-[22rpx] leading-[26rpx] price-font">￥</text>
                                        <text class="text-[36rpx] font-500 leading-[40rpx] price-font">{{ parseFloat(item.order_money).toFixed(2).split('.')[0] }}</text>
                                        <text class="text-[22rpx] font-500 leading-[28rpx] price-font">.{{ parseFloat(item.order_money).toFixed(2).split('.')[1] }}</text>
                                    </view>
                                </view>
                            </view>
                    </view>
            </template>
        </view>
    </view>
</template>

<script setup lang="ts">
import { ref, reactive, computed, nextTick } from 'vue'
import { onPageScroll, onReachBottom, onLoad } from '@dcloudio/uni-app'
import { getInvoiceInfo} from '@/addon/shop/api/invoice'
import { img ,redirect} from '@/utils/common'
const list = ref<Array<Object>>([])
const loading = ref(true)

onLoad((option: any) => {
    if (option.invoice_id) {
        loading.value = true
        getInvoiceInfo(option.invoice_id).then(res=>{
            list.value = res.data.order_list
            loading.value = false
        })
    }
});


const toLink = (data: AnyObject) => {
    redirect({ url: '/addon/shop/pages/order/detail', param: { order_id: data.order_id } })
}



</script>

<style lang="scss" scoped></style>