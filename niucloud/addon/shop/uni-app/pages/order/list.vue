<template>
    <view class="bg-[var(--page-bg-color)] min-h-screen overflow-hidden order-list" :style="themeColor()">
        <view class="search-box px-[30rpx] z-10 bg-[#f6f6f6] fixed top-0 left-0 right-0 box-border">
            <view class="h-[100rpx] flex items-center flex-1  justify-between" :style="navbarInnerStyle">
                <!-- #ifdef MP-WEIXIN || APP-PLUS -->
                <view class="back-wrap text-[26px] nc-iconfont nc-icon-zuoV6xx" @click="back"></view>
                <!-- #endif -->
                <view class="search-input bg-[#fff]">
                    <text @click.stop="searchNameFn" class="nc-iconfont nc-icon-sousuo-duanV6xx1 btn"></text>
                    <input class="input" type="text" v-model.trim="searchName" placeholder="请输入商品名称" @confirm="searchNameFn" placeholderClass="text-[var(--text-color-light9)]">
                    <text v-if="searchName" class="nc-iconfont nc-icon-cuohaoV6xx1 clear" @click="searchName=''"></text>
                </view>
            </view> 
        </view>
        <view class="fixed left-0 top-0 right-0 z-10" v-if="statusLoading" :style="`top: ${categoryTop}`">
            <view class="flex justify-center items-center">
                <scroll-view :scroll-x="true" class="tab-style-2  pr-[180rpx]" scroll-with-animation :scroll-into-view="'id' + orderState">
                    <view class="tab-content">
                        <view :id="'id' + index"  class="tab-items mr-[40rpx] flex-shrink-0" :class="{ 'class-select': orderState === item.status.toString() }" @click="orderStateFn(item.status)" v-for="(item, index) in orderStateList">{{ item.name }}</view>
                    </view>
                </scroll-view>
                <view @click="isEdit = !isEdit" class="text-[#333] text-[28rpx] bg-[#f6f6f6] flex items-center pr-[20rpx] absolute right-0 top-0 h-[88rpx] z-10">
                    <image class="h-[30rpx] w-[1rpx] mr-[20rpx]" :src="img('addon/shop/rectangle.png')" mode="heightFix" />
                    <image class="h-[26rpx] w-[26rpx]" :src="img('addon/shop/invoice.png')" mode="heightFix" />
                    <view class="flex-1 whitespace-nowrap ml-[10rpx]">开发票</view>
                </view>
            </view>
        </view>
        <mescroll-body ref="mescrollRef" :top="listTop" @init="mescrollInit" :down="{ use: false }" @up="getShopOrderFn">
            <view class="sidebar-margin pt-[var(--top-m)]" v-if="list.length">
                <template v-for="(item, index) in list" :key="index">
                    <view class="flex justify-between items-center">
                        <view class="flex items-center h-[34rpx] mb-[20rpx]" v-if="isEdit">
                            <view class="self-center w-[58rpx]  flex items-center" @click.stop="isSelectGroup(item)">
                                <view class="bg-[#fff] w-[30rpx] h-[30rpx] rounded-[20rpx] flex items-center justify-center">
                                    <text class=" iconfont text-primary text-[30rpx] w-[30rpx] h-[30rpx] rounded-[20rpx] overflow-hidden shrink-0"
                                        :class="{ 'iconxuanze1':item.checked,'border-[2rpx] bg-[#f6f6f6] border-solid border-[#ccc] w-[28rpx] h-[27rpx]':!item.checked,'bg-[#eee]':item.is_can_invoice != 1 }"></text>
                                </view>
                            </view>
                            <view class="text-[28rpx] font-500 text-[#333] ">{{ item.date }}</view>
                        </view>
                    <view class="mb-[var(--top-m)] card-template flex-1">
                            <view @click.stop="toLink(item)">
                                <view class="flex justify-between items-center">
                                    <view class="text-[#303133] text-[24rpx] font-400 leading-[36rpx] flex items-center">
                                        <view v-if="item.activity_type_name" class="text-primary text-[18rpx] border-primary border-[2rpx] border-solid rounded-[4rpx] leading-[24rpx] px-[3rpx]">{{ item.activity_type_name }}</view>
                                        <view class="ml-[10rpx] text-[24rpx] font-400 text-[#303133]">{{ item.create_time }}</view>
                                    </view>
                                    <view v-if="item.status  == -1" class="text-[#303133] text-[26rpx] max-w-[150px] leading-[34rpx] truncate" :class="{'text-primary': item.status  == 1,'!text-[var(--text-color-light9)]' :item.status  == 5 || item.status  == -1}">{{ item.close_type_name }}</view>
                                    <view v-else class="text-[#303133] text-[26rpx] leading-[34rpx]" :class="{'text-primary': item.status  == 1,'!text-[var(--text-color-light9)]' :item.status  == 5 || item.status  == -1}">{{ item.status_name.name }}</view>
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
                                                <template v-if="item.activity_type == 'exchange'">
                                                    <view class="text-right ml-[10rpx] leading-[42rpx]" v-if="parseFloat(subItem.price)">
                                                        <text class="text-[22rpx] font-400 price-font">￥</text>
                                                        <text class="text-[36rpx] font-500 price-font">{{ parseFloat(subItem.price).toFixed(2).split('.')[0] }}</text>
                                                        <text class="text-[22rpx] font-500 price-font">.{{ parseFloat(subItem.price).toFixed(2).split('.')[1] }}</text>
                                                    </view>
                                                </template>
                                                <template v-else-if="subItem.extend && subItem.extend.is_impulse_buy">
                                                    <view class="text-right ml-[10rpx] leading-[42rpx]" v-if="parseFloat(subItem.goods_money)">
                                                        <text class="text-[22rpx] font-400 price-font">￥</text>
                                                        <text class="text-[36rpx] font-500 price-font">{{ parseFloat(subItem.goods_money).toFixed(2).split('.')[0] }}</text>
                                                        <text class="text-[22rpx] font-500 price-font">.{{ parseFloat(subItem.goods_money).toFixed(2).split('.')[1] }}</text>
                                                    </view>
                                                </template>
                                                <template v-else>
                                                    <view class="text-right leading-[42rpx] ml-[10rpx]">
                                                        <text class="text-[22rpx] price-font">￥</text>
                                                        <text class="text-[36rpx] font-500 price-font">{{ parseFloat(subItem.price).toFixed(2).split('.')[0] }}</text>
                                                        <text class="text-[22rpx] font-500 price-font">.{{ parseFloat(subItem.price).toFixed(2).split('.')[1] }}</text>
                                                    </view>
                                                </template>
                                            </view>
                                            <view class="flex justify-between items-baseline text-[#303133] mt-[14rpx]">
                                                <view>
                                                    <view class="text-[24rpx] text-[var(--text-color-light6)] font-400 truncate leading-[34rpx] max-w-[369rpx] mb-[10rpx]" v-if="subItem.sku_name">{{ subItem.sku_name }}</view>
                                                    <view class="text-[24rpx] font-400 leading-[34rpx] text-[var(--text-color-light6)]" v-if="item.delivery_type != 'virtual'">{{ t('deliveryType') }} ： {{ item.delivery_type_name }}</view>
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
                                    <view class="text-[22rpx] text-[var(--text-color-light9)] leading-[30rpx] mr-[6rpx]" v-if="parseFloat(item.delivery_money)">{{ t('service') }}</view>
                                    <view class="text-[22rpx] font-400 leading-[30rpx] text-[#303133]">{{ t('actualPayment') }}：</view>
                                    <view class="leading-[1] text-[var(--price-text-color)]">
                                        <template v-if="item.activity_type == 'exchange'">
                                            <text class="text-[36rpx] mr-[2rpx] leading-[40rpx] price-font font-500">{{ item.point }}</text>
                                            <text class="text-[20rpx] leading-[28rpx] font-500">{{ t('point') }}</text>
                                            <template v-if="parseFloat(item.order_money)">
                                                <text class="text-[20rpx] mx-[4rpx] font-500 leading-[28rpx]">+</text>
                                                <text class="text-[36rpx] font-500 leading-[40rpx] price-font">{{ parseFloat(item.order_money).toFixed(2) }}</text>
                                                <text class="text-[20rpx] font-500 leading-[28rpx] ml-[2rpx]">{{ t('money') }}</text>
                                            </template>
                                        </template>
                                        <template v-else>
                                            <text class="text-[22rpx] leading-[26rpx] price-font">￥</text>
                                            <text class="text-[36rpx] font-500 leading-[40rpx] price-font">{{ parseFloat(item.order_money).toFixed(2).split('.')[0] }}</text>
                                            <text class="text-[22rpx] font-500 leading-[28rpx] price-font">.{{ parseFloat(item.order_money).toFixed(2).split('.')[1] }}</text>
                                        </template>
                                    </view>
                                </view>
                            </view>
                            <view class="flex justify-end text-[28rpx] mt-[20rpx] items-center"  v-if="((item.status == 1) || (item.status == 3) || (item.status == 5 && evaluateConfig.evaluate_is_show && evaluateConfig.is_evaluate == 1) || item.is_can_invoice) && !isEdit">
                                <view class="text-[24rpx] font-500 leading-[52rpx] h-[56rpx] min-w-[150rpx] text-center border-[2rpx] border-solid border-[#ccc] rounded-full text-[var(--text-color-light3)] box-border" v-if="item.status == 1" @click.stop="orderBtnFn(item, 'close')">{{ t('orderClose') }}</view>
                                <view class="text-[24rpx] font-500 flex-center h-[56rpx] min-w-[150rpx] text-center border-[0] text-[#fff] primary-btn-bg rounded-full ml-[20rpx] box-border" v-if="item.status == 1" @click.stop="orderBtnFn(item, 'pay')">{{ t('topay') }}</view>
                                <view class="text-[24rpx] font-500 flex-center h-[56rpx] min-w-[150rpx] text-center border-[0] text-[#fff] primary-btn-bg rounded-full ml-[20rpx] box-border" v-if="item.status == 3" @click.stop="orderBtnFn(item, 'finish')">{{ t('orderFinish') }}</view>
                                <view class="text-[24rpx] font-500 leading-[52rpx] h-[56rpx] min-w-[150rpx] text-center border-[2rpx] border-solid border-[#ccc] rounded-full ml-[20rpx]  text-[var(--text-color-light3)] box-border"
                                    v-if="item.status == 5 && evaluateConfig.evaluate_is_show && evaluateConfig.is_evaluate == 1"
                                    @click.stop="orderBtnFn(item, 'evaluate')">{{ item.is_evaluate == 1 ? t('selectedEvaluate') : t('evaluate') }}</view>
                                <view class="text-[24rpx] font-500 leading-[52rpx] h-[56rpx] min-w-[150rpx] text-center border-[2rpx] border-solid border-[#ccc] rounded-full text-[var(--text-color-light3)] ml-[20rpx] box-border" v-if="item.is_can_invoice" @click.stop="orderBtnFn(item, 'invoice')">开票</view>

                            </view>
                        
                    </view>
                    </view>
                    
                </template>
            </view>
            <mescroll-empty v-if="!list.length && loading" :option="{tip : '暂无订单'}"></mescroll-empty>
        </mescroll-body>
        <view v-if="list.length && isEdit" class="fixed left-0 right-0 bottom-0 z-200 bg-[#fff]  pb-ios">
            <view v-if="checkedNum"
                  class="h-[66rpx] flex items-center justify-between pl-[30rpx] pr-[20rpx] border-0  border-b-[1rpx] border-solid border-[#f6f6f6]">
                <view class="text-[24rpx]">
                    <text>已选</text>
                    <text class="text-primary">{{ checkedNum }}</text>
                    <text>个订单</text>
                </view>
            </view>
            <view class="flex h-[100rpx] items-center  justify-between pl-[30rpx] pr-[20rpx]">
                <view class="flex items-center" @click="allChange">
                    <text class="self-center iconfont text-primary text-[30rpx] mr-[10rpx] w-[30rpx] h-[30rpx] rounded-[17rpx] overflow-hidden flex-shrink-0"
                        :class="{'iconxuanze1': isSelectAll, 'border-[2rpx] border-solid border-[#ccc] !w-[28rpx] !h-[28rpx]': !isSelectAll } "></text>
                    <text class="font-400 text-[#303133] text-[26rpx]">全选</text>
                </view>
                <button class="w-[180rpx] h-[70rpx] font-500 text-[26rpx] leading-[70rpx] !text-[#fff] m-0 rounded-full primary-btn-bg remove-border" @click="toInvoice"
                    >开票</button>
            </view>
        </view>
        <pay ref="payRef" @close="payClose"></pay>
        <!-- #ifdef MP-WEIXIN -->
        <!-- 小程序隐私协议 -->
        <wx-privacy-popup ref="wxPrivacyPopupRef"></wx-privacy-popup>
        <!-- #endif -->
    </view>
</template>

<script setup lang="ts">
import { ref, nextTick,computed } from 'vue';
import { t } from '@/locale'
import { img, redirect, copy ,pxToRpx} from '@/utils/common'
import { getShopOrderStatus, getShopOrder, orderClose, orderFinish } from '@/addon/shop/api/order';
import { getEvaluateConfig } from '@/addon/shop/api/shop';
import MescrollBody from '@/components/mescroll/mescroll-body/mescroll-body.vue';
import MescrollEmpty from '@/components/mescroll/mescroll-empty/mescroll-empty.vue';
import useMescroll from '@/components/mescroll/hooks/useMescroll.js';
import { onLoad, onPageScroll, onReachBottom, onShow } from '@dcloudio/uni-app';
import useConfigStore from "@/stores/config";
import { topTabar } from '@/utils/topTabbar';
import useSystemStore from '@/stores/system';
const systemStore = useSystemStore()
/********* 自定义头部 - start ***********/
const topTabarObj = topTabar()

let topTabbarData = topTabarObj.setTopTabbarParam({ topStatusBar:{style :'style-5',textColor:'#606266'} })
/********* 自定义头部 - end ***********/

const navbarInnerStyle = ref('')
// #ifdef MP-WEIXIN || MP-BAIDU || MP-TOUTIAO || MP-QQ
let rightButtonWidth = systemStore.menuButtonInfo.width ? systemStore.menuButtonInfo.width * 2 + 'rpx' : '70rpx';
navbarInnerStyle.value += 'padding-right:calc(' + rightButtonWidth + ' + 30rpx);';
navbarInnerStyle.value += 'padding-top:' + systemStore.menuButtonInfo.top + 'px;';
navbarInnerStyle.value += 'padding-bottom: 8px;';
navbarInnerStyle.value+= 'height:' + systemStore.menuButtonInfo.height + 'px;';
// #endif

// #ifdef APP-PLUS
navbarInnerStyle.value += 'padding-top:' + systemStore.systemInfo.statusBarHeight + 'px;';
// #endif
const categoryTop = computed(() => {
    let height = Object.keys(systemStore.menuButtonInfo).length && systemStore.menuButtonInfo.height ? (pxToRpx(Number(systemStore.menuButtonInfo.height)) + pxToRpx(systemStore.menuButtonInfo.top) + pxToRpx(8)) : 100;
    
    return height + 'rpx';
});

const listTop = computed(() => {
    let height = Object.keys(systemStore.menuButtonInfo).length && systemStore.menuButtonInfo.height ? (pxToRpx(Number(systemStore.menuButtonInfo.height)) + pxToRpx(systemStore.menuButtonInfo.top) + pxToRpx(8) + 88) : 188;
    // let height = Object.keys(systemStore.menuButtonInfo).length && systemStore.menuButtonInfo.height ? 88: 128;
    return height + 'rpx';
});

const { mescrollInit, downCallback, getMescroll } = useMescroll(onPageScroll, onReachBottom);
const list = ref<Array<Object>>([]);
const loading = ref<boolean>(false);
const statusLoading = ref<boolean>(false);
const orderState = ref('')
const orderStateList: any = ref([]);
const evaluateConfig = ref("")

const isEdit = ref(false)

const mch_id = ref('')
const isTradeManaged = ref(false)

const wxPrivacyPopupRef: any = ref(null)

onLoad((option: any) => {
    orderState.value = option.status || "";
    evaluateEvent()
    getShopOrderStatusFn();
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

const evaluateEvent = () => {
    getEvaluateConfig().then((data: any) => {
        evaluateConfig.value = data.data
    })
}

const getShopOrderFn = (mescroll: any) => {
    loading.value = false;
    let data: object = {
        page: mescroll.num,
        limit: mescroll.size,
        status: orderState.value,
        body: searchName.value,
    };

    getShopOrder(data).then((res: any) => {
        let newArr = (res.data.data as Array<Object>);
        //设置列表数据
        if (mescroll.num == 1) {
            list.value = []; //如果是第一页需手动制空列表
        }

        newArr.forEach((item: any) => {
            item.checked = false;
            item.is_show_evaluate = true;
            let evaluateCount = 0;
            for (let i = 0; i < item.order_goods.length; i++) {
                if (item.order_goods[i].status != 1 || item.order_goods[i].is_enable_refund == 1) {
                    evaluateCount++;
                }
            }
            if (evaluateCount == item.order_goods.length) {
                item.is_show_evaluate = false;
            }
        })

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

// 选择数量
const checkedNum = computed(() => {
    let num = 0
    list.value.forEach((item: any) => {
        item.checked && (num += 1)
    })
    return num
})

/**
 * 全选
 */
//判断是否全选状态
const isSelectAll = ref(false)
const isallclick = () => {
    const allowSelectOrders = list.value.filter(item => item.is_can_invoice == 1);
    if (allowSelectOrders.length === 0) {
        isSelectAll.value = false;
        return;
    }
    const isActive = allowSelectOrders.every((item: any) => {
        return item.checked
    })
    if (isActive) {
        isSelectAll.value = true
    } else {
        isSelectAll.value = false
    }
}
// 单个选择
const isSelectGroup = (data: any) => {
    if (data.is_can_invoice !== 1) return; // 若当前订单禁用，直接返回
    data.checked = !data.checked
    isallclick()
}
// 全选
const allChange = () => {
    isSelectAll.value = !isSelectAll.value
    list.value.forEach((item: any) => {
        if (item.is_can_invoice == 1) { // 仅允许选择的订单才会被全选影响
            item.checked = isSelectAll.value;
        }
    })
}

const getShopOrderStatusFn = () => {
    statusLoading.value = false;
    orderStateList.value = [];
    let obj = { name: '全部', status: '' };
    orderStateList.value.push(obj);

    getShopOrderStatus().then((res: any) => {
        Object.values(res.data).forEach((item, index) => {
            orderStateList.value.push(item);
        });
        statusLoading.value = true;
    }).catch(() => {
        statusLoading.value = true;
    })
}

const orderStateFn = (status: any) => {
    orderState.value = status.toString();
    list.value = [];
    getMescroll().resetUpScroll();
};

const toLink = (data: any) => {
    redirect({ url: '/addon/shop/pages/order/detail', param: { order_id: data.order_id } })
}

const searchName = ref('')
const searchNameFn = () => {
    list.value = [];
    getMescroll().resetUpScroll();
}
const back = () => {
    if (getCurrentPages().length > 1) {
        uni.navigateBack()
    } else {
        redirect({
            url: '/addon/shop/pages/index',
            mode: 'reLaunch'
        });
    }
}

// 支付
const payRef = ref(null)
const orderBtnFn = (data: any, type = '') => {
    if (type == 'pay')
        payRef.value?.open(data.order_type, data.order_id, `/addon/shop/pages/order/detail?order_id=${ data.order_id }`);
    else if (type == 'close') {
        close(data);
    } else if (type == 'finish') {
        finish(data);
    } else if (type == 'evaluate') {
        if (!data.is_evaluate) {
            redirect({ url: '/addon/shop/pages/evaluate/order_evaluate', param: { order_id: data.order_id } })
        } else {
            redirect({ url: '/addon/shop/pages/evaluate/order_evaluate_view', param: { order_id: data.order_id } })
        }
    } else if (type == 'invoice') {

        redirect({ url: '/addon/shop/pages/invoice/invoice', param: { order_ids: JSON.stringify([data.order_id]) } })
    }
}
const optionLoading = ref(false)
const toInvoice = (data: any) => {
    if (!checkedNum.value) {
        uni.showToast({ title: '还没有选择商品', icon: 'none' })
        return
    }
    if (optionLoading.value) return
    optionLoading.value = true

    const ids: any = []
    list.value.forEach((item: any) => {
        item.checked && ids.push(item.order_id)
    })
    redirect({ url: '/addon/shop/pages/invoice/invoice', param: { order_ids: JSON.stringify(ids) } })
    optionLoading.value = false
    isEdit.value = false
    isSelectAll.value = false
    getMescroll().resetUpScroll();
}

//关闭订单
const close = (item: any) => {
    uni.showModal({
        title: '提示',
        content: '您确定要关闭该订单吗？',
        confirmColor: useConfigStore().themeColor['--primary-color'],
        success: res => {
            if (res.confirm) {
                orderClose(item.order_id).then((data) => {
                    getMescroll().resetUpScroll();
                })
            }
        }
    })
}

//订单完成
const finish = (item: any) => {
    // 如果不在微信小程序中
    // #ifndef MP-WEIXIN
    uni.showModal({
        title: '提示',
        content: '您确定物品已收到吗？',
        confirmColor: useConfigStore().themeColor['--primary-color'],
        success: res => {
            if (res.confirm) {
                orderFinish(item.order_id).then((data) => {
                    getMescroll().resetUpScroll();
                })
            }
        }
    })
    // #endif

    // #ifdef MP-WEIXIN
    // 检测微信小程序是否已开通发货信息管理服务
    if (item.pay && item.pay.type == 'wechatpay' && isTradeManaged.value && wx.openBusinessView) {
        wx.openBusinessView({
            businessType: 'weappOrderConfirm',
            extraData: {
                merchant_id: mch_id.value,
                merchant_trade_no: item.out_trade_no
            },
            success: (res: any) => {
                orderFinish(item.order_id).then((data) => {
                    getMescroll().resetUpScroll();
                })
            },
            fail: (res: any) => {
                console.log('小程序确认收货组件打开失败 fail', res);
            }
        })
    } else {
        uni.showModal({
            title: '提示',
            content: '您确定物品已收到吗？',
            confirmColor: useConfigStore().themeColor['--primary-color'],
            success: res => {
                if (res.confirm) {
                    orderFinish(item.order_id).then((data) => {
                        getMescroll().resetUpScroll();
                    })
                }
            }
        })
    }
    // #endif
}
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
</style>
