<template>
	<!-- 内容 -->
	<div class="content-wrap" v-show="diyStore.editTab == 'content'">

		<div class="edit-attr-item-wrap">
			<h3 class="mb-[10px]">{{ t('selectStyle') }}</h3>
			<el-form label-width="80px" class="px-[10px]">
				<el-form-item :label="t('selectStyle')" class="flex">
					<span class="text-primary flex-1 cursor-pointer" @click="showCouponStyle">{{ diyStore.editComponent.styleName }}</span>
					<el-icon>
						<ArrowRight />
					</el-icon>
				</el-form-item>
			</el-form>

			<el-dialog v-model="showCouponDialog" :title="t('selectStyle')" width="500px">

				<div class="flex flex-wrap">
					<template v-for="(item,index) in couponStyleList" :key="index">
						<div :class="{ 'border-primary': selectCouponStyle.value == item.value }" @click="changeCouponStyle(item)" class="flex items-center justify-center overflow-hidden w-[200px] h-[100px] mr-[12px] cursor-pointer border bg-gray-50">
							<img :src="img(item.url)" />
						</div>
					</template>
				</div>

				<template #footer>
					<span class="dialog-footer">
						<el-button @click="showCouponDialog = false">{{ t('cancel') }}</el-button>
						<el-button type="primary" @click="confirmCouponStyle">{{ t('confirm') }}</el-button>
					</span>
				</template>

			</el-dialog>
		</div>

		<div class="edit-attr-item-wrap">
			<h3 class="mb-[10px]">{{ t('couponContent') }}</h3>
			<el-form label-width="90px" class="px-[10px]">

				<el-form-item :label="t('couponTitle')">
					<el-input v-model="diyStore.editComponent.couponTitle" clearable maxlength="8" show-word-limit/>
				</el-form-item>

				<el-form-item :label="t('couponSubTitle')">
					<el-input v-model="diyStore.editComponent.couponSubTitle" clearable maxlength="10" show-word-limit/>
				</el-form-item>

			</el-form>

		</div>

		<div class="edit-attr-item-wrap">
			<h3 class="mb-[10px]">{{ t('couponData') }}</h3>
			<el-form label-width="90px" class="px-[10px]">

				<el-form-item :label="t('selectCoupon')">
					<el-radio-group v-model="diyStore.editComponent.source" :title="t('goodsSelectPopupSelectGoodsButton')">
						<el-radio label="all">{{ t('allSources') }}</el-radio>
						<el-radio label="custom">{{ t('manualSelectionSources') }}</el-radio>
					</el-radio-group>
				</el-form-item>
				<el-form-item :label="t('manualSelectionSources')" v-if="diyStore.editComponent.source == 'custom'">
					<coupon-select-popup ref="couponSelectPopupRef" v-model="diyStore.editComponent.couponIds" :min="1" :max="20" />
				</el-form-item>
				<el-form-item :label="t('couponNum')" v-if="diyStore.editComponent.source == 'all'">
					<div class="flex items-center w-full ml-[5px]">
						<el-slider class="flex-1" v-model="diyStore.editComponent.num" :min="1" max="20" size="small" />
						<span class="ml-[15px]">{{ diyStore.editComponent.num }}</span>
					</div>
				</el-form-item>
				<el-form-item :label="t('couponBtnText')">
					<el-input v-model="diyStore.editComponent.btnText" clearable maxlength="5" show-word-limit/>
				</el-form-item>

			</el-form>

		</div>

	</div>

	<!-- 样式 -->
	<div class="style-wrap" v-show="diyStore.editTab == 'style'">

		<!-- 组件样式 -->
		<slot name="style"></slot>
	</div>

</template>

<script lang="ts" setup>
import { t } from '@/lang'
import useDiyStore from '@/stores/modules/diy'
import { img } from '@/utils/common'
import { ref, reactive } from 'vue'
import Sortable from 'sortablejs'
import { range } from 'lodash-es'
import couponSelectPopup from '@/addon/shop/views/goods/components/coupon-select-popup.vue'

const diyStore = useDiyStore()
diyStore.editComponent.ignore = ['componentBgColor','componentBgUrl'] // 忽略公共属性

// 组件验证
diyStore.editComponent.verify = (index: number) => {
    const res = { code: true, message: '' }

    if(diyStore.value[index].source == 'custom'){
        if(diyStore.value[index].couponIds.length == 0){
            res.code = false
            res.message = t('couponPlaceholder')
	        return res;
        }
    }

    if(diyStore.value[index].btnText == ''){
        res.code = false
        res.message = t('couponBtnTextPlaceholder')
        return res;
    }

    if(diyStore.value[index].couponTitle == ''){
        res.code = false
        res.message = t('couponTitlePlaceholder')
        return res;
    }

    if(diyStore.value[index].couponSubTitle == ''){
        res.code = false
        res.message = t('couponSubTitlePlaceholder')
        return res;
    }

    return res
}

const selectCouponStyle = reactive({
    title: diyStore.editComponent.styleName,
    value: diyStore.editComponent.style
})

// 风格样式
const showCouponDialog = ref(false)

const showCouponStyle = () => {
    showCouponDialog.value = true
    selectCouponStyle.title = diyStore.editComponent.styleName;
    selectCouponStyle.value = diyStore.editComponent.style;
}

const couponStyleList = reactive([
    {
        url: 'addon/shop/diy/goods_coupon/style-1.png',
        title: '风格1',
        value: 'style-1'
    },
    {
        url: 'addon/shop/diy/goods_coupon/style-2.png',
        title: '风格2',
        value: 'style-2'
    }
])

const changeCouponStyle = (item:any) => {
    selectCouponStyle.title = item.title;
    selectCouponStyle.value = item.value;
}

const confirmCouponStyle = () => {
    diyStore.editComponent.styleName = selectCouponStyle.title;
    diyStore.editComponent.style = selectCouponStyle.value;
    showCouponDialog.value = false
}

defineExpose({})

</script>

<style lang="scss" scoped>
</style>
