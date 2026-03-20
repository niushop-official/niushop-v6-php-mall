<template>
    <view :style="warpCss" class="overflow-hidden">
        <view class="goods-sku card-template" v-if="diyComponent.goods && diyComponent.goods.attr_format && Object.keys(diyComponent.goods.attr_format).length || diyComponent.goods.goods_brand || diyComponent.weight >0 || diyComponent.volume>0">
            <view class="title mb-[30rpx]">商品属性</view>
            <view>
                <template v-for="(item,index) in filteredAttrFormat" :key="index">
                    <view v-if="index < 4 || isAttrFormatShow" class="card-template-item">
                        <text class="text-[26rpx] leading-[30rpx] w-[160rpx] font-400 shrink-0 text-[var(--text-color-light9)]">{{ item.attr_value_name }}</text>
                        <view class="text-[#333] box-border value-wid text-[26rpx] leading-[30rpx] font-400 pl-[20rpx]">{{ Array.isArray(item.attr_child_value_name) ? item.attr_child_value_name.join(',') : item.attr_child_value_name }}</view>
                    </view>
                </template>
                <view v-if="filteredAttrFormat.length > 4" class="flex-center" @click="isAttrFormatShow = !isAttrFormatShow">
                    <text class="text-[24rpx] mr-[10rpx]">{{ !isAttrFormatShow ? '展开' : '收起' }}</text>
                    <text class="nc-iconfont !text-[22rpx]" :class="{'nc-icon-xiaV6xx': !isAttrFormatShow, 'nc-icon-shangV6xx-1': isAttrFormatShow}"></text>
                </view>
            </view>
        </view>
    </view>
</template>

<script setup lang="ts">
import { ref, computed, watch, onMounted, nextTick } from 'vue';
import { img } from '@/utils/common';
import useDiyStore from '@/app/stores/diy';
import useGoodsDetailStore from '@/addon/shop/stores/goodsDetail'

const props = defineProps(['component', 'index', 'value']);
const diyStore = useDiyStore();
const emits = defineEmits(['update:componentIsShow']);
const diyComponent = computed(() => {
    if (diyStore.mode == 'decorate') {
        const obj = {
            goods: {
                attr_format: [
                    {
                       attr_value_name: '颜色',
                        attr_child_value_name: '红色,蓝色,灰色'
                    },{
                        attr_value_name: '尺寸',
                        attr_child_value_name: '大,中,小'
                    }
                ]
            }
        }
        return Object.assign({},obj,diyStore.value[props.index]);
    } else {
        return Object.assign({}, props.component, useGoodsDetailStore().goodsDetail);
    }
})

// 过滤后的属性数组（去掉空值项，包含重量和体积品牌）
const filteredAttrFormat = computed(() => {
    const originalAttrs = diyComponent.value.goods?.attr_format || [];
    // 第一步：过滤原始属性的空值项
    const filteredOriginalAttrs = originalAttrs.filter(item => {
        if (Array.isArray(item.attr_child_value_name)) {
            return item.attr_child_value_name.length > 0;
        } else {
            return !!item.attr_child_value_name;
        }
    });

    // 第二步：定义重量和体积属性项（保持和原有属性结构一致）
    const extraAttrs = [];
    if (diyComponent.value.goods.goods_brand) {
        extraAttrs.push({
            attr_value_name: '品牌',
            attr_child_value_name: diyComponent.value.goods.goods_brand.brand_name
        });
    }
    // 重量属性（非空时添加）
    if (diyComponent.value.weight && diyComponent.value.weight > 0) {
        extraAttrs.push({
            attr_value_name: '重量',
            attr_child_value_name: diyComponent.value.weight + 'kg'
        });
    }
    // 体积属性（非空时添加）
    if (diyComponent.value.volume && diyComponent.value.volume > 0) {
        extraAttrs.push({
            attr_value_name: '体积',
            attr_child_value_name: diyComponent.value.volume + 'm³'
        });
    }
    // 第三步：合并原始过滤后的属性 + 重量/体积属性
    return [...extraAttrs, ...filteredOriginalAttrs];
});

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

const isAttrFormatShow = ref(false); //控制属性是否展开

onMounted(() => {
    // 装修模式下刷新
    if (diyStore.mode == 'decorate') {
        watch(
            () => diyComponent.value,
            (newValue, oldValue) => {
                if (newValue && newValue.componentName == 'ShopGoodsDetailAttr') {
                    nextTick(() => {})
                }
            }
        )
    } else {
        watch(
            () => diyComponent.value,
            (newValue, oldValue) => {
                if (newValue && newValue.componentName == 'ShopGoodsDetailAttr') {
                    nextTick(() => {
                        if (diyComponent.value.goods && Object.keys(diyComponent.value.goods).length && (diyComponent.value.goods.attr_format && Object.keys(diyComponent.value.goods.attr_format).length || diyComponent.value.goods.goods_brand || diyComponent.value.weight >0|| diyComponent.value.volume >0)&& diyComponent.value.isShow) {
                            emits('update:componentIsShow', true)
                        }else{
                            emits('update:componentIsShow', false)
                        }
                    })
                }
            },
            { immediate: true }
        )
    }
});
</script>
<style lang="scss" scoped>
.card-template{
    background-color: transparent !important;
    border-radius: 0 !important;
}
.goods-sku .value-wid {
    width: calc(100% - 160rpx);
}
</style>
