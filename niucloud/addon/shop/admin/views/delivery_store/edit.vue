<template>
    <div class="main-container">
        <el-card class="card !border-none mb-[15px]" shadow="never">
            <el-page-header :content="pageName" :icon="ArrowLeft" @back="back" />
        </el-card>

        <el-card class="box-card !border-none" shadow="never">
            <el-alert type="warning" :closable="false" class="!mb-[15px]" v-if="formData.is_system === 1">
                <template #default>
                    <p>{{ t('deliveryStoreTips') }}<span class="text-primary cursor-pointer" @click="newWindow()">去修改</span><span class="text-primary cursor-pointer ml-[10px]" @click="refreshAddress(true)">刷新</span></p>
                </template>
            </el-alert>
            <el-form label-width="120px" ref="formRef" :rules="formRules" :model="formData" class="page-form" v-loading="loading">
                <el-form-item :label="t('提货类型')" prop="pick_up_type">
                    <el-checkbox-group v-model="formData.pick_up_type" @change="changePickUpType">
                        <el-checkbox :label="index" v-for="(item,index) in pickUpType" :key="index">{{ item }}</el-checkbox>
                    </el-checkbox-group>
                </el-form-item>
                <el-form-item :label="t('storeName')" prop="store_name">
                    <el-input v-model.trim="formData.store_name" clearable :placeholder="t('storeNamePlaceholder')" class="input-width" maxlength="30" :disabled="formData.is_system === 1" />
                </el-form-item>
                <el-form-item :label="t('contactName')" prop="contact_name">
                    <el-input v-model.trim="formData.contact_name" clearable :placeholder="t('contactNamePlaceholder')" class="input-width" maxlength="30" :disabled="formData.is_system === 1" />
                </el-form-item>
                <el-form-item :label="t('storeMobile')" prop="store_mobile">
                    <el-input v-model.trim="formData.store_mobile" clearable :placeholder="t('storeMobilePlaceholder')" class="input-width" @keyup="filterNumber($event)" @blur="formData.store_mobile = $event.target.value" maxlength="11" :disabled="formData.is_system === 1" />
                </el-form-item>
                <el-form-item :label="t('tradeTime')" prop="trade_time">
                    <div>
                        <el-input v-model.trim="formData.trade_time" clearable :placeholder="t('tradeTimePlaceholder')" class="input-width" :disabled="formData.is_system === 1" />
                        <p class="text-[12px] text-[#999]">{{ t('tradeTimeTips') }}</p>
                    </div>
                </el-form-item>
                <el-form-item :label="t('开启状态')" prop="status">
                    <el-switch v-model="formData.status" :active-value="1" :inactive-value="0" />
                </el-form-item>
                <el-form-item :label="t('timeIsOpen')" prop="time_is_open">
                    <div>
                        <el-radio-group v-model="formData.time_is_open">
                            <el-radio :label="1">{{ t('open') }}</el-radio>
                            <el-radio :label="0">{{ t('close') }}</el-radio>
                        </el-radio-group>
                        <div class="mt-[10px] text-[12px] text-[#999] leading-[20px]">{{t('timeIsOpenTips')}}</div>
                    </div>
                </el-form-item>
                <template v-if="formData.time_is_open === 1">
                    <el-form-item :label="t('自提日期')" prop="time_week" >
                        <el-checkbox-group v-model="formData.time_week">
                            <el-checkbox :label="'1'">{{ t('monday') }}</el-checkbox>
                            <el-checkbox :label="'2'">{{ t('tuesday') }}</el-checkbox>
                            <el-checkbox :label="'3'">{{ t('wednesday') }}</el-checkbox>
                            <el-checkbox :label="'4'">{{ t('thursday') }}</el-checkbox>
                            <el-checkbox :label="'5'">{{ t('friday') }}</el-checkbox>
                            <el-checkbox :label="'6'">{{ t('saturday') }}</el-checkbox>
                            <el-checkbox :label="'0'">{{ t('sunday') }}</el-checkbox>
                            <br />
                        </el-checkbox-group>
                    </el-form-item>
                    <el-form-item :label="t('deliveryTime')" prop="trade_time_json">
                        <div>
                            <div>
                                <div v-for="(timeRange, index) in formData.trade_time_json" :key="index" class="mb-3">
                                    <el-time-picker v-model="timeRange.start_time" :placeholder="t('startTime')"
                                        format="HH:mm" value-format="HH:mm"
                                        :picker-options="{selectableRange: '00:00 - 23:59'}" />
                                    <span class="mx-2">-</span>
                                    <el-time-picker v-model="timeRange.end_time" :placeholder="t('endTime')" format="HH:mm"
                                        value-format="HH:mm" :picker-options="{selectableRange: '00:00 - 23:59'}" />
                                    <span v-if="index > 0" class="text-primary cursor-pointer ml-[10px]"
                                        @click="removeTimeRange(index)"> {{ t('delete') }}</span>
                                </div>
                                <span class="text-primary cursor-pointer mr-[10px]" @click="addTimeRange"
                                    v-if="formData.trade_time_json.length < 3"> {{ t('addTime') }}</span>
                            </div>
                            <div class="text-[12px] text-[#999]">{{ t('deliveryTimeTips') }}</div>
                        </div>

                    </el-form-item>

                    <el-form-item :label="t('timeInterval')" prop="time_interval">
                        <div>
                            <el-radio-group v-model="formData.time_interval">
                                <el-radio v-for="(item, key) in time_interval_list" :key="key" :label="item.type">{{ item.name }}</el-radio>
                            </el-radio-group>
                        </div>

                    </el-form-item>
                </template>
                <template v-if="formData.pick_up_type.indexOf('local_delivery') != -1">
                    <el-form-item :label="t('feeType')">
                        <el-radio-group v-model="initData.fee_type" >
                            <el-radio label="region" disabled>{{ t('region') }}</el-radio>
                            <el-radio label="distance" disabled>{{ t('distance') }}</el-radio>
                        </el-radio-group>
                    </el-form-item>
                    <el-form-item :label="t('feeSetting')" prop="distance" v-show="initData.fee_type == 'distance'">
                        <div class="flex">
                            <div class="w-[60px] mx-[5px]">
                                <el-input v-model.number="initData.base_dist" disabled type="text" maxlength="6" @keyup="filterDigit($event)" />
                            </div>
                            {{ t('feeSettingTextOne') }}
                            <div class="w-[60px] mx-[5px]">
                                <el-input v-model.trim="initData.base_price" disabled type="text"  maxlength="8" @keyup="filterDigit($event)" />
                            </div>
                            {{ t('feeSettingTextTwo') }}
                            <div class="w-[60px] mx-[5px]">
                                <el-input v-model.number="initData.grad_dist" disabled type="text"  maxlength="6" @keyup="filterDigit($event)" />
                            </div>
                            {{ t('feeSettingTextThree') }}
                            <div class="w-[60px] mx-[5px]">
                                <el-input v-model.trim="initData.grad_price" disabled  type="text"  maxlength="8" @keyup="filterDigit($event)" />
                            </div>
                            {{ t('priceUnit') }}
                        </div>
                    </el-form-item>
                    <el-form-item :label="t('weightFee')" prop="weight">
                        <div class="flex">
                            {{ t('weightFeeTextOne') }}
                            <div class="w-[60px] mx-[5px]">
                                <el-input v-model.trim="initData.weight_start" disabled type="text"  maxlength="6" @keyup="filterDigit($event)" />
                            </div>
                            {{ t('weightFeeTextTwo') }}
                            <div class="w-[60px] mx-[5px]">
                                <el-input v-model.trim="initData.weight_unit" disabled type="text"  maxlength="6" @keyup="filterDigit($event)" />
                            </div>
                            {{ t('weightFeeTextThree') }}
                            <div class="w-[60px] mx-[5px]">
                                <el-input v-model.trim="initData.weight_price" disabled type="text"  maxlength="8" @keyup="filterDigit($event)" />
                            </div>
                            {{ t('priceUnit') }}
                        </div>
                    </el-form-item>
                </template>
                <el-form-item :label="t('地址')" prop="address_area">
                    <el-select v-model="formData.province_id" value-key="id" @change="handleFlash"  clearable class="w-[200px]"  ref="provinceRef" :disabled="formData.is_system === 1">
                        <el-option :label="t('provincePlaceholder')" :value="0"/>
                        <el-option v-for="(item, index) in areaList.province" :key="index" :label="item.name"  :value="item.id"/>
                    </el-select>
                    <el-select v-model="formData.city_id" value-key="id" @change="handleFlash" clearable class="w-[200px] ml-3" ref="cityRef" :disabled="formData.is_system === 1">
                        <el-option :label="t('cityPlaceholder')" :value="0"/>
                        <el-option v-for="(item, index) in areaList.city " :key="index" :label="item.name"  :value="item.id"/>
                    </el-select>
                    <el-select v-model="formData.district_id" value-key="id" @change="handleFlash" clearable class="w-[200px] ml-3"  ref="districtRef" :disabled="formData.is_system === 1">
                        <el-option :label="t('districtPlaceholder')" :value="0"/>
                        <el-option v-for="(item, index) in areaList.district " :key="index" :label="item.name"  :value="item.id"/>
                    </el-select>
                </el-form-item>

                <el-form-item prop="address">
                    <div>
                        <el-input v-model.trim="formData.address" clearable :placeholder="t('addressPlaceholder')" @input="areaChange()" class="input-width" :disabled="formData.is_system === 1"/>
                    </div>
                </el-form-item>

                <el-form-item prop="area" v-loading="mapLoading">
                    <div class="relative w-full">
                        <div id="container" class="w-full h-[520px]"></div>
                        <div class="absolute bg-white w-[270px] h-[500px] top-[10px] left-[10px] region-list" v-if="formData.pick_up_type.indexOf('local_delivery') != -1">
                            <el-scrollbar>
                                <div class="p-[10px] region-item pr-[50px] relative" v-for="(item, index) in formData.area" :key="index" :class="{ '!border-primary': index == currArea }" @click="selectArea(index)">
                                    <el-form label-width="80px" :model="item" :rules="formRules" class="page-form"
                                        ref="areaFromRef">
                                        <div class="pb-[18px]">
                                            <el-form-item :label="t('areaName')" prop="area_name">
                                                <el-input v-model.trim="formData.area[index].area_name" type="text" @click.stop=""/>
                                            </el-form-item>
                                        </div>
                                        <div class="pb-[18px]">
                                            <el-form-item :label="t('startPrice')" prop="start_price">
                                                <el-input v-model.trim="formData.area[index].start_price"  maxlength="8" type="text"
                                                    @keyup="filterDigit($event)" @click.stop="" />
                                            </el-form-item>
                                        </div>
                                        <div class="pb-[10px]" v-show="initData.fee_type == 'region'">
                                            <el-form-item :label="t('deliveryPrice')" prop="delivery_price">
                                                <el-input v-model.trim="formData.area[index].delivery_price" type="text"
                                                    @keyup="filterDigit($event)"  @click.stop=""/>
                                            </el-form-item>
                                        </div>
                                        <el-form-item :label="t('areaType')">
                                            <el-radio-group v-model="formData.area[index].area_type" @change="areaTypeChange(index)">
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
import { ref, reactive, computed, onMounted, onBeforeUnmount, watch } from 'vue'
import { t } from '@/lang'
import { ArrowLeft } from '@element-plus/icons-vue'
import { useRoute, useRouter } from 'vue-router'
import { guid, filterNumber, filterDigit, deepClone, debounce } from '@/utils/common'
import { getMap, getAreaListByPid, getAreaByCode } from '@/app/api/sys'
import { createCircle, deleteGeometry, createPolygon, selectGeometry, createMarker, latLngToAddress, addressToLatLng } from '@/utils/qqmap'
import { getDeliveryStoreDetail, addDeliveryStore, editDeliveryStore, getStoreInit, getStorePickUpType } from '@/addon/shop/api/delivery'
import { FormInstance, ElMessage } from 'element-plus'
import Test from '@/utils/test'

const route = useRoute()
const router = useRouter()
const loading = ref(true)
const pageName = route.meta.title
const formRef = ref<FormInstance>()
const areaFromRef: any = ref<FormInstance[]>()
const initialFormData = {
    store_id: '',
    pick_up_type: [],
    store_name: '',
    contact_name: '',
    store_mobile: '',
    status: 1,
    province_id: 0,
    city_id: 0,
    district_id: 0,
    address: '',
    full_address: '',
    latitude: 39.908626,
    longitude: 116.397190,
    trade_time: '',
    time_is_open: 1,
    time_week: ['1', '2', '3', '4', '5', '6', '0'],
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
    ],
    trade_time_json: [
        { start_time: '', end_time: '' } // 初始一个时间段
    ],
    time_interval: 30,
    is_system: 0
}
const formData: Record<string, any> = reactive({ ...initialFormData })
formData.store_id = route.query.store_id
const pickUpType = ref([])
const initData = reactive<any>({
    fee_type: 'region',
    base_dist: '',
    base_price: '',
    grad_dist: '',
    grad_price: '',
    weight_start: 0.000,
    weight_unit: 0,
    weight_price: 0
})
const flash = ref(false)
const getStorePickUpTypeFn = () => {
    getStorePickUpType().then(res => {
        pickUpType.value = res.data
    })
}
getStorePickUpTypeFn()

const getDeliveryStoreDetailFn = () => {
    getDeliveryStoreDetail(formData.store_id).then(({ data }) => {
        loading.value = false
        Object.keys(formData).forEach((key: string) => {
            if (data[key] != undefined) formData[key] = data[key]
            if (key == 'trade_time_json' && Array.isArray(data[key])) {
                formData[key] = data[key].map((item: any) => ({
                    start_time: timestampTransition(item.start_time),
                    end_time: timestampTransition(item.end_time)
                }))
            }
        })
    }).catch(() => {
        loading.value = false
    })
}
if (route.query.store_id) {
    getDeliveryStoreDetailFn()
} else {
    loading.value = false
}

const refreshAddress = (bool = false) => {
    getDeliveryStoreDetail(formData.store_id).then(({ data }) => {
        loading.value = false
        Object.keys(formData).forEach((key: string) => {
            const addressKeyArr = ['store_name', 'contact_name', 'store_mobile', 'trade_time', 'province_id', 'city_id', 'district_id', 'address', 'full_address', 'latitude', 'longitude']
            if (addressKeyArr.includes(key)) {
                if (data[key] != undefined) formData[key] = data[key]
            }
        })
        if (bool) {
            ElMessage({
                message: t('refreshSuccess'),
                type: 'success'
            })
        }
    }).catch(() => {
        loading.value = false
    })
}

const week_list = ref({})
const time_interval_list = ref({})
const getStoreInitFn = () => {
    getStoreInit().then(res => {
        week_list.value = res.data.week_list
        time_interval_list.value = res.data.time_interval_list
        Object.keys(initData).forEach((key: string) => {
            if (res.data.local_delivery_config[key] != undefined) initData[key] = res.data.local_delivery_config[key]
        })
    })
}

interface areaType{
    province: any[],
    city: any[],
    district: any[]
}
const areaList = reactive<areaType>({
    province: [],
    city: [],
    district: []
})
const provinceRef = ref()
const cityRef = ref()
const districtRef = ref()

/**
 * 获取省
 */
getAreaListByPid(0).then(res => {
    areaList.province = res.data
})

let mapKey: string = ''
onMounted(() => {
    const mapScript = document.createElement('script')
    getMap().then(res => {
        mapKey = res.data.key
        mapScript.type = 'text/javascript'
        mapScript.src = 'https://map.qq.com/api/gljs?libraries=tools,service&v=1.exp&key=' + res.data.key
        document.body.appendChild(mapScript)
    })
    mapScript.onload = () => {
        setTimeout(() => {
            initMap()
        }, 500)
    }
    getStoreInitFn()
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
    const center = new LatLng(formData.latitude, formData.longitude)

    map = new TMap.Map('container', {
        center,
        zoom: 14
    })

    map.on('tilesloaded', () => {
        mapLoading.value = false
    })

    marker = createMarker(map)

    map.on('click', (evt: any) => {
        map.setCenter(evt.latLng)
        marker.updateGeometries({
            id: 'center',
            position: evt.latLng
        })
        latLngChange(evt.latLng.lat, evt.latLng.lng)
    })

    latLngChange(center.lat, center.lng)

    if (formData.pick_up_type.indexOf('local_delivery') != -1) {
        formData.area.forEach(item => {
            item.area_type == 'radius' ? createCircle(map, item.area_json) : createPolygon(map, item.area_json)
        })
    }
}

const storeArea = reactive({
    province_id: 0,
    city_id: 0,
    district_id: 0
})

const latLngChange = (lat: number, lng: number) => {
    latLngToAddress({ mapKey, lat, lng }).then(({ message, result }) => {
        if (message == 'query ok' || message == 'Success') {
            formData.latitude = result.location.lat
            formData.longitude = result.location.lng
            formData.address = result.formatted_addresses && result.formatted_addresses.recommend ? result.formatted_addresses.recommend : ''

            getAreaByCode(result.ad_info.adcode).then(({ data }) => {
                storeArea.province_id = data.province ? data.province.id : 0
                storeArea.city_id = data.city ? data.city.id : (data.district ? data.district.id : 0)
                storeArea.district_id = data.city ? (data.district ? data.district.id : 0) : 0
            })
        } else {
            console.error(message, result)
        }
    }).catch(() => {
        // console.log(err)
    })
}

/**
 * 获取市
 */
watch(() => formData.province_id, (nval) => {
    if (nval) {
        getAreaListByPid(formData.province_id).then(res => {
            areaList.city = res.data

            const cityId = formData.city_id
            if (cityId) {
                let isExist = false
                for (let i = 0; i < res.data.length; i++) {
                    if (cityId == res.data[i].id) {
                        isExist = true
                        break
                    }
                }
                if (isExist) {
                    formData.city_id = cityId
                    return
                }
            }
            formData.city_id = 0
            areaChange()
        })
    } else {
        formData.city_id = 0
    }
})

/**
* 获取区
 */
watch(() => formData.city_id, (nval) => {
    if (nval) {
        getAreaListByPid(formData.city_id).then(res => {
            areaList.district = res.data

            const districtId = formData.district_id
            if (districtId) {
                let isExist = false
                for (let i = 0; i < res.data.length; i++) {
                    if (districtId == res.data[i].id) {
                        isExist = true
                        break
                    }
                }
                if (isExist) {
                    formData.district_id = districtId
                    return
                }
            }
            areaChange()
            formData.district_id = 0
        })
    } else {
        formData.district_id = 0
    }
})

watch(() => formData.district_id, (nval) => {
    if (nval) {
        areaChange()
    }
})

const handleFlash = () => {
    flash.value = true
}

const areaChange = debounce(() => {
    setTimeout(() => {
        const province = areaList.province.map((item) => { if (item.id == formData.province_id) { return item.name } })
        const city = areaList.city.map((item) => { if (item.id == formData.city_id) { return item.name } })
        const district = areaList.district.map((item) => { if (item.id == formData.district_id) { return item.name } })

        const address = [
            formData.province_id ? (provinceRef.value.selectedLabel || province) : '',
            formData.city_id ? (cityRef.value.selectedLabel || city) : '',
            formData.district_id ? (districtRef.value.selectedLabel || district) : '',
            formData.address
        ]
        addressToLatLng({ mapKey, address: address.join('') }).then(({ message, result }) => {
            if (message == 'Success' || message == 'query ok') {
                const latLng = new (window as any).TMap.LatLng(result.location.lat, result.location.lng)
                map.setCenter(latLng)

                marker.updateGeometries({
                    id: 'center',
                    position: latLng
                })
                formData.latitude = result.location.lat
                formData.longitude = result.location.lng
                if (formData.pick_up_type.indexOf('local_delivery') != -1 && flash.value) {
                    formData.area.forEach(item => {
                        // 先删除旧的图形
                        deleteGeometry(item.area_json.key)
                        // 重新创建图形
                        item.area_json.center = {
                            lat: result.location.lat,
                            lng: result.location.lng
                        }
                        item.area_type == 'radius' ? createCircle(map, item.area_json) : createPolygon(map, item.area_json)
                    })
                }
            } else {
                console.error(message, result)
            }
        })
    }, 500)
}, 500)

/**
 * 地图点选获取市
 */
watch(() => storeArea.province_id, (nval) => {
    if (nval) {
        getAreaListByPid(storeArea.province_id).then(res => {
            areaList.city = res.data
            formData.province_id = storeArea.province_id
            formData.city_id = storeArea.city_id
        })
    }
})

/**
* 地图点选获取区
 */
watch(() => storeArea.city_id, (nval) => {
    if (nval) {
        getAreaListByPid(storeArea.city_id).then(res => {
            areaList.district = res.data
            formData.city_id = storeArea.city_id
            formData.district_id = storeArea.district_id
        })
    }
})

/**
 * 地图点选获取区
 */
watch(() => storeArea.district_id, (nval) => {
    if (nval) {
        formData.district_id = storeArea.district_id
    }
})

// 正则表达式
const regExp = {
    required: /[\S]+/,
    number: /^\d{0,10}$/,
    digit: /^\d{0,10}(.?\d{0,2})$/,
    special: /^\d{0,10}(.?\d{0,3})$/
}

// 表单验证规则
const formRules = reactive({
    store_name: [
        { required: true, message: t('storeNamePlaceholder'), trigger: 'blur' }
    ],
    contact_name: [
        { required: true, message: t('contactNamePlaceholder'), trigger: 'blur' }
    ],
    store_mobile: [
        { required: true, message: t('storeMobilePlaceholder'), trigger: 'blur' }
    ],
    trade_time: [
        { required: true, message: t('tradeTimePlaceholder'), trigger: 'blur' }
    ],
    time_week: [
        { required: true, message: t('timeWeekRequire'), trigger: 'change' }
    ],
    distance: [
        {
            validator: (rule: any, value: any, callback: any) => {
                if (initData.fee_type == 'distance') {
                    if (Test.require(formData.base_dist)) {
                        callback(new Error(t('baseDistRequire')))
                    } else if (Number(formData.base_dist) <= 0) {
                        callback(new Error(t('起始公里数不能小于等于0')))
                    }
                    if (Test.require(formData.base_price)) {
                        callback(new Error(t('basePriceRequire')))
                    }
                    if (Test.require(formData.grad_dist)) {
                        callback(new Error(t('gradDistRequire')))
                    } else if (Number(formData.grad_dist) <= 0) {
                        callback(new Error(t('超出公里数不能小于等于0')))
                    }
                    if (Test.require(formData.grad_price)) {
                        callback(new Error(t('gradPriceRequire')))
                    }
                    if (Number(formData.weight_start) <= 0) { // 重量不能小于等于0
                        callback(new Error(t('商品重量不能小于等于0')))
                    }
                    if (Number(formData.weight_unit) <= 0) { // 超出重量不能小于等于0
                        callback(new Error(t('商品超出重量不能小于等于0')))
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
                if (initData.fee_type == 'distance') {
                    if (Number(formData.weight_start) <= 0) { // 重量不能小于等于0
                        callback(new Error(t('商品重量不能小于等于0')))
                    }
                    if (Number(formData.weight_unit) <= 0) { // 超出重量不能小于等于0
                        callback(new Error(t('商品超出重量不能小于等于0')))
                    }
                }
                callback()
            },
            trigger: ['blur', 'change']
        }
    ],
    area_name: [{ required: true, message: t('areaNameRequire'), trigger: 'blur' }],
    start_price: [
        { required: true, message: t('startPriceRequire'), trigger: 'blur' },
        {
            validator: (rule: any, value: any, callback: any) => {
                if (parseInt(value) < 0) {
                    callback(new Error(t('startPriceMin')))
                }
                callback()
            },
            trigger: 'blur'
        }
    ],
    delivery_price: [
        { required: initData.fee_type == 'region', message: t('deliveryPriceRequire'), trigger: 'blur' },
        {
            validator: (rule: any, value: any, callback: any) => {
                if (parseInt(value) < 0) {
                    callback(new Error(t('deliveryPriceMin')))
                }
                callback()
            },
            trigger: 'blur'
        }
    ],
    area: [
        {
            validator: (rule: any, value: any, callback: any) => {
                if (Test.empty(formData.area)) {
                    callback(new Error(t('areaPlaceholder')))
                }
                callback()
            },
            trigger: 'blur'
        }
    ],
    trade_time_json: [
        {
            validator: (rule: any, value: any, callback: any) => {
                if (!value || value.length === 0) {
                    return callback(new Error(t('tradeTimePlaceholderTwo')))
                }
                for (let i = 0; i < value.length; i++) {
                    const timeRange = value[i]
                    if (!timeRange.start_time || !timeRange.end_time) {
                        return callback(new Error(t('tradeTimePlaceholderTwo')))
                    }
                    // 结束时间不能小于或等于开始时间
                    if (timeRange.end_time <= timeRange.start_time) {
                        return callback(new Error(t('tradeTimePlaceholderFour')))
                    }
                    // 确保后一个时间段的开始时间不能小于前一个时间段的结束时间
                    if (i > 0 && value[i].start_time < value[i - 1].end_time) {
                        return callback(new Error(t('tradeTimePlaceholderFive')))
                    }
                }
                callback()
            },
            trigger: 'change',
            required: true
        }
    ],
    time_interval: [
        { required: true, message: t('tradeTimePlaceholderThree'), trigger: 'change' }
    ],

    address_area: [
        {
            validator: (rule: any, value: any, callback: any) => {
                if (!formData.province_id) {
                    callback(new Error(t('provincePlaceholder')))
                }
                if (!formData.city_id) {
                    callback(new Error(t('cityPlaceholder')))
                }
                if (areaList.district.length && !formData.district_id) {
                    callback(new Error(t('districtPlaceholder')))
                }
                callback()
            }
        }
    ],
    address: [
        { required: true, message: t('addressPlaceholder'), trigger: 'blur' }
    ]
})

// 添加时间段
const addTimeRange = () => {
    formData.trade_time_json.push({ start_time: '', end_time: '' })
}

// 删除时间段
const removeTimeRange = (index: number) => {
    formData.trade_time_json.splice(index, 1)
}

const timeTransition = (time:any) => {
    const arr = time.split(':')
    const num = arr[0] * 60 * 60 + arr[1] * 60
    return num
}

const timestampTransition = (timeStamp:any) => {
    let hour = Math.floor(timeStamp / (60 * 60))
    let minute = Math.floor(timeStamp / 60) - (hour * 60)
    hour = hour < 10 ? ('0' + hour) : hour
    minute = minute < 10 ? ('0' + minute) : minute

    return hour + ':' + minute
}

const currArea = ref<number>(0)

/**
 * 添加配送区域
 */
const addArea = () => {
    formData.area.push({
        area_name: '',
        area_type: 'radius',
        start_price: 0,
        delivery_price: 0,
        area_json: {
            key: guid()
        }
    })
    const index = formData.area.length - 1
    createCircle(map, formData.area[index].area_json)
}

/**
 * 删除配送区域
 */
const deleteArea = (index: number) => {
    const data = formData.area[index]
    deleteGeometry(data.area_json.key)
    formData.area.splice(index, 1)
}

const selectArea = (index: number) => {
    currArea.value = index
    const data = formData.area[index]
    selectGeometry(data.area_json.key)
}

const areaTypeChange = (index: number) => {
    const data = formData.area[index]
    deleteGeometry(data.area_json.key)
    data.area_type == 'radius' ? createCircle(map, data.area_json) : createPolygon(map, data.area_json)
}

onBeforeUnmount(() => {
    map.destroy()
})

// 当配送类型发生改变时
const changePickUpType = (val: any) => {
    if (val.indexOf('local_delivery') == -1) {
        formData.area.forEach((item: any) => {
            deleteGeometry(item.area_json.key)
        })
    } else {
        formData.area.forEach((item: any) => {
            deleteGeometry(item.area_json.key)
            item.area_type == 'radius' ? createCircle(map, item.area_json) : createPolygon(map, item.area_json)
        })
    }
}

const onSave = async (formEl: FormInstance | undefined) => {
    if (loading.value || !formEl) return
    await formEl.validate(async (valid) => {
        let areaValidate = true

        for (let i = 0; i < areaFromRef.value?.length; i++) {
            const ref = areaFromRef.value[i]
            await ref.validate(async (valid) => {
                areaValidate = valid
            })
            if (!areaValidate) break
        }
        if (!areaValidate) return

        if (valid) {
            loading.value = true
            await formEl.validate(async (valid) => {
                const param: any = deepClone(formData)
                formData.province_name = formData.province_id ? provinceRef.value.selectedLabel : ''
                formData.city_name = formData.city_id ? cityRef.value.selectedLabel : ''
                formData.district_name = formData.district_id ? districtRef.value.selectedLabel : ''
                const address = [
                    param.province_id ? provinceRef.value.selectedLabel : '',
                    param.city_id ? cityRef.value.selectedLabel : '',
                    param.district_id ? districtRef.value.selectedLabel : '',
                    param.address
                ]
                param.full_address = address.join('')
                param.trade_time_json = param.trade_time_json.map((range: any) => ({
                    start_time: range.start_time ? timeTransition(range.start_time) : null,
                    end_time: range.end_time ? timeTransition(range.end_time) : null
                }))
                const api = formData.store_id ? editDeliveryStore : addDeliveryStore
                api(param).then(() => {
                    loading.value = false
                    router.push({ path: '/shop/delivery_store' })
                }).catch(() => {
                    loading.value = false
                })
            })
        }
    })
}
const back = () => {
    router.push({ path: '/shop/delivery_store' })
}

// 新窗口打开
const newWindow = () => {
    const url = router.resolve({
        path: '/shop/setting'
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
</style>
