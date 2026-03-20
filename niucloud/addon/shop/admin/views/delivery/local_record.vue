<template>
    <div class="main-container">
        <el-card class="box-card !border-none" shadow="never">
            <div class="flex justify-between items-center">
                <span class="right">{{ pageName }}</span>
            </div>
            <el-card class="box-card !border-none my-[10px] table-search-wrap" shadow="never">
                <el-form :inline="true" :model="localRecordTable.searchParam" ref="searchFormRef">
                    <el-form-item :label="t('deliveryNo')" prop="delivery_no">
                        <el-input v-model.trim="localRecordTable.searchParam.delivery_no" :placeholder="t('deliveryNoPlaceholder')" />
                    </el-form-item>
                    <el-form-item :label="t('tradeNo')" prop="trade_no">
                        <el-input v-model.trim="localRecordTable.searchParam.trade_no" :placeholder="t('tradeNoPlaceholder')" />
                    </el-form-item>
                    <el-form-item :label="t('deliveryStatus')" prop="status">
                        <el-select v-model="localRecordTable.searchParam.status" :placeholder="t('deliveryStatusPlaceholder')" clearable>
                            <el-option v-for="(item,key) in statusList" :key="key" :label="item" :value="key" />
                        </el-select>
                    </el-form-item>
                    <el-form-item :label="t('createTime')" prop="create_time">
                        <el-date-picker v-model="localRecordTable.searchParam.create_time" type="datetimerange"
                            value-format="YYYY-MM-DD HH:mm:ss" :start-placeholder="t('startDate')"
                            :end-placeholder="t('endDate')" />
                    </el-form-item>
                    <el-form-item>
                        <el-button type="primary" @click="loadLocalDeliveryList()">{{ t('search') }}</el-button>
                        <el-button @click="resetForm(searchFormRef)">{{ t('reset') }}</el-button>
                    </el-form-item>
                </el-form>
            </el-card>
            <div class="mt-[10px]">
                <el-table :data="localRecordTable.data" size="large" v-loading="localRecordTable.loading">
                    <template #empty>
                        <span>{{ !localRecordTable.loading ? t('emptyData') : '' }}</span>
                    </template>
                    <el-table-column prop="delivery_service_name" :label="t('deliveryServiceName')" min-width="120" />
                    <el-table-column prop="body" :label="t('goodsInfo')" min-width="120" />
                    <el-table-column prop="delivery_no" :label="t('deliveryNo')" min-width="120" />
                    <el-table-column prop="trade_no" :label="t('tradeNo')" min-width="120" />
                    <el-table-column prop="delivery_start" :label="t('deliveryStart')" min-width="120">
                        <template #default="{ row }">
                            <div class="cursor-pointer" @click="openMap(row,'start')">
                                <span>{{ row.delivery_start }}</span>
                                <span class="text-primary ml-[4px] nc-iconfont nc-icon-chakandituV6xx" ></span>
                            </div>
                        </template>
                    </el-table-column>
                    <el-table-column prop="delivery_end" :label="t('deliveryEnd')" min-width="120">
                        <template #default="{ row }">
                            <div class="cursor-pointer" @click="openMap(row, 'end')">
                                <span>{{ row.delivery_end }}</span>
                                <span class="text-primary ml-[4px] nc-iconfont nc-icon-chakandituV6xx" ></span>
                            </div>
                        </template>
                    </el-table-column>
                    <el-table-column prop="status_name" :label="t('deliveryStatus')" min-width="120" />
                    <el-table-column prop="" :label="t('deliveryDistance')" min-width="120">
                        <template #default="{ row }">
                            {{ distance(row.delivery_distance) }}
                        </template>
                    </el-table-column>
                    <el-table-column :label="t('deliveryMoney')" min-width="120">
                        <template #default="{ row }">
                            ￥{{ row.delivery_money }}
                        </template>
                    </el-table-column>
                    <el-table-column prop="create_time" :label="t('createTime')" min-width="120" />
                    <el-table-column prop="remark" :label="t('remark')" min-width="120" />
                    <el-table-column :label="t('operation')" fixed="right" min-width="120" align="right">
                        <template #default="{ row }">
                            <el-button type="primary" link @click="detailEvent(row)">{{ t('detail') }}</el-button>
                            <el-button type="primary" link @click="syncEvent(row)" v-if="row.pre_status_name?.sync && row.pre_status_name?.sync.indexOf(Number(row.status)) != -1">{{ t('sync') }}</el-button>
                            <el-button type="primary" link @click="finishEvent(row)" v-if="row.pre_status_name?.finish && row.pre_status_name?.finish.indexOf(Number(row.status)) != -1">{{ t('finish') }}</el-button>
                            <el-button type="primary" link @click="cancelEvent(row)" v-if="row.pre_status_name?.cancel && row.pre_status_name?.cancel.indexOf(Number(row.status)) != -1">{{ t('cancel') }}</el-button>
                        </template>
                    </el-table-column>
                </el-table>
                <div class="mt-[16px] flex justify-end">
                    <el-pagination v-model:current-page="localRecordTable.page" v-model:page-size="localRecordTable.limit"
                        layout="total, sizes, prev, pager, next, jumper" :total="localRecordTable.total"
                        @size-change="loadLocalDeliveryList()" @current-change="loadLocalDeliveryList" />
                </div>
            </div>
        </el-card>
        <local-record-detail ref="localRecordDetailRef" />
        <!-- 查看地图 -->
        <map-info ref="mapInfoRef" />
        <el-dialog v-model="showDialog" :title="t('deliveryCancel')" width="480" class="diy-dialog-wrap" :destroy-on-close="true">
            <el-form :model="formData" label-width="120px" ref="formRef" :rules="formRules" class="page-form" v-loading="loading">
                <template v-if="formData.cancel_type != 'merchant'">
                    <el-form-item :label="t('cancelReason')" prop="cancel_reason_id">
                        <el-select v-model="formData.cancel_reason_id" clearable class="input-item" :placeholder="t('cancelReasonSelectPlaceholder')">
                            <el-option v-for="(item, index) in reasonList" :key="index" :label="item" :value="index"></el-option>
                        </el-select>
                    </el-form-item>
                    <el-form-item  prop="cancel_reason" v-if="formData.cancel_reason_id == 10000">
                    <el-input v-model="formData.cancel_reason" type="textarea" rows="4" maxlength="200" clearable class="input-width" :placeholder="t('cancelReasonPlaceholder')" />
                    </el-form-item>
                </template>
                <el-form-item :label="t('cancelReason')" prop="cancel_reason" v-if="formData.cancel_type == 'merchant'">
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
import { getLocalDeliveryList, getLocalDeliveryStatus, getLocalDeliverySync, getLocalDeliveryCancelReason, getDeliveryFinish, setLocalDeliveryCancel } from '@/addon/shop/api/delivery'
import LocalRecordDetail from '@/addon/shop/views/delivery/components/local_record_detail.vue'

const route = useRoute()
const router = useRouter()
const pageName = route.meta.title

const localRecordTable = reactive({
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

const getLocalDeliveryStatusFn = () => {
    getLocalDeliveryStatus().then(res => {
        statusList.value = res.data
    })
}
getLocalDeliveryStatusFn()
/**
 * 获取配送记录列表
 */
const loadLocalDeliveryList = (page: number = 1) => {
    localRecordTable.loading = true
    localRecordTable.page = page

    getLocalDeliveryList({
        page: localRecordTable.page,
        limit: localRecordTable.limit,
        ...localRecordTable.searchParam
    }).then((res: any) => {
        localRecordTable.loading = false
        localRecordTable.data = res.data.data
        localRecordTable.total = res.data.total
    }).catch(() => {
        localRecordTable.loading = false
    })
}
loadLocalDeliveryList()

// 详情
const localRecordDetailRef = ref()
const detailEvent = (row: any) => {
    localRecordDetailRef.value?.setFormData(row)
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

// 同步
const syncEvent = (data: any) => {
    ElMessageBox.confirm(t('syncTips'), t('warning'),
        {
            confirmButtonText: t('confirm'),
            cancelButtonText: t('cancel'),
            type: 'warning'
        }).then(() => {
        getLocalDeliverySync(data.id).then(() => {
            loadLocalDeliveryList()
        })
    })
}

// 完成
const finishEvent = (data: any) => {
    ElMessageBox.confirm(t('finishTips'), t('warning'),
        {
            confirmButtonText: t('confirm'),
            cancelButtonText: t('cancel'),
            type: 'warning'
        }).then(() => {
        getDeliveryFinish(data.id).then(() => {
            loadLocalDeliveryList()
        })
    })
}
// 取消
const showDialog = ref(false)
const reasonList = ref([])
const loading = ref(false)
const formData = reactive<any>({
    id: '',
    cancel_reason_id: '',
    cancel_reason: '',
    cancel_type: ''
})
const formRef = ref<FormInstance>()

const cancelEvent = async (data: any) => {
    formData.id = data.id
    formData.cancel_type = data.delivery_service
    if (data.delivery_service != 'merchant') {
        reasonList.value = await (await getLocalDeliveryCancelReason({ service: data.delivery_service })).data
    }
    showDialog.value = true
}

// 表单验证规则
const formRules = computed(() => {
    return {
        cancel_reason_id: [
            { required: formData.cancel_type != 'merchant', message: t('cancelReasonSelectPlaceholder'), trigger: 'blur' }
        ],
        cancel_reason: [
            { required: formData.cancel_reason_id == 10000 || formData.cancel_type == 'merchant', message: t('cancelReasonPlaceholder'), trigger: 'blur' }
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

            setLocalDeliveryCancel(formData).then(res => {
                loading.value = false
                showDialog.value = false
                setTimeout(() => {
                    loadLocalDeliveryList()
                }, 1500)
            }).catch(() => {
                loading.value = false
            })
        }
    })
}
const resetForm = (formEl: FormInstance | undefined) => {
    if (!formEl) return
    formEl.resetFields()
    loadLocalDeliveryList()
}
</script>

<style scoped lang="scss">
:deep(.el-form-item){
    align-items: flex-start;
}
</style>