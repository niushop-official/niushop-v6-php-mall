<template>
    <el-dialog v-model="invoiceDialog" :title="t('添加发票')" width="600px">
        <el-form :model="formData" label-width="120px" ref="formRef" :rules="formRules" class="page-form" v-loading="loading">
            <el-form-item :label="t('订单号')" prop="order_no">
                <el-input v-model.trim="formData.order_no" clearable :placeholder="t('请输入订单号')" class="input-width" maxlength="50" />
            </el-form-item>
            <el-form-item :label="t('抬头类型')" prop="header_type">
                <el-radio-group v-model="formData.header_type">
                    <el-radio :label="1">{{ t('个人') }}</el-radio>
                    <el-radio :label="2">{{ t('企业') }}</el-radio>
                </el-radio-group>
            </el-form-item>
            <el-form-item :label="t('发票类型')" prop="type">
                <el-radio-group v-model="formData.type">
                    <el-radio :label="1">{{ t('普票') }}</el-radio>
                    <el-radio :label="2">{{ t('专票') }}</el-radio>
                </el-radio-group>
            </el-form-item>
            <el-form-item :label="t('发票内容')" prop="name">
                <el-select v-model="formData.name" :placeholder="t('请选择发票内容')" class="input-width" clearable>
                    <el-option v-for=" item in invoiceContentList" :key="item" :label="item" :value="item" />
                </el-select>
            </el-form-item>

            <el-form-item :label="t('发票抬头')" prop="header_name">
                <el-input v-model.trim="formData.header_name" clearable :placeholder="t('请输入发票抬头')" class="input-width" maxlength="50" />
            </el-form-item>
            <el-form-item :label="t('发票金额')" prop="money">
                <el-input v-model.trim="formData.money" clearable :placeholder="t('请输入发票金额')" class="input-width" maxlength="8" />
            </el-form-item>
            <el-form-item :label="t('开票人手机号')" prop="mobile">
                <el-input v-model.trim="formData.mobile" clearable :placeholder="t('请输入开票人手机号')" class="input-width" maxlength="11" />
            </el-form-item>
            <el-form-item :label="t('邮箱')" prop="email">
                <el-input v-model.trim="formData.email" clearable :placeholder="t('请输入邮箱')" class="input-width" maxlength="20" />
            </el-form-item>
            <template v-if="formData.header_type === 2">
                <el-form-item :label="t('纳税人识别号')" prop="tax_number">
                    <el-input v-model.trim="formData.tax_number" clearable :placeholder="t('请输入纳税人识别号')" class="input-width" maxlength="20" />
                </el-form-item>

                <el-form-item :label="t('注册地址')" prop="address">
                    <el-input v-model.trim="formData.address" clearable :placeholder="t('(选填)请输入企业注册地址')" class="input-width" maxlength="120" />
                </el-form-item>

                <el-form-item :label="t('注册电话')" prop="telephone">
                    <el-input v-model.trim="formData.telephone" clearable :placeholder="t('(选填)请输入企业注册电话')" class="input-width" maxlength="12" />
                </el-form-item>

                <el-form-item :label="t('开户银行')" prop="bank_name">
                    <el-input v-model.trim="formData.bank_name" clearable :placeholder="t('(选填)请输入企业开户银行')" class="input-width" maxlength="50" />
                </el-form-item>

                <el-form-item :label="t('银行账号')" prop="bank_card_number">
                    <el-input v-model.trim="formData.bank_card_number" clearable :placeholder="t('(选填)请输入企业开户银行账号')" class="input-width" maxlength="25" />
                </el-form-item>
            </template>
        </el-form>
        <template #footer>
            <span class="dialog-footer">
                <el-button @click="invoiceDialog = false">{{ t('cancel') }}</el-button>
                <el-button type="primary" :loading="loading" @click="save(formRef)">{{ t('confirm') }}</el-button>
            </span>
        </template>
    </el-dialog>
</template>

<script lang="ts" setup>
import { ref, computed } from 'vue'
import { t } from '@/lang'
import { FormInstance } from 'element-plus'
import { createInvoice ,getConfig } from '@/addon/shop/api/order'

const invoiceDialog = ref(false)

/**
 * 表单数据
 */
const formData = ref({
    header_type: 1,
    type: 1,
    name: '',
    email: '',
    header_name: '',
    tax_number: '',
    address: '',
    telephone: '',
    bank_name: '',
    bank_card_number: '',
    order_no: '',
    mobile: '',
    money: ''
})

const loading = ref(false)
const invoiceContentList = ref([])
const setInvoiceData = async (row: any = null) => {
    loading.value = true
    getConfig().then((res) => {
        invoiceContentList.value = res.data.invoice.invoice_content
        loading.value = false
    })
}

const emit = defineEmits(['complete'])
const save = async (formEl: FormInstance | undefined) => {
    if (loading.value || !formEl) return
    await formEl.validate(async (valid) => {
        if (valid) {
            loading.value = true
            const data = formData.value
            createInvoice(data).then((res) => {
                emit('complete')
                invoiceDialog.value = false
                loading.value = false
            }).catch(() => {
                loading.value = false
            })
        }
    })
}
const formRef = ref<FormInstance>()

// 表单验证规则
const formRules = computed(() => {
    return {
        type: [{ required: true, message: t('请选择发票类型'), trigger: 'change' }],
        header_type: [{ required: true, message: t('请选择发票抬头类型'), trigger: 'change' }],
        order_no: [{ required: true, message: t('请输入订单号'), trigger: 'blur' }],
        header_name: [{ required: true, message: t('请输入发票抬头'), trigger: 'blur' }],
        name: [{ required: true, message: t('请选择发票内容'), trigger: 'change' }],
        tax_number: [
            {
                required: true,
                trigger: 'blur',
                validator: (rule: any, value: any, callback: any) => {
                    if (formData.value.header_type == 2 && !value) {
                        callback(new Error(t('请输入纳税人识别号')))
                    } else {
                        let reg = /^[0-9a-zA-Z]+$/
                        if (!reg.test(value)) {
                            callback(new Error(t('请输入正确的纳税人识别号')))
                        } else{
                            callback()
                        }
                    }
                }
            }
        ],
        mobile:[
            {
                trigger: 'blur',
                validator: (rule: any, value: any, callback: any) => {
                    if (value == '') {
                        callback()
                    } else {
                        let reg = /^1[3456789]\d{9}$/
                        if (!reg.test(value)) {
                            callback(new Error(t('请输入正确的开票人手机号')))
                        } else{
                            callback()
                        }
                    }
                }
            }
        ],
        telephone:[
            {
                trigger: 'blur',
                validator: (rule: any, value: any, callback: any) => {
                    if (value == '') {
                        callback()
                    } else {
                        let reg = /^1[3456789]\d{9}$/
                        if (!reg.test(value)) {
                            callback(new Error(t('请输入正确的注册电话')))
                        } else{
                            callback()
                        }
                    }
                }
            }
        ],
        email:[
            {
                trigger: 'blur',
                validator: (rule: any, value: any, callback: any) =>{
                    if (value == '') {
                        callback()
                    } else {
                        let reg = /^[a-zA-Z0-9_.-]+@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)*\.[a-zA-Z0-9]{2,6}$/
                        if (!reg.test(value)) {
                            callback(new Error(t('请输入正确的邮箱')))
                        } else{
                            callback()
                        }
                    }
                }
            }
        ],
        money:[
            {
                required: true,
                trigger: 'blur',
                validator: (rule: any, value: any, callback: any) => {
                    if (value == ''){
                        callback(new Error(t('请输入开票金额')))
                    } else {
                        let reg = /^([1-9]\d{0,7}|0)(\.\d{1,2})?$/
                        if (!reg.test(value)) {
                            callback(new Error(t('请输入正确的开票金额')))
                        } else{
                            callback()
                        }
                    }
                }
            }
        ]
    }
})
defineExpose({
    invoiceDialog,
    setInvoiceData
})
</script>
