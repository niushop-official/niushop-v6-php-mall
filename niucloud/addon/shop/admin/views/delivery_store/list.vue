<template>
    <div class="main-container">
       <el-card class="box-card !border-none" shadow="never">
            <div class="flex justify-between items-center">
                <span class="right">{{ pageName }}</span>
                <el-button type="primary" @click="addEvent">{{ t('添加提货点') }}</el-button>
            </div>
            <el-card class="box-card !border-none my-[10px] table-search-wrap" shadow="never">
                <el-form :inline="true" :model="deliveryStoreTable.searchParam" ref="searchFormRef">
                    <el-form-item :label="t('提货点名称')" prop="store_name">
                        <el-input v-model.trim="deliveryStoreTable.searchParam.store_name" :placeholder="t('请输入提货点名称')" />
                    </el-form-item>
                    <el-form-item :label="t('提货类型')" prop="pick_up_type">
                        <el-select v-model="deliveryStoreTable.searchParam.pick_up_type" :placeholder="t('请选择提货类型')" clearable>
                            <el-option v-for="(item,key) in pickUpType" :key="key" :label="item" :value="key" />
                        </el-select>
                    </el-form-item>
                    <el-form-item>
                        <el-button type="primary" @click="loadDeliveryStoreList()">{{ t('search') }}</el-button>
                        <el-button @click="resetForm(searchFormRef)">{{ t('reset') }}</el-button>
                    </el-form-item>
                </el-form>
            </el-card>
            <div class="mt-[10px]">
                <el-table :data="deliveryStoreTable.data" size="large" v-loading="deliveryStoreTable.loading">
                    <template #empty>
                        <span>{{ !deliveryStoreTable.loading ? t('emptyData') : '' }}</span>
                    </template>
                    <el-table-column :label="t('提货点编号')" min-width="200" :show-overflow-tooltip="true">
                        <template #default="{ row }">
                            <el-tag size="small" v-if="row.is_system">{{ t('系统') }}</el-tag>
                            <span class="ml-[8px]">{{row.store_no}}</span>
                       </template>
                    </el-table-column>
                    <el-table-column prop="store_name" :label="t('提货点名称')" min-width="120" />
                    <el-table-column prop="contact_name" :label="t('联系人姓名')" min-width="80" />
                    <el-table-column prop="store_mobile" :label="t('联系人电话')" min-width="80" />
                    <el-table-column prop="full_address" :label="t('提货地址')" min-width="120" />
                    <el-table-column prop="trade_time" :label="t('营业时间')" min-width="120" />
                    <el-table-column prop="pick_up_type_name" :label="t('提货类型')" min-width="120" />
                    <el-table-column :label="t('开启状态')" min-width="120">
                        <template #default="{ row }">
                            <el-tag class="cursor-pointer" :type="row.status ? 'success' : 'danger'" @click="updateStatus(row)">{{ row.status  ? '开启' : '关闭' }}</el-tag>
                        </template>
                    </el-table-column>
                    <el-table-column :label="t('operation')" fixed="right" min-width="120" align="right">
                        <template #default="{ row }">
                            <el-button type="primary" link @click="deliverySetEvent(row)">配送服务商设置</el-button>
                            <el-button type="primary" link @click="editEvent(row)">编辑</el-button>
                            <el-button type="primary" link @click="deleteEvent(row)" v-if="row.is_system == 0">删除</el-button>
                        </template>
                    </el-table-column>
                </el-table>
                <div class="mt-[16px] flex justify-end">
                    <el-pagination v-model:current-page="deliveryStoreTable.page" v-model:page-size="deliveryStoreTable.limit"
                        layout="total, sizes, prev, pager, next, jumper" :total="deliveryStoreTable.total"
                        @size-change="loadDeliveryStoreList()" @current-change="loadDeliveryStoreList" />
                </div>
            </div>
       </el-card>
    </div>
</template>

<script lang="ts" setup>
import { reactive, ref } from 'vue'
import { t } from '@/lang'
import { useRoute, useRouter } from 'vue-router'
import { ElMessageBox, FormInstance } from 'element-plus'
import { getDeliveryStoreList, getStorePickUpType, deleteDeliveryStore, editDeliveryStoreStatus } from '@/addon/shop/api/delivery'

const route = useRoute()
const router = useRouter()
const pageName = route.meta.title

const deliveryStoreTable = reactive({
    page: 1,
    limit: 10,
    total: 0,
    loading: true,
    data: [],
    searchParam: {
        store_name: '',
        pick_up_type: '',
        create_time: ''
    }
})

const searchFormRef = ref<FormInstance>()
const pickUpType = ref([])

const getStorePickUpTypeFn = () => {
    getStorePickUpType().then(res => {
        pickUpType.value = res.data
    })
}
getStorePickUpTypeFn()
/**
 * 获取门店自提列表
 */
const loadDeliveryStoreList = (page: number = 1) => {
    deliveryStoreTable.loading = true
    deliveryStoreTable.page = page

    getDeliveryStoreList({
        page: deliveryStoreTable.page,
        limit: deliveryStoreTable.limit,
        ...deliveryStoreTable.searchParam
    }).then((res: any) => {
        deliveryStoreTable.loading = false
        deliveryStoreTable.data = res.data.data
        deliveryStoreTable.total = res.data.total
    }).catch(() => {
        deliveryStoreTable.loading = false
    })
}
loadDeliveryStoreList()

/**
 * 添加门店
 */
const addEvent = () => {
    router.push('/shop/delivery_store/edit')
}
// 配送设置
const deliverySetEvent = (data: any) => {
    router.push('/shop/delivery_store/delivery_set?id=' + data.store_id)
}
// 编辑
const editEvent = (row: any) => {
    router.push({ path: '/shop/delivery_store/edit', query: { store_id: row.store_id } })
}
// 删除
const deleteEvent = (row: any) => {
    ElMessageBox.confirm(t('确定要删除该门店吗'), t('warning'), {
        confirmButtonText: t('confirm'),
        cancelButtonText: t('cancel'),
        type: 'warning'
    }).then(() => {
        deleteDeliveryStore(row.store_id).then(() => {
            loadDeliveryStoreList()
        })
    }).catch(() => {})
}
// 更新状态
const updateStatus = (row: any) => {
    row.status = row.status ? 0 : 1
    editDeliveryStoreStatus({
        store_id: row.store_id,
        status: row.status
    }).then(() => {

    })
}

const resetForm = (formEl: FormInstance | undefined) => {
    if (!formEl) return
    formEl.resetFields()
    loadDeliveryStoreList()
}
</script>