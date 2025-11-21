<template>
    <view class="bg-[var(--page-bg-color)] min-h-screen overflow-hidden" :style="themeColor()">
        <view class="m-[var(--top-m)] sidebar-margin px-[var(--pad-sidebar-m)] py-[var(--pad-top-m)] rounded-[var(--rounded-big)] bg-white">
            <u-form labelPosition="left" :model="formData" labelWidth="200rpx"
                    :labelStyle="{'font-size': '28rpx'}" errorType='toast' :rules="rules" ref="formRef">
                <view>
                    <view class="mt-[10rpx]">
                        <u-form-item label="抬头类型">
                            <view class="flex">
                                <view class="h-[60rpx] box-border rounded px-[30rpx] mr-[20rpx] leading-[56rpx] border-[2rpx] border-[var(--temp-bg)] border-solid text-[24rpx] bg-[var(--temp-bg)]"
                                    :class="{'!bg-[var(--primary-color-light)] !text-[var(--primary-color)] !border-primary': formData.header_type == 1}"
                                    @click="formData.header_type = 1">个人
                                </view>
                                <view
                                    class="h-[60rpx] box-border rounded px-[30rpx] leading-[56rpx] border-[2rpx] border-[var(--temp-bg)] border-solid text-[24rpx] bg-[var(--temp-bg)]"
                                    :class="{'!bg-[var(--primary-color-light)] !text-[var(--primary-color)] !border-primary': formData.header_type == 2}"
                                    @click="formData.header_type = 2">企业
                                </view>
                            </view>
                        </u-form-item>
                    </view>
                    <view class="mt-[10rpx]">
                        <u-form-item label="发票内容" prop="header_name">
                            <view class="flex flex-wrap">
                                <template v-for="(item,index) in config.invoice_content">
                                    <view
                                        class="box-border rounded px-[20rpx] py-[12rpx] leading-[1.4] border-[2rpx] border-[var(--temp-bg)] border-solid text-[24rpx] bg-[var(--temp-bg)] my-[10rpx]"
                                        :class="{'!bg-[var(--primary-color-light)] !text-[var(--primary-color)] !border-primary': formData.name == item, 'mr-[20rpx]': (config.invoice_content.length-1) != index}"
                                        @click="formData.name = item">{{ item }}</view>
                                </template>
                            </view>
                        </u-form-item>
                    </view>
                    <view class="mt-[10rpx]">
                        <u-form-item label="发票抬头" prop="header_name">
                            <u-input fontSize="28rpx" v-model.trim="formData.header_name" border="none" maxlength="50"
                                     placeholder-class="!text-[var(--text-color-light9)] text-[28rpx]" clearable
                                     placeholder="请输入发票抬头" />
                        </u-form-item>
                    </view>
                    <view class="mt-[10rpx]">
                        <u-form-item label="发票金额" prop="money">
                            <text class="mt-[5rpx]">{{ formData.money }}</text>
                        </u-form-item>
                    </view>
                    <view class="mt-[10rpx]">
                            <u-form-item label="支付凭证" prop="pay_voucher">
                                <view class="mt-[10rpx]">
                                    <u-upload :fileList="voucherListPreview" @afterRead="afterRead" @delete="deletePic" multiple :maxCount="5" />
                                </view>
                            </u-form-item>
                        </view>
                    <view v-if="formData.header_type == 2">
                        <view class="mt-[10rpx]">
                            <u-form-item label="纳税人识别号" prop="tax_number">
                                <u-input fontSize="28rpx" v-model.trim="formData.tax_number" border="none"
                                         clearable placeholder="请输入纳税人识别号" maxlength="20"
                                         placeholder-class="!text-[var(--text-color-light9)] text-[28rpx]"
                                         @change="inputChange" />
                            </u-form-item>
                        </view>
                        <!-- <view class="py-[20rpx] h-[48rpx] flex items-center">
                          <text class="text-[30rpx]">更多选填内容</text>
                          <text class="text-xs text-gray-subtitle ml-[10rpx]">注册地址、电话、开户银行及账号</text>
                          <view class="text-xs text-right flex-1" @click="optionalShow = !optionalShow">
                            <text>{{ optionalShow ? '收起' : '展开' }}</text>
                            <text class="text-[30rpx] nc-iconfont text-gray-subtitle ml-[5rpx]" :class="optionalShow ? 'nc-icon-shangV6xx-1' : 'nc-icon-xiaV6xx'"></text>
                          </view>
                        </view> -->
                        <view class="mt-[10rpx]">
                            <u-form-item label="注册地址">
                                <u-input fontSize="28rpx" v-model="formData.address"
                                         placeholder-class="!text-[var(--text-color-light9)] text-[28rpx]"
                                         border="none" clearable maxlength="120"
                                         placeholder="(选填)请输入企业注册地址" />
                            </u-form-item>
                        </view>
                        <view class="mt-[10rpx]">
                            <u-form-item label="注册电话">
                                <u-input fontSize="28rpx" v-model="formData.telephone"
                                         placeholder-class="!text-[var(--text-color-light9)] text-[28rpx]"
                                         border="none" clearable maxlength="12"
                                         placeholder="(选填)请输入企业注册电话" />
                            </u-form-item>
                        </view>
                        <view class="mt-[10rpx]">
                            <u-form-item label="开户银行">
                                <u-input fontSize="28rpx" v-model="formData.bank_name"
                                         placeholder-class="!text-[var(--text-color-light9)] text-[28rpx]"
                                         border="none" clearable maxlength="50"
                                         placeholder="(选填)请输入企业开户银行" />
                            </u-form-item>
                        </view>
                        <view class="mt-[10rpx]">
                            <u-form-item label="银行账号">
                                <u-input fontSize="28rpx" v-model="formData.bank_card_number"
                                         placeholder-class="!text-[var(--text-color-light9)] text-[28rpx]"
                                         border="none" clearable maxlength="25"
                                         placeholder="(选填)请输入企业开户银行账号" />
                            </u-form-item>
                        </view>
                    </view>
                </view>
            </u-form>
        </view>
        <view class="w-full">
            <view class="py-[var(--top-m)] px-[var(--sidebar-m)] box-border">
                <button class="primary-btn-bg !text-[#fff] h-[80rpx] leading-[80rpx] rounded-[100rpx] text-[26rpx] font-500" @click="confirm">提交</button>
            </view>
        </view>
    </view>
</template>

<script setup lang="ts">
import { ref, computed, nextTick } from 'vue'
import { getInvoiceConfig } from '@/addon/shop/api/config'
import { calculateInvoice ,addInvoice ,getInvoiceInfo ,editInvoice} from '@/addon/shop/api/invoice'
import { uploadImage } from '@/app/api/system'
import {  img,redirect,deepClone } from '@/utils/common'
import { onLoad } from '@dcloudio/uni-app'

const orderIds = ref([])
const invoiceId = ref('')
onLoad((option: any) => {
    if (option.order_ids) {
        orderIds.value = JSON.parse(option.order_ids || []);
        calculateInvoice({trade_ids:orderIds.value}).then((res)=>{
            formData.value.money = res.data.invoice_money
        })
    }
    if(option.invoice_id){
        invoiceId.value = option.invoice_id
        getInvoiceInfo(option.invoice_id).then((res)=>{
            for (const key in formData.value) {
                if (res.data[key]) formData.value[key] = res.data[key]
            }
            formData.value.pay_voucher = res.data.pay_voucher.split(',') || []
        })
    }
})

const config = ref({
    is_invoice: 2,
    invoice_content: []
})

const formData: any = ref({
    header_type: 1,
    header_name: '',
    type: '',
    name: '',
    tax_number: '',
    telephone: '',
    address: '',
    bank_name: '',
    bank_card_number: '',
    pay_voucher: [],
    money: ''
})

getInvoiceConfig().then(({ data }) => {
    config.value = data
    data.invoice_content.length && (formData.value.name = data.invoice_content[0])
}).catch()

const inputChange = (e: any) => {
    nextTick(() => {
        formData.value.tax_number = e.replace(/[^0-9a-zA-Z]/g, '')
    })
}

const formRef: any = ref(null)

const voucherListPreview = computed(() => {
    return formData.value.pay_voucher.map(item => {
        return { url: img(item) }
    })
})

const afterRead = (event: any) => {
    event.file.forEach(item => {
        uploadImage({
            filePath: item.url,
            name: 'file'
        }).then(res => {
            if (formData.value.pay_voucher.length < 9) {
                formData.value.pay_voucher.push(res.data.url)
            }
        })
    })
}

const deletePic = (event: any) => {
    formData.value.pay_voucher.splice(event.index, 1)
}
const rules = computed(() => {
    return {
        'header_name': {
            type: 'string',
            required: true,
            message: '请输入发票抬头',
            trigger: ['blur', 'change'],
        },
        'money':{
            type: 'number',
            required: true,
            message: '请输入发票金额',
            trigger: ['blur', 'change'],
        },
        'tax_number': [{
            type: 'string',
            required: formData.value.header_type == 2,
            message: '请输入纳税人识别号',
            trigger: ['blur', 'change'],
        }, {
            validator(rule: any, value: any, callback: any) {
                const limit = /^[0-9a-zA-Z]+$/;
                if (!limit.test(value) && formData.header_type == 2) {
                    callback(new Error('请输入正确的纳税人识别号'))
                } else {
                    callback()
                }
            }
        }],
        'pay_voucher':{
            type: 'array',
            required: true,
            message: '请上传支付凭证',
            trigger: ['blur', 'change'],
        }
    }
})

const confirm = () => {
    formRef.value.validate().then(() => {
        let submitData = deepClone(formData.value)
        submitData.trade_ids = orderIds.value
        submitData.pay_voucher = submitData.pay_voucher.join(',')
        if(invoiceId.value){
            delete submitData.trade_ids
            editInvoice(invoiceId.value,submitData).then((res)=>{
                navigateBack()
            }).catch((err)=>{
            })
        }else{
            addInvoice(submitData).then((res)=>{
                navigateBack()
            }).catch((err)=>{
            })
        }
    })
}

const navigateBack = () => {
    if (getCurrentPages().length > 1) {
        uni.navigateBack({
            delta: 1
        });
    } else {
        redirect({
            url: '/app/pages/index/index',
            mode: 'reLaunch'
        });
    }
}
</script>

<style lang="scss" scoped>
:deep(.u-form-item__body__left){
    align-items:flex-start !important;
}
:deep(.u-input--square){
    margin-top: 5rpx !important;
}
</style>
