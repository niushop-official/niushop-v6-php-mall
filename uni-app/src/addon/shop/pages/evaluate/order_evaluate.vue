<template>
     <view class="bg-[#f8f8f8] min-h-screen" :style="themeColor()">
          <view class="px-[24rpx] py-[20rpx]">
               <template v-for="(item, index)  in info" :key="index">
                    <view class="bg-white py-[20rpx] px-[24rpx] mb-[20rpx] rounded-[16rpx]">
                         <view class="flex mb-[20rpx]">
                              <u--image class="rounded-[10rpx] overflow-hidden" width="200rpx" height="200rpx" :src="img(item.goods_image_thumb_small ? item.goods_image_thumb_small : '')" model="aspectFill">
                                   <template #error>
                                        <u-icon name="photo" color="#999" size="50"></u-icon>
                                   </template>
                              </u--image>
                              <view class="flex-1 flex flex-wrap ml-[20rpx]">
                                   <view>
                                        <view class="text-[26rpx] h-[80rpx] leading-[40rpx]  multi-hidden mb-[10rpx]">{{ item.goods_name }}</view>
                                        <view class="w-[404rpx] mt-[12rpx] truncate text-[#888] text-[24rpx] leading-[32rpx]">{{ item.sku_name }}</view>
                                   </view>
                                   <view class="mt-auto flex  self-end justify-between w-[100%]">
                                        <view class="flex flex-col">
                                             <text class="text-[28rpx] text-[var(--price-text-color)] price-font">￥{{ item.price }}</text>
                                        </view>
                                        <text class="text--[24rpx] text-[#666]">x{{ item.num }}</text>
                                   </view>
                              </view>
                         </view>
                         <view class="pt-[20rpx] flex items-center border-0 border-t-[2rpx] border-solid border-[#ebebec]">
                              <u-rate :count="5" v-model="form[index].scores" active-color="var(--primary-color)" :size="'32rpx'"></u-rate>
                              <text class="ml-[20rpx] text-[26rpx] text-[#888]">{{ form[index].scores === 1 ? '差评' : form[index].scores === 2 || form[index].scores === 3 ? '中评' : '好评' }}</text>
                         </view>
                         <textarea class="!text-[26rpx] mt-[20rpx] w-[100%]" v-model.trim="form[index].content" placeholder="请在此处输入你的评价" maxlength="200" />
                         <view class="text-right text-[24rpx] text-[#888]">{{ form[index].content.length >= 200 ? 200 : form[index].content.length }}/200</view>
                         <upload-img class="mt-[20rpx]" v-model="form[index].images" :max-count="9" :multiple="true"/>
                    </view>
               </template>
          </view>
          <u-tabbar :fixed="true" :placeholder="true" :safeAreaInsetBottom="true" zIndex="9999">
               <view class="flex items-center px-[30rpx] py-[10rpx] box-border  justify-between w-[100%]">
                    <view class="flex items-center" @click="selectCheck">
                         <text class="iconfont text-color text-[34rpx] mr-[12rpx]" :class="{'iconxuanze1 text-[var(--primary-color)]' : is_anonymous === '1' ,'nc-iconfont nc-icon-yuanquanV6xx':is_anonymous !== '1'}"></text>
                         <text class="font-500 text-[28rpx]" :class="{'text-[var(--primary-color)]' :is_anonymous === '1', 'text-[#676767]':is_anonymous !== '1'}">匿名</text>
                    </view>
                    <button class="!w-[444rpx] !h-[80rpx] text-[32rpx] !m-0 leading-[80rpx] rounded-full text-white bg-[var(--primary-color)] remove-border" @click="submit">提交</button>
               </view>
          </u-tabbar>
          <u-loading-page bg-color="rgb(248,248,248)" :loading="loading" loadingText="" fontSize="16" color="#303133"></u-loading-page>
		  <!-- #ifdef MP-WEIXIN -->
		  <!-- 小程序隐私协议 -->
		  <wx-privacy-popup ref="wxPrivacyPopupRef"></wx-privacy-popup>
		  <!-- #endif -->
     </view>
</template>
<script lang="ts" setup>
import { ref } from 'vue';
import { getShopOrderDetail } from '@/addon/shop/api/order';
import { setOrderEvaluate } from '@/addon/shop/api/evaluate';
import { onLoad } from '@dcloudio/uni-app';
import { img, redirect } from '@/utils/common';
import uploadImg from '@/addon/shop/pages/evaluate/components/upload-img'

const info = ref<Array<any>>([])
const form = ref<Array<any>>([])
const is_anonymous = ref('2')
const loading = ref(false)
const order_id = ref('')
onLoad((option) => {
     order_id.value = option.order_id
     getShopOrderDetailFn(order_id.value)
})
const getShopOrderDetailFn = (id) => {
     loading.value = true
     getShopOrderDetail(id).then(res => {
          if (res.data.is_evaluate) {
               toLink(order_id.value)
               return false
          }
          info.value = res.data.order_goods
          res.data.order_goods.forEach((el) => {
               form.value.push({
                    order_id: el.order_id,
                    order_goods_id: el.order_goods_id,
                    goods_id: el.goods_id,
                    content: '',
                    images: [],
                    scores: 5
               })
          })
          loading.value = false
     }).catch(() => {
          loading.value = false
     })
}
const selectCheck = () => {
     is_anonymous.value = is_anonymous.value === '1' ? '2' : '1'
}
const submit = () => {
     if (form.value.some(el => el.content == '')) {
          uni.showToast({ title: '请输入你的评价', icon: 'none' })
          return false
     }
     for (let i = 0; i < form.value.length; i++) {
          let item = form.value[i];
          if (item.content.length > 200) {
               item.content = item.content.substr(0, 200);
          }
     }
     form.value.forEach(v => v.is_anonymous = is_anonymous.value)
     loading.value = true
     setOrderEvaluate({ evaluate_array: form.value }).then(res => {
          loading.value = false
          toLink(order_id.value)
     }).catch(() => {
          loading.value = false
     })
}
const toLink = (order_id: any) => {
     redirect({ url: '/addon/shop/pages/evaluate/order_evaluate_view', param: { order_id }, mode: 'redirectTo' })
}
</script>
<style lang="scss">
     .remove-border {
          &::after {
               border: none;
          }
     }
</style>