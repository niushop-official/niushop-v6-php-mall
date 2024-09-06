<template>
	<view class="member-record-detail" :style="themeColor()">
		<block v-if="!loading">
			<view class="money-wrap">
				<text>-{{ cashOutInfo.apply_money }}</text>
				<text>{{ cashOutInfo.status_name }}</text>
			</view>
			<!-- 状态0待审核1.待转账2已转账 -1拒绝' -->
			<view class="item">
				<view class="line-wrap">
					<text class="label w-[200rpx]">{{t('cashOutNo')}}</text>
					<text class="value">{{ cashOutInfo.cash_out_no }}</text>
				</view>
				<view class="line-wrap">
					<text class="label w-[200rpx]">{{t('serviceMoney')}}</text>
					<text class="value">￥{{ cashOutInfo.service_money }}</text>
				</view>
				<view class="line-wrap">
					<text class="label w-[200rpx]">{{t('createTime')}}</text>
					<text class="value">{{ cashOutInfo.create_time }}</text>
				</view>
				<view class="line-wrap" v-if="cashOutInfo.status && cashOutInfo.audit_time">
					<text class="label w-[200rpx]">{{t('auditTime')}}</text>
					<text class="value">{{ cashOutInfo.audit_time  }}</text>
				</view>
				<view class="line-wrap" v-if="cashOutInfo.transfer_bank">
					<text class="label w-[200rpx]">{{t('transferBank')}}</text>
					<text class="value truncate">{{ cashOutInfo.transfer_bank }}</text>
				</view>
				<view class="line-wrap">
					<text class="label w-[200rpx]">{{t('transferAccount')}}</text>
					<text class="value truncate">{{ cashOutInfo.transfer_type == 'wechatpay' ? '微信' : cashOutInfo.transfer_account }}</text>
				</view>
				<view class="line-wrap" v-if="cashOutInfo.status == -1 && cashOutInfo.refuse_reason">
					<text class="label w-[200rpx]">{{t('refuseReason')}}</text>
					<text class="value truncate">{{ cashOutInfo.refuse_reason }}</text>
				</view>
				<view class="line-wrap" v-if="cashOutInfo.status == 2">
					<text class="label w-[200rpx]">{{t('transferTypeName')}}</text>
					<text class="value truncate">{{ cashOutInfo.transfer_type_name }}</text>
				</view>
				<view class="line-wrap" v-if="cashOutInfo.status == 2 && cashOutInfo.transfer_time">
					<text class="label w-[200rpx]">{{t('transferTime')}}</text>
					<text class="value truncate">{{ cashOutInfo.transfer_time }}</text>
				</view>
			</view>
		</block>
		<u-loading-page bg-color="rgb(248,248,248)" :loading="loading" loadingText="" fontSize="16" color="#303133"></u-loading-page>
	</view>
</template>

<script lang="ts" setup>
import { reactive, ref } from 'vue'
import { onLoad } from '@dcloudio/uni-app'
import { t } from '@/locale'
import { redirect, img } from '@/utils/common';
import { getCashOutDetail } from '@/app/api/member';

const cashOutInfo = ref({});
const loading = ref<boolean>(true);
onLoad((option: any) => {
	let id = option.id || "";
	getCashoutAccountListFn(id)
})

const getCashoutAccountListFn = (id) => {
	loading.value = true;

	getCashOutDetail(id).then((res) => {
		cashOutInfo.value = res.data;
		loading.value = false;
	}).catch(() => {
		loading.value = false;
	})
}
</script>

<style lang="scss">
@import '@/styles/member_record_detail.scss';
</style>
