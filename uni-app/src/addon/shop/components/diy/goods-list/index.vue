<template>
	<x-skeleton :type="skeleton.type" :loading="skeleton.loading" :config="skeleton.config">
		<view :style="warpCss">
			<view :style="maskLayer"></view>
			<div class="diy-shop-goods-list relative flex flex-wrap justify-between">
				<block v-if="diyComponent.style == 'style-1'">
					<view class="bg-white w-full flex p-[20rpx] mx-[20rpx] rounded-[8rpx] overflow-hidden" :class="{ 'mt-[20rpx]': index > -10,'mb-[20rpx]': (index+1) == goodsList.length }" :style="itemCss" v-for="(item,index) in goodsList" :key="item.goods_id" @click="toLink(item)">
						<u--image radius="10rpx" width="190rpx" height="190rpx" :src="img(item.goods_cover_thumb_mid || '')" model="aspectFill">
							<template #error>
								<image class="w-[190rpx] h-[190rpx] rounded-[10rpx] overflow-hidden" :src="img('static/resource/images/diy/shop_default.jpg')" mode="aspectFill"></image>
							</template>
						</u--image>
						<view class="flex-1 flex flex-col ml-[20rpx] py-[6rpx]">
							<view class="text-[28rpx] leading-[40rpx] text-[#303133] multi-hidden mb-[10rpx]" :style="{ color : diyComponent.goodsNameStyle.color, fontWeight : diyComponent.goodsNameStyle.fontWeight }">{{item.goods_name}}</view>
							<view class="mt-auto flex justify-between items-center items-baseline">
								<view class="font-bold text-[var(--price-text-color)] price-font flex items-baseline" :style="{ color : diyComponent.priceStyle.mainColor }">
									<text class="text-[26rpx] font-500">￥</text>
									<text class="text-[36rpx] font-500">{{ parseFloat(goodsPrice(item)).toFixed(2).split('.')[0] }}</text>
									<text class="text-[24rpx] font-500">.{{ parseFloat(goodsPrice(item)).toFixed(2).split('.')[1] }}</text>
									<image class="h-[24rpx] ml-[6rpx]" v-if="priceType(item) == 'member_price'" :src="img('addon/shop/VIP.png')" mode="heightFix" />
									<image class="h-[24rpx] ml-[6rpx]" v-if="priceType(item) == 'discount_price'" :src="img('addon/shop/discount.png')" mode="heightFix" />
								</view>
								<text class="text-[22rpx] text-[#999]" :style="{ color : diyComponent.saleStyle.color }">已售{{item.sale_num}}{{item.unit || '件'}}</text>
							</view>
						</view>
					</view>
				</block>
				<block v-if="diyComponent.style == 'style-2'">
					<view class="flex flex-col bg-[#fff] box-border rounded-[12rpx] overflow-hidden" :class="{'mt-[24rpx]': index > 1}" :style="itemCss" v-for="(item,index) in goodsList" :key="item.goods_id" @click="toLink(item)">
						<u--image :width="style2Width" :height="style2Width" :src="img(item.goods_cover_thumb_mid || '')" model="aspectFill">
							<template #error>
								<image :style="{'width': style2Width,'height': style2Width}" :src="img('static/resource/images/diy/shop_default.jpg')" mode="aspectFill"></image>
							</template>
						</u--image>
						<view class="px-[16rpx] flex-1 pt-[16rpx] pb-[20rpx] flex flex-col justify-between">
							<view class="text-[#303133] leading-[40rpx] text-[28rpx] multi-hidden" :style="{ color : diyComponent.goodsNameStyle.color, fontWeight : diyComponent.goodsNameStyle.fontWeight }">{{item.goods_name}}</view>
							<view class="flex justify-between flex-wrap items-baseline mt-[16rpx]" >
								<view class="text-[var(--price-text-color)] price-font flex items-baseline" :style="{ color : diyComponent.priceStyle.mainColor }">
									<text class="text-[26rpx] font-500">￥</text>
									<text class="text-[36rpx] font-500">{{ parseFloat(goodsPrice(item)).toFixed(2).split('.')[0] }}</text>
									<text class="text-[24rpx] font-500">.{{ parseFloat(goodsPrice(item)).toFixed(2).split('.')[1] }}</text>
									<image class="h-[24rpx] ml-[6rpx]" v-if="priceType(item) == 'member_price'" :src="img('addon/shop/VIP.png')" mode="heightFix" />
									<image class="h-[24rpx] ml-[6rpx]" v-if="priceType(item) == 'discount_price'" :src="img('addon/shop/discount.png')" mode="heightFix" />
								</view>
								<text class="text-[22rpx] text-[#999]" :style="{ color : diyComponent.saleStyle.color }">已售{{item.sale_num}}{{item.unit || '件'}}</text>
							</view>
						</view>
					</view>
				</block>
				<block v-if="diyComponent.style == 'style-3'">
					<view :style="style3Css">
						<scroll-view :id="'warpStyle3-'+diyComponent.id" class="whitespace-nowrap h-[284rpx]" :scroll-x="true">
							<view :id="'item'+index+diyComponent.id" class="w-[214rpx] rounded-[10rpx] inline-block bg-[#fff] box-border overflow-hidden" :class="{'!mr-[0rpx]' : index == (goodsList.length-1)}" :style="itemCss+itemStyle3" v-for="(item,index) in goodsList" :key="item.goods_id" @click="toLink(item)">
								<u--image width="214rpx" height="160rpx" :src="img(item.goods_cover_thumb_mid || '')" model="aspectFill">
									<template #error>
										<image class="w-[214rpx] h-[160rpx]" :src="img('static/resource/images/diy/shop_default.jpg')" mode="aspectFill"></image>
									</template>
								</u--image>
								<view class="px-[10rpx] pt-[16rpx] pb-[10rpx]">
									<view class="text-[26rpx] text-[#303133] truncate" :style="{ color : diyComponent.goodsNameStyle.color, fontWeight : diyComponent.goodsNameStyle.fontWeight }">{{item.goods_name}}</view>
									<view class="text-[var(--price-text-color)] py-[10rpx] flex-wrap font-bold price-font flex items-baseline leading-[1]" :style="{ color : diyComponent.priceStyle.mainColor }">
										<view class="truncate">
											<text class="text-[26rpx] font-500">￥</text>
											<text class="text-[36rpx] font-500">{{ parseFloat(goodsPrice(item)).toFixed(2).split('.')[0] }}</text>
											<text class="text-[24rpx] font-500">.{{ parseFloat(goodsPrice(item)).toFixed(2).split('.')[1] }}</text>
										</view>
										<image class="h-[24rpx] ml-[6rpx]" v-if="priceType(item) == 'member_price'" :src="img('addon/shop/VIP.png')" mode="heightFix" />
										<image class="h-[24rpx] ml-[6rpx]" v-if="priceType(item) == 'discount_price'" :src="img('addon/shop/discount.png')" mode="heightFix" />
									</view>
								</view>
							</view>
						</scroll-view>
					</view>
					
				</block>
			</div>
		</view>
	</x-skeleton>
</template>

<script setup lang="ts">
	// 商品列表
    import { ref,reactive,computed, watch, onMounted, nextTick,getCurrentInstance } from 'vue';
	import { redirect, img, getToken } from '@/utils/common';
	import useDiyStore from '@/app/stores/diy';
	import { getGoodsComponents } from '@/addon/shop/api/goods';

	const props = defineProps(['component', 'index', 'pullDownRefreshCount','value']);
	const diyStore = useDiyStore();

    const skeleton = reactive({
        type: '',
        loading: diyStore.mode == 'decorate' ? false : true,
        config: {}
    })

	const goodsList = ref<Array<any>>([]);

	const diyComponent = computed(() => {
        if(props.value) {
            return props.value;
        }else if (diyStore.mode == 'decorate') {
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

    const itemCss = computed(() => {
        var style = '';
        if (diyComponent.value.elementBgColor) style += 'background-color:' + diyComponent.value.elementBgColor + ';';
        if (diyComponent.value.topElementRounded) style += 'border-top-left-radius:' + diyComponent.value.topElementRounded * 2 + 'rpx;';
        if (diyComponent.value.topElementRounded) style += 'border-top-right-radius:' + diyComponent.value.topElementRounded * 2 + 'rpx;';
        if (diyComponent.value.bottomElementRounded) style += 'border-bottom-left-radius:' + diyComponent.value.bottomElementRounded * 2 + 'rpx;';
        if (diyComponent.value.bottomElementRounded) style += 'border-bottom-right-radius:' + diyComponent.value.bottomElementRounded * 2 + 'rpx;';
		if(diyComponent.value.style == 'style-2'){
			if(diyComponent.value.margin && diyComponent.value.margin.both) style += 'width: calc((100vw - ' + (diyComponent.value.margin.both*4) + 'rpx - 20rpx) / 2)'
			else style += 'width: calc((100vw - 20rpx) / 2 )'
		}
        return style;
    })
	
	const style2Width = computed(() => {
		var style = '';
		if(diyComponent.value.margin && diyComponent.value.margin.both) style += 'calc((100vw - ' + (diyComponent.value.margin.both*4) + 'rpx - 20rpx) / 2)'
		else style += 'calc((100vw - 20rpx) / 2 )'
		return style;
	})
	
	const style3Css = computed(() => {
        var style = '';
        style += 'padding:0 20rpx;';
        if (diyComponent.value.margin && diyComponent.value.margin.both){style += 'width: calc(100vw - ' + ((diyComponent.value.margin.both * 4) + 40) + 'rpx);'}
		else{style += 'box-sizing: border-box; width: 100vw;';}
        return style;
    })

	//商品样式三
	const itemStyle3 = ref('');
	const setItemStyle3 = ()=>{
		// #ifdef  MP-WEIXIN
			uni.createSelectorQuery().in(instance).select('#warpStyle3-'+diyComponent.value.id).boundingClientRect((res:any) => {
				uni.createSelectorQuery().in(instance).select('#item0'+diyComponent.value.id).boundingClientRect((data:any) => {
					itemStyle3.value = `margin-right:${(res.width - data.width*3)/2}px;`
				}).exec()
			}).exec()
		// #endif
		// #ifdef  H5
			itemStyle3.value= 'margin-right:14rpx;'
		// #endif
	}

	watch(
		() => props.pullDownRefreshCount,
		(newValue, oldValue) => {
			// 处理下拉刷新业务
		}
	)

	const getGoodsListFn = () => {
        let data = {
            num: (diyComponent.value.source == 'all' || diyComponent.value.source == 'category') ? diyComponent.value.num : '',
            goods_ids: diyComponent.value.source == 'custom' ? diyComponent.value.goods_ids : '',
            goods_category: diyComponent.value.source == 'category' ? diyComponent.value.goods_category : '',
            order: diyComponent.value.sortWay
        }
        getGoodsComponents(data).then((res) => {
            goodsList.value = res.data;
            skeleton.loading = false;
            if(diyComponent.value.componentBgUrl) {
                setTimeout(() => {
                    const query = uni.createSelectorQuery().in(instance);
                    query.select('.diy-shop-goods-list').boundingClientRect((data: any) => {
                        height.value = data.height;
                    }).exec();
                }, 1000)
            }
			nextTick(()=>{
				setTimeout(()=>{
					if(diyComponent.value.style == 'style-3') setItemStyle3()
				},500)
			})
        });
    }

	const initSkeleton = ()=> {
        if (diyComponent.value.style == 'style-1') {

            // 单列 风格
            skeleton.type = 'list'
            skeleton.type = 'list'
            skeleton.config = {
                textRows: 2
            };
        } else if (diyComponent.value.style == 'style-2') {

            // 两列 风格
            skeleton.type = 'waterfall'
            skeleton.config = {
                headHeight: '320rpx',
                gridRows: 1,
                textRows: 2,
                textWidth: ['100%', '80%']
            };
        } else if (diyComponent.value.style == 'style-3') {

            // 横向滑动 风格
            skeleton.type = 'waterfall'
            skeleton.config = {
                gridRows: 1,
                gridColumns: 3,
                headHeight: '200rpx',
                textRows: 2,
                textWidth: ['100%', '80%']
            };
        }
    }

    const instance = getCurrentInstance();
    const height = ref(0)

	onMounted(() => {
		refresh();
        // 装修模式下刷新
        if (diyStore.mode == 'decorate') {
            watch(
                () => diyComponent.value,
                (newValue, oldValue) => {
                    if (newValue && newValue.componentName == 'GoodsList') {
                        nextTick(() => {
                            const query = uni.createSelectorQuery().in(instance);
                            query.select('.diy-shop-goods-list').boundingClientRect((data: any) => {
                                height.value = data.height;
                            }).exec();
                        })
                    }
                }
            )
        }else{
            watch(
                () => diyComponent.value,
                (newValue, oldValue) => {
                    refresh();
                },
                {deep: true}
            )
        }
	});

	const refresh = () => {
		// 装修模式下设置默认图
		if (diyStore.mode == 'decorate') {
			let obj = {
				goods_cover_thumb_mid: "",
				goods_name: "商品名称",
				sale_num: "100",
				unit: "件",
				goodsSku:{price:100}
			};
			goodsList.value.push(obj);
			goodsList.value.push(obj);
			nextTick(()=>{
				if(diyComponent.value.style == 'style-3') setItemStyle3()
			})
		}else{
            initSkeleton();
			getGoodsListFn();
		}
		
		
	}

	const toLink = (data: any) => {
		redirect({ url: '/addon/shop/pages/goods/detail', param: { goods_id: data.goods_id } })
	}
	
	// 价格类型 
	const priceType = (data:any) =>{
		let type = "";
		if(data.is_discount && data.goodsSku.sale_price != data.goodsSku.price){
			type = 'discount_price'// 折扣
		}else if(data.member_discount && getToken() && data.goodsSku.member_price != data.goodsSku.price) {
			type = 'member_price' // 会员价
		}else{ 
			type = ""
		}
		return type;
	}

	// 商品价格
	const goodsPrice = (data:any) => {
        let price = "0.00";
        if (data.is_discount && data.goodsSku.sale_price != data.goodsSku.price) {
            price = data.goodsSku.sale_price ? data.goodsSku.sale_price : data.goodsSku.price // 折扣价
        } else if (data.member_discount && getToken() && data.goodsSku.member_price != data.goodsSku.price) {
            price = data.goodsSku.member_price ? data.goodsSku.member_price : data.goodsSku.price // 会员价
        } else {
            price = data.goodsSku.price
        }
        return price;
    }
	
</script>

<style lang="scss" scoped>
</style>