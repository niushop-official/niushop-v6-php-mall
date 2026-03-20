<template>
    <el-dialog v-model="showDialog" :title="showType == 'add' ? t('delivery') : t('修改物流信息')" :width="showType =='add' ? '700px' :'1000px'" class="diy-dialog-wrap" :destroy-on-close="true" :close-on-click-modal="false" @close="handleClose">
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
                <template v-if="formData.delivery_type == 'express'">
                    <el-form-item :label="t('deliveryWay')">
                        <el-radio-group v-model="formData.delivery_way">
                            <el-radio label="manual_write">{{ t('manualWriteWay') }}</el-radio>
                            <el-radio label="electronic_sheet">{{ t('electronicSheetWay') }}</el-radio>
                        </el-radio-group>
                    </el-form-item>
                    <el-form-item :label="t('company')" prop="express_company_id">
                        <el-select v-model="formData.express_company_id" :placeholder="t('companyPlaceholder')" class="input-width" @change="companyChange">
                            <el-option v-for="(item) in companyData" :key="item.index" :label="item.company_name" :value="item.company_id" />
                        </el-select>
                    </el-form-item>
                    <el-form-item :label="t('expressNumber')" v-if="formData.delivery_way == 'manual_write'" prop="express_number">
                        <el-input v-model.trim="formData.express_number" clearable :placeholder="t('expressNumberPlaceholder')" class="input-width" maxlength="30" />
                    </el-form-item>
                    <el-form-item :label="t('electronicSheetTemplate')" v-if="formData.delivery_way == 'electronic_sheet'" prop="electronic_sheet_id">
                        <el-select v-model="formData.electronic_sheet_id" :placeholder="t('electronicSheetTemplatePlaceholder')" clearable class="input-width">
                            <el-option v-for="(item) in electronicSheetData" :key="item.id" :label="item.template_name" :value="item.id" />
                        </el-select>
                    </el-form-item>
                </template>
                <template v-if="formData.delivery_type == 'local_delivery'">
                    <el-form-item :label="t('deliveryWay')">
                        <el-radio-group v-model="formData.delivery_way" @change="deliveryWayChange">
                            <el-radio label="store_delivery">{{ t('storeDeliveryWay') }}</el-radio>
                            <el-radio label="third_party_delivery">{{ t('thirdPartyDeliveryWay') }}</el-radio>
                        </el-radio-group>
                    </el-form-item>
                    <div v-if="formData.delivery_way == 'store_delivery'">
                        <el-form-item :label="t('deliveryStore')" prop="store_id">
                            <el-select v-model="formData.store_id" :placeholder="t('deliveryStorePlaceholder')" clearable class="input-width">
                                <el-option v-for="(item) in deliveryStoreData" :key="item.store_id" :label="item.store_name" :value="item.store_id" />
                            </el-select>
                        </el-form-item>
                        <el-form-item :label="t('deliverName')" prop="local_deliver_id">
                            <el-select v-model="formData.local_deliver_id" :placeholder="t('deliverNamePlaceholder')" clearable class="input-width" @change="deliverNameChange">
                                <el-option v-for="(item) in deliverData" :key="item.deliver_id" :label="item.deliver_name" :value="item.deliver_id" />
                            </el-select>
                        </el-form-item>
                        <el-form-item :label="t('deliverMobile')" prop="deliver_mobile">
                            <el-input v-model.trim="formData.deliver_mobile" clearable :disabled="true" :placeholder="t('deliverMobilePlaceholder')" class="input-width" maxlength="30" />
                        </el-form-item>
                    </div>
                    <div v-if="formData.delivery_way == 'third_party_delivery'">
                        <el-form-item :label="t('deliveryStore')" prop="store_id">
                            <div>
                                <el-select v-model="formData.store_id" :placeholder="t('deliveryStorePlaceholder')" clearable class="input-width" @change="deliveryStoreChange">
                                    <el-option v-for="(item) in deliveryStoreData" :key="item.store_id" :label="item.store_name" :value="item.store_id" />
                                </el-select>
                                <span class="ml-[10px] cursor-pointer text-primary" @click="toAddress" v-if="curThirdDelivery?.open_status == 2">去开通</span>
                            </div>
                        </el-form-item>
                        <el-form-item :label="t('thirdDeliveryName')" prop="local_delivery_type">
                            <div class="flex items-center">
                                <el-select v-model="formData.local_delivery_type" :placeholder="t('thirdDeliveryNamePlaceholder')" clearable class="input-width" @change="thirdDeliveryChange">
                                    <el-option v-for="(item,index) in thirdDeliveryData" :key="index" :label="item.name" :value="item.key" />
                                </el-select>
                                <div class="ml-[10px]">
                                    <span class="cursor-pointer text-primary mr-[10px]" @click="getInUseLocalDeliveryListFn(formData.store_id)">{{ t('refresh') }}</span>
                                    <span class="cursor-pointer text-primary" @click="toThirdDelivery">{{ t('去配置') }}</span>
                                </div>
                            </div>
                        </el-form-item>
                        <el-form-item :label="t('goodsWeight')" prop="goods_weight">
                            <el-input v-model.trim="formData.goods_weight" clearable placeholder="0.000" class="input-width" maxlength="6" @blur="thirdDeliveryChange">
                                <template #append>kg</template>
                            </el-input>
                        </el-form-item>
                        <el-form-item :label="t('配送费用')" >
                           <div class="input-width">{{ deliveryFee }}元</div>
                        </el-form-item>
                    </div>
                </template>
            </el-form>
            <el-table :data="goodsDataArr" size="large" @selection-change="handleSelectionChange" ref="tableRef" max-height="400px">
                <el-table-column type="selection" width="55" :selectable="selectable" />
                <el-table-column prop="goods_name" :label="t('goodsName')" min-width="200" >
                    <template #default="{ row }">
                        <div class="flex cursor-pointer">
                            <div class="flex items-center min-w-[50px] mr-[10px]">
                                <img class="w-[50px] h-[50px]" v-if="row.goods_image" :src="img(row.goods_image)" alt="">
                                <img class="w-[50px] h-[50px]" v-else src="" alt="">
                            </div>
                            <div class="flex  flex-col items-start">
                                <span class="multi-hidden text-[14px]">{{row.goods_name}}</span>
                                <span class="text-[#999] text-[12px]" v-if="row.sku_name">{{row.sku_name}}</span>
                                <span class="px-[4px]  text-[12px] text-[#fff] rounded-[4px] bg-primary leading-[18px]" v-if="row.is_gift == 1">赠品</span>
                            </div>
                        </div>
                    </template>
                </el-table-column>
                <el-table-column prop="num" :label="t('num')" min-width="80" />
                <el-table-column prop="status_name" :label="t('refundStatusName')" min-width="80" />
                <el-table-column prop="delivery_status_name" :label="t('deliveryStatusName')" min-width="80" />
                 <el-table-column  :label="t('deliveryPlatform')" min-width="80" v-if="showType == 'edit' && formData.delivery_type == 'local_delivery'">
                    <template #default="{ row }">
                        <span>{{row.delivery_info?.delivery_service_name }}</span>
                    </template>
                </el-table-column>
                <el-table-column prop="local_delivery_status_name" :label="t('deliveryStatus')" min-width="80" v-if="showType == 'edit' && formData.delivery_type == 'local_delivery'">
                    <template #default="{ row }">
                        <span>{{ row.delivery_info?.local_delivery_status_name }}</span>
                    </template>
                </el-table-column>
                <el-table-column prop="express_company_name" :label="t('物流公司')" min-width="80" v-if="showType == 'edit' && formData.delivery_type == 'express'">
                    <template #default="{ row }">
                        <span class="text-[#999]">{{ row.delivery_info?.express_company_name }}</span>
                    </template>
                </el-table-column>
                <el-table-column prop="express_number" :label="t('物流单号')" min-width="80" v-if="showType == 'edit' && formData.delivery_type == 'express'">
                    <template #default="{ row }">
                        <span class="text-[#999]">{{ row.delivery_info?.express_number }}</span>
                    </template>
                </el-table-column>
            </el-table>
        </div>
        <template #footer>
            <span class="dialog-footer">
                <el-button @click="handleClose">{{ t('cancel') }}</el-button>
                <el-button type="primary" :loading="loading" @click="confirm(formRef)">{{ t('confirm') }}</el-button>
            </span>
        </template>
    </el-dialog>
</template>

<script lang="ts" setup>
import { ref, reactive, computed, nextTick } from 'vue'
import { t } from '@/lang'
import { img } from '@/utils/common'
import { FormInstance, ElMessage } from 'element-plus'
import { getCompanyList, getShopDeliverList, getInUseLocalDeliveryList, getDeliveryStoreListAll } from '@/addon/shop/api/delivery'
import { getOrderDeliveryType, orderDelivery, getSelectOrderGoodsWeight, getDeliveryFee } from '@/addon/shop/api/order'
import { getIsTradeManaged } from '@/app/api/weapp'
import { cloneDeep } from 'lodash-es'
import { getElectronicSheetConfig, getElectronicSheetList, printElectronicSheet } from '@/addon/shop/api/electronic_sheet'
import { loadCLodop, getLodop } from '@/utils/lodop'
import { useRouter } from 'vue-router'

const router = useRouter()
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

getIsTradeManaged().then(res => {
    isTradeManaged.value = res.data.is_trade_managed
})

getElectronicSheetConfig().then((res:any) => {
    if (res.data) {
        loadCLodop(res.data)
    }
})

const electronicSheetAll: any = ref([])
const electronicSheetData: any = ref([])
getElectronicSheetList({
    status: 1
}).then((res: any) => {
    if (res.data) {
        electronicSheetAll.value = res.data
    }
})

const companyChange = (value: any) => {
    electronicSheetData.value = []
    formData.electronic_sheet_id = ''
    electronicSheetAll.value.forEach((item: any) => {
        if (item.express_company_id == value) {
            electronicSheetData.value.push(cloneDeep(item))
        }
    })
}

/**
 * 配送员
 */
const deliverNameChange = (value: any) => {
    if (value) {
        deliverData.value.forEach((item: any) => {
            if (item.deliver_id == value) {
                formData.deliver_mobile = item.deliver_mobile
            }
        })
    } else {
        formData.deliver_mobile = ''
        formData.local_deliver_id = ''
    }
}

const deliverData = ref([])
getShopDeliverList({}).then((res: any) => {
    deliverData.value = res.data
})

/**
 * 三方配送列表
 */
const thirdDeliveryData = ref([])
const getInUseLocalDeliveryListFn = (value: any) => {
    getInUseLocalDeliveryList({store_id: value}).then((res: any) => {
        thirdDeliveryData.value = res.data
    })
}

/**
 * 获取提货点列表
 */
const deliveryStoreData = ref([])
getDeliveryStoreListAll({
    pick_up_type: 'local_delivery',
}).then((res: any) => {
    deliveryStoreData.value = res.data
})

const deliveryStoreChange = (value: any) => {
    if (formData.delivery_way == 'third_party_delivery' && value) {
        formData.local_delivery_type = ''
        deliveryFee.value = 0
        getInUseLocalDeliveryListFn(value)
    } else {
        thirdDeliveryData.value = []
    }
}

/**
 * 表单数据
 */
const goodsData = ref([])
const initialFormData = {
    order_id: 0,
    delivery_type: '',
    delivery_way: 'manual_write',
    local_delivery_type: 'merchant',
    goods_weight: 1,
    local_deliver_id: '',
    deliver_mobile: '',
    express_company_id: '',
    express_number: '',
    electronic_sheet_id: '',
    order_goods_ids: [],
    delivery_ids: [],
    store_id: ''
}

const formData: Record<string, any> = reactive({ ...initialFormData })

const formRef = ref<FormInstance>()

// 正则表达式
const regExp: any = {
    required: /[\S]+/,
    number: /^\d{0,10}$/,
    digit: /^\d{0,10}(.?\d{0,2})$/,
    special: /^\d{0,10}(.?\d{0,3})$/
}

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
        ],
        electronic_sheet_id: [
            { required: true, validator: electronicSheetIdPass, trigger: 'blur' }
        ],
        local_deliver_id: [
            { required: true, validator: localDeliverIdPass, trigger: 'blur' }
        ],
        store_id: [
            { required: true, validator: storeIdPass, trigger: 'blur' }
        ],
        local_delivery_type: [
            { required: true, validator: thirdDeliveryTypePass, trigger: ['blur', 'change'] }
        ],
        goods_weight: [
            { required: true, validator: goodsWeightPass, trigger: 'blur' }
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
    if (formData.delivery_type == 'express' && formData.delivery_way == 'manual_write' && value === '') {
        callback(new Error(t('expressNumberPlaceholder')))
    } else {
        callback()
    }
}

const electronicSheetIdPass = (rule: any, value: any, callback: any) => {
    if (formData.delivery_type == 'express' && formData.delivery_way == 'electronic_sheet' && value === '') {
        callback(new Error(t('electronicSheetTemplatePlaceholder')))
    } else {
        callback()
    }
}

const localDeliverIdPass = (rule: any, value: any, callback: any) => {
    if (formData.delivery_type == 'local_delivery' && formData.delivery_way == 'store_delivery' && value == '') {
        callback(new Error(t('deliverNamePlaceholder')))
    } else {
        callback()
    }
}

const storeIdPass = (rule: any, value: any, callback: any) => {
    if (formData.delivery_type == 'local_delivery' && value == '') {
        callback(new Error(t('deliveryStorePlaceholder')))
    } else {
        callback()
    }
}

const thirdDeliveryTypePass = (rule: any, value: any, callback: any) => {
    if (formData.delivery_type == 'local_delivery' && formData.delivery_way == 'third_party_delivery' && (value == '' || value == undefined)) {
        callback(new Error(t('thirdDeliveryNamePlaceholder')))
    } else {
        callback()
    }
}

const goodsWeightPass = (rule: any, value: any, callback: any) => {
    if (formData.delivery_type == 'local_delivery' && formData.delivery_way == 'third_party_delivery' && value == '') {
        callback(new Error(t('goodsWeightPlaceholder')))
    } else if (isNaN(value) || !regExp.special.test(value)) {
        callback(new Error(t('weightTips')))
    } else if (value <= 0) {
        callback(new Error(t('weightNotZeroTips')))
    } else {
        callback()
    }
}

const selectable = (row:any, index:number) => {
    if ((row.status == 2 || row.delivery_status != 'wait_delivery' || row.status == 3 || row.is_gift == 1) && (showType.value == 'add' || !showType.value)) {
        return false
    }
    if (row.delivery_info && row.delivery_info.delivery_service == 'dada' && (row.delivery_info.local_delivery_status == '-2' || row.delivery_info.local_delivery_status == '-1' || row.delivery_info.local_delivery_status == '8')) {
        return false
    }
    if (formData.delivery_type == 'local_delivery') {
        return false
    }
    return true
}
interface goodsDataType{
    goods_type:string
}
const goodsDataArr = ref<goodsDataType[]>([])
const deliveryChange = () => {
    const arr: any = []
    if (formData.delivery_type && formData.delivery_type == 'virtual') {
        goodsData.value.forEach((item: any, index) => {
            if (item.goods_type == 'virtual') {
                arr.push(item)
            }
        })
    } else if (formData.delivery_type && formData.delivery_type != 'virtual') {
        goodsData.value.forEach((item: any, index) => {
            if (item.goods_type != 'virtual') {
                arr.push(item)
            }
        })
    }
    goodsDataArr.value = cloneDeep(arr)
    if (formData.delivery_type && formData.delivery_type == 'express') {
        formData.delivery_way = 'manual_write'
    } else if (formData.delivery_type && formData.delivery_type == 'local_delivery') {
        formData.delivery_way = 'store_delivery'
    }
}

const deliveryWayChange = () => {
    curThirdDelivery.value = {}
    if (formData.delivery_way && formData.delivery_way == 'store_delivery') {
        formData.local_delivery_type = 'merchant'
    } else {
        formData.local_delivery_type = ''
    }
}

const handleSelectionChange = (val:any) => {
    formData.order_goods_ids = cloneDeep([])
    formData.delivery_ids = cloneDeep([])
    for (const v in val) {
        formData.order_goods_ids.push(val[v].order_goods_id)
        if (showType.value == 'edit') {
            formData.delivery_ids.push(val[v].delivery_id)
        }
    }
}

const getSelectOrderGoodsWeightFn = () => {
    getSelectOrderGoodsWeight({
        order_goods_ids: formData.order_goods_ids
    }).then((res: any) => {
        formData.goods_weight = res.data
    })
}

// 计算配送费用
const deliveryFee = ref<any>(0)
const getDeliveryFeeFn = () => {
    getDeliveryFee(formData).then((res: any) => {
        deliveryFee.value = res.data
    })
}
const curThirdDelivery = ref<any>(null)
const thirdDeliveryChange = (value: any) => {
    if (formData.delivery_way == 'third_party_delivery' && value) {
        curThirdDelivery.value = thirdDeliveryData.value?.find((item: any) => item.key == value)
        getDeliveryFeeFn()
    } else {
        deliveryFee.value = 0
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
                if (formData.delivery_type == 'express' && formData.delivery_way == 'electronic_sheet') {
                    // 打印电子面单
                    printElectronicSheetFn()
                }
                loading.value = false
                showDialog.value = false
                emit('complete')
                initFormData()
            }).catch(() => {
                loading.value = false
            })
        }
    })
}
const showType = ref('')
const tableRef = ref()
const setFormData = async (row: any = null,type:any) => {
    loading.value = true
    showType.value = type
    if (row) {
        formData.order_id = row.order_id
        formData.delivery_type = ''
        formData.delivery_way = 'manual_write'
        formData.local_delivery_type = 'merchant'
        formData.store_id = row.take_store_id
        formData.local_deliver_id = row.order_goods[0]?.delivery_info?.local_deliver_id
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
        if (isHasVirtual.value) {
            Object.assign(deliveryType.value, { virtual: t('virtualDelivery') })
        }
        if (formData.delivery_type == 'local_delivery') {
            nextTick(() => {
                if (tableRef.value) {
                    if (row.status == 2) {
                        goodsDataArr.value.forEach((item: any, index: any) => {
                            tableRef.value.toggleRowSelection(item, true)
                        })
                    } else if (row.status == 3) {
                        goodsDataArr.value.forEach((item: any, index: any) => {
                            if (item.delivery_info.local_delivery_status == '-2' || item.delivery_info.local_delivery_status == '-1' || item.delivery_info.local_delivery_status == '8') {
                                tableRef.value.toggleRowSelection(item, true)
                            }
                        })
                    }
                }
                getSelectOrderGoodsWeightFn()
                getInUseLocalDeliveryListFn(formData.store_id)
            })
        }
    }
    loading.value = false
}

const initFormData = () => {
    formData.order_id = 0
    formData.delivery_type = ''
    formData.delivery_way = 'manual_write'
    formData.local_delivery_type = 'merchant'
    formData.store_id = ''
    formData.local_deliver_id = ''
    formData.express_company_id = ''
    formData.electronic_sheet_id = ''
    formData.express_number = ''
    formData.order_goods_ids = []
}

const printElectronicSheetFn = () => {
    const LODOP = getLodop()
    if (!LODOP) return

    printElectronicSheet({
        print_type: 'multiple',
        order_id: formData.order_id,
        electronic_sheet_id: formData.electronic_sheet_id,
        order_goods_ids: formData.order_goods_ids
    }).then((res: any) => {
        if (res.data) {
            const data = res.data
            let haveSuccessData = false
            LODOP.PRINT_INIT('打印电子面单') // 只能初始化一次

            // 单订单打印
            for (let i = 0; i < data.length; i++) {
                if (data[i].success) {
                    // 调用打印机，开始打印
                    const html = data[i].print_template
                    LODOP.ADD_PRINT_HTM(0, 0, '100%', '100%', html)
                    LODOP.NewPage() // 批量打印，分页
                    haveSuccessData = true
                }
            }

            if (haveSuccessData) {
                LODOP.PREVIEW() // 预览
            }
        }
    })
}

const handleClose = () => {
    showDialog.value = false
    initFormData()
}
// 去开通
const toThirdDelivery = () => {
    const url = router.resolve({
        path: '/shop/delivery/local_delivery_service'
    })
    window.open(url.href)
}

const toAddress = () => {
    const url = router.resolve({
        path: '/shop/delivery_store/delivery_set',
        query: {
            id: formData.store_id
        }
    })
    window.open(url.href)
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
