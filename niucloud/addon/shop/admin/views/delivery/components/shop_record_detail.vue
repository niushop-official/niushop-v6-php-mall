<template>
    <el-drawer v-model="showDialog" :title="popTitle" direction="rtl" :before-close="handleClose" class="local-record-detail-drawer">
        <div class="main-container" v-loading="loading">
            <el-form :model="detailData" label-width="100px" ref="formRef" class="page-form"  label-position="left">
                <div class="row-bg px-[20px] mb-[20px] multiple-layout">
                    <el-form-item :label="t('deliveryNo')">
						<div class="input-width">{{ detailData.delivery_no }}</div>
					</el-form-item>
					<el-form-item :label="t('goodsInfo')">
						<div class="input-width">{{ detailData.body }}</div>
					</el-form-item>
                    <el-form-item :label="t('tradeNo')">
						<div class="input-width text-primary cursor-pointer" @click="toLink">{{ detailData.trade_no }}</div>
					</el-form-item>
                    <el-form-item :label="t('deliveryStart')">
						<div class="input-width">{{ detailData.delivery_start }}</div>
					</el-form-item>
                    <el-form-item :label="t('deliveryEnd')">
						<div class="input-width">{{ detailData.delivery_end }}</div>
					</el-form-item>
                    <el-form-item :label="t('deliveryStatus')">
						<div class="input-width">{{ detailData.status_name }}</div>
					</el-form-item>
                    <el-form-item :label="t('deliveryDistance')">
						<div class="input-width">{{ distance(detailData.delivery_distance) }}</div>
					</el-form-item>
                    <el-form-item :label="t('deliveryMoney')">
						<div class="input-width">￥{{ detailData.delivery_money }}</div>
					</el-form-item>
                    <el-form-item :label="t('cancelTime')" v-if="detailData.cancel_time">
						<div class="input-width">{{ detailData.cancel_time }}</div>
					</el-form-item>
                    <el-form-item :label="t('riderName')" v-if="detailData.rider_name">
						<div class="input-width">{{ detailData.rider_name }}</div>
					</el-form-item>
                    <el-form-item :label="t('riderMobile')" v-if="detailData.rider_mobile">
						<div class="input-width">{{ detailData.rider_mobile }}</div>
					</el-form-item>
                    <el-form-item :label="t('outDeliveryNo')">
						<div class="input-width">{{ detailData.out_delivery_no }}</div>
					</el-form-item>
                    <el-form-item :label="t('remark')">
						<div class="input-width">{{ detailData.remark }}</div>
					</el-form-item>
                </div>
            </el-form>
            <div>
                <h3 class="mt-[50px] mb-[20px]" v-if="detailData.orderLog.length > 0">配送日志</h3>
				<div class="mb-[100px]" style="min-height: 100px" v-if="detailData.orderLog.length > 0">
					<div class="flex" v-for="(items, index) in detailData.orderLog" :key="index">
						<div class="mr-[20px] min-w-[71px]">
							<div class="leading-[1] w-full text-[14px] w-[100px] flex justify-end">
								{{ items.create_time && items.create_time.split(' ')[0] }}
							</div>
							<div class="leading-[1] w-full text-[14px]  w-[100px] flex justify-end mt-[15px]">
								{{ items.create_time && items.create_time.split(' ')[1] }}
							</div>
						</div>
						<div>
							<div class="w-[16px] h-[16px] flex items-center bg-[#D1EBFF] border-[1px] border-[#0091FF] rounded-[999px]">
								<div class="w-[8px] h-[8px] mx-auto bg-[#0091FF] rounded-[999px]"></div>
							</div>
							<div v-if="index + 1 != detailData.orderLog.length" class="w-[2px] h-[50px] bg-[#D1EBFF] mx-auto">
							</div>
						</div>
						<div>
							<div class="leading-[1] ml-[20px] text-[14px]">
								{{ items.main_type_name }}{{ items.main_name }}
							</div>
							<div class="leading-[1] ml-[20px] text-[14px] mt-[15px]">
								<span>{{ items.remark }}</span>
							</div>
						</div>
					</div>
				</div>
            </div>
        </div>
    </el-drawer>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { t } from '@/lang'
import { distance } from '@/utils/common'
import { getShopDeliveryInfo } from '@/addon/shop/api/delivery'
import { useRouter } from 'vue-router'

const router = useRouter()
const showDialog = ref(false)
const loading = ref(false)
const popTitle: string = '配送详情'
const detailData = ref<any>({})
let id: any = ''

const handleClose = (done: () => void) => {
    showDialog.value = false
}

const getShopDeliveryInfoFn = async () => {
    loading.value = true
    if (id) {
        detailData.value = await (await getShopDeliveryInfo(id)).data
        loading.value = false
    } else {
        loading.value = false
    }
    showDialog.value = true
}
const setFormData = async (row: any = null) => {
    id = row.id
    getShopDeliveryInfoFn()
}

const toLink = () => {
    const path = '/shop/order/detail'
    const url = router.resolve({
        path,
        query: {
            order_id: detailData.value.trade_id
        }
    })
    window.open(url.href)
}


defineExpose({
    showDialog,
    setFormData
})
</script>

<style lang="scss">
.local-record-detail-drawer{
    width: 1300px !important;
    .input-width{
        width: 280px;
    }
}
</style>