<template>
	<view :style="warpCss">
		<view :style="maskLayer"></view>
		<view class="diy-image-ads">
			<view v-if="diyComponent.list.length == 1" class="leading-0 overflow-hidden" :style="swiperWarpCss">
				<view @click="diyStore.toRedirect(diyComponent.list[0].link)">
					<image v-if="diyComponent.list[0].imageUrl" :src="img(diyComponent.list[0].imageUrl)" :style="{height: imgHeight}" mode="heightFix" class="!w-full" :show-menu-by-longpress="true"/>
					<image v-else :src="img('static/resource/images/diy/figure.png')" :style="{height: imgHeight}" mode="heightFix" class="!w-full" :show-menu-by-longpress="true"/>
				</view>
			</view>

			<swiper v-else class="swiper" :style="{ height: imgHeight }" autoplay="true" circular="true" @change="swiperChange">
				<swiper-item class="swiper-item" v-for="(item) in diyComponent.list" :key="item.id" :style="swiperWarpCss">
					<view @click="diyStore.toRedirect(item.link)">
						<view class="item" :style="{height: imgHeight}">
							<image v-if="item.imageUrl" :src="img(item.imageUrl)" mode="scaleToFill" class="w-full h-full" :show-menu-by-longpress="true"/>
							<image v-else :src="img('static/resource/images/diy/figure.png')" mode="scaleToFill" class="w-full h-full" :show-menu-by-longpress="true"/>
						</view>
					</view>
				</swiper-item>
			</swiper>
		</view>
	</view>
</template>

<script lang="ts" setup>
	// 图片广告
    import { ref,computed, watch, onMounted, nextTick,getCurrentInstance } from 'vue';
	import useDiyStore from '@/app/stores/diy';
    import { img } from '@/utils/common';

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
        style += 'position:relative;';
        if(diyComponent.value.componentStartBgColor) {
            if (diyComponent.value.componentStartBgColor && diyComponent.value.componentEndBgColor) style += `background:linear-gradient(${diyComponent.value.componentGradientAngle},${diyComponent.value.componentStartBgColor},${diyComponent.value.componentEndBgColor});`;
            else style += 'background-color:' + diyComponent.value.componentStartBgColor + ';';
        }

        if(diyComponent.value.componentBgUrl) {
            style += `background-image:url('${ img(diyComponent.value.componentBgUrl) }');`;
            style += 'background-size: cover;background-repeat: no-repeat;';
        }
		return style;
	})

	const swiperWarpCss = computed(() => {
		var style = '';
		if (diyComponent.value.topRounded) style += 'border-top-left-radius:' + diyComponent.value.topRounded * 2 + 'rpx;';
		if (diyComponent.value.topRounded) style += 'border-top-right-radius:' + diyComponent.value.topRounded * 2 + 'rpx;';
		if (diyComponent.value.bottomRounded) style += 'border-bottom-left-radius:' + diyComponent.value.bottomRounded * 2 + 'rpx;';
		if (diyComponent.value.bottomRounded) style += 'border-bottom-right-radius:' + diyComponent.value.bottomRounded * 2 + 'rpx;';
		return style;
	})

    // 背景图加遮罩层
    const maskLayer = computed(()=>{
        var style = '';
        if(diyComponent.value.componentBgUrl) {
            style += 'position:absolute;top:0;width:100%;';
            style += `background: rgba(0,0,0,${diyComponent.value.componentBgAlpha / 10});`;
            style += `height:${height.value}px;`;

            if (diyComponent.value.topRounded) style += 'border-top-left-radius:' + diyComponent.value.topRounded * 2 + 'rpx;';
            if (diyComponent.value.topRounded) style += 'border-top-right-radius:' + diyComponent.value.topRounded * 2 + 'rpx;';
            if (diyComponent.value.bottomRounded) style += 'border-bottom-left-radius:' + diyComponent.value.bottomRounded * 2 + 'rpx;';
            if (diyComponent.value.bottomRounded) style += 'border-bottom-right-radius:' + diyComponent.value.bottomRounded * 2 + 'rpx;';
        }

        return style;
    });

	watch(
		() => props.pullDownRefreshCount,
		(newValue, oldValue) => {
			// 处理下拉刷新业务
		}
	)

	const imgHeight = computed(() => {
		return (diyComponent.value.imageHeight * 2) + 'rpx';
	})

	const swiperIndex = ref(0);

	const swiperChange = e => {
		swiperIndex.value = e.detail.current;
	};

	onMounted(() => {
		refresh();
		// 装修模式下刷新
		if (diyStore.mode == 'decorate') {
			watch(
				() => diyComponent.value,
				(newValue, oldValue) => {
					if (newValue && newValue.componentName == 'ImageAds') {
						refresh();
					}
				}
			)
		}
	});

    const instance = getCurrentInstance();
    const height = ref(0)

	const refresh = () => {
		// 装修模式下设置默认图
		if (diyStore.mode == 'decorate') {
			diyComponent.value.list.forEach((item : any) => {
				if (item.imageUrl == '') {
					item.imgWidth = 690;
					item.imgHeight = 330;
				}
			});
		}
        nextTick(() => {
            const query = uni.createSelectorQuery().in(instance);
            query.select('.diy-image-ads').boundingClientRect((data: any) => {
                height.value = data.height;
            }).exec();
        })
	}
</script>

<style lang="scss" scoped>
</style>
