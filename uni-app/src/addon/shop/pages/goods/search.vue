<template>
	<view :style="themeColor()">
		<view class="cate-search">
			<view class="search-box">
				<input class="uni-input text-[24rpx]" maxlength="50" v-model="inputValue" confirm-type="search" focus @confirm="search()" placeholder="搜索商品" />
				<u-icon v-if="inputValue" name="close-circle-fill" color="#A5A6A6" size="28rpx" @click="inputValue=''"></u-icon>
				<text class="nc-iconfont nc-icon-sousuo-duanV6xx1 !text-[32rpx]" @click="search()"></text>
			</view>
		</view>

		<view class="search-content">
			<!-- 历史搜索 -->
			<view class="history" v-if="historyList.length">
				<view class="history-box">
					<view class="history-top">
						<view class="title">历史搜索</view>
						<view class="icon nc-iconfont nc-icon-shanchu-yuangaizhiV6xx text-[28rpx] text-[#999]" @click="deleteHistoryList"></view>
					</view>
					<view class="history-bottom " id="history-list" :style="{ maxHeight: !isAllHistory ? '100%' : '168rpx' }">
						<view class="history-li" v-for="(item, index) in historyList" :key="index" @click="otherSearch(item)">
							<view>{{ item }}</view>
						</view>
						<view class="history-li history_more" v-if="isAllHistory" @click="isAllHistory = false">
							<view>
								<text class="text-[30rpx] nc-iconfont nc-icon-xiaV6xx"></text>
							</view>
						</view>
					</view>
				</view>
			</view>
		</view>

	</view>
</template>
<script setup lang="ts">
import { onLoad, onShow } from '@dcloudio/uni-app'
import { ref, nextTick } from 'vue';
import { redirect } from '@/utils/common';
import useConfigStore from "@/stores/config";

const inputValue = ref('') //搜索框的值
const historyList = ref([]) //历史搜索记录
const isAllHistory = ref(false)

onLoad((options:any) => {
    uni.getStorageSync('goodsSearchHistory') ? '' : uni.setStorageSync('goodsSearchHistory', []);
});

onShow(() => {
    findHistoryList()
    nextTick(()=> {
        getHistoryHeight();
    });
})

//获取历史搜索记录
const findHistoryList =()=> {
    historyList.value = uni.getStorageSync('goodsSearchHistory').reverse();
}

// 删除所有历史记录
const deleteHistoryList =()=> {
    uni.showModal({
        title: '提示',
        content: '确认删除全部历史记录？',
        confirmColor: useConfigStore().themeColor['--primary-color'],
        success: res => {
            if (res.confirm) {
                uni.setStorageSync('goodsSearchHistory', []);
                findHistoryList();
            }
        }
    });
}

//搜索
const search = ()=> {
    if (inputValue.value.trim() != '') {

        // 对历史搜索处理,判断有无,最近搜索显示在最前
        let historyList = uni.getStorageSync('goodsSearchHistory');
        let array = [];
        if (historyList.length) {
            array = historyList.filter(v => {
                return v != inputValue.value.trim();
            });
            array.push(inputValue.value.trim());
        } else {
            array.push(inputValue.value.trim());
        }
        uni.setStorageSync('goodsSearchHistory', array);

        redirect({ url: '/addon/shop/pages/goods/list', param: { goods_name: inputValue.value.trim() }, mode: 'navigateTo' })
    }
}

// 点击历史搜索
const otherSearch = (e:any)=> {
    inputValue.value = e;
    search();
}

// 获取元素高度
const getHistoryHeight =()=> {
    const query = uni.createSelectorQuery().in(this);
    query.select('#history-list').boundingClientRect(data => {
        if (data && data.height > uni.upx2px(70) * 2 + uni.upx2px(35) * 2) {
            isAllHistory.value = true;
        }
    }).exec();
}

</script>
<style lang="scss" scoped>
	.content {
		width: 100vw;
		/* #ifdef MP */
		height: 100vh;
		/* #endif */
		/* #ifdef H5 */
		height: calc(100vh - env(safe-area-inset-bottom) - var(--status-bar-height));
		height: calc(100vh - constant(safe-area-inset-bottom) - var(--status-bar-height));
		/* #endif */
		/* #ifdef APP-PLUS */
		height: calc(100vh - 44px - env(safe-area-inset-bottom));
		height: calc(100vh - 44px - constant(safe-area-inset-bottom));
		/* #endif */
		background: #ffffff;
	}

	.cate-search {
		width: 100%;
		background: #ffffff;
		padding: 10rpx 30rpx;
		box-sizing: border-box;
		/* #ifdef H5 */
		padding-top: 30rpx;
		/* #endif */

		input {
			font-size: 24rpx;
			height: 100%;
			padding: 0 25rpx 0 32rpx;
			width: calc(100% - 120rpx);
			
		}
		.input-placeholder {
			font-size: 24rpx;
		}
		text {
			font-size: 32rpx;
			color: #909399;
			padding-right: 32rpx;
			padding-left: 10rpx;
			text-align: center;
		}

		.search-box {
			width: 100%;
			height: 64rpx;
			background: #f8f8f8;
			display: flex;
			justify-content: center;
			align-items: center;
			border-radius: 40rpx;
		}
	}

	.search-content {
		box-sizing: border-box;
		background: #ffffff;
	}

	.history {
		width: 100%;
		box-sizing: border-box;

		.history-box {
			width: 100%;
			height: 100%;
			background: #ffffff;
			padding: 30rpx 30rpx 0rpx 30rpx;

			box-sizing: border-box;
			overflow: hidden;

			.history-top {
				width: 100%;
				height: 60rpx;
				display: flex;
				justify-content: space-between;
				align-items: center;

				.title {
					font-size: 28rpx;
				}

				.iconfont {
					color: #909399;
					font-size: 28rpx;
				}
			}

			.history-bottom {
				width: 100%;
				padding-top: 20rpx;
				position: relative;

				.history-li {
					display: inline-block;
					margin-right: 20rpx;
					margin-bottom: 15rpx;
					max-width: 100%;

					view {
						line-height: 66rpx;
						background: #f8f8f8 !important;
						height: 66rpx;
						color: #303133 !important;
						margin: 0 0rpx 4rpx 0 !important;
						padding: 0 20rpx;
						overflow: hidden;
						white-space: nowrap;
						text-overflow: ellipsis;
						border-radius: 20rpx;
						font-size: 24rpx;
					}

					&.history_more {
						margin-right: 0;
						position: absolute;
						bottom: 0;
						right: 0;
					}
				}
			}
		}
	}

</style>
