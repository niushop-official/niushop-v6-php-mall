<template>
    <div>
        <el-dialog v-model="showDialog" :title="t('goodsSpreadTitle')" width="500px" :destroy-on-close="true">

            <div class="promote-flex flex">
                <div class="promote-img flex justify-center items-center bg-[#f8f8f8] w-[150px] h-[150px]">
                    <el-image :src="wapImage" />
                </div>

                <div class="px-[20px] flex-1">
                    <div class="mb-[10px]">{{ t('spreadLink') }}</div>
                    <el-input class="mb-[10px]" readonly :value="wapPreview">
                        <template #append>
                            <el-button @click="copyEvent(wapPreview)">{{ t('copy') }}</el-button>
                        </template>
                    </el-input>
                    <a class="text-primary" :href="wapImage" download>{{ t('downloadQrcode') }}</a>
                </div>

            </div>

        </el-dialog>
    </div>
</template>

<script lang="ts" setup>
import { t } from '@/lang'
import { ref, reactive, watch } from 'vue'
import { ElMessage } from 'element-plus'
import QRCode from 'qrcode'
import storage from '@/utils/storage'
import { useClipboard } from '@vueuse/core'

import { getUrl } from '@/app/api/sys'

const goods: any = reactive({})

const showDialog = ref(false)

const wapUrl = ref('')
const wapDomain = ref('')
const wapImage = ref('')
const wapPreview = ref('')
const page = ref('')

getUrl().then((res: any) => {
    wapUrl.value = res.data.wap_url

    // 生产模式禁止
    if (import.meta.env.MODE == 'production') return

    wapDomain.value = res.data.wap_domain

    // env文件配置过wap域名
    if (wapDomain.value) {
        wapUrl.value = wapDomain.value + '/wap'
    }

    const wapDomainStorage = storage.get('wap_domain')
    if (wapDomainStorage) {
        wapUrl.value = wapDomainStorage
    }
})

const loadQrcode = () => {
    wapPreview.value = `${wapUrl.value}${page.value}`
    // errorCorrectionLevel：密度容错率L（低）H(高)
    QRCode.toDataURL(wapPreview.value, { errorCorrectionLevel: 'L', margin: 0, width: 120 }).then(url => {
        wapImage.value = url
    })
}

const show = (data: any,type='goods') => {
    Object.assign(goods, data)
    if(type=='goods'){
        page.value = `/addon/shop/pages/goods/detail?goods_id=${goods.goods_id}`
    }else{
        page.value = `/addon/shop/pages/point/detail?id=${goods.id}`
    }
    
    loadQrcode()
    showDialog.value = true
}

// 复制
const { copy, isSupported, copied } = useClipboard()
const copyEvent = (text: string) => {
    if (!isSupported.value) {
        ElMessage({
            message: t('notSupportCopy'),
            type: 'warning'
        })
    }
    copy(text)
}

watch(copied, () => {
    if (copied.value) {
        ElMessage({
            message: t('copySuccess'),
            type: 'success'
        })
    }
})

defineExpose({
    showDialog,
    show
})
</script>

<style lang="scss" scoped></style>
