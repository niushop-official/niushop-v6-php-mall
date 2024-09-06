<template>
    <u-popup :show="show" @close="show = false" mode="bottom" :round="10" :closeable="true" :safeAreaInsetBottom="true" :customStyle="{maxHeight:'58vh'}">
        <view @touchmove.prevent.stop>
			<view class="text-center p-[30rpx] box-border h-[111rpx]">请填写发票信息</view>
			<scroll-view :scroll-y="true"  class="max-h-[calc(50vh-211rpx)]">
				<view class="p-[30rpx] pt-0 text-sm ">
					<u-form labelPosition="left" :model="formData" labelWidth="200rpx" errorType='toast' :rules="rules" ref="formRef">
						<view class="mt-[10rpx]">
							<u-form-item label="需要发票" :border-bottom="true">
								<view class="flex">
									<view
										class="rounded px-[30rpx] py-[10rpx] mr-[20rpx] border-1 !border-[#eee] border-solid text-sm"
										:class="{'bg-primary text-white !border-primary': !need}"
										@click="need = false">不需要</view>
									<view class="rounded px-[30rpx] py-[10rpx] border-1 !border-[#eee] border-solid text-sm"
										:class="{'bg-primary text-white !border-primary': need}"
										@click="need = true">需要</view>
								</view>
							</u-form-item>
						</view>
						<view v-show="need">
							<view class="mt-[10rpx]">
								<u-form-item label="抬头类型" :border-bottom="true">
									<view class="flex">
										<view
											class="rounded px-[30rpx] py-[10rpx] mr-[20rpx] border-1 !border-[#eee] border-solid text-sm"
											:class="{'bg-primary text-white !border-primary': formData.header_type == 1}"
											@click="formData.header_type = 1">个人</view>
										<view class="rounded px-[30rpx] py-[10rpx] border-1 !border-[#eee] border-solid text-sm"
											:class="{'bg-primary text-white !border-primary': formData.header_type == 2}"
											@click="formData.header_type = 2">企业</view>
									</view>
								</u-form-item>
							</view>
							<view class="mt-[10rpx]">
								<u-form-item label="发票内容" prop="header_name" :border-bottom="true">
									<view class="flex flex-wrap">
										<block v-for="item in config.invoice_content">
											<view class="rounded px-[30rpx] my-[10rpx] py-[10rpx] mr-[20rpx] border-1 !border-[#eee] border-solid text-sm whitespace-normal"
												:class="{'bg-primary text-white !border-primary': formData.name == item}"
												@click="formData.name = item">{{ item  }}</view>
										</block>
									</view>
								</u-form-item>
							</view>
							<view class="mt-[10rpx]">
								<u-form-item label="发票抬头" prop="header_name" :border-bottom="true">
									<u-input v-model.trim="formData.header_name" border="none" clearable placeholder="请输入发票抬头" ></u-input>
								</u-form-item>
							</view>
							<view v-if="formData.header_type == 2">
								<view class="mt-[10rpx]">
									<u-form-item label="纳税人识别号" prop="tax_number" :border-bottom="true">
										<u-input v-model.trim="formData.tax_number" border="none" clearable placeholder="请输入纳税人识别号" @change="inputChange"></u-input>
									</u-form-item>
								</view>
								<view class="py-[20rpx] flex items-baseline">
									<text class="text-[30rpx]">更多选填内容</text>
									<text class="text-xs text-gray-subtitle ml-[10rpx]">注册地址、电话、开户银行及账号</text>
									<view class="text-xs text-right flex-1" @click="optionalShow = !optionalShow">
										<text>{{ optionalShow ? '收起' : '展开' }}</text>
										<text class="text-[30rpx] nc-iconfont text-gray-subtitle ml-[5rpx]" :class="optionalShow ? 'nc-icon-shangV6xx-1' : 'nc-icon-xiaV6xx'"></text>
									</view>
								</view>
								<view v-show="optionalShow">
									<view class="mt-[10rpx]">
										<u-form-item label="注册地址" :border-bottom="true">
											<u-input v-model="formData.address" border="none" clearable placeholder="(选填)请输入企业注册地址"></u-input>
										</u-form-item>
									</view>
									<view class="mt-[10rpx]">
										<u-form-item label="注册电话" :border-bottom="true">
											<u-input v-model="formData.telephone" border="none" clearable placeholder="(选填)请输入企业注册电话"></u-input>
										</u-form-item>
									</view>
									<view class="mt-[10rpx]">
										<u-form-item label="开户银行" :border-bottom="true">
											<u-input v-model="formData.bank_name" border="none" clearable placeholder="(选填)请输入企业开户银行"></u-input>
										</u-form-item>
									</view>
									<view class="mt-[10rpx]">
										<u-form-item label="银行账号" :border-bottom="true">
											<u-input v-model="formData.bank_card_number" border="none" clearable placeholder="(选填)请输入企业开户银行账号"></u-input>
										</u-form-item>
									</view>
								</view>
							</view>
						</view>
					</u-form>
				</view>
			</scroll-view>
			<view class="p-[30rpx]">
                <button class="bg-[var(--primary-color)] text-[#fff] h-[80rpx] leading-[80rpx] rounded-[100rpx] text-[28rpx]" @click="confirm">确认</button>
			</view>
		</view>
    </u-popup>
</template>

<script setup lang="ts">
    import { ref, computed,nextTick } from 'vue'
    import { getInvoiceConfig } from '@/addon/shop/api/config'

    const show = ref(false)
    const config = ref({
        is_invoice: 2,
        invoice_content: []
    })
    const need = ref(false)
    const optionalShow = ref(false)
    const formData = ref({
        header_type: 1,
        header_name: '',
        type: '',
        name: '',
        tax_number: '',
        telephone: '',
        address: '',
        bank_name: '',
        bank_card_number: ''
    })

    const invoiceOpen = computed(() => {
        return config.value.is_invoice == 1
    })

    getInvoiceConfig().then(({ data }) => {
        config.value = data
        data.invoice_content.length && (formData.value.name = data.invoice_content[0])
    }).catch()

	const inputChange = (e) =>{
		nextTick(()=>{
			formData.value.tax_number = e.replace(/[^0-9a-zA-Z]/g,'')
		})
	}

    const formRef = ref(null)

    const rules = computed(() => {
        return {
            'header_name': {
                type: 'string',
                required: need.value,
                message: '请输入发票抬头',
                trigger: ['blur', 'change'],
            },
            'tax_number': [{
                type: 'string',
                required: need.value && formData.value.header_type == 2,
                message: '请输入纳税人识别号',
                trigger: ['blur', 'change'],
            }, {
                validator(rule, value, callback) {
                    const limit = /^[0-9a-zA-Z]+$/;
                    if (!limit.test(value) && formData.header_type == 2) {
                        callback(new Error('请输入正确的纳税人识别号'))
                    } else {
                        callback()
                    }
                }
            }]
        }
    })

    const open = () => {
        show.value = true
    }

    const emits = defineEmits(['confirm'])

    const confirm = () => {
        formRef.value.validate().then(() => {
            const invoice = need.value ? formData.value : {}
            emits('confirm', invoice)
            show.value = false
        })
    }

    defineExpose({
        open,
        invoiceOpen
    })
</script>

<style lang="scss" scoped>
</style>
