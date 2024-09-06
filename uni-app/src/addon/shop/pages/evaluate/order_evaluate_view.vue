<template>
     <view class="bg-[#f8f8f8] min-h-screen" :style="themeColor()">
		 <mescroll-body ref="mescrollRef" top="90rpx" @init="mescrollInit" :down="{ use: false }" @up="getShopOrderFn">
			<view class="px-[24rpx] py-[20rpx]" v-if="info.length">
               <template v-for="(item, index)  in info" :key="index">
                    <view class="bg-white py-[20rpx] px-[24rpx] mb-[20rpx] rounded-[16rpx]">
                         <view class="flex mb-[20rpx]" @click="redirect({ url: '/addon/shop/pages/goods/detail', param: { goods_id: item.goods_id } })">
                              <u--image class="rounded-[10rpx] overflow-hidden" width="200rpx" height="200rpx" :src="img(item.order_goods.goods_image_thumb_mid ? item.order_goods.goods_image_thumb_mid : '')" model="aspectFill">
                                   <template #error>
                                        <u-icon name="photo" color="#999" size="50"></u-icon>
                                   </template>
                              </u--image>
                              <view class="flex-1 flex flex-wrap ml-[20rpx]">
                                   <view>
                                        <view class="text-[26rpx] h-[80rpx] leading-[40rpx]  multi-hidden mb-[10rpx]">{{ item.order_goods.goods_name }}</view>
                                        <view class="w-[404rpx] mt-[12rpx] truncate text-[#888] text-[24rpx] leading-[32rpx]">{{ item.order_goods.sku_name }}</view>
                                   </view>
                                   <view class="mt-auto flex  self-end justify-between w-[100%]">
                                        <view class="flex flex-col">
                                             <text class="text-[28rpx] text-[var(--price-text-color)] price-font">￥{{ item.order_goods.price }}</text>
                                        </view>
                                        <text class="text--[24rpx] text-[#666]">x{{ item.order_goods.num }}</text>
                                   </view>
                              </view>
                         </view>
                         <view class="pt-[20rpx] flex items-center border-0 border-t-[2rpx] border-solid border-[#ebebec]">
                              <u-rate :count="5" v-model="item.scores" active-color="var(--primary-color)" :size="'32rpx'" readonly></u-rate>
                              <text class="ml-[20rpx] text-[26rpx] text-[#888]">{{ item.scores === 1 ? '差评' : item.scores === 2 || item.scores === 3 ? '中评' : '好评' }}</text>
                         </view>
                         <view class=" text-[28rpx] text-[#888] my-[20rpx] break-all">{{ item.content }}</view>
                         <template v-if="item.image_mid.length === 1">
                            <u--image class="rounded-[8rpx] overflow-hidden mt-[10rpx]" width="420rpx" height="420rpx" :src="img(item.image_mid[0])" model="aspectFill" @click="imgListPreview(item.images[0])">
                                <template #error>
                                    <u-icon name="photo" color="#999" size="50"></u-icon>
                                </template>
                            </u--image>
                        </template>
                        <template v-if="item.image_mid.length === 2">
                            <view class="flex justify-between mt-[10rpx]">
                                <u--image class="rounded-[8rpx] overflow-hidden" width="322rpx" height="322rpx" :src="img(item.image_mid[0])" model="aspectFill" @click="imgListPreview(item.images[0])">
                                    <template #error>
                                        <u-icon name="photo" color="#999" size="50"></u-icon>
                                    </template>
                                </u--image>
                                <u--image class="rounded-[8rpx] overflow-hidden" width="322rpx" height="322rpx" :src="img(item.image_mid[1])" model="aspectFill" @click="imgListPreview(item.images[1])">
                                    <template #error>
                                        <u-icon name="photo" color="#999" size="50"></u-icon>
                                    </template>
                                </u--image>
                            </view>
                        </template>
                        <template v-if="item.image_mid.length === 3">
                            <view class="flex justify-between mt-[10rpx]">
                                <u--image class="rounded-[8rpx] overflow-hidden" width="430rpx" height="430rpx" :src="img(item.image_mid[0])" model="aspectFill" @click="imgListPreview(item.images[0])">
                                    <template #error>
                                        <u-icon name="photo" color="#999" size="50"></u-icon>
                                    </template>
                                </u--image>
                                <view>
                                    <u--image class="rounded-[8rpx] overflow-hidden mb-[20rpx]" width="205rpx" height="205rpx" :src="img(item.image_mid[1])" model="aspectFill" @click="imgListPreview(item.images[1])">
                                        <template #error>
                                            <u-icon name="photo" color="#999" size="50"></u-icon>
                                        </template>
                                    </u--image>
                                    <u--image class="rounded-[8rpx] overflow-hidden" width="205rpx" height="205rpx" :src="img(item.image_mid[2])" model="aspectFill" @click="imgListPreview(item.images[2])">
                                        <template #error>
                                            <u-icon name="photo" color="#999" size="50"></u-icon>
                                        </template>
                                    </u--image>
                                </view>
                            </view>
                        </template>
                        <template v-if="item.image_mid.length === 4">
                            <view class="flex flex-wrap mt-[10rpx]">
                                <u--image class="rounded-[8rpx] overflow-hidden mr-[15rpx] mb-[15rpx]" width="215rpx" height="215rpx" :src="img(item.image_mid[0])" model="aspectFill" @click="imgListPreview(item.images[0])">
                                    <template #error>
                                        <u-icon name="photo" color="#999" size="50"></u-icon>
                                    </template>
                                </u--image>
                                <u--image class="rounded-[8rpx] overflow-hidden mr-[15rpx] mb-[15rpx]" width="215rpx" height="215rpx" :src="img(item.image_mid[1])" model="aspectFill" @click="imgListPreview(item.images[1])">
                                    <template #error>
                                        <u-icon name="photo" color="#999" size="50"></u-icon>
                                    </template>
                                </u--image>
                                <u--image class="rounded-[8rpx] overflow-hidden mr-[15rpx]" width="215rpx" height="215rpx" :src="img(item.image_mid[2])" model="aspectFill" @click="imgListPreview(item.images[2])">
                                    <template #error>
                                        <u-icon name="photo" color="#999" size="50"></u-icon>
                                    </template>
                                </u--image>
                                <u--image class="rounded-[8rpx] overflow-hidden mr-[15rpx]" width="215rpx" height="215rpx" :src="img(item.image_mid[3])" model="aspectFill" @click="imgListPreview(item.images[3])">
                                    <template #error>
                                        <u-icon name="photo" color="#999" size="50"></u-icon>
                                    </template>
                                </u--image>
                            </view>
                        </template>
                        <template v-if="item.image_mid.length > 4">
                            <view class="flex flex-wrap mt-[10rpx]">
                                <u--image v-for="(imageItem, imageIndex) in item.images"
                                    :class="['rounded-[8rpx] overflow-hidden mb-[10rpx]', (imageIndex + 1) % 3 === 0 ? '' : 'mr-[10rpx]']"
                                    width="211rpx" height="211rpx" :src="img(imageItem)" model="aspectFill"
                                    @click="imgListPreview(imageItem)">
                                    <template #error>
                                        <u-icon name="photo" color="#999" size="50"></u-icon>
                                    </template>
                                </u--image>
                            </view>
                        </template>
                        <view v-if="item.explain_first !=''" class="text-[26rpx] !text-[#666] mt-[20rpx] pt-[20rpx] border-0 border-t-[2rpx] border-solid border-[#ebebec] w-[100%] overflow-clip leading-[1.2] break-all">
                            <text class="text-[var(--primary-color)]">商家回复：</text>
							<text>{{ item.explain_first }}</text>
                        </view>
                    </view>
               </template>
			</view>
			<view class="h-[100vh] w-[100vw] px-[30rpx] pt-[20rpx] box-border" v-if="!info.length && !loading">
				<view class=" bg-[#fff] rounded-[16rpx] flex items-center justify-center noData">
					<mescroll-empty :option="{tip : '暂无评价'}"></mescroll-empty>
				</view>
			</view>
			<u-loading-page bg-color="rgb(248,248,248)" :loading="loading" loadingText="" fontSize="16" color="#303133"></u-loading-page>
		  </mescroll-body>
     </view>
</template>
<script lang="ts" setup>
import { ref } from 'vue';
import { getOrderEvaluate } from '@/addon/shop/api/evaluate';
import { onLoad,onUnload,onPageScroll,onReachBottom } from '@dcloudio/uni-app';
import { img,redirect } from '@/utils/common';
import useMescroll from '@/components/mescroll/hooks/useMescroll.js';
import MescrollEmpty from '@/components/mescroll/mescroll-empty/mescroll-empty.vue';

const { mescrollInit, downCallback, getMescroll } = useMescroll(onPageScroll, onReachBottom);
const info = ref<Array<any>>([])
const loading = ref(false)
onLoad((option:any) => {
     getOrderEvaluateFn(option.order_id)
})
const getOrderEvaluateFn = (id) => {
     loading.value = true
     getOrderEvaluate(id).then(res => {
          info.value = res.data
          loading.value = false
     }).catch(()=>{
          loading.value = false
     })
}

//预览图片
const imgListPreview = (item:any) => {
    if (item === '') return false
    var urlList = []
    urlList.push(img(item))  //push中的参数为 :src="item.img_url" 中的图片地址
    uni.previewImage({
        indicator: "number",
        loop: true,
        urls: urlList
    })
}

// 关闭预览图片
onUnload(()=>{
    // #ifdef  H5 || APP
    uni.closePreviewImage()
    // #endif
})
</script>
<style>
	.noData{
		height: calc(100vh - 40rpx - constant(safe-area-inset-bottom));
		height: calc(100vh - 40rpx - env(safe-area-inset-bottom));
	 }
</style>
