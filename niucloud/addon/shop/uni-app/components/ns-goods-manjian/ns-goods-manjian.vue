<template>
    <!-- 满减 -->
    <view @touchmove.prevent.stop>
        <u-popup class="manjian-popup" :show="manjianShow" @close="manjianShow = false" zIndex="999999">
            <view class="min-h-[480rpx] popup-common" @touchmove.prevent.stop>
                <view class="title !pb-[30rpx]">满减送</view>
                <scroll-view class="h-[520rpx]" scroll-y="true">
                    <view class="px-[var(--popup-sidebar-m)] pt-[30rpx]">
                        <!-- 1. 外层循环：遍历多个满减活动 -->
                        <view v-for="(activity, actIndex) in dataList" :key="actIndex" class="mb-[40rpx]">
                            <!-- 活动名称标识（区分不同活动） -->
                            <view class="text-[28rpx] font-500 text-[#333] mb-[30rpx]">
                                {{ activity.manjian_name }}
                            </view>
                            <!-- 2. 内层循环：遍历当前活动的规则（和原逻辑一致） -->
                            <view v-for="(item, ruleIndex) in activity.content" :key="ruleIndex" class="mb-[30rpx]">
                                <view class="flex items-center">
                                    <text class="nc-iconfont nc-icon-qianbaoyueV6xx !text-[28rpx] mr-[10rpx]"></text>
                                    <text class="text-[26rpx] font-500">{{ item.limit }}</text>
                                </view>
                                <view class="mt-[20rpx]">
                                    <!-- 赠品 -->
                                    <view v-if="item.goods && item.goods.length" class="flex mt-[20rpx] ml-[10rpx]">
                                        <view class="w-[100rpx] flex justify-end">
                                            <view class="bg-[var(--primary-color-light)] text-[var(--primary-color)] rounded-[6rpx] text-[22rpx] flex items-center justify-center px-[12rpx] h-[38rpx] mr-[6rpx]">赠品</view>
                                        </view>
                                        <view class="flex-1 ml-[8rpx]">
                                            <view class="flex p-[20rpx] bg-[#f8f8f8] rounded-[var(--goods-rounded-big)] overflow-hidden"
                                                :class="{'mb-[20rpx]': goodsIndex != (item.goods.length-1)}"
                                                v-for="(goodsItem,goodsIndex) in item.goods" :key="goodsIndex"
                                                @click="goodsEvent(goodsItem.goods_id)">
                                                <up-image radius="var(--goods-rounded-mid)" width="120rpx" height="120rpx" :src="img(goodsItem.sku_image)" model="aspectFill">
                                                    <template #error>
                                                        <image class="w-[120rpx] h-[120rpx] rounded-[var(--goods-rounded-big)] overflow-hidden" :src="img('static/resource/images/diy/shop_default.jpg')" mode="aspectFill" />
                                                    </template>
                                                </up-image>
                                                <view class="flex flex-1 w-0 flex-col justify-between ml-[20rpx] pt-[6rpx] pb-[10rpx]">
                                                    <view class="truncate text-[#303133] text-[24rpx] leading-[32rpx]">{{ goodsItem.goods_name }}</view>
                                                    <view class="flex items-baseline">
                                                        <view v-if="goodsItem.sku_name" class="truncate text-[22rpx] mt-[4rpx] text-[#999]">{{ goodsItem.sku_name }}</view>
                                                        <view class="font-400 ml-[auto] text-[24rpx] text-[#303133]">
                                                            <text>x</text>
                                                            <text>{{ goodsItem.num }}</text>
                                                        </view>
                                                    </view>
                                                </view>
                                            </view>
                                        </view>
                                    </view>
                                    <!-- 满减/包邮/积分/余额 -->
                                    <template v-if="item.give && item.give.length">
                                        <view class="flex items-center mt-[16rpx] ml-[10rpx]" v-for="(giveItem,giveIndex) in item.give" :key="giveIndex">
                                            <view class="w-[100rpx] flex justify-end">
                                                <view class="bg-[var(--primary-color-light)] text-[var(--primary-color)] rounded-[6rpx] text-[22rpx] flex items-center justify-center px-[12rpx] h-[38rpx] mr-[6rpx]">{{ giveItem.label }}</view>
                                            </view>
                                            <text class="text-[24rpx]">{{ giveItem.content }}</text>
                                        </view>
                                    </template>
                                    <!-- 优惠券 -->
                                    <view class="flex items-baseline mt-[16rpx] ml-[33rpx]" v-if="item.coupon && item.coupon.length">
                                        <view class="w-[100rpx] flex justify-end">
                                            <view class="bg-[var(--primary-color-light)] text-[var(--primary-color)] rounded-[6rpx] text-[22rpx] flex items-center justify-center px-[12rpx] h-[38rpx] mr-[6rpx]">优惠券</view>
                                        </view>
                                        <view class="flex flex-wrap flex-1">
                                            <text class="flex items-center text-[24rpx] leading-[1.3]" :class="{'mb-[16rpx]': couponIndex != (item.coupon.length-1)}" v-for="(couponItem,couponIndex) in item.coupon" :key="couponIndex">{{ couponItem.num }}张{{ couponItem.coupon_name }}优惠券</text>
                                        </view>
                                    </view>
                                </view>
                            </view>
                        </view>
                    </view>
                </scroll-view>
                <view class="btn-wrap">
                    <button class="primary-btn-bg btn" @click="manjianShow = false">确定</button>
                </view>
            </view>
        </u-popup>
    </view>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { img, deepClone, redirect } from '@/utils/common'
import { cloneDeep } from 'lodash-es'
import { t } from '@/locale'

const manjianShow = ref(false);
const dataList = ref([]); // 改为数组，存储多个满减活动

const open = (parameter: any = [],traverseType:any) => {
    console.log('parameter', parameter,traverseType)
    // 清空原有数据
    dataList.value = [];
    // 1. 循环处理每个满减活动
    parameter.forEach((activity: any) => {
        // 深拷贝当前活动，避免修改原数据
        const clonedActivity = cloneDeep(activity);
        // 2. 处理当前活动的规则，组装成可展示的content
        clonedActivity.content = [];
        // 根据传入的类型，选择要遍历的对象
        const targetList = traverseType === 'rule_json' ? clonedActivity.rule_json : ([clonedActivity.rule] || []);
        targetList.forEach((rule: any) => {
            // 只处理需要显示的规则
            if (rule.is_show === undefined || rule.is_show) {
                const ruleObj: any = {};
                // 组装门槛文案（满X元/件）
                ruleObj.limit = `门槛满${ clonedActivity.condition_type === 'over_n_yuan' ? parseFloat(rule.limit).toFixed(2) : rule.limit }${ clonedActivity.condition_type === 'over_n_yuan' ? '元' : '件' }`;
                // 组装赠品（如有）
                if (rule.is_give_goods && rule.goods && rule.goods.length) {
                    ruleObj.goods = deepClone(rule.goods);
                }

                // 组装满减/包邮/积分/余额等权益
                ruleObj.give = [];
                // 满减
                if (rule.is_discount && rule.discount_money) {
                    ruleObj.give.push({
                        label: '满减',
                        content: `订单金额${  rule.discount_type === 1 ? '减' : '打' }${ rule.discount_type === 1  ? parseFloat(rule.discount_money).toFixed(2)  : rule.discount_money }${  rule.discount_type === 1 ? '元' : '折' }`
                    });
                }
                // 包邮
                if (rule.is_free_shipping) {
                    ruleObj.give.push({
                        label: '包邮',
                        content: '商品包邮'
                    });
                }
                // 积分
                if (rule.is_give_point && rule.point) {
                    ruleObj.give.push({
                        label: '积分',
                        content: `送${ rule.point }积分`
                    });
                }
                // 余额
                if (rule.is_give_balance && rule.balance) {
                    ruleObj.give.push({
                        label: '余额',
                        content: `送${ parseFloat(rule.balance).toFixed(2) }余额`
                    });
                }

                // 组装优惠券（如有）
                if (rule.is_give_coupon && rule.coupon && rule.coupon.length) {
                    ruleObj.coupon = deepClone(rule.coupon);
                }

                // 将当前规则加入活动的content
                clonedActivity.content.push(ruleObj);
            }
        });

        // 将处理好的活动加入数据列表
        dataList.value.push(clonedActivity);
    });

    // 打开弹窗
    manjianShow.value = true;
}

const goodsEvent = (id: number) => {
    redirect({
        url: '/addon/shop/pages/goods/detail',
        param: {
            goods_id: id
        }
    })
}

defineExpose({
    open
})
</script>

<style lang="scss" scoped>
::v-deep .manjian-popup .u-slide-up-enter-to {
    z-index: 999999 !important;
}
</style>
