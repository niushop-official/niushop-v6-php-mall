<template>
	<view :style="warpCss">
		<view :style="maskLayer"></view>
		<view class="diy-active-cube relative">
			<view class="active-cube-wrap p-[20rpx]">
				<view class="flex items-center" v-if="diyComponent.titleStyle.value == 'style-1'">
					<view class="mr-[20rpx] font-bold text-[32rpx]" :style="{color: diyComponent.titleColor }" @click="diyStore.toRedirect(diyComponent.textLink)">{{ diyComponent.text }}</view>
					<view v-if="diyComponent.subTitle.text" @click="diyStore.toRedirect(diyComponent.subTitle.link)" class="text-center text-[24rpx] rounded-[40rpx] rounded-tl-none py-[10rpx] px-[20rpx]" :style="{'color': diyComponent.subTitle.textColor, background: 'linear-gradient(90deg, '+ diyComponent.subTitle.startColor + ', '+ diyComponent.subTitle.endColor + ')'}">{{ diyComponent.subTitle.text }}</view>
				</view>
				<view class="flex items-center" v-if="diyComponent.titleStyle.value == 'style-2'">
					<view class="mr-[20rpx] font-bold text-[32rpx]" :style="{color: diyComponent.titleColor }" @click="diyStore.toRedirect(diyComponent.textLink)">{{ diyComponent.text }}</view>
					<view v-if="diyComponent.subTitle.text" @click="diyStore.toRedirect(diyComponent.subTitle.link)" class="text-center text-[24rpx] rounded-[10rpx] py-[10rpx] px-[20rpx]" :style="{'color': diyComponent.subTitle.textColor, background: 'linear-gradient(90deg, '+ diyComponent.subTitle.startColor + ', '+ diyComponent.subTitle.endColor + ')'}">{{ diyComponent.subTitle.text }}</view>
				</view>
				<view class="flex items-center" v-if="diyComponent.titleStyle.value == 'style-3'">
					<view class="mr-[20rpx] font-bold text-[32rpx]" @click="diyStore.toRedirect(diyComponent.textLink)" :style="{color: diyComponent.titleColor }">{{ diyComponent.text }}</view>
					<view class="relative h-[44rpx]" @click="diyStore.toRedirect(diyComponent.subTitle.link)">
						<view v-if="diyComponent.subTitle.text" class="text-center text-[24rpx] py-[10rpx] pl-[16rpx] pr-[36rpx]" :style="{'color': diyComponent.subTitle.textColor, background: 'linear-gradient(90deg, '+ diyComponent.subTitle.startColor + ', '+ diyComponent.subTitle.endColor + ')'}">{{ diyComponent.subTitle.text }}</view>
						<image class="absolute left-0 top-0 bottom-0 !w-[16rpx] !h-[44rpx]" :src="img('static/resource/images/diy/active_cube/block_style2_1.png')" mode="scaleToFill"/>
						<image class="absolute right-0 top-0 bottom-0 !w-[28rpx] !h-[44rpx]" :src="img('static/resource/images/diy/active_cube/block_style2_2.png')" mode="scaleToFill"/>
					</view>
				</view>
				<view class="flex items-center justify-between" v-if="diyComponent.titleStyle.value == 'style-4'">
					<view class="font-bold text-[32rpx]" @click="diyStore.toRedirect(diyComponent.textLink)" :style="{color: diyComponent.titleColor }">{{ diyComponent.text }}</view>
					<view v-if="diyComponent.subTitle.text" @click="diyStore.toRedirect(diyComponent.subTitle.link)" class="text-[24rpx] rounded-[40rpx] py-[10rpx] pl-[16rpx] pr-[12rpx] flex items-center" :style="{'color': diyComponent.subTitle.textColor, background: 'linear-gradient(90deg, '+ diyComponent.subTitle.startColor + ', '+ diyComponent.subTitle.endColor + ')'}">
						<text>{{ diyComponent.subTitle.text }}</text>
						<text class="nc-iconfont nc-icon-youV6xx !text-[26rpx]"></text>
					</view>
				</view>

				<view class="bd flex flex-wrap justify-between">
					<template v-for="item in diyComponent.list" :key="item.id">
						<view v-if="diyComponent.blockStyle.value == 'style-1'" @click="diyStore.toRedirect(item.link)" class="item flex justify-between p-[20rpx] bg-white mt-[20rpx] rounded-[16rpx]" :style="{ backgroundColor : diyComponent.elementBgColor }">
							<view class="flex-1 flex items-baseline flex-col">
								<view class="text--[28rpx] pb-[20rpx]" :style="{ fontWeight : diyComponent.blockStyle.fontWeight }">{{ item.title.text }}</view>
								<view class="text--[24rpx] text-gray-500 pb-[20rpx]">{{ item.subTitle.text }}</view>
								<view class="link relative text-[24rpx] leading-[40rpx] flex items-center text-white rounded-r-[20rpx] h-[40rpx] pl-[26rpx] pr-[10rpx]" :style="btnCss(item.moreTitle)" v-if="item.moreTitle.text">
									<text class="mr-[8rpx]">{{ item.moreTitle.text }}</text>
									<text class="iconfont iconjiantou-you-cuxiantiao-fill !text-[20rpx] text-[#fff]"></text>
									<image class="absolute left-0 top-0 bottom-0 !w-[28rpx]" :src="img('static/resource/images/diy/active_cube/block_style1_1.png')" mode="scaleToFill"/>
								</view>
							</view>
							<view class="img-box ml-[10rpx] w-[130rpx]" v-if="item.imageUrl">
								<image :src="img(item.imageUrl)" mode="aspectFit" />
							</view>
							<view class="img-box ml-[10rpx] flex items-center justify-center w-[130rpx] bg-[#f3f4f6]" v-else>
								<u-icon name="photo" color="#999" size="50"></u-icon>
							</view>
						</view>
						<view v-if="diyComponent.blockStyle.value == 'style-2'" @click="diyStore.toRedirect(item.link)" class="item flex justify-between p-[20rpx] bg-white mt-[20rpx] rounded-[16rpx]" :style="{ backgroundColor : diyComponent.elementBgColor }">
							<view class="flex-1 flex items-baseline flex-col">
								<view class="text--[28rpx] pb-[20rpx]" :style="{ fontWeight : diyComponent.blockStyle.fontWeight }">{{ item.title.text }}</view>
								<view class="text--[24rpx] text-gray-500 pb-[20rpx]">{{ item.subTitle.text }}</view>
								<view class="link relative text-[24rpx] leading-[40rpx] flex items-center text-white rounded-[20rpx] h-[40rpx] pl-[20rpx] pr-[10rpx]" :style="btnCss(item.moreTitle)" v-if="item.moreTitle.text">
									<text class="mr-[8rpx]">{{ item.moreTitle.text }}</text>
									<text class="iconfont iconjiantou-you-cuxiantiao-fill !text-[20rpx] text-[#fff]"></text>
								</view>
							</view>
							<view class="img-box ml-[10rpx] w-[130rpx]" v-if="item.imageUrl">
								<image :src="img(item.imageUrl)" mode="aspectFit" />
							</view>
							<view class="img-box ml-[10rpx] flex items-center justify-center w-[130rpx] bg-[#f3f4f6]" v-else>
								<u-icon name="photo" color="#999" size="50"></u-icon>
							</view>
						</view>
					</template>
				</view>
				<scroll-view scroll-x="true" class="whitespace-nowrap" :id="'warpStyle3-'+diyComponent.id" v-if="diyComponent.blockStyle.value == 'style-3'">
					<view v-for="(item,index) in diyComponent.list" :key="item.id" class="inline-flex">
						<view :id="'item'+index+diyComponent.id" @click="diyStore.toRedirect(item.link)" class="flex flex-col items-center justify-between p-[10rpx] bg-white mt-[20rpx] w-[157rpx] h-[200rpx] rounded-[10rpx] box-border" :style="itemStyle3"  :class="{'!mr-[0rpx]': index+1 === diyComponent.list.length}">
							<view class="w-[141rpx] h-[141rpx]" v-if="item.imageUrl">
								<image class="w-[141rpx] h-[141rpx]" :src="img(item.imageUrl)" mode="aspectFit" />
							</view>
							<view class="w-[141rpx] h-[141rpx] relative flex-shrink-0" v-else>
								<view class="absolute left-0 top-0 flex items-center justify-center w-[141rpx] h-[141rpx] bg-[#f3f4f6]">
									<u-icon name="photo" color="#999" size="50"></u-icon>
								</view>
							</view>
							<view class="my-[10rpx] text-[26rpx]" :style="{ color : item.title.textColor,fontWeight : diyComponent.blockStyle.fontWeight }">{{ item.title.text }}</view>
						</view>
					</view>
				</scroll-view>
				
				<scroll-view scroll-x="true" class="whitespace-nowrap" :id="'warpStyle4-'+diyComponent.id" v-if="diyComponent.blockStyle.value == 'style-4'">
					<view v-for="(item,index) in diyComponent.list" :key="item.id" class="inline-flex">
						<view :id="'item'+index+diyComponent.id" @click="diyStore.toRedirect(item.link)" class="flex flex-col items-center justify-between p-[4rpx] bg-[#F93D02] mt-[20rpx] rounded-[20rpx] box-border" :class="{'!mr-[0rpx]': index+1 === diyComponent.list.length}" :style="'background :linear-gradient('+ item.listFrame.startColor +','+ item.listFrame.endColor + ');'+itemStyle4">
							<view class="w-[149rpx] h-[149rpx] box-border px-[18rpx] pt-[16rpx] pb-[6rpx] bg-[#fff] flex flex-col items-center rounded-[16rpx]">
								<view class="w-[112rpx] h-[102rpx]" v-if="item.imageUrl">
									<image class="w-[112rpx] h-[102rpx]" :src="img(item.imageUrl)" mode="aspectFit" />
								</view>
								<view class="w-[112rpx] h-[102rpx] relative flex-shrink-0" v-else>
									<view class="absolute left-0 top-0 flex items-center justify-center w-[112rpx] h-[102rpx] bg-[#f3f4f6]">
										<u-icon name="photo" color="#999" size="50"></u-icon>
									</view>
								</view>
								<view class="relative -mt-[10rpx] text-[22rpx] bg-[#F3DAC5] text-[#ED6E00] rounded-[16rpx] px-[12rpx] leading-[36rpx]" :style="{ color : item.subTitle.textColor, background : 'linear-gradient(to right,'+ item.subTitle.startColor +','+ item.subTitle.endColor + ')' }">{{ item.subTitle.text }}</view>
							</view>
							<view class="mt-[10rpx] mb-[6rpx] text-[28rpx] text-[#fff]" :style="{ fontWeight : diyComponent.blockStyle.fontWeight }">{{ item.title.text }}</view>
						</view>
					</view>
				</scroll-view>
			</view>
		</view>
	</view>
</template>

<script setup lang="ts">
	// 活动魔方组件
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
	//商品样式三
	const itemStyle3 = ref('');
	const setItemStyle3 = ()=>{
		// #ifdef  MP-WEIXIN
			uni.createSelectorQuery().in(instance).select('#warpStyle3-'+diyComponent.value.id).boundingClientRect((res:any) => {
				uni.createSelectorQuery().in(instance).select('#item0'+diyComponent.value.id).boundingClientRect((data:any) => {
					itemStyle3.value = `margin-right:${(res.width - data.width*4)/3}px;`
				}).exec()
			}).exec()
		// #endif
		// #ifdef  H5
			itemStyle3.value= 'margin-right:14rpx;'
		// #endif
	};
	//商品样式四
	const itemStyle4 = ref('');
	const setItemStyle4 = ()=>{
		// #ifdef  MP-WEIXIN
			uni.createSelectorQuery().in(instance).select('#warpStyle4-'+diyComponent.value.id).boundingClientRect((res:any) => {
				uni.createSelectorQuery().in(instance).select('#item0'+diyComponent.value.id).boundingClientRect((data:any) => {
					itemStyle4.value = `margin-right:${(res.width - data.width*4)/3}px;`

				}).exec()
			}).exec()
		// #endif
		// #ifdef  H5
			itemStyle4.value= 'margin-right:14rpx;'
		// #endif
	};
    const btnCss = (item:any) => {
        var style = '';
        style += `background:linear-gradient(90deg,${item.startColor},${item.endColor});`;
        return style;
    };

	watch(
		() => props.pullDownRefreshCount,
		(newValue, oldValue) => {
			// 处理下拉刷新业务
		}
	)

    onMounted(() => {
        refresh();
        // 装修模式下刷新
        if (diyStore.mode == 'decorate') {
            watch(
                () => diyComponent.value,
                (newValue, oldValue) => {
                    if (newValue && newValue.componentName == 'ActiveCube') {
                        refresh();
                    }
                }
            )
        }
    });

    const instance = getCurrentInstance();
    const height = ref(0)

    const refresh = ()=> {
        nextTick(() => {
            const query = uni.createSelectorQuery().in(instance);
            query.select('.diy-active-cube').boundingClientRect((data: any) => {
                height.value = data.height;
            }).exec();

			if(diyComponent.value.blockStyle.value == 'style-3') setItemStyle3()
			if(diyComponent.value.blockStyle.value == 'style-4') setItemStyle4()
        })
    }
</script>

<style lang="scss" scoped>
	.active-cube-wrap {
		.bd {
			.item {
				width: calc(46% - 20rpx);

				image {
					width: 100%;
					height: 100%;
				}

			}
		}
	}
</style>
