<template>
    <view class="bg-[var(--page-bg-color)] min-h-screen overflow-hidden order-list" :style="themeColor()">
        <view class="fixed left-0 top-0 right-0 z-10">
            <scroll-view :scroll-x="true" class="tab-style-2">
                <view class="tab-content !justify-evenly">
                    <view class="tab-items" :class="{ 'class-select': orderState === item.status.toString() }" @click="orderStateFn(item.status)" v-for="(item, index) in orderStateList">{{ item.name }}</view>
                </view>
            </scroll-view>
        </view>

        <mescroll-body ref="mescrollRef" top="88rpx" @init="mescrollInit" :down="{ use: false }" @up="getShopOrderFn">
            <view class="sidebar-margin pt-[var(--top-m)]" v-if="list.length">
                <template v-for="(item, index) in list" :key="index">
                    <view class="mb-[var(--top-m)] card-template" @click.stop="toLink(item)">
                        <view >
                            <view class="flex justify-between items-center">
                                <view class="text-[#303133] text-[24rpx] font-400 leading-[36rpx] flex items-center">
                                    <view class="text-[#333] text-[26rpx] px-[3rpx]">电子发票</view>
                                    <view class="ml-[10rpx] text-[26rpx]">{{ item.create_time }}</view>
                                </view>
                                <view  class="text-[#303133] text-[26rpx] leading-[34rpx]" :class="{'text-[var(--text-color-light9)]': item.is_invoice  == 1,'!text-primary' :item.is_invoice  == 0 || item.is_invoice  == 2}">{{ item.invoice_type_name }}</view>
                            </view>
                            
                        </view>
                        <u-divider  lineColor="#f6f6f6"></u-divider>
                        <view class="leading-none">{{ item.header_name }}</view>
                        <view class="mt-[20rpx] grid grid-cols-[1fr,1fr,auto] gap-[20rpx]" :class="{'grid-cols-[1fr,auto]': item.is_invoice !=1}">
                            <view class="truncate" v-if="item.is_invoice==1">
                                <view class="text-[24rpx] text-[#999]">
                                    发票代码
                                </view>
                                <view class="text-[28rpx] text-[#333] mt-[20rpx] truncate">
                                    {{ item.invoice_number }}
                                </view>
                            </view>
                            <view class="truncate">
                                <view class="text-[24rpx] text-[#999]">
                                    开票类型
                                </view>
                                <view class="text-[28rpx] text-[#333] mt-[20rpx] truncate">
                                    {{ item.header_type_name }}
                                </view>
                            </view>
                            <view class="truncate flex flex-col justify-end items-end">
                                <view class="text-[34rpx] text-[#333] mt-[20rpx] truncate price-font">
                                    ￥{{ item.money }}
                                </view>
                            </view>
                        </view>

                        <view class="flex justify-end text-[28rpx] mt-[20rpx] items-center" >
                            <view v-if="item.is_invoice==2" class="text-[24rpx] font-500 leading-[52rpx] h-[56rpx] min-w-[150rpx] text-center border-[2rpx] border-solid border-[#ccc] rounded-full text-[var(--text-color-light3)] box-border" @click.stop="cancelFn(item)">取消申请</view>
                            <view v-if="item.is_invoice==2" class="text-[24rpx] font-500 flex-center h-[56rpx] min-w-[150rpx] text-center border-[0] text-[#fff] primary-btn-bg rounded-full ml-[20rpx] box-border" @click.stop="editFn(item)">修改申请</view>
                        </view>
                        
                    </view>
                </template>
            </view>
            <mescroll-empty v-if="!list.length && loading" :option="{tip : '暂无数据'}"></mescroll-empty>
        </mescroll-body>
    </view>
</template>

<script setup lang="ts">
import { ref, nextTick } from 'vue';
import { t } from '@/locale'
import { img, redirect, copy ,isWeixinBrowser ,timeStampTurnTime} from '@/utils/common'
import { getInvoiceList,cancelInvoice} from '@/addon/shop/api/invoice';
import MescrollBody from '@/components/mescroll/mescroll-body/mescroll-body.vue';
import MescrollEmpty from '@/components/mescroll/mescroll-empty/mescroll-empty.vue';
import useMescroll from '@/components/mescroll/hooks/useMescroll.js';
import { onLoad, onPageScroll, onReachBottom, onShow } from '@dcloudio/uni-app';
import useConfigStore from "@/stores/config";
import useSystemStore from '@/stores/system'

const { mescrollInit, downCallback, getMescroll } = useMescroll(onPageScroll, onReachBottom);
const list = ref<Array<Object>>([]);
const loading = ref<boolean>(false);
const statusLoading = ref<boolean>(false);
const orderState = ref('')
const orderStateList: any = ref([
    {
        name: '全部',
        status: ''
    },
    {
        name:'待开票',
        status: 0
    },
    {
        name:'已开票',
        status: 1
    },
]);
const invoiceRef = ref()
const mch_id = ref('')
const isTradeManaged = ref(false)
const systemStore = useSystemStore()
const wxPrivacyPopupRef: any = ref(null)

onLoad((option: any) => {
    orderState.value = option.status || '';
    // getShopOrderStatusFn();
    // #ifdef MP
    nextTick(() => {
        if (wxPrivacyPopupRef.value) wxPrivacyPopupRef.value.proactive();
    })
    // #endif
    
});

onShow(() => {
    nextTick(() => {
        // 评价完成之后，会返回到订单列表，需要请求最新数据
        if (getMescroll()) {
            getMescroll().resetUpScroll();
        }
    })
})


const getShopOrderFn = (mescroll: any) => {
    loading.value = false;
    let data: object = {
        page: mescroll.num,
        limit: mescroll.size,
        status: orderState.value
    };

    getInvoiceList(data).then((res: any) => {
        let newArr = (res.data.data as Array<Object>);
        //设置列表数据
        if (mescroll.num == 1) {
            list.value = []; //如果是第一页需手动制空列表
        }

        list.value = list.value.concat(newArr);
        mescroll.endSuccess(newArr.length);

        mch_id.value = res.data.mch_id;
        isTradeManaged.value = res.data.is_trade_managed;
        loading.value = true;
    }).catch(() => {
        loading.value = true;
        mescroll.endErr(); // 请求失败, 结束加载
    })
}

const cancelFn = (item: any) => {
    uni.showModal({
        title: '提示',
        content: '您确定要取消申请吗？',
        confirmColor: useConfigStore().themeColor['--primary-color'],
        success: res => {
            if (res.confirm) {
                cancelInvoice(item.id).then((data) => {
                    getMescroll().resetUpScroll();
                })
            }
        }
    })
}

const editFn = (item: any) => {
    redirect({ url: '/addon/shop/pages/invoice/invoice_edit', param: { invoice_id: item.id } })
}

const toLink = (data: any) => {
    redirect({ url: '/addon/shop/pages/invoice/detail', param: { invoice_id: data.id } })
}
const orderStateFn = (status: any) => {
    orderState.value = status.toString();
    list.value = [];
    getMescroll().resetUpScroll();
};

</script>
<style>
.order-list .mescroll-body {
    padding-bottom: constant(safe-area-inset-bottom) !important;
    padding-bottom: env(safe-area-inset-bottom) !important;
}

.order-list :deep(.u-count-down__text) {
    font-size: 24rpx !important;
    color: #EF000C !important;
}
</style>
<style lang="scss" scoped>
.text-color {
    color: var(--primary-color);
}

.bg-color {
    background-color: var(--primary-color);
}
:deep(.u-divider){
    margin: 15rpx 0 !important;
}
</style>
