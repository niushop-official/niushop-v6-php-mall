<template>
	<view class="bg-[#F4F6F8] min-h-screen">
		<view class="mescroll-box" v-if="tabsData.length">
			<view v-if="config.search.control" class="search-box z-10 bg-[#fff] fixed top-0 left-0 right-0 h-[106rpx] box-border">
				<input class="search-ipt pl-[32rpx] text-[24rpx]" :class="{'pr-[106rpx]':searchName,'pr-[32rpx]':!searchName}" type="text" v-model="searchName" :placeholder="config.search.title"  @confirm="searchNameFn">
				<view class="flex items-center h-[70rpx] absolute right-[56rpx] top-[18rpx]  z-2">
					<u-icon v-if="searchName" name="close-circle-fill" color="#A5A6A6" size="28rpx" @click="searchName=''"></u-icon>
					<view class="h-[70rpx] w-[40rpx] text-center leading-[70rpx]" @click.stop="searchNameFn"><text class="nc-iconfont nc-icon-sousuo-duanV6xx1 text-[32rpx]"></text></view>
				</view>
			</view>
			<view class="tabs-box z-2 fixed left-0 bg-[#fff] bottom-[50px] top-0" :class="{ '!top-[106rpx]': config.search.control }">
				<scroll-view :scroll-y="true" class="scroll-height">
					<view class="bg-[#F4F6F8]">
						<view class="tab-item truncate" :class="{ 'tab-item-active': index == tabActive,'rounded-br-[12rpx]':tabActive-1===index,'rounded-tr-[12rpx]':tabActive+1===index  }" v-for="(item, index) in tabsData" :key="index" @click="firstLevelClick(index, item)">
							<view class="text-box px-[16rpx] truncate">
								{{ item.category_name }}
							</view>
						</view>
					</view>
				</scroll-view>
			</view>
			<scroll-view class="h-[100vh]" :scroll-y="true">
				<view class=" pl-[202rpx] scroll-ios pt-[20rpx] pr-[20rpx]" :class="{ '!pt-[126rpx]': config.search.control }">
					<view class="bg-[#fff] grid grid-cols-3 gap-x-[50rpx] gap-y-[32rpx] py-[33rpx] px-[23rpx] rounded-[16rpx]" v-if="tabsData[tabActive]?.child_list && !loading">
						<template v-for="(item, index) in tabsData[tabActive]?.child_list" :key="item.category_id">
							<view class="" @click="toLink(item.category_id)">
								<u--image  radius="12rpx" width="129rpx" height="129rpx" :src="img(item.image ? item.image : '')" model="aspectFill">
									<template #error>
										<image class="rounded-[12rpx] overflow-hidden w-[129rpx] h-[129rpx]" :src="img('static/resource/images/diy/shop_default.jpg')" mode="aspectFill"></image>
									</template>
								</u--image>
								<view class="text-[24rpx] text-center mt-[12rpx] leading-[34rpx]">{{ item.category_name }}</view>
							</view>
						</template>
					</view>
					<view class="flex justify-center w-[100%] rounded-[16rpx] flex items-center" :class="{'noData1':config.search.control,'noData2':!(config.search.control)}" v-if="!tabsData[tabActive]?.child_list && !loading">
						<mescroll-empty :option="{tip : '暂无商品分类'}"></mescroll-empty>
					</view>
				</view>
			</scroll-view>
		</view>
		<tabbar />
		<view class="flex justify-center w-[100%] items-center justify-center h-[100vh]" v-if="!tabsData.length && !loading">
			<mescroll-empty :option="{tip : '暂无商品分类'}"></mescroll-empty>
		</view>
		<u-loading-page bg-color="rgb(248,248,248)" :loading="loading" loadingText="" fontSize="16" color="#303133"></u-loading-page>
	</view>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { img, redirect } from '@/utils/common';
import { getGoodsCategoryTree } from '@/addon/shop/api/goods';
import MescrollEmpty from '@/components/mescroll/mescroll-empty/mescroll-empty.vue';
import { t } from '@/locale';

const prop = defineProps({
    config: {
        type: Object,
        default: (() => {
            return {}
        })
    },
    categoryId: {
        type: [String, Number],
        default: 0
    }
})
let config = prop.config
let categoryId = prop.categoryId;
const searchName = ref("");
const loading = ref<boolean>(true);

onMounted(() => {
	getCategoryData()
})

/**
 * @description 获取分类数据
 * */
const tabsData = ref<Array<Object>>([])
const getCategoryData = () => {
    loading.value = true;
    getGoodsCategoryTree().then((res: any) => {
        tabsData.value = res.data;
        if (categoryId) {
            for (let i = 0; i < tabsData.value.length; i++) {
                if (tabsData.value[i].category_id == categoryId) {
                    tabActive.value = i;
                    break;
                }
                if(tabsData.value[i].child_list){
                    for(let k=0;k<tabsData.value[i].child_list.length;k++){
                        if(tabsData.value[i].child_list[k].category_id == categoryId){
                            tabActive.value = i;
                            break;
                        }
                    }
                }
            }
        }
        loading.value = false;
    }).catch(() => {
        loading.value = false;
    });
}

// 一级菜单样式控制
const tabActive = ref<number>(0)

// 一级菜单点击事件
const firstLevelClick = (index: number, data: Object) => {
	tabActive.value = index;
}
const toLink = (curr_goods_category: string) => {
	redirect({ url: '/addon/shop/pages/goods/list', param: { curr_goods_category } })
}
// 搜索名字
const searchNameFn = () => {
	// getMescroll().resetUpScroll();
	if(searchName.value) redirect({ url: '/addon/shop/pages/goods/list', param: { goods_name: searchName.value } })
}
</script>

<style lang="scss" scoped>
.border-color {
	border-color: var(--primary-color) !important;
}

.text-color {
	color: var(--primary-color) !important;
}

.bg-color {
	background-color: var(--primary-color) !important;
}

.class-select {
	position: relative;
	font-weight: bold;

	&::before {
		content: "";
		position: absolute;
		bottom: 0;
		height: 6rpx;
		background-color: $u-primary;
		width: 90%;
		left: 50%;
		transform: translateX(-50%);
	}
}

.list-select {
	position: relative;
	margin-right: 28rpx;

	&::before {
		content: "";
		position: absolute;
		background-color: #999;
		width: 2rpx;
		height: 70%;
		top: 50%;
		right: -14rpx;
		transform: translatey(-50%);
	}
}

.transform-rotate {
	transform: rotate(180deg);
}

.text-color {
	color: $u-primary;
}

.bg-color {
	background-color: $u-primary;
}

.search-box {
	// position: relative;
	padding: 20rpx 24rpx;
}

.search-box .search-ipt {
	height: 64rpx;
	background-color: #F6F8F8;
	padding-left: 20rpx;
	border-radius: 33rpx;
}

.search-box .search-ipt .input-placeholder {
	color: #A5A6A6;
}

.tabs-box {
	width: 182rpx;
	font-size: 28rpx;
}

.tabs-box .tab-item {
	height: 92rpx;
	text-align: center;
	line-height: 92rpx;
	background-color:#fff;
}

.tabs-box .tab-item-active {
	position: relative;
	color: var(--primary-color);
	background-color:#F4F6F8;
	&::before {
		display: inline-block;
		position: absolute;
		left: 0rpx;
		top: 50%;
		transform: translateY(-50%);
		content: '';
		width: 6rpx;
		height: 48rpx;
		background-color: var(--primary-color);
	}

	&::after {
		display: inline-block;
		position: absolute;
		left: 0rpx;
		top: 50%;
		transform: translateY(-50%);
		content: '';
		width: 6rpx;
		height: 48rpx;
		background-color: var(--primary-color);
	}
}

$white-bj: #fff;

.mescroll-box {
	height: 100vh;
}

.panic-buying {
	background-color: var(--primary-color);
	color: $white-bj;
}

:deep(.mescroll-upwarp) {
	box-sizing: border-box;
	padding-left: 182rpx;
}

:deep(.tab-bar-placeholder) {
	display: none !important;
}

:deep(.u-tabbar__placeholder) {
	display: none !important;
}
/*  #ifdef  H5  */
.scroll-ios{
	padding-bottom: calc(50px + 20rpx + constant(safe-area-inset-bottom)) !important;
	padding-bottom: calc(50px  + 20rpx  + env(safe-area-inset-bottom)) !important;
}
/*  #endif  */
/*  #ifndef  H5  */
.scroll-ios{
	padding-bottom: calc(120rpx  + constant(safe-area-inset-bottom)) !important;
	padding-bottom: calc(120rpx  + env(safe-area-inset-bottom)) !important;
}
/*  #endif  */
.scroll-height{
	height: 100%;
}
/*  #ifdef  H5  */
	.noData1{
		height: calc(100vh - 146rpx - 50px - constant(safe-area-inset-bottom));
		height: calc(100vh - 146rpx - 50px - env(safe-area-inset-bottom));
	}
	.noData2{
		height: calc(100vh - 40rpx - 50px - constant(safe-area-inset-bottom));
		height: calc(100vh - 40rpx - 50px - env(safe-area-inset-bottom));
	}
/*  #endif  */
/*  #ifndef  H5  */
	.noData1{
		height: calc(100vh - 146rpx - 100rpx - constant(safe-area-inset-bottom));
		height: calc(100vh - 146rpx - 100rpx - env(safe-area-inset-bottom));
	}
	.noData2{
		height: calc(100vh - 40rpx - 100rpx - constant(safe-area-inset-bottom));
		height: calc(100vh - 40rpx - 100rpx - env(safe-area-inset-bottom));
	}
/*  #endif  */
</style>
