<template>
    <div class="main-container bg-[#fff] rounded-[4px]">
        <div class="flex ml-[18px] justify-between items-center pt-[20px]">
			<span class="text-page-title">{{ pageName }}</span>
		</div>
        <el-form :model="formData" label-width="150px" ref="formRef" class="page-form" v-loading="loading">
            <el-card class="box-card !border-none" shadow="never">
                <el-form-item :label="t('isStoreSelect')">
                    <div>
                        <el-switch v-model="formData.is_local_delivery_store_select" :active-value="1" :inactive-value="0" />
                        <div class="text-[12px] text-[#999]">不开启时将默认选择就近配送门店，开启后同城配送下单时可主动选择配送门店</div>
                    </div>
                </el-form-item>
                <el-form-item :label="t('isShowPolyline')">
                    <div>
                        <el-switch v-model="formData.is_show_polyline" :active-value="1" :inactive-value="0" />
                        <div class="text-[12px] text-[#999]">此功能需耗费配额,需要具备"驾车路线规划"接口的调用权限和配额方可使用</div>
                    </div>
                </el-form-item>
            </el-card>
        </el-form>
        <div class="fixed-footer-wrap">
            <div class="fixed-footer">
                <el-button type="primary" :loading="loading" @click="save(formRef)">{{ t('save') }}</el-button>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { reactive, ref } from 'vue'
import { t } from '@/lang'
import { setDeliveryConfig, getDeliveryConfig } from '@/addon/shop/api/delivery'
import { FormInstance } from 'element-plus'
import { useRoute } from 'vue-router'

const route = useRoute()
const pageName = route.meta.title

const loading = ref(true)

const formData = reactive<any>({
    is_local_delivery_store_select: 1,
    is_show_polyline: 1
})

const formRef = ref<FormInstance>()

/**
 * 获取配置
 */
getDeliveryConfig().then(res => {
    Object.assign(formData, res.data)
    loading.value = false
}).catch(() => {
    loading.value = false
})

/**
 * 保存
 */
const save = async (formEl: FormInstance | undefined) => {
    if (loading.value || !formEl) return

    await formEl.validate(async (valid) => {
        if (valid) {
            loading.value = true
            setDeliveryConfig(formData).then(() => {
                loading.value = false
            }).catch(() => {
                loading.value = false
            })
        }
    })
}
</script>

<style scoped>

</style>