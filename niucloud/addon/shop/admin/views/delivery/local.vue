<template>
    <div class="main-container">
        <el-card class="card !border-none mb-[15px]" shadow="never">
            <el-page-header :content="pageName" :icon="ArrowLeft" @back="back" />
        </el-card>

        <el-card class="box-card !border-none" shadow="never">
            <el-form label-width="80px" ref="formRef" :rules="formRules" :model="formData" class="page-form" v-loading="loading">
                <el-form-item :label="t('feeType')">
                    <el-radio-group v-model="formData.fee_type">
                        <el-radio label="region">{{ t('region') }}</el-radio>
                        <el-radio label="distance">{{ t('distance') }}</el-radio>
                    </el-radio-group>
                </el-form-item>
                <el-form-item :label="t('feeSetting')" prop="distance" v-show="formData.fee_type == 'distance'">
                    <div class="flex">
                        <div class="w-[60px] mx-[5px]">
                            <el-input v-model.number="formData.base_dist" type="text" maxlength="6" @keyup="filterDigit($event)" />
                        </div>
                        {{ t('feeSettingTextOne') }}
                        <div class="w-[60px] mx-[5px]">
                            <el-input v-model.trim="formData.base_price" type="text"  maxlength="8" @keyup="filterDigit($event)" />
                        </div>
                        {{ t('feeSettingTextTwo') }}
                        <div class="w-[60px] mx-[5px]">
                            <el-input v-model.number="formData.grad_dist" type="text"  maxlength="6" @keyup="filterDigit($event)" />
                        </div>
                        {{ t('feeSettingTextThree') }}
                        <div class="w-[60px] mx-[5px]">
                            <el-input v-model.trim="formData.grad_price" type="text"  maxlength="8" @keyup="filterDigit($event)" />
                        </div>
                        {{ t('priceUnit') }}
                    </div>
                </el-form-item>
                <el-form-item :label="t('weightFee')" prop="weight">
                    <div class="flex">
                        {{ t('weightFeeTextOne') }}
                        <div class="w-[60px] mx-[5px]">
                            <el-input v-model.trim="formData.weight_start" type="text"  maxlength="6" @keyup="filterDigit($event)" />
                        </div>
                        {{ t('weightFeeTextTwo') }}
                        <div class="w-[60px] mx-[5px]">
                            <el-input v-model.trim="formData.weight_unit" type="text"  maxlength="6" @keyup="filterDigit($event)" />
                        </div>
                        {{ t('weightFeeTextThree') }}
                        <div class="w-[60px] mx-[5px]">
                            <el-input v-model.trim="formData.weight_price" type="text"  maxlength="8" @keyup="filterDigit($event)" />
                        </div>
                        {{ t('priceUnit') }}
                    </div>
                </el-form-item>
                <el-form-item :label="t('deliveryArea')" prop="delivery_area">
                     <div class="w-full border-[1px] border-solid border-[#dcdfe6] flex" v-loading="mapLoading">
                        <div class="store-wrap  w-[270px] h-[520px] border-r-[1px] border-solid border-[#dcdfe6]">
                            <div class="relative h-[520px]" v-if="deliveryStoreList.length">
                                <div class="h-[450px] overflow-y-auto">
                                    <div class="h-[70px] cursor-pointer px-[10px] flex flex flex-col justify-center border-b-[1px] border-solid border-[#dcdfe6] box-border" :class="{'bg-[#E6F1FE] text-primary': curDelivery.store_id == item.store_id }" v-for="(item,index) in deliveryStoreList" :key="index" @click="handleMap(item)">
                                        <div class="text-[16px] leading-[20px] mb-[6px] flex items-center justify-between">
                                            <span class="using-hidden">{{ item.store_name  }}</span>
                                            <el-button type="primary" @click.stop="toLink(item)" link class="ml-[10px] !text-[12px]">配置</el-button>
                                        </div>
                                        <div class="text-[12px] text-[#999] leading-[16px] multi-hidden" :class="{'!text-primary': curDelivery.store_id == item.store_id }">地址：{{ item.full_address }}</div>
                                    </div>
                                </div>
                                <div class="absolute left-0 right-0 bottom-0 h-[70px] flex items-center bg-[#fff] px-[20px]">
                                    <el-button type="primary" @click="newWindow()" class="w-full box-border">新增配送点</el-button>
                                </div>
                            </div>
                            <div v-else  class="h-full flex flex-col items-center justify-center">
                                <span class="text-[14px] mb-[10px]">请到配送点中添加配送区域</span>
                                <el-button type="primary" @click="newWindow()" link class="ml-[10px]">{{ t('toSetting') }}</el-button>
                            </div>
                        </div>
                        <div class="relative flex-1">
                            <div id="container" class="w-full h-[520px]"></div>
                            <div class="absolute bg-white w-[270px] h-[500px] top-[10px] left-[10px] region-list" v-if="deliveryStoreList.length">
                                <el-scrollbar>
                                    <div class="p-[10px] region-item pr-[50px] relative"  v-for="(item, index) in curDelivery.area" :key="index" :class="{ '!border-primary': index == currArea }" @click="selectArea(index)">
                                        <el-form label-width="80px" :model="item" :rules="formRules" class="page-form" ref="areaFromRef">
                                            <div class="pb-[18px]">
                                                <el-form-item :label="t('areaName')" prop="area_name">
                                                    <el-input v-model.trim="curDelivery.area[index].area_name" type="text" @click.stop=""/>
                                                </el-form-item>
                                            </div>
                                            <div class="pb-[18px]">
                                                <el-form-item :label="t('startPrice')" prop="start_price">
                                                <el-input v-model.trim="curDelivery.area[index].start_price"  maxlength="8" type="text"
                                                    @keyup="filterDigit($event)" @click.stop="" />
                                            </el-form-item>
                                        </div>
                                        <div class="pb-[10px]" v-show="formData.fee_type == 'region'">
                                            <el-form-item :label="t('deliveryPrice')" prop="delivery_price">
                                                <el-input v-model.trim="curDelivery.area[index].delivery_price" type="text"
                                                    @keyup="filterDigit($event)"  @click.stop=""/>
                                            </el-form-item>
                                        </div>
                                        <el-form-item :label="t('areaType')">
                                            <el-radio-group v-model="curDelivery.area[index].area_type" @change="areaTypeChange(index)">
                                                <el-radio label="radius" size="large" class="!mr-[10px]">{{ t('radius') }}</el-radio>
                                                <el-radio label="custom" size="large" class="!mr-[0px]">{{ t('custom') }}</el-radio>
                                            </el-radio-group>
                                        </el-form-item>
                                    </el-form>
                                    <el-button type="primary" link class="absolute z-1 top-[10px] right-[10px]"
                                        @click.stop="deleteArea(index)">{{ t('delete') }}</el-button>
                                </div>
                                <div class="p-[10px] text-center">
                                    <el-button plain @click="addArea">{{ t('addDeliveryArea') }}</el-button>
                                </div>
                            </el-scrollbar>
                            </div>
                        </div>
                    </div>
                </el-form-item>
            </el-form>
        </el-card>
        <div class="fixed-footer-wrap">
            <div class="fixed-footer">
                <el-button type="primary" @click="onSave(formRef)" :disabled="loading">{{ t('save') }}</el-button>
                <el-button @click="back()">{{ t('cancel') }}</el-button>
            </div>
        </div>
    </div>
</template>

<script lang="ts" setup>
import { ref, computed, onMounted, onBeforeUnmount, toRaw } from 'vue'
import { t } from '@/lang'
import { ArrowLeft } from '@element-plus/icons-vue'
import { useRoute, useRouter } from 'vue-router'
import { getMap } from '@/app/api/sys'
import { guid, filterDigit, deepClone } from '@/utils/common'
import { createCircle, deleteGeometry, createPolygon, selectGeometry, createMarker } from '@/utils/qqmap'
import { setLocal, getLocal, getDeliveryStoreListAll } from '@/addon/shop/api/delivery'
import { FormInstance, ElMessage } from 'element-plus'
import Test from '@/utils/test'

const route = useRoute()
const router = useRouter()
const loading = ref(true)
const pageName = route.meta.title
const formRef = ref<FormInstance>()

const deliveryStoreList = ref<any>([])
const curDelivery = ref<any>({
    area: [
        {
            area_name: '',
            area_type: 'radius',
            start_price: 0,
            delivery_price: 0,
            area_json: {
                key: guid()
            }
        }
    ]
})
const getDeliveryStoreListAllFn = async () => {
    deliveryStoreList.value = await (await getDeliveryStoreListAll({ pick_up_type: 'local_delivery' })).data
    curDelivery.value = deliveryStoreList.value[0]
}
getDeliveryStoreListAllFn()

const formData = ref({
    center: {
        lat: '',
        lng: ''
    },
    fee_type: 'region',
    base_dist: '',
    base_price: '',
    grad_dist: '',
    grad_price: '',
    weight_start: 0.000,
    weight_unit: 0,
    weight_price: 0
})

// 正则表达式
const regExp = {
    required: /[\S]+/,
    number: /^\d{0,10}$/,
    digit: /^\d{0,10}(.?\d{0,2})$/,
    special: /^\d{0,10}(.?\d{0,3})$/
}

// 表单验证规则
const formRules = computed(() => {
    return {
        distance: [
            {
                validator: (rule: any, value: any, callback: any) => {
                    if (formData.value.fee_type == 'distance') {
                        if (Test.require(formData.value.base_dist)) {
                            callback(new Error(t('baseDistRequire')))
                        } else if (Number(formData.value.base_dist) <= 0) {
                            callback(new Error(t('起始公里数不能小于等于0')))
                        }
                        if (Test.require(formData.value.base_price)) {
                            callback(new Error(t('basePriceRequire')))
                        }
                        if (Test.require(formData.value.grad_dist)) {
                            callback(new Error(t('gradDistRequire')))
                        } else if (Number(formData.value.grad_dist) <= 0) {
                            callback(new Error(t('超出公里数不能小于等于0')))
                        }
                        if (Test.require(formData.value.grad_price)) {
                            callback(new Error(t('gradPriceRequire')))
                        }
                    }
                    callback()
                },
                trigger: ['blur', 'change']
            }
        ],
        weight: [
            {
                validator: (rule: any, value: any, callback: any) => {
                    if (formData.value.fee_type == 'distance') {
                        if (Number(formData.value.weight_start) <= 0) { // 重量不能小于等于0
                            callback(new Error(t('商品重量不能小于等于0')))
                        }
                        if (Number(formData.value.weight_unit) <= 0) { // 超出重量不能小于等于0
                            callback(new Error(t('商品超出重量不能小于等于0')))
                        }
                    }
                    callback()
                },
                trigger: ['blur', 'change']
            }
        ]
    }
})

getLocal().then(({ data }) => {
    loading.value = false
    if (data) Object.assign(formData.value, data)
}).catch(() => {
    loading.value = false
})

onMounted(() => {
    const mapScript = document.createElement('script')
    getMap().then(res => {
        mapScript.type = 'text/javascript'
        mapScript.src = 'https://map.qq.com/api/gljs?libraries=tools,service&v=1.exp&key=' + res.data.key
        document.body.appendChild(mapScript)
    })
    mapScript.onload = () => {
        setTimeout(() => {
            initMap()
        }, 500)
    }
})

/**
 * 初始化地图
 */
let map: any
let marker: any
const mapLoading = ref(true)
const initMap = () => {
    const TMap = (window as any).TMap
    const LatLng = TMap.LatLng
    const center = new LatLng(
        curDelivery.value ? curDelivery.value.latitude : 39.980619,
        curDelivery.value ? curDelivery.value.longitude : 116.321277
    )

    map = new TMap.Map('container', {
        center,
        zoom: 14
    })

    marker = createMarker(map)

    map.on('tilesloaded', () => {
        mapLoading.value = false
    })

    if (curDelivery.value) {
        curDelivery.value.area.forEach((item: any) => {
            item.area_type == 'radius' ? createCircle(map, item.area_json) : createPolygon(map, item.area_json)
        })
    }
}
const currArea = ref<number>(0)

const addArea = () => {
    curDelivery.value.area.push({
        area_name: '',
        area_type: 'radius',
        start_price: 0,
        delivery_price: 0,
        area_json: {
            key: guid()
        }
    })
    const index = curDelivery.value.area.length - 1
    createCircle(map, curDelivery.value.area[index].area_json)
}

/**
 * 删除配送区域
 */
const deleteArea = (index: number) => {
    const data = curDelivery.value.area[index]
    deleteGeometry(data.area_json.key)
    curDelivery.value.area.splice(index, 1)
}

const selectArea = (index: number) => {
    currArea.value = index
    const data = curDelivery.value.area[index]
    selectGeometry(data.area_json.key)
}

const areaTypeChange = (index: number) => {
    const data = curDelivery.value.area[index]
    deleteGeometry(data.area_json.key)
    data.area_type == 'radius' ? createCircle(map, data.area_json) : createPolygon(map, data.area_json)
}
const handleMap = (data:any) => {
    deliveryStoreList.value.forEach((item: any) => {
        if (item.store_id == curDelivery.value.store_id) {
            item = deepClone(toRaw(item))
        }
    })
    curDelivery.value.area.forEach((item: any) => {
        deleteGeometry(item.area_json.key)
    })
    curDelivery.value = data
    setTimeout(() => {
        const latLng = new (window as any).TMap.LatLng(curDelivery.value.latitude, curDelivery.value.longitude)
        map.setCenter(latLng)
        marker.updateGeometries({
            id: 'center',
            position: latLng
        })
        curDelivery.value.area.forEach((item: any) => {
            // 重新创建图形
            item.area_json.center = {
                lat: curDelivery.value.latitude,
                lng: curDelivery.value.longitude
            }
            item.area_type == 'radius' ? createCircle(map, item.area_json) : createPolygon(map, item.area_json)
        })
    }, 500)
}

onBeforeUnmount(() => {
    map?.destroy()
})

const verify = () => {
    let flag = true
    for (let i = 0; i < deliveryStoreList.value.length; i++) {
        const temp = deliveryStoreList.value[i].area
        if (Test.require(temp)) {
            flag = false
            ElMessage({ type: 'warning', message: `门店${deliveryStoreList.value[i].store_name}的配送区域不能为空` })
            break
        }
        for (let j = 0; j < temp.length; j++) {
            const val = temp[j]
            if (Test.require(val.area_name)) {
                flag = false
                ElMessage({ type: 'warning', message: `门店${deliveryStoreList.value[i].store_name}${j + 1}的配送区域名称不能为空` })
                break
            }
            if (parseInt(val.start_price) < 0) {
                flag = false
                ElMessage({ type: 'warning', message: `门店${deliveryStoreList.value[i].store_name}${j + 1}的起送价不能小于0` })
                break
            }
            if (parseInt(val.delivery_price) < 0 && formData.value.fee_type == 'region') {
                flag = false
                ElMessage({ type: 'warning', message: `门店${deliveryStoreList.value[i].store_name}${j + 1}的配送费不能小于0` })
                break
            }
        }
        if (!flag) break
    }
    return flag
}
const onSave = async (formEl: FormInstance | undefined) => {
    if (!verify()) return

    if (loading.value || !formEl) return

    await formEl.validate(async (valid) => {
        if (valid) {
            loading.value = true
            await formEl.validate(async (valid) => {
                const param = deepClone(toRaw(formData.value))
                const data: any = {}
                deliveryStoreList.value.forEach((item: any) => {
                    item = deepClone(toRaw(item))
                    data[item.store_id] = item.area
                })
                param.area_data = data
                setLocal(param).then(() => {
                    loading.value = false
                }).catch(() => {
                    loading.value = false
                })
            })
        }
    })
}
// 新窗口打开
const newWindow = () => {
    const url = router.resolve({
        path: '/shop/delivery_store'
    })
    window.open(url.href)
}
const back = () => {
    router.push({ path: '/shop/delivery/config' })
}

const toLink = (data: any) => {
    const url = router.resolve({
        path: '/shop/delivery_store/edit',
        query: {
            store_id: data.store_id
        }
    })
    window.open(url.href)
}
</script>

<style lang="scss" scoped>
.region-list {
    border: 1px solid var(--el-border-color-lighter);
    z-index: 3;

    .region-item {
        border: 1px solid transparent;
        border-bottom-color: var(--el-border-color-lighter);
    }
}
#container :deep(div){
    z-index: 2 !important;
}

.store-wrap::-webkit-scrollbar{
    width:4px;
    border-radius:2px;
    background-color:#f1f1f1;
}
.store-wrap::-webkit-scrollbar-thumb{
    background-color:#c1c1c1;
    border-radius:2px;
}
</style>
