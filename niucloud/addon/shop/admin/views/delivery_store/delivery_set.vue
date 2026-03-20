<template>
    <div class="main-container">
        <el-card class="card !border-none mb-[15px]" shadow="never">
            <el-page-header :content="pageName" :icon="ArrowLeft" @back="router.push(`/shop/delivery_store`)" />
        </el-card>
        <el-card class="box-card !border-none" shadow="never">

            <el-table :data="deliveryServiceTable.data" size="large" v-loading="deliveryServiceTable.loading">
                <template #empty>
                    <span>{{ !deliveryServiceTable.loading ? t('emptyData') : '' }}</span>
                </template>

                <el-table-column prop="name" :label="t('deliveryServiceName')" min-width="200" :show-overflow-tooltip="true"/>

                <el-table-column prop="open_status" :label="t('openStatus')" width="200">
                    <template #default="{ row }">
                        <div v-if="row.open_status == ''">—</div>
                        <el-tag v-else class="cursor-pointer" :type="row.open_status == 1 ? 'success' : 'danger'">{{ row.open_status == 1 ? t('pass') : t('notPass') }}</el-tag>
                    </template>
                </el-table-column>

                <el-table-column prop="edit_status" :label="t('editStatus')" width="200">
                    <template #default="{ row }">
                        <div v-if="row.edit_status == ''">—</div>
                        <el-tag v-else class="cursor-pointer" :type="row.edit_status == 1 ? 'success' : 'danger'">{{ row.edit_status == 1 ? t('pass') : t('notPass') }}</el-tag>
                    </template>
                </el-table-column>

                <el-table-column prop="reason" :label="t('reason')" :show-overflow-tooltip="true" min-width="500"/>

                <el-table-column :label="t('operation')" fixed="right" align="right" min-width="120">
                   <template #default="{ row }">
                       <el-button type="primary" link @click="openEvent(row)" v-if="row.is_merchant == 0 && row.open_status != 1">{{ t('openService') }}</el-button>
                       <el-button type="primary" link @click="editEvent(row)" v-if="row.is_merchant == 0 && row.open_status == 1">{{ t('editService') }}</el-button>

                   </template>
                </el-table-column>

            </el-table>

        </el-card>

        <!-- 开通/修改品类 -->
        <el-dialog v-model="showDialog" :title="title" width="500px" :destroy-on-close="true">
            <el-form :model="formData" label-width="120px" ref="formRef" :rules="formRulesDeliverySet" class="page-form" v-loading="loadingDeliverySet">
                <el-form-item :label="t('business')" prop="business">
					<el-select v-model="formData.business" clearable :placeholder="t('businessPlaceholder')">
                        <el-option :label="value" :value="key" v-for="(value, key, index) in businessOptions" :key="index"/>
                    </el-select>
				</el-form-item>

            </el-form>

            <template #footer>
            <span class="dialog-footer">
                <el-button @click="showDialog = false">{{ t('cancel') }}</el-button>
                <el-button type="primary" :loading="loadingDeliverySet" @click="confirmDeliverySet(formRef)">{{ t('confirm') }}</el-button>
            </span>
            </template>
        </el-dialog>
    </div>
</template>

<script lang="ts" setup>
import { reactive, ref, computed } from 'vue'
import { t } from '@/lang'
import { ArrowLeft } from '@element-plus/icons-vue'
import { FormInstance } from 'element-plus'
import { useRoute, useRouter } from 'vue-router'
import { getDeliveryServiceList, deliveryShopOpen, deliveryShopEdit } from '@/addon/shop/api/delivery'

import { cloneDeep } from 'lodash-es'

const route = useRoute()
const router = useRouter()

const pageName = route.meta.title

const id:any = ref(route.query.id || 0)

const deliveryServiceTable = reactive({
    loading: true,
    data: []
})

const loadDeliveryServiceList = () => {
    // 查询配送服务商列表
    getDeliveryServiceList(id.value).then((res: any) => {
        deliveryServiceTable.data = cloneDeep(res.data)
        deliveryServiceTable.loading = false
    })
}

loadDeliveryServiceList()

const businessOptions = ref({})

const showDialog = ref(false)
const loadingDeliverySet = ref(false)
const formRef = ref<FormInstance>()
const formData = reactive({
    id: id.value,
    delivery_type: '',
    business: '',
    extend_data: {},
})

// 表单验证规则
const formRulesDeliverySet = computed(() => {
    return {
        business: [
            { required: true, message: t('businessPlaceholder'), trigger: 'blur' }
        ]
    }
})


const title = ref('')
const isOpen = ref(false)
// 开通
const openEvent = (data:any) => {
    title.value = t('openService') + data.name
    businessOptions.value = data.business_list
    formData.delivery_type = data.key
    formData.extend_data = data.extend_data
    showDialog.value = true
}

// 修改品类
const editEvent = (data:any) => {
    title.value = t('editService')
    businessOptions.value = data.business_list
    formData.delivery_type = data.key
    formData.business = data.business
    showDialog.value = true
    isOpen.value = data.open_status == 1
}

/**
 * 确认
 * @param formEl
 */
const confirmDeliverySet = async (formEl: FormInstance | undefined) => {
    if (loadingDeliverySet.value || !formEl) return
    const save = isOpen.value ? deliveryShopEdit : deliveryShopOpen

    await formEl.validate(async (valid) => {
        if (valid) {
            loadingDeliverySet.value = true
            save(formData).then(res => {
                loadingDeliverySet.value = false
                showDialog.value = false
                loadDeliveryServiceList()
            }).catch(() => {
                loadingDeliverySet.value = false
            })
        }
    })
}

</script>

<style lang="scss" scoped>
</style>
