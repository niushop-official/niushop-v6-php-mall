<template>
    <view :style="themeColor()">
        <view v-if="agreement && agreement.content" class="p-[30rpx]">
            <u-parse :content="agreement.content" :tagStyle="{img: 'vertical-align: top;'}"></u-parse>
        </view>
        <view v-else class="h-[100vh] w-full flex items-center justify-center">
            <u-empty :icon="img('static/resource/images/empty.png')" text="暂无协议" />
        </view>
    </view>
</template>

<script lang="ts" setup>
import { ref } from 'vue'
import { onLoad } from '@dcloudio/uni-app'
import { getAgreementInfo } from '@/app/api/system'
import { img} from '@/utils/common'

const agreement = ref(null)

onLoad((option)=> {
    getAgreementInfo(option.key).then((res: AnyObject) => {
        agreement.value = res.data
        uni.setNavigationBarTitle({
            title: res.data.title
        })
    })
})
</script>

<style lang="scss">
</style>
