<template>
    <view class="bg-[#f8f8f8] min-h-screen overflow-hidden" :style="themeColor()">
        <view class="fixed left-0 top-0 right-0 z-10">
            <scroll-view scroll-x="true" class="scroll-Y box-border px-[24rpx] bg-white">
                <view class="flex whitespace-nowrap justify-around">
                    <view :class="['text-[27rpx] leading-[90rpx]', { 'class-select': evaluateStatus === item.status }]" @click="statusClickFn(item.status, item.value)" v-for="(item, index) in statusList">{{ item.name }}
                    </view>
                </view>
            </scroll-view>
        </view>

        <mescroll-body ref="mescrollRef" top="90rpx" @init="mescrollInit" :down="{ use: false }" @up="getEvaluateListFn">
            <view class="py-[20rpx] px-[24rpx]">
                <template v-for="(item, index) in list">
                    <view class=" py-[20rpx] px-[24rpx] bg-[#fff] mb-[20rpx] rounded-[12rpx]">
                        <view class="flex items-center  justify-between">
                            <view class="flex items-center">
                                <u-avatar :src="img(item.member_head)" :default-url="img('static/resource/images/default_headimg.png')" :size="'50rpx'" leftIcon="none"></u-avatar>
                                <text class="text-[28rpx] ml-[10rpx]">{{ item.member_name }}</text>
                            </view>
                            <text class="text--[26rpx] text-[#737373]">{{ item.create_time ? item.create_time.slice(0, 10) : '' }}</text>
                        </view>

                        <view class="pt-[20rpx] flex items-center">
                            <u-rate :count="5" v-model="item.scores" active-color="var(--primary-color)" :size="'32rpx'" readonly></u-rate>
                            <text class="ml-[20rpx] text-[28rpx] text-[#888]">{{ item.scores === 1 ? '差评' : item.scores === 2 || item.scores === 3 ? '中评' : '好评' }}</text>
                        </view>
                        <view class="text-[26rpx] break-all leading-[1.2] text-[#666] my-[20rpx] overflow-clip">{{ item.content }}</view>
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
                                <u--image v-for="(imageItem, imageIndex) in item.image_mid"
                                    :class="['rounded-[8rpx] overflow-hidden mb-[10rpx]', (imageIndex + 1) % 3 === 0 ? '' : 'mr-[10rpx]']"
                                    width="211rpx" height="211rpx" :src="img(imageItem)" model="aspectFill"
                                    @click="imgListPreview(item.images[imageIndex])">
                                    <template #error>
                                        <u-icon name="photo" color="#999" size="50"></u-icon>
                                    </template>
                                </u--image>
                            </view>
                        </template>
                        <view v-if="item.explain_first !=''" class="text-[26rpx]  !text-[#666] mt-[20rpx] pt-[20rpx] border-0 border-t-[2rpx] border-solid border-[#ebebec] w-[100%] overflow-clip leading-[1.2]  break-all">
                            <text class=" text-[var(--primary-color)]">商家回复：</text>{{ item.explain_first }}
                        </view>
                    </view>
                </template>
                <view class="bg-[#fff] noData rounded-[12rpx] flex items-center justify-center" v-if="!list.length && loading">
                    <mescroll-empty :option="{tip : '暂无评价'}"></mescroll-empty>
                </view>
            </view>
            
        </mescroll-body>
        <pay ref="payRef"></pay>
    </view>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { img } from '@/utils/common'
import { getEvaluateList } from '@/addon/shop/api/evaluate'
import MescrollBody from '@/components/mescroll/mescroll-body/mescroll-body.vue'
import MescrollEmpty from '@/components/mescroll/mescroll-empty/mescroll-empty.vue'
import useMescroll from '@/components/mescroll/hooks/useMescroll.js'
import { onLoad, onUnload,onPageScroll, onReachBottom } from '@dcloudio/uni-app'

const { mescrollInit, downCallback, getMescroll } = useMescroll(onPageScroll, onReachBottom)
const list = ref<Array<Object>>([]);
const loading = ref<boolean>(false);
const statusList = <Array<Object>>([
    { status: 1, name: '全部', value: [] },
    { status: 2, name: '好评', value: [4, 5] },
    { status: 3, name: '中评', value: [2, 3] },
    { status: 4, name: '差评', value: [1] },
]);
const evaluateStatus = ref(1);
const evaluateValue = ref([]);
const goodsId = ref('')
onLoad((option) => {
    goodsId.value = option.goods_id || ''
})

const statusClickFn = (status: any, value: any) => {
    evaluateStatus.value = status;
    evaluateValue.value = value
    list.value = []; //如果是第一页需手动制空列表
    getMescroll().resetUpScroll();
};

const getEvaluateListFn = (mescroll:any) => {
    loading.value = false;
    let data: object = {
        page: mescroll.num,
        limit: mescroll.size,
        goods_id: goodsId.value,
        scores: evaluateValue.value
    };

    getEvaluateList(data).then((res:any) => {
        let newArr = (res.data.data as Array<Object>);
        //设置列表数据
        if (mescroll.num == 1) {
            list.value = []; //如果是第一页需手动制空列表
        }
        list.value = list.value.concat(newArr);
		
        mescroll.endSuccess(newArr.length);
        loading.value = true;
    }).catch(() => {
        loading.value = true;
        mescroll.endErr(); // 请求失败, 结束加载
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

//关闭预览图片
onUnload(()=>{
	// #ifdef  H5 || APP
	uni.closePreviewImage()
	// #endif
})
</script>

<style lang="scss" scoped>
.class-select {
    position: relative;
    font-weight: bold;

    &::before {
        content: "";
        position: absolute;
        bottom: 0;
        height: 6rpx;
        background-color: var(--primary-color);
        width: 90%;
        left: 50%;
        transform: translateX(-50%);
    }
}
.noData{
	height: calc(100vh - 130rpx - constant(safe-area-inset-bottom));
	height: calc(100vh - 130rpx - env(safe-area-inset-bottom));
 }
</style>
