<template>
    <el-dialog v-model="showDialog" :title="t('delivery')" width="700px" class="diy-dialog-wrap" :destroy-on-close="true">
        <div v-loading="loading">

            <el-alert type="warning" :closable="false" class="!mb-[10px]" v-if="isTradeManaged">
                <template #default>
                    <p>您已开通微信小程序发货信息管理服务，平台会将发货信息以消息的形式推送给购买的微信用户。</p>
                    <p>注意：每个订单只能更新一次发货信息，请谨慎操作！</p>
                </template>
            </el-alert>
            <el-form :model="formData" label-width="100px" ref="formRef" :rules="formRules" class="page-form mb-[30px]">
                <el-form-item :label="t('deliveryType')" prop="delivery_type">
                    <el-radio-group v-model="formData.delivery_type" @change="deliveryChange">
                        <el-radio :label="index" v-for="(item, index) in deliveryType" :key="index">{{ item }}</el-radio>
                    </el-radio-group>
                </el-form-item>
                <el-form-item :label="t('company')" v-if="formData.delivery_type == 'express'" prop="express_company_id">
                    <el-select v-model="formData.express_company_id" :placeholder="t('companyPlaceholder')" class="input-width">
                        <el-option v-for="(item) in companyData" :key="item.index" :label="item.company_name" :value="item.company_id" />
                    </el-select>
                </el-form-item>
                <el-form-item :label="t('expressNumber')" v-if="formData.delivery_type == 'express'" prop="express_number">
                    <el-input v-model.trim="formData.express_number" clearable :placeholder="t('expressNumberPlaceholder')" class="input-width" maxlength="30" />
                </el-form-item>
            </el-form>
            <el-table :data="goodsDataArr" size="large" @selection-change="handleSelectionChange">
                <el-table-column type="selection" width="55" :selectable="selectable" />
                <el-table-column prop="goods_name" :label="t('goodsName')" min-width="200" >
                    <template #default="{ row }">
                        <div class="flex cursor-pointer">
                            <div class="flex items-center min-w-[50px] mr-[10px]">
                                <img class="w-[50px] h-[50px]" v-if="row.goods_image" :src="img(row.goods_image)" alt="">
                                <img class="w-[50px] h-[50px]" v-else src="" alt="">
                            </div>
                            <div class="flex  flex-col">
                                <span class="multi-hidden text-[14px]">{{row.goods_name}}</span>
                                <span class="text-[#999] text-[12px]" v-if="row.sku_name">{{row.sku_name}}</span>
                            </div>
                        </div>
                    </template>
                </el-table-column>
                <el-table-column prop="num" :label="t('num')" min-width="80" />
                <el-table-column prop="status_name" :label="t('refundStatusName')" min-width="80" />
                <el-table-column prop="delivery_status_name" :label="t('deliveryStatusName')" min-width="80" align="right" />
            </el-table>
        </div>
        <template #footer>
            <span class="dialog-footer">
                <el-button @click="showDialog = false">{{ t('cancel') }}</el-button>
                <el-button type="primary" :loading="loading" @click="confirm(formRef)">{{ t('confirm') }}</el-button>
            </span>
        </template>
    </el-dialog>
</template>

<script lang="ts" setup>
import { ref, reactive, computed } from 'vue'
import { t } from '@/lang'
import { img } from '@/utils/common'
import { FormInstance, ElMessage } from 'element-plus'
import { getCompanyList } from '@/addon/shop/api/delivery'
import { getOrderDeliveryType, orderDelivery } from '@/addon/shop/api/order'
import { getIsTradeManaged } from '@/app/api/weapp'
import { cloneDeep } from 'lodash-es'

const showDialog = ref(false)
const loading = ref(false)
interface companyDataType{
    company_id: number,
    company_name: string,
    index: number
}
const companyData = ref<companyDataType[]>([])
const isHasVirtual = ref(false)
const deliveryType = ref([])
const isTradeManaged = ref(false)

getCompanyList({}).then((data) => {
    companyData.value = data.data
})

getIsTradeManaged().then(res=>{
    isTradeManaged.value = res.data.is_trade_managed;
})

/**
 * 表单数据
 */
const goodsData = ref([])
const initialFormData = {
    order_id: 0,
    delivery_type: '',
    express_company_id: '',
    express_number: '',
    order_goods_ids: []
}
const formData: Record<string, any> = reactive({ ...initialFormData })

const formRef = ref<FormInstance>()

// 表单验证规则
const formRules = computed(() => {
    return {
        delivery_type: [
            { required: true, message: t('deliveryTypePlaceholder'), trigger: 'blur' }
        ],
        express_company_id: [
            { required: true, validator: companyPass, trigger: 'blur' }
        ],
        express_number: [
            { required: true, validator: expressNumberPass, trigger: 'blur' }
        ]
    }
})

const companyPass = (rule: any, value: any, callback: any) => {
    if (formData.delivery_type == 'express' && value === '') {
        callback(new Error(t('companyPlaceholder')))
    } else {
        callback()
    }
}

const expressNumberPass = (rule: any, value: any, callback: any) => {
    if (formData.delivery_type == 'express' && value === '') {
        callback(new Error(t('expressNumberPlaceholder')))
    } else {
        callback()
    }
}

const selectable = (row:any, index:number) => {
    if (row.status == 2 || row.delivery_status == 'delivery_finish') {
        return false
    }
    return true
}
interface goodsDataType{
    goods_type:string
}
const goodsDataArr = ref<goodsDataType[]>([])
const deliveryChange = () => {
    let arr: any = []
    if (formData.delivery_type && formData.delivery_type == 'virtual') {
        goodsData.value.forEach((item: any, index) => {
            if (item.goods_type == 'virtual') {
                arr.push(item);
            }
        })
    } else if (formData.delivery_type && formData.delivery_type != 'virtual') {
        goodsData.value.forEach((item: any, index) => {
            if (item.goods_type != 'virtual') {
                arr.push(item);
            }
        })
    }
    goodsDataArr.value = cloneDeep(arr)
}

const handleSelectionChange = (val:any) => {
    formData.order_goods_ids = cloneDeep([])
    for (const v in val) {
        formData.order_goods_ids.push(val[v].order_goods_id)
    }
}

const emit = defineEmits(['complete'])

/**
 * 确认
 * @param formEl
 */
const confirm = async (formEl: FormInstance | undefined) => {
    if (loading.value || !formEl) return
    if (formData.order_goods_ids.length <= 0) {
        ElMessage({
            message: t('orderGoodsPlaceholder'),
            type: 'warning'
        })
        return
    }

    await formEl.validate(async (valid) => {
        if (valid) {
            loading.value = true
            const data = formData
            orderDelivery(data).then(res => {
                loading.value = false
                showDialog.value = false
                emit('complete')
                initFormData();
            }).catch(err => {
                loading.value = false
                showDialog.value = false
                initFormData();
            })
        }
    })
}

const setFormData = async (row: any = null) => {
    loading.value = true
    if (row) {
        formData.order_id = row.order_id
        formData.delivery_type = ''
        goodsData.value = row.order_goods
        goodsDataArr.value = row.order_goods
        isHasVirtual.value = false
        await getOrderDeliveryType({
            delivery_type: row.delivery_type
        }).then((data) => {
            deliveryType.value = data.data
            for (const v in data.data) {
                formData.delivery_type = v
                break
            }
            deliveryChange()
        })

        for (let i = 0; i < row.order_goods.length; i++) {
            if (row.order_goods[i].goods_type == 'virtual') {
                isHasVirtual.value = true
                break
            }
        }
        if (isHasVirtual.value == true) {
            Object.assign(deliveryType.value, { virtual: t('virtualDelivery') })
        }
    }
    loading.value = false
}

const initFormData = ()=> {
    formData.order_id = 0;
    formData.delivery_type = '';
    formData.express_company_id = '';
    formData.express_number = '';
    formData.order_goods_ids = [];
}

defineExpose({
    showDialog,
    setFormData
})
</script>

<style lang="scss" scoped></style>
<style lang="scss">
.diy-dialog-wrap .el-form-item__label {
    height: auto !important;
}
</style>
