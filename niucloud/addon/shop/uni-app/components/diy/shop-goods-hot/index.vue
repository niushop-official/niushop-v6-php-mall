<template>
    <view :style="warpCss" class="overflow-hidden" v-if="goodsList.length && !loading">
        <view class="flex items-center relative">
            <view class="desc flex flex-col justify-center items-center mr-[20rpx]">
                <text v-if="diyComponent.titleStyle.way == 'text'" class="whitespace-nowrap mb-[12rpx]" :style="titleCss"  @click="diyStore.toRedirect(diyComponent.titleStyle.link)">{{ diyComponent.titleStyle.text }}</text>
                <image v-else-if="diyComponent.titleStyle.imgUrl" class="h-[40rpx] max-w-[120rpx] mb-[12rpx]" @click="diyStore.toRedirect(diyComponent.titleStyle.link)" :src="img(diyComponent.titleStyle.imgUrl)" mode="heightFix" />
                <text v-if="diyComponent.subTitleStyle.text" class="h-[36rpx] mb-[18rpx] px-[8rpx] whitespace-nowrap flex items-center" :style="subTitleCss">{{ diyComponent.subTitleStyle.text }}</text>
                <view v-if="diyComponent.btnStyle.text" class="bg-[#fff] buy-btn   text-[#FF4142] rounded-[50rpx] font-bold pl-[20rpx] pr-[14rpx] leading-[normal] py-[2rpx]" :style="titleBtnCss" @click="diyStore.toRedirect(diyComponent.btnStyle.link)">
                    <text class="whitespace-nowrap">{{ diyComponent.btnStyle.text }}</text>
                    <text class="iconfont iconxiangyoujiantou !text-[20rpx] pt-[4rpx]"></text>
                </view>
            </view>
            <scroll-view scroll-x="true" v-if="goodsList.length >= 1" class="flex-1"  style="width: 0" >
                <view class="flex">
                    <template v-for="(item,index) in goodsList" :key="index">
                        <view v-if="index == 0" class="box-border p-[10rpx] pr-[18rpx] inline-flex relative" @click="toDetail(item)" :style="goodsItemCss">
                            <view class="relative overflow-hidden" :style="goodsImgCss">
                                <up-image width="150rpx" height="186rpx" :src="img(item.goods_cover_thumb_small || '')"  :mode="diyComponent.mode">
                                    <template #error>
                                        <image class="w-[150rpx] h-[186rpx] overflow-hidden" :src="img('static/resource/images/diy/shop_default.jpg')" mode="aspectFill" />
                                    </template>
                                </up-image>
                                <text class="absolute left-0 top-0 z-[10] px-[8rpx] py-[6rpx] rounded-br-[20rpx] leading-[1]" :style="labelCss">{{ diyComponent.goodsStyle.labelText }}</text>
                            </view>
                            <view class="ml-[10rpx] flex flex-col">
                                <view class="text-[24rpx] w-[160rpx] leading-[1.5] text-[#303133] multi-hidden whitespace-pre-wrap mb-[10rpx]">{{ item.goods_name }}</view>
                                <view class="flex flex-col mt-[auto] items-start">
                                    <view
                                        class="px-[8rpx] py-[6rpx] flex items-center save-money-bg text-[#fff] text-[22rpx] inline-block rounded-[6rpx]">
                                        <image :src="img('/addon/shop/diy/index/hot_indicate.png')" class="w-[20rpx] h-[16rpx]" />
                                        <text class="ml-[6rpx]">低至{{getDiscount(item)}}折</text>
                                    </view>
                                    <text class="iconfont icona-xiangxiaV6xx1 leading-1 text-[#FF0000] ml-[14rpx]"></text>
                                </view>
                                <view class="flex items-baseline leading-[1]">
                                    <view
                                        class="font-bold text-[var(--price-text-color)] price-font block truncate max-w-[350rpx]">
                                        <text class="text-[20rpx] font-500 mr-[4rpx]">￥</text>
                                        <text class="text-[32rpx] font-500">{{ Number(item.goodsSku.show_price) }}</text>
                                    </view>
                                </view>
                            </view>
                        </view>
                        <view v-else class="box-border p-[8rpx] inline-flex flex-col items-center relative ml-[18rpx]" @click="toDetail(item)" :style="goodsItemCss">
                            <view class="relative overflow-hidden" :style="goodsImgCss">
                                <up-image width="140rpx" height="140rpx" :src="img(item.goods_cover_thumb_small || '')"  :mode="diyComponent.mode">
                                    <template #error>
                                        <image class="w-[140rpx] h-[140rpx] overflow-hidden" :src="img('static/resource/images/diy/shop_default.jpg')" mode="aspectFill" />
                                    </template>
                                </up-image>
                                <text class="absolute left-0 top-0 z-[10] bg-[red] text-[22rpx] px-[8rpx] py-[6rpx] rounded-br-[20rpx] text-[#fff]" :style="labelCss">{{ diyComponent.goodsStyle.labelText }}</text>
                            </view>
                            <view class="flex w-[100%] items-center bg-[#FFE2E2] h-[40rpx] mt-[auto] rounded-[50rpx]">
                                <view
                                class="font-bold flex-1 text-[var(--price-text-color)] price-font block truncate max-w-[80rpx] text-center">
                                    <text class="text-[20rpx] font-500 mr-[4rpx]">￥</text>
                                    <text class="text-[32rpx] font-500">{{ Number(item.goodsSku.show_price) }}</text>
                                </view>
                                <view class="flex items-center h-[100%] text-[#fff] w-[58rpx] box-border pl-[16rpx] flex items-center" :style="{'background-image':'url(' + img('/addon/shop/diy/index/hot_btn_bg.png') + ')','background-size':'100% 100%','background-repeat':'no-repeat'}">
                                    <text class="text-[22rpx]">抢</text>
                                    <text class="iconfont iconarrow-right pt-[2rpx] !text-[16rpx]"></text>
                                </view>
                            </view>
                        </view>
                    </template>
                </view>
            </scroll-view>
        </view>
        <!-- 装修时，防止点击 -->
        <view v-if="diyStore.mode == 'decorate'" class="absolute z-10 top-0 right-0 bottom-0 left-0"></view>
    </view>
</template>

<script setup lang="ts">
import { ref, reactive, computed, watch, onMounted, nextTick } from 'vue';
import { redirect, img } from '@/utils/common';
import useDiyStore from '@/app/stores/diy';
import { getGoodsComponents } from '@/addon/shop/api/goods';

const props = defineProps(['component', 'index', 'value']);
const diyStore = useDiyStore();
const goodsList = ref([]) // 商品列表

const diyComponent = computed(() => {
    if (props.value) {
        return props.value;
    } else if (diyStore.mode == 'decorate') {
        return diyStore.value[props.index];
    } else {
        return props.component;
    }
})

// 组件框架样式
const warpCss = computed(() => {
    let style = '';
    style += 'position:relative;';
    if (diyComponent.value.componentStartBgColor && diyComponent.value.componentEndBgColor) style += `background:linear-gradient(${ diyComponent.value.componentGradientAngle },${ diyComponent.value.componentStartBgColor },${ diyComponent.value.componentEndBgColor });`;
    else style += 'background-color:' + (diyComponent.value.componentStartBgColor || diyComponent.value.componentEndBgColor) + ';';

    if (diyComponent.value.componentBgUrl) {
        style += `background-image:url('${ img(diyComponent.value.componentBgUrl) }');`;
        style += 'background-size: cover;background-repeat: no-repeat;';
    }

    if (diyComponent.value.topRounded) style += 'border-top-left-radius:' + diyComponent.value.topRounded * 2 + 'rpx;';
    if (diyComponent.value.topRounded) style += 'border-top-right-radius:' + diyComponent.value.topRounded * 2 + 'rpx;';
    if (diyComponent.value.bottomRounded) style += 'border-bottom-left-radius:' + diyComponent.value.bottomRounded * 2 + 'rpx;';
    if (diyComponent.value.bottomRounded) style += 'border-bottom-right-radius:' + diyComponent.value.bottomRounded * 2 + 'rpx;';
    return style;
})

const titleCss = computed(() => {
    let style = ''
    style += 'color:' + diyComponent.value.titleStyle.textColor + ';';
    style += 'font-size:' + diyComponent.value.titleStyle.fontSize*2 + 'rpx;';
    if(diyComponent.value.titleStyle.fontWeight == 'bold') style += 'font-weight:' + diyComponent.value.titleStyle.fontWeight + ';';
    return style
})


const subTitleCss = computed(() => {
    let style = ''
    style += 'color:' + diyComponent.value.subTitleStyle.textColor + ';';
    style += 'background-color:' + diyComponent.value.subTitleStyle.bgColor + ';';
    style += 'font-size:' + diyComponent.value.subTitleStyle.fontSize*2 + 'rpx;';
    style += 'border-radius:' + diyComponent.value.subTitleStyle.rounded*2 + 'rpx;';
    return style
})

const titleBtnCss = computed(() => {
    let style = ''
    style += 'color:' + diyComponent.value.btnStyle.textColor + ';';
    style += 'font-size:' + diyComponent.value.btnStyle.fontSize*2 + 'rpx;';
    if (diyComponent.value.btnStyle.startBgColor && diyComponent.value.btnStyle.endBgColor) {
        if(diyComponent.value.btnStyle.gradientType == 'radial'){
            style += `background:radial-gradient(${ diyComponent.value.btnStyle.startBgColor },${ diyComponent.value.btnStyle.endBgColor });`;
        }else{
            style += `background:linear-gradient(${ diyComponent.value.btnStyle.startBgColor },${ diyComponent.value.btnStyle.endBgColor });`;
        }
    } else style += 'background-color:' + (diyComponent.value.btnStyle.startBgColor || diyComponent.value.btnStyle.endBgColor) + ';';
    return style
})

const labelCss = computed(() => {
    let style = ''
    style += 'color:' + diyComponent.value.goodsStyle.labelColor + ';';
    style += 'background-color:' + diyComponent.value.goodsStyle.labelBgColor + ';';
    style += 'font-size:' + diyComponent.value.goodsStyle.labelSize*2 + 'rpx;';
    return style
})

const goodsItemCss = computed(() => {
    let style = ''
    style += 'background-color:' + diyComponent.value.goodsStyle.bgColor + ';';
    style += 'border-radius:' + diyComponent.value.goodsStyle.rounded*2 + 'rpx;';
    return style
})

const goodsImgCss = computed(() => {
    let style = ''
    style += 'border-radius:' + diyComponent.value.goodsStyle.imgRounded*2 + 'rpx;';
    return style
})

const getDiscount = (item: any) => {
    if (item.goodsSku.market_price == 0) item.goodsSku.market_price = item.goodsSku.price
    let discount = Number((item.goodsSku.show_price / item.goodsSku.market_price).toFixed(2))
    discount = Number((discount * 10).toFixed(1))
    return discount
}

const loading = ref(false)
const refreshGoodsList = () => {
    loading.value = true
    let data = {
        num: diyComponent.value.goodsStyle.source == 'all' ? diyComponent.value.goodsStyle.num : '',
        goods_ids: diyComponent.value.goodsStyle.source == 'custom' ? diyComponent.value.goodsStyle.goodsIds : ''
    }
    getGoodsComponents(data).then((res) => {
        goodsList.value = res.data
        loading.value = false
    });
}

onMounted(() => {
    refresh();
    // 装修模式下刷新
    if (diyStore.mode == 'decorate') {
        goodsList.value = [
            {
                goods_cover_thumb_small: '',
                goods_name: '商品名称',
                goodsSku: {

                    market_price: 500,
                    show_price: 200
                }
            },{
                goods_cover_thumb_small: '',
                goods_name: '商品名称',
                goodsSku: {

                    market_price: 500,
                    show_price: 200
                }
            }
        ]
    } else {
        refreshGoodsList()
    }
});

const refresh = () => {
    // 装修模式下设置默认图
    if (diyStore.mode == 'decorate') {

    } else {

    }
}

const toDetail = (item: any) => {
    if (diyStore.mode == 'decorate') return;
    redirect({ url: '/addon/shop/pages/goods/detail', param: { goods_id: item.goods_id } })
}
</script>
<style lang="scss" scoped>
.buy-btn{
    background: radial-gradient(#FFEBC8, #FFEBC8, #fff);
}
.save-money-bg{
    background: linear-gradient(to right, #FF0000, #FF6200);
}
</style>
