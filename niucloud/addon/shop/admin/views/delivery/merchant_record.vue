<template>
    <div class="main-container">
        <el-card class="box-card !border-none" shadow="never">
            <div class="flex justify-between items-center">
                <span class="right">{{ pageName }}</span>
            </div>
            <el-card class="box-card !border-none my-[10px] table-search-wrap" shadow="never">
                <el-form :inline="true" :model="shopRecordTable.searchParam" ref="searchFormRef">
                    <el-form-item :label="t('deliveryNo')" prop="delivery_no">
                        <el-input v-model.trim="shopRecordTable.searchParam.delivery_no" :placeholder="t('deliveryNoPlaceholder')" />
                    </el-form-item>
                    <el-form-item :label="t('tradeNo')" prop="trade_no">
                        <el-input v-model.trim="shopRecordTable.searchParam.trade_no" :placeholder="t('tradeNoPlaceholder')" />
                    </el-form-item>
                    <el-form-item :label="t('deliveryStatus')" prop="status">
                        <el-select v-model="shopRecordTable.searchParam.status" :placeholder="t('deliveryStatusPlaceholder')" clearable>
                            <el-option v-for="(item,key) in statusList" :key="key" :label="item" :value="key" />
                        </el-select>
                    </el-form-item>
                    <el-form-item :label="t('createTime')" prop="create_time">
                        <el-date-picker v-model="shopRecordTable.searchParam.create_time" type="datetimerange"
                            value-format="YYYY-MM-DD HH:mm:ss" :start-placeholder="t('startDate')"
                            :end-placeholder="t('endDate')" />
                    </el-form-item>
                    <el-form-item>
                        <el-button type="primary" @click="loadShopDeliveryList()">{{ t('search') }}</el-button>
                        <el-button @click="resetForm(searchFormRef)">{{ t('reset') }}</el-button>
                    </el-form-item>
                </el-form>
            </el-card>
            <div class="mt-[10px]">
                <el-table :data="shopRecordTable.data" size="large" v-loading="shopRecordTable.loading">
                    <template #empty>
                        <span>{{ !shopRecordTable.loading ? t('emptyData') : '' }}</span>
                    </template>
                    <el-table-column prop="delivery_no" :label="t('deliveryNo')" min-width="120" />
                    <el-table-column prop="body" :label="t('goodsInfo')" min-width="120" />
                    <el-table-column prop="trade_no" :label="t('tradeNo')" min-width="120" />
                    <el-table-column prop="delivery_start" :label="t('deliveryStart')" min-width="120">
                        <template #default="{ row }">
                            <div class="cursor-pointer" @click="openMap(row,'start')">
                                <span>{{ row.delivery_start }}</span>
                                <span class="text-primary ml-[4px] nc-iconfont nc-icon-chakandituV6xx"></span>
                            </div>
                        </template>
                    </el-table-column>
                    <el-table-column prop="delivery_end" :label="t('deliveryEnd')" min-width="120">
                        <template #default="{ row }">
                            <div class="cursor-pointer" @click="openMap(row, 'end')">
                                <span>{{ row.delivery_end }}</span>
                                <span class="text-primary ml-[4px] cursor-pointer nc-iconfont nc-icon-chakandituV6xx"></span>
                            </div>
                        </template>
                    </el-table-column>
                    <el-table-column prop="status_name" :label="t('deliveryStatus')" min-width="120" />
                    <el-table-column prop="delivery_distance" :label="t('deliveryDistance')" min-width="120">
                        <template #default="{ row }">
                             {{ distance(row.delivery_distance) }}
                        </template>
                    </el-table-column>
                    <el-table-column prop="delivery_money" :label="t('deliveryMoney')" min-width="120">
                        <template #default="{ row }">
                            ￥{{ row.delivery_money }}
                        </template>
                    </el-table-column>
                    <el-table-column prop="create_time" :label="t('createTime')" min-width="120" />
                    <el-table-column prop="remark" :label="t('remark')" min-width="120" />
                    <el-table-column :label="t('operation')" fixed="right" min-width="120" align="right">
                        <template #default="{ row }">
                            <el-button type="primary" link @click="detailEvent(row)">{{ t('detail') }}</el-button>
                            <el-button type="primary" link @click="finishEvent(row)" v-if="row.pre_status_name?.finish && row.pre_status_name?.finish.indexOf(Number(row.status)) != -1">{{ t('finish') }}</el-button>
                            <el-button type="primary" link @click="cancelEvent(row)" v-if="row.pre_status_name?.cancel && row.pre_status_name?.cancel.indexOf(Number(row.status)) != -1">{{ t('cancel') }}</el-button>
                        </template>
                    </el-table-column>
                </el-table>
                <div class="mt-[16px] flex justify-end">
                    <el-pagination v-model:current-page="shopRecordTable.page" v-model:page-size="shopRecordTable.limit"
                        layout="total, sizes, prev, pager, next, jumper" :total="shopRecordTable.total"
                        @size-change="loadShopDeliveryList()" @current-change="loadShopDeliveryList" />
                </div>
            </div>
        </el-card>
        <shop-record-detail ref="shopRecordDetailRef" />
        <!-- 查看地图 -->
        <map-info ref="mapInfoRef" />
        <el-dialog v-model="showDialog" :title="t('deliveryCancel')" width="480" class="diy-dialog-wrap" :destroy-on-close="true">
            <el-form :model="formData" label-width="120px" ref="formRef" :rules="formRules" class="page-form" v-loading="loading">
                <el-form-item :label="t('cancelReason')" prop="cancel_reason">
                   <el-input v-model="formData.cancel_reason" type="textarea" rows="4" maxlength="200" clearable class="input-width" :placeholder="t('cancelReasonPlaceholder')" />
                </el-form-item>
            </el-form>
            <template #footer>
                <span class="dialog-footer">
                    <el-button @click="showDialog = false">{{ t('cancel') }}</el-button>
                    <el-button type="primary" :loading="loading" @click="confirm(formRef)">{{ t('confirm') }}</el-button>
                </span>
            </template>
        </el-dialog>
    </div>
</template>

<script setup lang="ts">
import { ElMessageBox, FormInstance } from 'element-plus'
import { t } from '@/lang'
import { distance } from '@/utils/common'
import { computed, reactive, ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { getShopDeliveryOrderList, getShopDeliveryStatus, getShopDeliveryFinish, setShopDeliveryCancel } from '@/addon/shop/api/delivery'
import ShopRecordDetail from '@/addon/shop/views/delivery/components/shop_record_detail.vue'

const route = useRoute()
const router = useRouter()
const pageName = route.meta.title

const shopRecordTable = reactive({
    page: 1,
    limit: 10,
    total: 0,
    loading: true,
    data: [],
    searchParam: {
        delivery_no: '',
        trade_no: route.query.trade_no || '',
        trade_type: route.query.trade_type || '',
        status: '',
        create_time: ''
    }
})
const statusList = ref<any>([])
const searchFormRef = ref<FormInstance>()

const getShopDeliveryStatusFn = () => {
    getShopDeliveryStatus().then(res => {
        statusList.value = res.data
    })
}
getShopDeliveryStatusFn()

/**
 * 获取配送记录列表
 */
const loadShopDeliveryList = (page: number = 1) => {
    shopRecordTable.loading = true
    shopRecordTable.page = page

    getShopDeliveryOrderList({
        page: shopRecordTable.page,
        limit: shopRecordTable.limit,
        ...shopRecordTable.searchParam
    }).then((res: any) => {
        shopRecordTable.loading = false
        shopRecordTable.data = res.data.data
        shopRecordTable.total = res.data.total
    }).catch(() => {
        shopRecordTable.loading = false
    })
}
loadShopDeliveryList()

// 详情
const shopRecordDetailRef = ref()
const detailEvent = (row: any) => {
    shopRecordDetailRef.value?.setFormData(row)
}

// 查看地图
const mapInfoRef = ref()
const openMap = (data: any, type: string) => {
    const obj: any = {}
    if (type == 'start') {
        obj.taker_latitude = data.delivery_start_lat
        obj.taker_longitude = data.delivery_start_lng
        obj.taker_address = data.delivery_start
    } else {
        obj.taker_latitude = data.delivery_end_lat
        obj.taker_longitude = data.delivery_start_lng
        obj.taker_address = data.delivery_end
    }
    mapInfoRef.value?.setFormData(obj)
}

// 完成
const finishEvent = (data: any) => {
    ElMessageBox.confirm(t('finishTips'), t('warning'),
        {
            confirmButtonText: t('confirm'),
            cancelButtonText: t('cancel'),
            type: 'warning'
        }).then(() => {
        getShopDeliveryFinish(data.id).then(() => {
            loadShopDeliveryList()
        })
    })
}
// 取消
const showDialog = ref(false)
const loading = ref(false)
const formData = reactive<any>({
    id: '',
    cancel_reason: ''
})
const formRef = ref<FormInstance>()
const cancelEvent = async (data: any) => {
    formData.id = data.id
    showDialog.value = true
}

// 表单验证规则
const formRules = computed(() => {
    return {
        cancel_reason: [
            { required: true, message: t('cancelReasonPlaceholder'), trigger: 'blur' }
        ]
    }
})

/**
 * 确认
 * @param formEl
 */
const confirm = async (formEl: FormInstance | undefined) => {
    if (loading.value || !formEl) return

    await formEl.validate(async (valid) => {
        if (valid) {
            loading.value = true

            setShopDeliveryCancel(formData).then(res => {
                loading.value = false
                showDialog.value = false
                loadShopDeliveryList()
            }).catch(() => {
                loading.value = false
            })
        }
    })
}
const resetForm = (formEl: FormInstance | undefined) => {
    if (!formEl) return
    formEl.resetFields()
    loadShopDeliveryList()
}
</script>

<style scoped lang="scss">
:deep(.el-form-item){
    align-items: flex-start;
}
</style>