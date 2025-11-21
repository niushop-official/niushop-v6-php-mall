<template>
    <view class="diy-group" id="componentList">
        <top-tabbar :scrollBool="diyGroup.componentsScrollBool.TopTabbar" v-if="data.global && Object.keys(data.global).length && data.global.topStatusBar && data.global.topStatusBar.isShow" ref="topTabbarRef" :data="data.global" />
        <pop-ads v-if="data.global && Object.keys(data.global).length && data.global.popWindow && data.global.popWindow.show" ref="popAbsRef" :data="data.global" />
        <template v-for="(component, index) in data.value" :key="component.id">
           <view v-show="component.componentIsShow"
               @click="diyStore.changeCurrentIndex(index, component)"
               :class="diyGroup.getComponentClass(index,component)" :style="component.pageStyle">
                <view class="relative" :style="{ marginTop : component.margin.top < 0 ? (component.margin.top * 2) + 'rpx' : '0', marginBottom : component.margin.bottom < 0 ? (component.margin.bottom * 2) + 'rpx' : '0' }">
                   <!-- 装修模式下，设置负上边距后超出的内容，禁止选中设置 -->
                    <view v-if="diyGroup.isShowPlaceHolder(index,component)" class="absolute w-full z-1" :style="{ height : (component.margin.top * 2 * -1) + 'rpx' }" @click.stop="diyGroup.placeholderEvent"></view>
                <template v-if="component.componentName == 'ActiveCube'">
                   <diy-active-cube ref="diyActiveCubeRef" :component="component" :global="data.global" :index="index" :scrollBool="diyGroup.componentsScrollBool.ActiveCube" @update:componentIsShow="component.componentIsShow = $event" />
                </template>
                <template v-if="component.componentName == 'CarouselSearch'">
                   <diy-carousel-search ref="diyCarouselSearchRef" :component="component" :global="data.global" :index="index" :scrollBool="diyGroup.componentsScrollBool.CarouselSearch" @update:componentIsShow="component.componentIsShow = $event" />
                </template>
                <template v-if="component.componentName == 'FloatBtn'">
                   <diy-float-btn ref="diyFloatBtnRef" :component="component" :global="data.global" :index="index" :scrollBool="diyGroup.componentsScrollBool.FloatBtn" @update:componentIsShow="component.componentIsShow = $event" />
                </template>
                <template v-if="component.componentName == 'FormAddress'">
                   <diy-form-address ref="diyFormAddressRef" :component="component" :global="data.global" :index="index" :scrollBool="diyGroup.componentsScrollBool.FormAddress" @update:componentIsShow="component.componentIsShow = $event" />
                </template>
                <template v-if="component.componentName == 'FormCheckbox'">
                   <diy-form-checkbox ref="diyFormCheckboxRef" :component="component" :global="data.global" :index="index" :scrollBool="diyGroup.componentsScrollBool.FormCheckbox" @update:componentIsShow="component.componentIsShow = $event" />
                </template>
                <template v-if="component.componentName == 'FormDate'">
                   <diy-form-date ref="diyFormDateRef" :component="component" :global="data.global" :index="index" :scrollBool="diyGroup.componentsScrollBool.FormDate" @update:componentIsShow="component.componentIsShow = $event" />
                </template>
                <template v-if="component.componentName == 'FormDateScope'">
                   <diy-form-date-scope ref="diyFormDateScopeRef" :component="component" :global="data.global" :index="index" :scrollBool="diyGroup.componentsScrollBool.FormDateScope" @update:componentIsShow="component.componentIsShow = $event" />
                </template>
                <template v-if="component.componentName == 'FormEmail'">
                   <diy-form-email ref="diyFormEmailRef" :component="component" :global="data.global" :index="index" :scrollBool="diyGroup.componentsScrollBool.FormEmail" @update:componentIsShow="component.componentIsShow = $event" />
                </template>
                <template v-if="component.componentName == 'FormFile'">
                   <diy-form-file ref="diyFormFileRef" :component="component" :global="data.global" :index="index" :scrollBool="diyGroup.componentsScrollBool.FormFile" @update:componentIsShow="component.componentIsShow = $event" />
                </template>
                <template v-if="component.componentName == 'FormIdentity'">
                   <diy-form-identity ref="diyFormIdentityRef" :component="component" :global="data.global" :index="index" :scrollBool="diyGroup.componentsScrollBool.FormIdentity" @update:componentIsShow="component.componentIsShow = $event" />
                </template>
                <template v-if="component.componentName == 'FormIdentityPrivacy'">
                   <diy-form-identity-privacy ref="diyFormIdentityPrivacyRef" :component="component" :global="data.global" :index="index" :scrollBool="diyGroup.componentsScrollBool.FormIdentityPrivacy" @update:componentIsShow="component.componentIsShow = $event" />
                </template>
                <template v-if="component.componentName == 'FormImage'">
                   <diy-form-image ref="diyFormImageRef" :component="component" :global="data.global" :index="index" :scrollBool="diyGroup.componentsScrollBool.FormImage" @update:componentIsShow="component.componentIsShow = $event" />
                </template>
                <template v-if="component.componentName == 'FormInput'">
                   <diy-form-input ref="diyFormInputRef" :component="component" :global="data.global" :index="index" :scrollBool="diyGroup.componentsScrollBool.FormInput" @update:componentIsShow="component.componentIsShow = $event" />
                </template>
                <template v-if="component.componentName == 'FormLocation'">
                   <diy-form-location ref="diyFormLocationRef" :component="component" :global="data.global" :index="index" :scrollBool="diyGroup.componentsScrollBool.FormLocation" @update:componentIsShow="component.componentIsShow = $event" />
                </template>
                <template v-if="component.componentName == 'FormMobile'">
                   <diy-form-mobile ref="diyFormMobileRef" :component="component" :global="data.global" :index="index" :scrollBool="diyGroup.componentsScrollBool.FormMobile" @update:componentIsShow="component.componentIsShow = $event" />
                </template>
                <template v-if="component.componentName == 'FormNumber'">
                   <diy-form-number ref="diyFormNumberRef" :component="component" :global="data.global" :index="index" :scrollBool="diyGroup.componentsScrollBool.FormNumber" @update:componentIsShow="component.componentIsShow = $event" />
                </template>
                <template v-if="component.componentName == 'FormPrivacy'">
                   <diy-form-privacy ref="diyFormPrivacyRef" :component="component" :global="data.global" :index="index" :scrollBool="diyGroup.componentsScrollBool.FormPrivacy" @update:componentIsShow="component.componentIsShow = $event" />
                </template>
                <template v-if="component.componentName == 'FormPrivacyPop'">
                   <diy-form-privacy-pop ref="diyFormPrivacyPopRef" :component="component" :global="data.global" :index="index" :scrollBool="diyGroup.componentsScrollBool.FormPrivacyPop" @update:componentIsShow="component.componentIsShow = $event" />
                </template>
                <template v-if="component.componentName == 'FormRadio'">
                   <diy-form-radio ref="diyFormRadioRef" :component="component" :global="data.global" :index="index" :scrollBool="diyGroup.componentsScrollBool.FormRadio" @update:componentIsShow="component.componentIsShow = $event" />
                </template>
                <template v-if="component.componentName == 'FormSubmit'">
                   <diy-form-submit ref="diyFormSubmitRef" :component="component" :global="data.global" :index="index" :scrollBool="diyGroup.componentsScrollBool.FormSubmit" @update:componentIsShow="component.componentIsShow = $event" />
                </template>
                <template v-if="component.componentName == 'FormTable'">
                   <diy-form-table ref="diyFormTableRef" :component="component" :global="data.global" :index="index" :scrollBool="diyGroup.componentsScrollBool.FormTable" @update:componentIsShow="component.componentIsShow = $event" />
                </template>
                <template v-if="component.componentName == 'FormTextarea'">
                   <diy-form-textarea ref="diyFormTextareaRef" :component="component" :global="data.global" :index="index" :scrollBool="diyGroup.componentsScrollBool.FormTextarea" @update:componentIsShow="component.componentIsShow = $event" />
                </template>
                <template v-if="component.componentName == 'FormTime'">
                   <diy-form-time ref="diyFormTimeRef" :component="component" :global="data.global" :index="index" :scrollBool="diyGroup.componentsScrollBool.FormTime" @update:componentIsShow="component.componentIsShow = $event" />
                </template>
                <template v-if="component.componentName == 'FormTimeScope'">
                   <diy-form-time-scope ref="diyFormTimeScopeRef" :component="component" :global="data.global" :index="index" :scrollBool="diyGroup.componentsScrollBool.FormTimeScope" @update:componentIsShow="component.componentIsShow = $event" />
                </template>
                <template v-if="component.componentName == 'FormVideo'">
                   <diy-form-video ref="diyFormVideoRef" :component="component" :global="data.global" :index="index" :scrollBool="diyGroup.componentsScrollBool.FormVideo" @update:componentIsShow="component.componentIsShow = $event" />
                </template>
                <template v-if="component.componentName == 'FormWechatName'">
                   <diy-form-wechat-name ref="diyFormWechatNameRef" :component="component" :global="data.global" :index="index" :scrollBool="diyGroup.componentsScrollBool.FormWechatName" @update:componentIsShow="component.componentIsShow = $event" />
                </template>
                <template v-if="component.componentName == 'GraphicNav'">
                   <diy-graphic-nav ref="diyGraphicNavRef" :component="component" :global="data.global" :index="index" :scrollBool="diyGroup.componentsScrollBool.GraphicNav" @update:componentIsShow="component.componentIsShow = $event" />
                </template>
                <template v-if="component.componentName == 'HorzBlank'">
                   <diy-horz-blank ref="diyHorzBlankRef" :component="component" :global="data.global" :index="index" :scrollBool="diyGroup.componentsScrollBool.HorzBlank" @update:componentIsShow="component.componentIsShow = $event" />
                </template>
                <template v-if="component.componentName == 'HorzLine'">
                   <diy-horz-line ref="diyHorzLineRef" :component="component" :global="data.global" :index="index" :scrollBool="diyGroup.componentsScrollBool.HorzLine" @update:componentIsShow="component.componentIsShow = $event" />
                </template>
                <template v-if="component.componentName == 'HotArea'">
                   <diy-hot-area ref="diyHotAreaRef" :component="component" :global="data.global" :index="index" :scrollBool="diyGroup.componentsScrollBool.HotArea" @update:componentIsShow="component.componentIsShow = $event" />
                </template>
                <template v-if="component.componentName == 'ImageAds'">
                   <diy-image-ads ref="diyImageAdsRef" :component="component" :global="data.global" :index="index" :scrollBool="diyGroup.componentsScrollBool.ImageAds" @update:componentIsShow="component.componentIsShow = $event" />
                </template>
                <template v-if="component.componentName == 'MemberInfo'">
                   <diy-member-info ref="diyMemberInfoRef" :component="component" :global="data.global" :index="index" :scrollBool="diyGroup.componentsScrollBool.MemberInfo" @update:componentIsShow="component.componentIsShow = $event" />
                </template>
                <template v-if="component.componentName == 'MemberLevel'">
                   <diy-member-level ref="diyMemberLevelRef" :component="component" :global="data.global" :index="index" :scrollBool="diyGroup.componentsScrollBool.MemberLevel" @update:componentIsShow="component.componentIsShow = $event" />
                </template>
                <template v-if="component.componentName == 'Notice'">
                   <diy-notice ref="diyNoticeRef" :component="component" :global="data.global" :index="index" :scrollBool="diyGroup.componentsScrollBool.Notice" @update:componentIsShow="component.componentIsShow = $event" />
                </template>
                <template v-if="component.componentName == 'PictureShow'">
                   <diy-picture-show ref="diyPictureShowRef" :component="component" :global="data.global" :index="index" :scrollBool="diyGroup.componentsScrollBool.PictureShow" @update:componentIsShow="component.componentIsShow = $event" />
                </template>
                <template v-if="component.componentName == 'RichText'">
                   <diy-rich-text ref="diyRichTextRef" :component="component" :global="data.global" :index="index" :scrollBool="diyGroup.componentsScrollBool.RichText" @update:componentIsShow="component.componentIsShow = $event" />
                </template>
                <template v-if="component.componentName == 'RubikCube'">
                   <diy-rubik-cube ref="diyRubikCubeRef" :component="component" :global="data.global" :index="index" :scrollBool="diyGroup.componentsScrollBool.RubikCube" @update:componentIsShow="component.componentIsShow = $event" />
                </template>
                <template v-if="component.componentName == 'Text'">
                   <diy-text ref="diyTextRef" :component="component" :global="data.global" :index="index" :scrollBool="diyGroup.componentsScrollBool.Text" @update:componentIsShow="component.componentIsShow = $event" />
                </template>
                </view>
            </view>
        </template>
        <template v-if="diyStore.mode == '' && data.global && diyGroup.showCopyright.value && data.global.copyright && data.global.copyright.isShow">
           <copy-right :textColor="data.global.copyright.textColor" />
        </template>

        <template v-if="diyStore.mode == '' && data.global && data.global.bottomTabBar && data.global.bottomTabBar.isShow">
            <view class="pt-[20rpx]"></view>
            <tabbar :addon="data.global.bottomTabBar.designNav.key" />
        </template>
    </view>
</template>
<script lang="ts" setup>
   import topTabbar from '@/components/top-tabbar/top-tabbar.vue'
   import popAds from '@/components/pop-ads/pop-ads.vue'
   import useDiyStore from '@/app/stores/diy';
   import { useDiyGroup } from './useDiyGroup';
   import { ref,getCurrentInstance } from 'vue';

   const props = defineProps(['data']);
   const instance: any = getCurrentInstance();
   const getFormRef = () => {
       return {
           componentRefs: instance.refs
       }
   }
   const diyStore = useDiyStore();
   const diyGroup = useDiyGroup({
       ...props,
       getFormRef
   });
   const data = ref(diyGroup.data);

   // 监听页面加载完成
   diyGroup.onMounted();

   // 监听滚动事件
   diyGroup.onPageScroll();
   defineExpose({
       refresh: diyGroup.refresh,
       getFormRef
   })
</script>
<style lang="scss" scoped>
   @import './index.scss';
</style>
