<template>
    <div class="main-container">
        <!-- 添加优惠券按钮 -->
        <el-card class="box-card !border-none" shadow="never">

            <div class="flex justify-between items-center">
                <span class="text-page-title">{{ pageName }}</span>
                <el-button type="primary" @click="handleChange">
                    {{ t('addCoupon') }}
                </el-button>
            </div>

             <!-- 搜索 -->
            <el-card class="box-card !border-none my-[20px] table-search-wrap" shadow="never">
                <el-form :inline="true" :model="tableData.searchParam" ref="searchFormRef">
                    <el-form-item :label="t('title')" prop="title">
                        <el-input v-model.trim="tableData.searchParam.title" :placeholder="t('titlePlaceholder')" />
                    </el-form-item>

                    <el-form-item>
                        <el-button type="primary" @click="loadCouponList()">{{ t('search') }}</el-button>
                        <el-button @click="resetForm(searchFormRef)">{{ t('reset') }}</el-button>
                    </el-form-item>
                </el-form>
            </el-card>

             <!-- 列表 -->
            <div class="mt-[10px]">
                <el-table :data="tableData.data" size="large" v-loading="tableData.loading">
                    <template #empty>
                        <span>{{ !tableData.loading ? t('emptyData') : '' }}</span>
                    </template>

                    <el-table-column prop="title" :label="t('title')" min-width="130" />
                    <el-table-column prop="type_name" :label="t('type')" min-width="130" />
                    <el-table-column prop="price" :label="t('price')" min-width="130" >
                        <template #default="{ row }">
                            <span >¥{{row.price}}</span>
                        </template>
                    </el-table-column>

                    <el-table-column  :label="t('receiveType')" min-width="130" >
                        <template #default="{ row }">
                            <span v-if="row.receive_type == 1">是</span>
                            <span v-else >否</span>
                        </template>
                    </el-table-column>

                    <el-table-column  :label="t('sumCount')" min-width="160">
                        <template #default="{ row }">
                            <span v-if="row.receive_type == 1 && row.sum_count != '-1'">{{ row.remain_count || '' }} / {{ row.sum_count || '' }}</span>
                            <span v-else>不限量</span>
                        </template>
                    </el-table-column>
                    <el-table-column  :label="t('remainCount')" min-width="140">
                        <template #default="{ row }">
                            <span >{{ row.receive_count || 0}} / {{ row.receive_use_count || 0}} </span>
                        </template>
                    </el-table-column>
                    <el-table-column  :label="t('threshold')" min-width="130" >
                        <template #default="{ row }">
                            <span v-if="row.min_condition_money == '0.00'">无门槛</span>
                            <span v-else >满{{ row.min_condition_money }}元可用</span>
                        </template>
                    </el-table-column>
                    <el-table-column  :label="t('validType')" min-width="210">
                        <template #default="{ row }">
                            <template v-if="row.receive_type == 1">
                                <span v-if="row.valid_type == 1">  领取之日起{{ row.length || '' }} 天内有效</span>
                                <span v-else> 使用截止时间至{{ row.valid_end_time || ''}} </span>
                            </template>
                            <span v-else>--</span>
                        </template>
                    </el-table-column>
                    <el-table-column  :label="t('receiveTypeTime')" min-width="210">
                        <template #default="{ row }">
                            <div v-if="row.receive_type == 1">
                                <div v-if="parseInt(row.start_time) != 0 && parseInt(row.end_time) !=0" class="flex flex-col">
                                    <span>开始时间：{{row.start_time}}</span>
                                    <span>结束时间：{{row.end_time}}</span>
                                </div>
                                <div v-else>不限时</div>
                            </div>
                            <span v-else>--</span>
                        </template>
                    </el-table-column>
                    <el-table-column prop="status_name" :label="t('statusName')" min-width="130" />
                    <el-table-column :label="t('operation')" fixed="right" align="right" min-width="160">
                       <template #default="{ row }">
                        <el-button type="primary" link @click="spreadEvent(row)">{{ t('spreadGoods') }}</el-button>
                            <el-button type="primary" link @click="editEvent(row)" v-if="row.status == 1">{{ t('edit') }}</el-button>
                            <el-button type="primary" link @click="deleteEvent(row)" v-if="row.status != 1">{{ t('delete') }}</el-button>
                            <el-button type="primary" link @click="closeEvent(row)" v-if="row.status == 1">{{ t('close') }}</el-button>
                            <el-button type="primary" link @click="collectionEvent(row)">{{ t('receive') }}</el-button>
                        </template>
                    </el-table-column>
                </el-table>
                <div class="mt-[16px] flex justify-end">
                    <el-pagination v-model:current-page="tableData.page" v-model:page-size="tableData.limit"
                        layout="total, sizes, prev, pager, next, jumper" :total="tableData.total"
                        @size-change="loadCouponList()" @current-change="loadCouponList" />
                </div>
            </div>
        </el-card>
        <!-- 商品推广弹出框 -->
        <coupon-spread-popup ref="couponSpreadPopupRef"/>
    </div>
</template>

<script lang="ts" setup>
import { reactive, ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { getCouponList, deleteCoupon, closeCoupon } from '@/addon/shop/api/marketing'
import { ElMessageBox, FormInstance } from 'element-plus'
import { t } from '@/lang'
import couponSpreadPopup from '@/addon/shop/views/marketing/coupon/components/coupon-spread-popup.vue'

const router = useRouter()
const route = useRoute()
const pageName = route.meta.title

// 表单内容
const tableData = reactive({
    page: 1,
    limit: 10,
    total: 0,
    loading: true,
    data: [],
    searchParam: {
        title: ''
    }
})

const searchFormRef = ref<FormInstance>()

const loadCouponList = (page: number = 1) => {
    tableData.loading = true
    tableData.page = page

    getCouponList({
        page: tableData.page,
        limit: tableData.limit,
        ...tableData.searchParam
    }).then(res => {
        tableData.loading = false
        tableData.data = res.data.data
        tableData.total = res.data.total
    }).catch(() => {
        tableData.loading = false
    })
}
loadCouponList()

// 商品推广
const couponSpreadPopupRef: any = ref(null)

const spreadEvent = (data: any) => {
    couponSpreadPopupRef.value.show(data)
}
// 添加优惠券
const handleChange = () => {
    router.push('/shop/marketing/coupon/add')
}
// 编辑优惠券
const editEvent = (data: any) => {
    router.push('/shop/marketing/coupon/edit?id=' + data.id)
}

// 领取记录
const collectionEvent = (data: any) => {
    router.push('/shop/marketing/coupon/collection?id=' + data.id)
}

/**
 * 删除
 */
const deleteEvent = (data: any) => {
    ElMessageBox.confirm(t('couponDeleteTips'), t('warning'),
        {
            confirmButtonText: t('confirm'),
            cancelButtonText: t('cancel'),
            type: 'warning'
        }
    ).then(() => {
        deleteCoupon(data.id).then(() => {
            loadCouponList()
        }).catch(() => {
        })
    })
}

// 关闭
const closeEvent = (data: any) => {
    ElMessageBox.confirm(t('couponColseTips'), t('warning'),
        {
            confirmButtonText: t('confirm'),
            cancelButtonText: t('cancel'),
            type: 'warning'
        }
    ).then(() => {
        closeCoupon(data.id).then(() => {
            loadCouponList()
        }).catch(() => {
        })
    })
}

const resetForm = (formEl: FormInstance | undefined) => {
    if (!formEl) return
    formEl.resetFields()
    loadCouponList()
}

</script>

<style lang="scss" scoped></style>
