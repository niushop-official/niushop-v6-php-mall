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
                     <div class="w-full border-[1px] border-solid border-[#dcdfe6] flex">
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
                        <div class="relative flex-1 overflow-hidden">
                            <map-selector
                                ref="mapSelectorRef"
                                id="container-radius"
                                 :container-id="'container-radius'"
                                :longitude="curDelivery.longitude"
                                :latitude="curDelivery.latitude"
                                :zoom="14"
                                :disabled-click-marker="true"
                                :container-style="'w-full h-[520px]'"
                                :selected-key="curDelivery?.area[currArea]?.area_json?.key || ''"
                                @areaChange="handleAreaChange"
                                @selectChange="handleSelectChange"
                            />
                            <div class="absolute bg-white w-[270px] h-[500px] top-[10px] left-[10px] region-list" v-if="deliveryStoreList.length">
                                <el-scrollbar>
                                    <div class="p-[10px] region-item pr-[50px] relative"  v-for="(item, index) in curDelivery.area" :key="item.area_json?.key || index" :class="{ '!border-primary': index == currArea }" @click="selectArea(index)">
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
import { ref, computed, onMounted, onBeforeUnmount, toRaw, watch } from 'vue'
import { t } from '@/lang'
import { ArrowLeft } from '@element-plus/icons-vue'
import { useRoute, useRouter } from 'vue-router'
import { guid, filterDigit, deepClone } from '@/utils/common'
import { setLocal, getLocal, getDeliveryStoreListAll } from '@/addon/shop/api/delivery'
import { FormInstance, ElMessage } from 'element-plus'
import Test from '@/utils/test'
import MapSelector from '@/components/map-selector/index.vue'

const route = useRoute()
const router = useRouter()
const loading = ref(true)
const pageName = route.meta.title
const formRef = ref<FormInstance>()
const mapSelectorRef = ref()

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
    initMapAreas()
}).catch(() => {
    loading.value = false
})

onMounted(() => {
    // 地图组件会自动初始化
})

const currArea = ref<number>(0)
/**
 * 初始化地图区域
 */
const initMapAreas = async () => {
    if (!mapSelectorRef.value) return
    // 等待地图初始化完成
    const checkMapReady = async () => {
        let attempts = 0
        const maxAttempts = 10
        while (attempts < maxAttempts) {
            try {
                // 尝试获取地图实例，检查地图是否已初始化
                const map = await mapSelectorRef.value.getMap()
                if (map) {
                    return true
                }
            } catch (error) {
                // 地图未初始化完成，继续等待
            }
            attempts++
            await new Promise(resolve => setTimeout(resolve, 200))
        }
        return false
    }
    // 检查地图是否就绪
    const isReady = await checkMapReady()
    if (!isReady) {
        console.warn('地图初始化超时，无法加载区域')
        return
    }
    // 清空现有覆盖物
    await mapSelectorRef.value.clearOverlays()
    // 绘制所有区域
    for (const item of curDelivery.value.area) {
        if (item.area_type === 'radius') {
            await mapSelectorRef.value.addCircle(item.area_json.center, item.area_json.radius, {
                key: item.area_json.key
            })
        } else if (item.area_type === 'custom') {
            mapSelectorRef.value.addPolygon(item.area_json.paths, {
                key: item.area_json.key
            })
        }
    }
    // 选中第一个区域
    if (curDelivery.value.area.length > 0) {
        currArea.value = 0
        mapSelectorRef.value.selectGeometry(curDelivery.value.area[0].area_json.key)
    }
}
/**
 * 添加配送区域
 */
const addArea = async () => {
    if (!mapSelectorRef.value) return
    // 获取地图中心点
    const map = await mapSelectorRef.value.getMap()
    const mapCenter = map ? map.getCenter() : { lat: 39.909187, lng: 116.397463 }
    // 创建新区域
    const newArea = {
        area_name: '',
        area_type: 'radius',
        start_price: 0,
        delivery_price: 0,
        area_json: {
            key: guid(),
            center: { lat: mapCenter.lat, lng: mapCenter.lng },
            radius: 1000
        } as any
    }
    curDelivery.value.area.push(newArea)
    const index = curDelivery.value.area.length - 1
    // 添加圆形覆盖物
    await mapSelectorRef.value.addCircle(newArea.area_json.center, newArea.area_json.radius, {
        key: newArea.area_json.key
    })
    // 选中新添加的区域
    currArea.value = index
    await mapSelectorRef.value.selectGeometry(newArea.area_json.key)
}

/**
 * 删除配送区域
 */
const deleteArea = async (index: number) => {
    const data = curDelivery.value.area[index]

    if (mapSelectorRef.value) {
        try {
            await mapSelectorRef.value.removeOverlay({ key: data.area_json.key })
        } catch (error) {
            console.error('删除区域失败:', error)
        }
    }

    curDelivery.value.area.splice(index, 1)
}

const selectArea = async (index: number) => {
    if (!mapSelectorRef.value) return
    currArea.value = index
    const data = curDelivery.value.area[index]
    await mapSelectorRef.value.selectGeometry(data.area_json.key)
}

/**
 * 区域类型改变时处理
 */
const areaTypeChange = async (index: number) => {
    const data = curDelivery.value.area[index]
    mapSelectorRef.value.removeOverlay({ key: data.area_json.key })
    const map = await mapSelectorRef.value.getMap()
    if (data.area_type == 'radius') {
        const mapCenter = map?.getCenter() || { lat: 39.909187, lng: 116.39746 }
        data.area_json.center = data.area_json.center || { lat: mapCenter.lat, lng: mapCenter.lng }
        data.area_json.radius = data.area_json.radius || 1000
        await mapSelectorRef.value.addCircle(data.area_json.center, data.area_json.radius, data.area_json)
    } else {
        if (!data.area_json.paths?.length) {
            const center = data.area_json.center || map?.getCenter() || { lat: 39.909187, lng: 116.397463 }
            data.area_json.paths = [
                { lat: center.lat + 0.01, lng: center.lng + 0.01 },
                { lat: center.lat - 0.01, lng: center.lng + 0.01 },
                { lat: center.lat - 0.01, lng: center.lng - 0.01 },
                { lat: center.lat + 0.01, lng: center.lng - 0.01 }
            ]
        }
        mapSelectorRef.value.addPolygon(data.area_json.paths, data.area_json)
    }
    setTimeout(() => {
        currArea.value = index
        mapSelectorRef.value.selectGeometry(data.area_json.key)
    }, 100)
}

const handleMap = async (data: any) => {
    deliveryStoreList.value.forEach((item: any) => {
        if (item.store_id == curDelivery.value.store_id) {
            item = deepClone(toRaw(item))
        }
    })

    // 清空所有覆盖物
    if (mapSelectorRef.value) {
        try {
            await mapSelectorRef.value.clearOverlays()
        } catch (error) {
            console.error('清空覆盖物失败:', error)
        }
    }

    curDelivery.value = data

    // 更新地图中心
    if (mapSelectorRef.value && data.latitude && data.longitude) {
        try {
            await mapSelectorRef.value.updateLocation(Number(data.latitude), Number(data.longitude))
        } catch (error) {
            console.error('更新地图位置失败:', error)
        }
    }

    initMapAreas()
}

// 处理地图区域变化事件
const handleAreaChange = (data: any) => {
    if (!data || !data.key) return
    // 根据key找到对应的区域
    const areaIndex = curDelivery.value.area.findIndex((item: any) => item.area_json?.key === data.key)
    if (areaIndex === -1) return
    // 更新区域路径数据
    curDelivery.value.area[areaIndex].area_json = { ...data.path }
    // console.log('handleAreaChange', curDelivery.value.area[areaIndex])
}

// 处理地图选中事件
const handleSelectChange = (key: string) => {
    // 找到对应的区域并设置选中状态
    const areaIndex = curDelivery.value.area.findIndex((item: any) => item.area_json?.key === key)
    if (areaIndex !== -1) {
        currArea.value = areaIndex
    }
}

onBeforeUnmount(() => {
    // 地图组件会自动清理
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
  #container-radius {
    z-index: 1 !important; // 降低地图容器的z-index
    pointer-events: auto;
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
