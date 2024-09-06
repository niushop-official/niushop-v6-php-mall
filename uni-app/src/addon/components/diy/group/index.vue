<template>
    <view class="diy-group" id="componentList">
        <top-tabbar :scrollBool="diyGroup.componentsScrollBool.TopTabbar" v-if="data.global && Object.keys(data.global).length && data.global.topStatusBar && data.global.topStatusBar.isShow" ref="topTabbarRef" :data="data.global" />
        <view v-for="(component, index) in data.value" :key="component.id"
        @click="diyStore.changeCurrentIndex(index, component)"
        :class="diyGroup.getComponentClass(index,component)" :style="component.pageStyle">
            <view class="relative" :style="{ marginTop : component.margin.top < 0 ? (component.margin.top * 2) + 'rpx' : '0' }">
                <!-- 装修模式下，设置负上边距后超出的内容，禁止选中设置 -->
                <view v-if="diyGroup.isShowPlaceHolder(index,component)" class="absolute w-full z-1" :style="{ height : (component.margin.top * 2 * -1) + 'rpx' }" @click.stop="diyGroup.placeholderEvent"></view>
            <template v-if="component.componentName == 'ActiveCube'">
                <diy-active-cube :component="component" :global="data.global" :index="index" :pullDownRefreshCount="props.pullDownRefreshCount" :scrollBool="diyGroup.componentsScrollBool.ActiveCube" />
            </template>
            <template v-if="component.componentName == 'CarouselSearch'">
                <diy-carousel-search :component="component" :global="data.global" :index="index" :pullDownRefreshCount="props.pullDownRefreshCount" :scrollBool="diyGroup.componentsScrollBool.CarouselSearch" />
            </template>
            <template v-if="component.componentName == 'FloatBtn'">
                <diy-float-btn :component="component" :global="data.global" :index="index" :pullDownRefreshCount="props.pullDownRefreshCount" :scrollBool="diyGroup.componentsScrollBool.FloatBtn" />
            </template>
            <template v-if="component.componentName == 'GraphicNav'">
                <diy-graphic-nav :component="component" :global="data.global" :index="index" :pullDownRefreshCount="props.pullDownRefreshCount" :scrollBool="diyGroup.componentsScrollBool.GraphicNav" />
            </template>
            <template v-if="component.componentName == 'HorzBlank'">
                <diy-horz-blank :component="component" :global="data.global" :index="index" :pullDownRefreshCount="props.pullDownRefreshCount" :scrollBool="diyGroup.componentsScrollBool.HorzBlank" />
            </template>
            <template v-if="component.componentName == 'HorzLine'">
                <diy-horz-line :component="component" :global="data.global" :index="index" :pullDownRefreshCount="props.pullDownRefreshCount" :scrollBool="diyGroup.componentsScrollBool.HorzLine" />
            </template>
            <template v-if="component.componentName == 'HotArea'">
                <diy-hot-area :component="component" :global="data.global" :index="index" :pullDownRefreshCount="props.pullDownRefreshCount" :scrollBool="diyGroup.componentsScrollBool.HotArea" />
            </template>
            <template v-if="component.componentName == 'ImageAds'">
                <diy-image-ads :component="component" :global="data.global" :index="index" :pullDownRefreshCount="props.pullDownRefreshCount" :scrollBool="diyGroup.componentsScrollBool.ImageAds" />
            </template>
            <template v-if="component.componentName == 'MemberInfo'">
                <diy-member-info :component="component" :global="data.global" :index="index" :pullDownRefreshCount="props.pullDownRefreshCount" :scrollBool="diyGroup.componentsScrollBool.MemberInfo" />
            </template>
            <template v-if="component.componentName == 'MemberLevel'">
                <diy-member-level :component="component" :global="data.global" :index="index" :pullDownRefreshCount="props.pullDownRefreshCount" :scrollBool="diyGroup.componentsScrollBool.MemberLevel" />
            </template>
            <template v-if="component.componentName == 'Notice'">
                <diy-notice :component="component" :global="data.global" :index="index" :pullDownRefreshCount="props.pullDownRefreshCount" :scrollBool="diyGroup.componentsScrollBool.Notice" />
            </template>
            <template v-if="component.componentName == 'RichText'">
                <diy-rich-text :component="component" :global="data.global" :index="index" :pullDownRefreshCount="props.pullDownRefreshCount" :scrollBool="diyGroup.componentsScrollBool.RichText" />
            </template>
            <template v-if="component.componentName == 'RubikCube'">
                <diy-rubik-cube :component="component" :global="data.global" :index="index" :pullDownRefreshCount="props.pullDownRefreshCount" :scrollBool="diyGroup.componentsScrollBool.RubikCube" />
            </template>
            <template v-if="component.componentName == 'Text'">
                <diy-text :component="component" :global="data.global" :index="index" :pullDownRefreshCount="props.pullDownRefreshCount" :scrollBool="diyGroup.componentsScrollBool.Text" />
            </template>
            <template v-if="component.componentName == 'GoodsCoupon'">
                <diy-goods-coupon :component="component" :global="data.global" :index="index" :pullDownRefreshCount="props.pullDownRefreshCount" :scrollBool="diyGroup.componentsScrollBool.GoodsCoupon" />
            </template>
            <template v-if="component.componentName == 'GoodsList'">
                <diy-goods-list :component="component" :global="data.global" :index="index" :pullDownRefreshCount="props.pullDownRefreshCount" :scrollBool="diyGroup.componentsScrollBool.GoodsList" />
            </template>
            <template v-if="component.componentName == 'ManyGoodsList'">
                <diy-many-goods-list :component="component" :global="data.global" :index="index" :pullDownRefreshCount="props.pullDownRefreshCount" :scrollBool="diyGroup.componentsScrollBool.ManyGoodsList" />
            </template>
            <template v-if="component.componentName == 'ShopExchangeGoods'">
                <diy-shop-exchange-goods :component="component" :global="data.global" :index="index" :pullDownRefreshCount="props.pullDownRefreshCount" :scrollBool="diyGroup.componentsScrollBool.ShopExchangeGoods" />
            </template>
            <template v-if="component.componentName == 'ShopExchangeInfo'">
                <diy-shop-exchange-info :component="component" :global="data.global" :index="index" :pullDownRefreshCount="props.pullDownRefreshCount" :scrollBool="diyGroup.componentsScrollBool.ShopExchangeInfo" />
            </template>
            <template v-if="component.componentName == 'ShopMemberInfo'">
                <diy-shop-member-info :component="component" :global="data.global" :index="index" :pullDownRefreshCount="props.pullDownRefreshCount" :scrollBool="diyGroup.componentsScrollBool.ShopMemberInfo" />
            </template>
            <template v-if="component.componentName == 'ShopOrderInfo'">
                <diy-shop-order-info :component="component" :global="data.global" :index="index" :pullDownRefreshCount="props.pullDownRefreshCount" :scrollBool="diyGroup.componentsScrollBool.ShopOrderInfo" />
            </template>
            <template v-if="component.componentName == 'ShopSearch'">
                <diy-shop-search :component="component" :global="data.global" :index="index" :pullDownRefreshCount="props.pullDownRefreshCount" :scrollBool="diyGroup.componentsScrollBool.ShopSearch" />
            </template>
            </view>
        </view>
        <template v-if="diyStore.mode == '' && data.global.bottomTabBarSwitch">
            <view class="pt-[20rpx]"></view>
            <tabbar />
        </template>
    </view>
</template>
<script lang="ts" setup>
   import diyGoodsCoupon from '@/addon/shop/components/diy/goods-coupon/index.vue';
   import diyGoodsList from '@/addon/shop/components/diy/goods-list/index.vue';
   import diyManyGoodsList from '@/addon/shop/components/diy/many-goods-list/index.vue';
   import diyShopExchangeGoods from '@/addon/shop/components/diy/shop-exchange-goods/index.vue';
   import diyShopExchangeInfo from '@/addon/shop/components/diy/shop-exchange-info/index.vue';
   import diyShopMemberInfo from '@/addon/shop/components/diy/shop-member-info/index.vue';
   import diyShopOrderInfo from '@/addon/shop/components/diy/shop-order-info/index.vue';
   import diyShopSearch from '@/addon/shop/components/diy/shop-search/index.vue';
   import topTabbar from '@/components/top-tabbar/top-tabbar.vue'
   import useDiyStore from '@/app/stores/diy';
   import { useDiyGroup } from './useDiyGroup';
   import { ref } from 'vue';

   const props = defineProps(['data','pullDownRefreshCount']);
   const topTabbarRef = ref(null);
   const diyStore = useDiyStore();
   const diyGroup = useDiyGroup({
       ...props,
       getFormRef() {
           return {
               topTabbarRef: topTabbarRef.value
           }
       }
   });
   const data = ref(diyGroup.data);

   // 监听页面加载完成
   diyGroup.onMounted();

   // 监听滚动事件
   diyGroup.onPageScroll();
   defineExpose({
       refresh: diyGroup.refresh
   })
</script>
<style lang="scss" scoped>
   @import './index.scss';
</style>
