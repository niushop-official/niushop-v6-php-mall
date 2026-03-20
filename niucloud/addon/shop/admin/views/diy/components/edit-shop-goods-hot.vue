<template>
    <!-- 内容 -->
    <div class="content-wrap" v-show="diyStore.editTab == 'content'">
        <div class="edit-attr-item-wrap">
            <h3 class="mb-[10px]">{{ t('标题内容') }}</h3>
            <el-form label-width="80px" class="px-[10px]">
                <el-form-item :label="t('标题风格')">
                    <el-radio-group v-model="diyStore.editComponent.titleStyle.way">
                        <el-radio :value="'text'">{{ t('文字') }}</el-radio>
                        <el-radio :value="'img'">{{ t('图片') }}</el-radio>
                    </el-radio-group>
                </el-form-item>
                <el-form-item :label="t('标题')" v-show="diyStore.editComponent.titleStyle.way == 'text'">
                    <el-input v-model.trim="diyStore.editComponent.titleStyle.text" :placeholder="t('请输入标题')" clearable maxlength="10" show-word-limit />
                </el-form-item>
                <el-form-item :label="t('image')" v-show="diyStore.editComponent.titleStyle.way == 'img'">
                    <upload-image v-model="diyStore.editComponent.titleStyle.imgUrl" :limit="1" />
                </el-form-item>
                <el-form-item :label="t('link')">
                    <diy-link v-model="diyStore.editComponent.titleStyle.link" />
                </el-form-item>
            </el-form>
        </div>
        <div class="edit-attr-item-wrap">
            <h3 class="mb-[10px]">{{ t('副标题内容') }}</h3>
            <el-form label-width="80px" class="px-[10px]">
                <el-form-item :label="t('副标题')">
                    <el-input v-model.trim="diyStore.editComponent.subTitleStyle.text" :placeholder="t('请输入副标题')" clearable maxlength="10" show-word-limit />
                </el-form-item>
            </el-form>
        </div>
        <div class="edit-attr-item-wrap">
            <h3 class="mb-[10px]">{{ t('按钮内容') }}</h3>
            <el-form label-width="80px" class="px-[10px]">
                <el-form-item :label="t('按钮名称')">
                    <el-input v-model.trim="diyStore.editComponent.btnStyle.text" :placeholder="t('请输入按钮名称')" clearable maxlength="4" show-word-limit />
                </el-form-item>
                <el-form-item :label="t('link')">
                    <diy-link v-model="diyStore.editComponent.btnStyle.link" />
                </el-form-item>
            </el-form>
        </div>
        <div class="edit-attr-item-wrap">
            <h3 class="mb-[10px]">{{ t("selectSource") }}</h3>
            <el-form label-width="80px" class="px-[10px]">
                <el-form-item :label="t('商品标签')">
                    <el-input v-model.trim="diyStore.editComponent.goodsStyle.labelText" :placeholder="t('请输入名称')" clearable maxlength="4" show-word-limit />
                </el-form-item>
                <el-form-item :label="t('选择商品')">
                    <el-radio-group v-model="diyStore.editComponent.goodsStyle.source" :title="t('选择商品')">
                        <el-radio label="all">{{ t('全部') }}</el-radio>
                        <el-radio label="custom">{{ t('手动选择') }}</el-radio>
                    </el-radio-group>
                </el-form-item>
                <el-form-item :label="t('goodsNum')" v-if="diyStore.editComponent.goodsStyle.source == 'all'">
                    <el-slider class="goods-list-slider" show-input v-model="diyStore.editComponent.goodsStyle.num" :min="1" max="20" size="small" />
                </el-form-item>
                <el-form-item :label="t('customGoods')" v-if="diyStore.editComponent.goodsStyle.source == 'custom'">
                    <goods-select-popup ref="goodsSelectPopupRef" v-model="diyStore.editComponent.goodsStyle.goodsIds" :min="1" :max="99" />
                </el-form-item>
            </el-form>
        </div>
        <div class="edit-attr-item-wrap">
            <h3 class="mb-[10px]">{{ t('图片展示模式') }}</h3>
            <el-form label-width="80px" class="px-[10px]">
                <el-form-item :label="t('模式')">
                    <el-radio-group v-model="diyStore.editComponent.mode">
                        <el-radio label="aspectFill">{{ t('短边完整，长边裁剪') }}</el-radio>
                        <el-radio label="aspectFit">{{ t('长边适配，等比缩放') }}</el-radio>
                    </el-radio-group>
                </el-form-item>
            </el-form>
        </div>
    </div>

    <!-- 样式 -->
    <div class="style-wrap" v-show="diyStore.editTab == 'style'">

        <div class="edit-attr-item-wrap" v-if="diyStore.editComponent.titleStyle.way == 'text'">
            <h3 class="mb-[10px]">{{ t('标题样式') }}</h3>
            <el-form label-width="100px" class="px-[10px]">
                <el-form-item :label="t('textFontSize')">
                    <el-slider v-model="diyStore.editComponent.titleStyle.fontSize" show-input size="small" class="ml-[10px] diy-nav-slider" :min="12" :max="20" />
                </el-form-item>
                <el-form-item :label="t('textFontWeight')">
                    <el-radio-group v-model="diyStore.editComponent.titleStyle.fontWeight">
                        <el-radio :value="'normal'">{{ t('fontWeightNormal') }}</el-radio>
                        <el-radio :value="'bold'">{{ t('fontWeightBold') }}</el-radio>
                    </el-radio-group>
                </el-form-item>
                <el-form-item :label="t('textColor')">
                    <el-color-picker v-model="diyStore.editComponent.titleStyle.textColor" />
                </el-form-item>
            </el-form>
        </div>
        <div class="edit-attr-item-wrap">
            <h3 class="mb-[10px]">{{ t('副标题样式') }}</h3>
            <el-form label-width="100px" class="px-[10px]">
                <el-form-item :label="t('textFontSize')">
                    <el-slider v-model="diyStore.editComponent.subTitleStyle.fontSize" show-input size="small" class="ml-[10px] diy-nav-slider" :min="12" :max="30" />
                </el-form-item>
                <el-form-item :label="t('textColor')">
                    <el-color-picker v-model="diyStore.editComponent.subTitleStyle.textColor" />
                </el-form-item>
                <el-form-item :label="t('背景色')">
                    <el-color-picker v-model="diyStore.editComponent.subTitleStyle.bgColor" />
                </el-form-item>
                <el-form-item :label="t('标题圆角')">
                    <el-slider v-model="diyStore.editComponent.subTitleStyle.rounded" show-input size="small" class="ml-[10px] diy-nav-slider" :min="0" :max="30" />
                </el-form-item>
            </el-form>
        </div>
        <div class="edit-attr-item-wrap">
            <h3 class="mb-[10px]">{{ t('按钮样式') }}</h3>
            <el-form label-width="100px" class="px-[10px]">
                <el-form-item :label="t('文字大小')">
                    <el-slider v-model="diyStore.editComponent.btnStyle.fontSize" show-input size="small" class="ml-[10px] diy-nav-slider" :min="10" :max="15" />
                </el-form-item>
                <el-form-item :label="t('文字颜色')">
                    <el-color-picker v-model="diyStore.editComponent.btnStyle.textColor" show-alpha :predefine="diyStore.predefineColors" />
                </el-form-item>
                <el-form-item :label="t('背景色')">
                    <el-color-picker v-model="diyStore.editComponent.btnStyle.startBgColor" show-alpha :predefine="diyStore.predefineColors" />
                    <icon name="iconfont iconmap-connect" size="20px" class="block !text-gray-400 mx-[5px]" />
                    <el-color-picker v-model="diyStore.editComponent.btnStyle.endBgColor" show-alpha :predefine="diyStore.predefineColors" />
                </el-form-item>
                <el-form-item :label="t('渐变方式')">
                    <el-radio-group v-model="diyStore.editComponent.btnStyle.gradientType">
                        <el-radio :value="'linear'">{{ t('线性渐变') }}</el-radio>
                        <el-radio :value="'radial'">{{ t('径向渐变') }}</el-radio>
                    </el-radio-group>
                </el-form-item>
            </el-form>
        </div>

        
        <div class="edit-attr-item-wrap">
            <h3 class="mb-[10px]">{{ t('商品样式') }}</h3>
            <el-form label-width="100px" class="px-[10px]">
                <el-form-item :label="t('标签大小')">
                    <el-slider v-model="diyStore.editComponent.goodsStyle.labelSize" show-input size="small" class="ml-[10px] diy-nav-slider" :min="10" :max="15" />
                </el-form-item>
                <el-form-item :label="t('标签文字色')">
                    <el-color-picker v-model="diyStore.editComponent.goodsStyle.labelColor" show-alpha :predefine="diyStore.predefineColors" />
                </el-form-item>
                <el-form-item :label="t('标签背景色')">
                    <el-color-picker v-model="diyStore.editComponent.goodsStyle.labelBgColor" show-alpha :predefine="diyStore.predefineColors" />
                </el-form-item>
                <el-form-item :label="t('背景色')">
                    <el-color-picker v-model="diyStore.editComponent.goodsStyle.bgColor" show-alpha :predefine="diyStore.predefineColors" />
                </el-form-item>
                <el-form-item :label="t('商品圆角')">
                    <el-slider v-model="diyStore.editComponent.goodsStyle.rounded" show-input size="small" class="ml-[10px] diy-nav-slider" :max="50" />
                </el-form-item>
                <el-form-item :label="t('图片圆角')">
                    <el-slider v-model="diyStore.editComponent.goodsStyle.imgRounded" show-input size="small" class="ml-[10px] diy-nav-slider" :max="50" />
                </el-form-item>
            </el-form>
        </div>

        <!-- 组件样式 -->
        <slot name="style"></slot>
    </div>

</template>

<script lang="ts" setup>
import { t } from '@/lang'
import useDiyStore from '@/stores/modules/diy'
import { ref, reactive, onMounted } from 'vue'
import goodsSelectPopup from '@/addon/shop/views/goods/components/goods-select-popup.vue'

const diyStore: any = useDiyStore()
diyStore.editComponent.ignore = [] // 忽略公共属性

// 组件验证
diyStore.editComponent.verify = (index: number) => {
    const res = { code: true, message: '' }

    if (diyStore.value[index].goodsStyle.source == 'custom' && !diyStore.value[index].goodsStyle.goodsIds.length) {
        res.code = false
        res.message = t('请选择商品')
    }
    return res
}

defineExpose({})

</script>

<style lang="scss">
.goods-list-slider {
    .el-slider__input {
        width: 100px;
    }
}
</style>
