<template>
	<view class="flex items-center justify-center min-h-[100vh]" :style="themeColor()">
		<view class="contact-wrap pt-[5%]">
			<image :src="img('static/resource/images/member/contact_service.png')" mode="widthFix" />
			<view class="mt-[40rpx] text-[28rpx]">
				欢迎您联系我们，提供您宝贵的意见！
			</view>
			<nc-contact
					:send-message-title="sendMessageTitle"
					:send-message-path="sendMessagePath"
					:send-message-img="sendMessageImg">
				<button type="primary" class="btn-wrap primary-btn-bg">{{ t('customerService') }}</button>
			</nc-contact>
		</view>
	</view>
</template>

<script setup lang="ts">
    import { t } from '@/locale'
    import { img } from '@/utils/common';
    import { ref } from 'vue';

    const sendMessageTitle = ref('')
    const sendMessagePath = ref('')
    const sendMessageImg = ref('')

    sendMessageImg.value = img('static/resource/images/member/contact_service.png')
</script>

<style lang="scss" scoped>
	.contact-wrap {
		display: flex;
		flex-direction: column;
		align-items: center;
		justify-content: center;
		image {
			width: 674rpx;
		}

		.btn-wrap {
			margin-top: 120rpx;
			width: 530rpx;
			height: 88rpx;
			font-size: 32rpx;
			border-radius: 50rpx;
			line-height: 88rpx;
		}
	}
</style>
