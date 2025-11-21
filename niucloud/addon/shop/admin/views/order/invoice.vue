<template>
    <div class="main-container">
        <el-card class="box-card !border-none" shadow="never">
            <div class="flex justify-between items-center">
                <span class="text-page-title">{{ pageName }}</span>
                <div class="flex items-center">
                    <el-button type="primary" @click="openInvoiceFn">{{ t('添加发票') }}</el-button>
                </div>
            </div>
            <el-card class="box-card !border-none my-[10px] table-search-wrap" shadow="never">
                <el-form :inline="true" :model="invoiceManagementTableData.searchParam" ref="searchFormRef">
                    <el-form-item :label="t('headerName')" prop="header_name">
                        <el-input v-model.trim="invoiceManagementTableData.searchParam.header_name" :placeholder="t('headerNamePlaceholder')" clearable />
                    </el-form-item>
                    <el-form-item :label="t('createTime')" prop="create_time">
                        <el-date-picker v-model="invoiceManagementTableData.searchParam.create_time" type="datetimerange"
                            value-format="YYYY-MM-DD HH:mm:ss" :start-placeholder="t('startDate')"
                            :end-placeholder="t('endDate')" />
                    </el-form-item>
                    <el-form-item :label="t('invoiceTime')" prop="invoice_time">
                        <el-date-picker v-model="invoiceManagementTableData.searchParam.invoice_time" type="datetimerange"
                            value-format="YYYY-MM-DD HH:mm:ss" :start-placeholder="t('startDate')"
                            :end-placeholder="t('endDate')" />
                    </el-form-item>
                    <el-form-item>
                        <el-button type="primary" @click="loadInvoiceList()">{{ t('search') }}</el-button>
                        <el-button @click="resetForm(searchFormRef)">{{ t('reset') }}</el-button>
                        <el-button type="primary" @click="exportEvent">{{ t('export') }}</el-button>
                    </el-form-item>
                </el-form>
            </el-card>
            <el-tabs v-model="activeName" class="demo-tabs" @tab-change="handleClick">
                <el-tab-pane :label="t('all')" name=""></el-tab-pane>
                <el-tab-pane :label="t('待审核')" name="2"></el-tab-pane>
                <el-tab-pane :label="t('未开票')" name="0"></el-tab-pane>
                <el-tab-pane :label="t('已开票')" name="1"></el-tab-pane>
               
            </el-tabs>
            <!-- 表格数据 -->
            <div>
                <div class="mb-[10px] flex items-center">
                    <el-checkbox v-model="toggleCheckbox" size="large" class="px-[14px]" @change="toggleChange" :indeterminate="isIndeterminate" />
                    <el-button @click="batchAuditFn" v-if="invoiceManagementTableData.searchParam.is_invoice== 2 || invoiceManagementTableData.searchParam.is_invoice== ''" size="small">{{ t("批量审核") }}</el-button>
                </div>
                <el-table :data="invoiceManagementTableData.data" size="large" v-loading="invoiceManagementTableData.loading" ref="invoiceListTableRef" @selection-change="handleSelectionChange">
                    <template #empty>
                        <span>{{ !invoiceManagementTableData.loading ? t('emptyData') : '' }}</span>
                    </template>
                    <el-table-column type="selection" width="55" :selectable="selectable" />
                    <el-table-column prop="header_name" :label="t('headerName')" min-width="100"  />
                    <el-table-column prop="header_type_name" :label="t('headerTypeName')" min-width="120" />
                    <el-table-column prop="tax_number" :label="t('taxNumber')" min-width="180" />
                    <el-table-column prop="name" :label="t('name')" min-width="120" />
                    <el-table-column prop="money" :label="t('money')" min-width="120" align="right" />
                    <el-table-column :label="t('createTime')" min-width="180">
                        <template #default="{ row }">
                            {{ row.create_time || '' }}
                        </template>
                    </el-table-column>
                    <el-table-column :label="t('invoiceTime')" min-width="180">
                        <template #default="{ row }">
                            {{ row.invoice_time || '' }}
                        </template>
                    </el-table-column>
                    <el-table-column :label="t('开票状态')" min-width="120">
                        <template #default="{ row }">
                            {{ row.invoice_type_name}}
                        </template>
                    </el-table-column>
                    <el-table-column :label="t('operation')" fixed="right" align="center" width="130">
                        <template #default="{ row }">
                            <el-button type="primary" link @click="detailEvent(row)">{{ t('detail') }}</el-button>
                            <el-button type="primary" link @click="invoiceEvent(row)" v-if="row.is_invoice === 0">{{ t('invoice') }}</el-button>
                            <el-button type="primary" link @click="auditEvent(row)" v-if="row.is_invoice === 2">{{ t('审核') }}</el-button>
                            <el-button type="primary" link @click="checkOrder(row)" v-if="row.is_can_jump">{{ t('viewOrder') }}</el-button>
                        </template>
                    </el-table-column>

                </el-table>
                <div class="mt-[16px] flex justify-end">
                    <el-pagination v-model:current-page="invoiceManagementTableData.page"
                        v-model:page-size="invoiceManagementTableData.limit"
                        layout="total, sizes, prev, pager, next, jumper" :total="invoiceManagementTableData.total"
                        @size-change="loadInvoiceList()" @current-change="loadInvoiceList" />
                </div>
                <invoice-detail ref="invoiceDetailDialog" @complete="loadInvoiceList(getTablePageStorage(invoiceManagementTableData.searchParam).page)" />
                <invoice-dialog ref="invoiceListDialog" @complete="loadInvoiceList(getTablePageStorage(invoiceManagementTableData.searchParam).page)" />
                <export-sure ref="exportSureDialog" :show="flag" type="shop_invoice" :searchParam="invoiceManagementTableData.searchParam" @close="handleClose" />
                <open-invoice ref="openInvoiceDialog" @complete="loadInvoiceList(getTablePageStorage(invoiceManagementTableData.searchParam).page)" />
            </div>
        </el-card>
      
    </div>
</template>

<script lang="ts" setup>
import { reactive, ref } from 'vue'
import { t } from '@/lang'
import { getInvoiceList,auditInvoice } from '@/addon/shop/api/order'
import { FormInstance } from 'element-plus'
import InvoiceDetail from '@/addon/shop/views/order/components/invoice-detail.vue'
import InvoiceDialog from '@/addon/shop/views/order/components/invoice-dialog.vue'
import openInvoice from '@/addon/shop/views/order/components/open-invoice.vue'
import { useRouter, useRoute } from 'vue-router'
import { setTablePageStorage, getTablePageStorage } from '@/utils/common'

const route = useRoute()
const router = useRouter()
const pageName = route.meta.title

const invoiceManagementTableData = reactive({
    page: 1,
    limit: 10,
    total: 0,
    loading: true,
    data: [],
    searchParam: {
        is_invoice: '',
        create_time: '',
        invoice_time: '',
        header_name: ''
    }
})

const searchFormRef = ref<FormInstance>()
const resetForm = (formEl: FormInstance | undefined) => {
    if (!formEl) return

    formEl.resetFields()
    loadInvoiceList()
}

const selectable = (row:any, index:number) => {
    if (row.is_invoice != 2) {
        return false
    }
    return true
}
const activeName = ref('')

const handleClick = (event: any) => {
    invoiceManagementTableData.searchParam.is_invoice = event
    loadInvoiceList()
}
/**
 * 获取发票列表数据
 */
const loadInvoiceList = (page: number = 1) => {
    invoiceManagementTableData.loading = true
    invoiceManagementTableData.page = page
    getInvoiceList({
        page: invoiceManagementTableData.page,
        limit: invoiceManagementTableData.limit,
        ...invoiceManagementTableData.searchParam
    }).then(res => {
        invoiceManagementTableData.loading = false
        invoiceManagementTableData.data = res.data.data
        invoiceManagementTableData.total = res.data.total
        setTablePageStorage(invoiceManagementTableData.page, invoiceManagementTableData.limit, invoiceManagementTableData.searchParam)
    }).catch(() => {
        invoiceManagementTableData.loading = false
    })
}

loadInvoiceList(getTablePageStorage(invoiceManagementTableData.searchParam).page)

const invoiceDetailDialog: Record<string, any> | null = ref(null)
/**
 * 查看发票详情
 * @param data
 */
const detailEvent = (data: any) => {
    invoiceDetailDialog.value.setFormData(data)
    invoiceDetailDialog.value.showDialog = true
}
/**
 * 开具发票
 * @param data
 */
const invoiceListDialog: Record<string, any> | null = ref(null)
const invoiceEvent = (data: any) => {
    invoiceListDialog.value.setInvoiceData(data)
    invoiceListDialog.value.invoiceDialog = true
}

// 订单详情
const checkOrder = (data: any) => {
    if (data.trade_type === 'shop') {
        const routeUrl = router.resolve({
            path: '/shop/order/detail',
            query: { order_id: data.trade_id }
        })
        window.open(routeUrl.href, '_blank')
    }
}

/**
 * 发票记录导出
 */
const exportSureDialog = ref(null)
const flag = ref(false)
const handleClose = (val) => {
    flag.value = val
}
const exportEvent = (data: any) => {
    flag.value = true
}

/**
 * 开发票
 */
 const openInvoiceDialog: Record<string, any> | null = ref(null)
const openInvoiceFn = (data: any) => {
    openInvoiceDialog.value.setInvoiceData(data)
    openInvoiceDialog.value.invoiceDialog = true
}

// 批量复选框
const toggleCheckbox = ref()

// 复选框中间状态
const isIndeterminate = ref(false)

// 监听批量复选框事件
const toggleChange = (value: any) => {
    isIndeterminate.value = false
    invoiceListTableRef.value.toggleAllSelection()
}

const invoiceListTableRef = ref()

// 选中数据
const multipleSelection: any = ref([])

// 监听表格单行选中
const handleSelectionChange = (val: []) => {
    multipleSelection.value = val

    toggleCheckbox.value = false
    if (
        multipleSelection.value.length > 0 &&
        multipleSelection.value.length < invoiceManagementTableData.data.length
    ) {
        isIndeterminate.value = true
    } else {
        isIndeterminate.value = false
    }

    if (multipleSelection.value.length == invoiceManagementTableData.data.length) {
        toggleCheckbox.value = true
    }
}

// 审核
const auditEvent = (data: any) => {
    
    ElMessageBox.confirm(t('确定要审核该发票吗？'), t('warning'), {
        confirmButtonText: t('confirm'),
        cancelButtonText: t('cancel'),
        type: 'warning'
    }).then(() => {
        auditInvoice({
            ids: [data.id]
        }).then(() => {
            loadInvoiceList(getTablePageStorage(invoiceManagementTableData.searchParam).page)
        }).catch(() => {
        })
    })
}

// 批量审核
const repeat = ref(false)
const batchAuditFn = () => {
    if (multipleSelection.value.length == 0) {
        ElMessage({
            type: 'warning',
            message: `${t('请选择要审核的发票')}`
        })
        return
    }

    ElMessageBox.confirm(t('确定要批量审核选中的发票吗？'), t('warning'), {
        confirmButtonText: t('confirm'),
        cancelButtonText: t('cancel'),
        type: 'warning'
    }).then(() => {
        if (repeat.value) return
        repeat.value = true

        const invoiceIds: any = []
        multipleSelection.value.forEach((item: any) => {
            invoiceIds.push(item.id)
        })

        auditInvoice({
            ids: invoiceIds
        }).then(() => {
            loadInvoiceList(getTablePageStorage(invoiceManagementTableData.searchParam).page)
            repeat.value = false
        }).catch(() => {
            repeat.value = false
        })
    })
}
</script>

<style lang="scss" scoped></style>
