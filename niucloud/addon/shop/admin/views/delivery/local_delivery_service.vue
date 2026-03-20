<template>
    <!--三方配送设置-->
    <div class="main-container">
        <el-card class="box-card !border-none" shadow="never">

            <div class="flex justify-between items-center">
                <span class="text-page-title">{{ pageName }}</span>
            </div>

            <div class="mt-[20px]">
                <el-table :data="thirdDeliveryTableData.data" size="large" v-loading="thirdDeliveryTableData.loading">
                    <template #empty>
                        <span>{{ !thirdDeliveryTableData.loading ? t('emptyData') : '' }}</span>
                    </template>

                    <el-table-column prop="name" :label="t('name')" min-width="100" :show-overflow-tooltip="true"/>
                    <el-table-column :label="t('isUse')" min-width="180" align="center">
                        <template #default="{ row }">
                            <el-tag class="ml-2" :type="row.is_use == 1 ? 'success': 'error'">{{ row.is_use == 1 ?  t('statusNormal') : t('statusDeactivate')}}</el-tag>
                        </template>
                    </el-table-column>
                    <el-table-column :label="t('operation')" align="right" fixed="right" min-width="200">
                       <template #default="{ row, $index }">
                            <template v-if="row.is_merchant">
                                <el-button type="primary" link @click="toStaff">配送员</el-button>
                                <el-button type="primary" link @click="toRecord">配送记录</el-button>
                            </template>
                           <el-button type="primary" link @click="editEvent(row, $index)" v-if="!row.is_merchant">{{ t('config') }}</el-button>
                       </template>
                    </el-table-column>

                </el-table>
            </div>

            <template v-for="(item, index) in thirdDeliveryTableData.data">
                <component :is="item.component" :ref="(el) => setThirdDeliveryTypeRefs(el, index)" v-if="item.component" @complete="loadThirdDeliveryList()"/>
            </template>
        </el-card>
    </div>
</template>

<script lang="ts" setup>
import { defineAsyncComponent, reactive, ref } from 'vue'
import { t } from '@/lang'
import { getLocalDeliveryServiceList } from '@/addon/shop/api/delivery'
import { useRoute, useRouter } from 'vue-router'

const route = useRoute()
const router = useRouter()
const pageName = route.meta.title
const thirdDeliveryTypeRefs = ref([])

const thirdDeliveryTableData = reactive({
    loading: true,
    data: []
})

const modules: any = import.meta.glob('@/addon/**/views/delivery/components/*.vue')
/**
 * 获取配置信息
 */
const loadThirdDeliveryList = () => {
    thirdDeliveryTableData.loading = true
    getLocalDeliveryServiceList().then(({ data }) => {
        Object.keys(data).forEach((key: string) => {
            data[key].component && (data[key].component = defineAsyncComponent(modules[data[key].component]))
        })
        thirdDeliveryTableData.data = data
        thirdDeliveryTableData.loading = false
    }).catch(() => {
        thirdDeliveryTableData.loading = false
    })
}

const setThirdDeliveryTypeRefs = (el, index) => {
    thirdDeliveryTypeRefs.value[index] = (el)
}

loadThirdDeliveryList()
const editEvent = (data: any, index: number) => {
    thirdDeliveryTypeRefs.value[index].setFormData(data)
    thirdDeliveryTypeRefs.value[index].showDialog = true
}

const toStaff = () => {
    router.push('/shop/delivery/staff')
}
const toRecord = () => {
    router.push('/shop/delivery/merchant/record')
}
</script>

<style lang="scss" scoped></style>
