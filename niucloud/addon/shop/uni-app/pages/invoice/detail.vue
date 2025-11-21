<template>
    <view :style="themeColor()">
        <view class="bg-[var(--page-bg-color)] min-h-screen overflow-hidden" v-if="!loading">
            <view v-if="!loading" class="pb-20rpx"> 
                <view class="sidebar-margin mt-[var(--top-m)] card-template">
                    <view class="justify-between card-template-item">
                        <view class="text-[28rpx]">
                            电子发票
                        </view>
                        <view class="text-[28rpx] text-[#999] flex items-center" @click="openInvoicePopup()">
                            <text :class="{'text-primary':detail.is_invoice ==1}">
                                {{ detail.invoice_type_name }}
                            </text>
                            <text class="iconfont iconyouV6xx text-[#666] text-[30rpx]" v-if="detail.is_invoice == 1"></text>
                        </view> 
                    </view>
                </view>
                <view class="sidebar-margin mt-[var(--top-m)] card-template">
                    <view v-if="detail.header_name" class="card-template-item">
                        <view class="text-[28rpx] text-[#666] min-w-[190rpx]">发票抬头</view>
                        <view class="text-[28rpx] leading-[35rpx]">{{ detail.header_name }}</view>
                    </view>
                    <view v-if="detail.header_type_name" class="card-template-item">
                        <view class="text-[28rpx] text-[#666] min-w-[190rpx]">抬头类型</view>
                        <view class="text-[28rpx] leading-[35rpx]">{{ detail.header_type_name }}</view>
                    </view>
                    <view v-if="detail.name" class="card-template-item">
                        <view class="text-[28rpx] text-[#666] min-w-[190rpx]">发票内容</view>
                        <view class="text-[28rpx] leading-[35rpx]">{{ detail.name }}</view>
                    </view>
                    <view v-if="detail.tax_number" class="card-template-item">
                        <view class="text-[28rpx] text-[#666] min-w-[190rpx]">纳税人识别号</view>
                        <view class="text-[28rpx] leading-[35rpx]">{{ detail.tax_number }}</view>
                    </view>
                    <view v-if="detail.pay_voucher_thumb_small && detail.pay_voucher_thumb_small.length>0" class="card-template-item !items-start">
                        <view class="text-[28rpx] text-[#666] min-w-[190rpx]">支付凭证</view>
                        <view class="text-[28rpx] flex flex-wrap">
                            <view v-for="(item,index) in detail.pay_voucher_thumb_small" :key="index" class="w-[86rpx] h-[86rpx] rounded-[8rpx] overflow-hidden mr-[15rpx] mb-[10rpx]">
                                <image :src="img(item)" mode="aspectFill" @click="imgListPreview(detail.pay_voucher[index],detail.pay_voucher)"  class="w-full h-full"></image>
                            </view>
                        </view>
                    </view>
                    <view v-if="detail.money" class="card-template-item">
                        <view class="text-[28rpx] text-[#666] min-w-[190rpx]">开票金额</view>
                        <view class="text-[28rpx] price-font text-[var(--price-text-color)]">￥{{ detail.money }}</view>
                    </view>
                    <view v-if="detail.invoice_time" class="card-template-item">
                        <view class="text-[28rpx] text-[#666] min-w-[190rpx]">开票时间</view>
                        <view class="text-[28rpx]">{{ detail.invoice_time }}</view>
                    </view>
                    <view v-if="detail.bank_name" class="card-template-item">
                        <view class="text-[28rpx] text-[#666] min-w-[190rpx]">开户银行</view>
                        <view class="text-[28rpx] leading-[35rpx]">{{ detail.bank_name }}</view>
                    </view>
                    <view v-if="detail.bank_card_number" class="card-template-item">
                        <view class="text-[28rpx] text-[#666] min-w-[190rpx]">开户行账号</view>
                        <view class="text-[28rpx] leading-[35rpx]">{{ detail.bank_card_number }}</view>
                    </view>
                    <view v-if="detail.address" class="card-template-item">
                        <view class="text-[28rpx] text-[#666] min-w-[190rpx]">注册地址</view>
                        <view class="text-[28rpx] leading-[35rpx]">{{ detail.address }}</view>
                    </view>
                    <view v-if="detail.telephone" class="card-template-item">
                        <view class="text-[28rpx] text-[#666] min-w-[190rpx]">联系电话</view>
                        <view class="text-[28rpx] leading-[35rpx]">{{ detail.telephone }}</view>
                    </view>
                    <view v-if="detail.create_time" class="card-template-item">
                        <view class="text-[28rpx] text-[#666] min-w-[190rpx]">创建时间</view>
                        <view class="text-[28rpx] leading-[35rpx]">{{ detail.create_time }}</view>
                    </view>
                    <view v-if="detail.remark" class="card-template-item">
                        <view class="text-[28rpx] text-[#666] min-w-[190rpx]">备注</view>
                        <view class="text-[28rpx] leading-[35rpx]">{{ detail.remark }}</view>
                    </view>
                </view>
                <view class="sidebar-margin mt-[var(--top-m)] card-template">
                    <view class="justify-between card-template-item">
                        <view class="text-[28rpx]">
                            <view class="text-[30rpx]">1张发票，含{{ detail.order_list.length }}个订单</view>
                            <view class="text-[24rpx] text-[#999] mt-[15rpx]">{{detail.create_time  }}</view>
                        </view>
                        <view class="flex items-center  text-[#999]" @click="toLink(detail)">
                            <view class="text-[24rpx] leading-1">查看</view>
                            <text class="iconfont iconyouV6xx text-[30rpx]"></text>
                        </view> 
                    </view>
                </view>
                <view class="sidebar-margin mt-[var(--top-m)] card-template" v-if="detail.email">
                    <view class="text-[30rpx]">
                        接收信息
                    </view>
                    <view class="card-template-item mt-[20rpx]">
                        <view class="text-[28rpx] text-[#666] min-w-[190rpx]">电子邮件</view>
                        <view class="text-[28rpx]">{{ detail.email }}</view>
                    </view>
                </view>

            </view>
            <view class="tab-bar-placeholder"></view>
        </view>

        <loading-page :loading="loading"></loading-page>

        <!-- #ifdef MP-WEIXIN -->
        <!-- 小程序隐私协议 -->
        <wx-privacy-popup ref="wxPrivacyPopupRef"></wx-privacy-popup>
        <!-- #endif -->
    </view>
</template>

<script setup lang="ts">
import { ref, reactive, computed, nextTick } from 'vue';
import { onLoad ,onUnload} from '@dcloudio/uni-app'
import { t } from '@/locale'
import { getInvoiceInfo ,editInvoice} from '@/addon/shop/api/invoice'
import { img, redirect, copy, goback } from '@/utils/common';
import useConfigStore from "@/stores/config";


const loading = ref<boolean>(true);
const invoiceId = ref<string>('');

onLoad((option: any) => {
    if (option.invoice_id) {
        invoiceId.value = option.invoice_id;
        getInvoiceInfoFn();
    } else {
        let parameter = {
            url: '/addon/shop/pages/order/list',
            title: '缺少订单id'
        };
        goback(parameter)
    }
});
const detail = ref<any>({});
const getInvoiceInfoFn = () => {
    loading.value = true;
    getInvoiceInfo(invoiceId.value).then(res => {
        loading.value = false;
        detail.value = res.data;
        detail.value.pay_voucher =  detail.value.pay_voucher.split(',');
    })
}

const toLink = (item: any) => {
    redirect({ url: '/addon/shop/pages/invoice/invoice_order', param: { invoice_id: item.id } })
}
const openInvoicePopup = () => {
    if(detail.value.is_invoice == 1 && detail.value.invoice_voucher){
        imgListPreview(detail.value.invoice_voucher)
    }
}


// 预览图片（支持单张/多张）
const imgListPreview = (current?: string, all?: string[]) => {
    // 如果没有传入数组，默认使用当前图片作为唯一项
    const urlList = all?.length ? all.map(img) : current ? [img(current)] : [];
    if (urlList.length === 0) return; // 无图片时不执行

    // 当前预览的图片（默认第一张）
    const currentUrl = current ? img(current) : urlList[0];

    uni.previewImage({
        current: currentUrl,
        urls: urlList,
        indicator: 'number',
        loop: true
    });
};

//关闭预览图片
onUnload(() => {
    // #ifdef  H5 || APP
    try {
        uni.closePreviewImage()
    } catch (e) {

    }
    // #endif
})
</script>
<style lang="scss" scoped>

</style>
