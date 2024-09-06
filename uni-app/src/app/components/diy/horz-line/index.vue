<template>
	<view class="horz-line-wrap">
		<view v-if="diyStore.mode == 'decorate'" class="h-[30rpx]"></view>
		<view :style="warpCss"></view>
		<view v-if="diyStore.mode == 'decorate'" class="h-[30rpx]"></view>
	</view>
</template>

<script setup lang="ts">
	// 辅助线
	import { computed, watch } from 'vue';
	import useDiyStore from '@/app/stores/diy';

	const props = defineProps(['component', 'index', 'pullDownRefreshCount']);

	const diyStore = useDiyStore();

	const diyComponent = computed(() => {
		if (diyStore.mode == 'decorate') {
			return diyStore.value[props.index];
		} else {
			return props.component;
		}
	})

	const warpCss = computed(() => {
        var style = '';
        style += 'border-top:' + (diyComponent.value.borderWidth * 2) + 'rpx ' + diyComponent.value.borderStyle + ' ' + diyComponent.value.borderColor + ';';
        return style;
    })

	watch(
		() => props.pullDownRefreshCount,
		(newValue, oldValue) => {
			// 处理下拉刷新业务
		}
	)
</script>

<style></style>
