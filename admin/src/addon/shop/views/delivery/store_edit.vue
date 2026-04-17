<template>
    <div class="main-container">
        <el-card class="card !border-none mb-[15px]" shadow="never">
            <el-page-header :content="id ? t('updateStore') : t('addStore')" :icon="ArrowLeft" @back="back" />
        </el-card>
        
        <el-card class="box-card !border-none" shadow="never">
            <el-form :model="formData" label-width="140px" ref="formRef" :rules="formRules" class="page-form">
                <el-form-item :label="t('storeName')" prop="store_name">
                    <el-input v-model.trim="formData.store_name" clearable :placeholder="t('storeNamePlaceholder')" class="input-width" />
                </el-form-item>
                <el-form-item :label="t('storeDesc')">
                    <el-input v-model.trim="formData.store_desc" type="textarea" rows="4" clearable :placeholder="t('storeDescPlaceholder')" class="input-width" />
                </el-form-item>
                <el-form-item :label="t('storeLogo')">
                    <upload-image v-model="formData.store_logo" />
                </el-form-item>
                <el-form-item :label="t('storeMobile')" prop="store_mobile">
                    <el-input v-model.trim="formData.store_mobile" clearable :placeholder="t('storeMobilePlaceholder')" class="input-width" @keyup="filterNumber($event)" @blur="formData.store_mobile = $event.target.value"/>
                </el-form-item>
                <el-form-item :label="t('tradeTime')" prop="trade_time">
                    <div>
                        <el-input v-model.trim="formData.trade_time" clearable :placeholder="t('tradeTimePlaceholder')" class="input-width" />
                        <p class="text-[12px] text-[#999]">{{ t('tradeTimeTips') }}</p>
                    </div>
                </el-form-item>
                <el-form-item :label="t('storeAddress')" prop="address_area">
                    <el-select v-model="formData.province_name" value-key="id" clearable class="w-[200px]" @change="checkCity">
                        <el-option :label="t('provincePlaceholder')" value="" />
                        <el-option v-for="(provinceId, provinceIndex) in areaList.province " :key="provinceIndex" :label="provinceId.name" :value="provinceId" />
                    </el-select>
                    <el-select v-model="formData.city_name" value-key="id" clearable class="w-[200px] ml-3" @change="checkDistrict">
                        <el-option :label="t('cityPlaceholder')" value="" />
                        <el-option v-for="(cityId, cityIndex) in areaList.city " :key="cityIndex" :label="cityId.name" :value="cityId" />
                    </el-select>
                    <el-select v-model="formData.district_name" value-key="id" clearable class="w-[200px] ml-3" @change="check">
                        <el-option :label="t('districtPlaceholder')" value="" />
                        <el-option v-for="(districtId, districtIndex) in areaList.district " :key="districtIndex" :label="districtId.name" :value="districtId" />
                    </el-select>
                </el-form-item>
                <el-form-item :label="t('storeAddressDetail')" prop="address">
                    <div>
                        <div>
                            <el-input v-model.trim="formData.address" clearable :placeholder="t('storeAddressDetailPlaceholder')" class="input-width" />
                            <el-button class="ml-3" @click="searchOn">{{ t('search') }}</el-button>
                        </div>
                        <div class="mt-4">
                            <div id="TxMap" class="map-item w-[800px] h-[500px]"></div>
                        </div>
                    </div>
                </el-form-item>
            </el-form>
        </el-card>
        <div class="fixed-footer-wrap">
            <div class="fixed-footer">
                <el-button type="primary" @click="onSave(formRef)">{{ t('save') }}</el-button>
                <el-button @click="back()">{{ t('cancel') }}</el-button>
            </div>
        </div>
    </div>
</template>

<script lang="ts" setup>
import { ref, reactive, computed, onMounted, watch } from 'vue'
import { t } from '@/lang'
import type { FormInstance } from 'element-plus'
import { getStoreInfo, addStore, editStore } from '@/addon/shop/api/delivery'
import { useRoute, useRouter } from 'vue-router'
import { getAreaListByPid, getAreatree, getAddressInfo, getContraryAddress, getMap } from '@/app/api/sys'
import { AnyObject } from '@/types/global'
import { filterNumber } from '@/utils/common'
import { ElMessage } from 'element-plus'

const route = useRoute()
const router = useRouter()
const id: number = parseInt(route.query.id as string)
const loading = ref(false)

/**
 * 表单数据
 */
const initialFormData = {
    store_id: 0,
    store_name: '',
    store_desc: '',
    store_logo: '',
    store_mobile: '',
    province_id: 0,
    province_name: '',
    city_id: 0,
    city_name: '',
    district_id: 0,
    district_name: '',
    address: '',
    full_address: '',
    longitude: '',
    latitude: '',
    trade_time: ''
}
const formData: Record<string, any> = reactive({ ...initialFormData })

const setFormData = async (id: number = 0) => {
    Object.assign(formData, initialFormData)
    const data = await (await getStoreInfo(id)).data
    Object.keys(formData).forEach((key: string) => {
        if (data[key] != undefined) formData[key] = data[key]
    })
}
if (id) setFormData(id)

const formRef = ref<FormInstance>()

// 表单验证规则
const formRules = computed(() => {
    return {
        store_name: [
            { required: true, message: t('storeNamePlaceholder'), trigger: 'blur' }
        ],
        store_logo: [
            { required: true, message: t('storeLogoPlaceholder'), trigger: 'blur' }
        ],
        store_mobile: [
            { required: true, message: t('storeMobilePlaceholder'), trigger: 'blur' }
        ],
        province_id: [
            { required: true, message: t('provinceIdPlaceholder'), trigger: 'blur' }
        ],
        city_id: [
            { required: true, message: t('cityIdPlaceholder'), trigger: 'blur' }
        ],
        district_id: [
            { required: true, message: t('districtIdPlaceholder'), trigger: 'blur' }
        ],
        address_area: [
            { required: true, validator: validatePass, trigger: 'blur' }
        ],
        address: [
            { required: true, message: t('storeAddressDetailPlaceholder'), trigger: 'blur' }
        ],
        trade_time: [
            { required: true, message: t('tradeTimePlaceholder'), trigger: 'blur' }
        ]
    }
})

const validatePass = (rule: any, value: any, callback: any) => {
    if (formData.province_name == '' || formData.city_name == '' || formData.district_name == '') {
        callback(new Error(t('storeAddressPlaceholder')))
    }
    callback()
}

const onSave = async (formEl: FormInstance | undefined) => {
    if (loading.value || !formEl) return
    await formEl.validate(async (valid) => {
        if (valid) {
            loading.value = true
            const data = formData

            const save = id ? editStore : addStore
            save(data).then(res => {
                loading.value = false
                router.push('/shop/order/delivery/store')
            }).catch(() => {
                loading.value = false
            })
        }
    })
}

interface AreaList {
    province: { id: number, name: string }[]
    city: { id: number, name: string }[]
    district: { id: number, name: string }[]
}
const areaList = reactive<AreaList>({
    province: [],
    city: [],
    district: []
})
const checkAreaTressFn = () => {
    getAreatree(1).then(res => {
        areaList.province = res.data
    })
}
checkAreaTressFn()
const checkCity = (arr:AnyObject) => {
    if (Object.keys(arr).length == 0) {
        arr.id = formData.province_id
    } else {
        formData.province_id = arr.id
        formData.province_name = arr.name
    }
    getAreaListByPid(arr.id).then(res => {
        areaList.city = res.data
    })
}
const checkDistrict = (arr:AnyObject) => {
    if (Object.keys(arr).length == 0) {
        arr.id = formData.city_id
    } else {
        formData.city_id = arr.id
        formData.city_name = arr.name
    }
    getAreaListByPid(arr.id).then(res => {
        areaList.district = res.data
    })
}
const check = (arr:AnyObject) => {
    formData.district_id = arr.id
    formData.district_name = arr.name
}

const searchOn = () => {
    if (formData.province_id && formData.city_id && formData.district_id && formData.address) {
        formData.full_address = formData.province_name + formData.city_name + formData.district_name + formData.address
        getAddressInfo({
            address: formData.full_address
        }).then(res => {
            formData.latitude = res.data.result.location.lat
            formData.longitude = res.data.result.location.lng
        })
    }
}

let mapFn:any
const initMap = () => {
    // 定义地图中心点坐标
    let latitude = formData.latitude
    let longitude = formData.longitude
    if (formData.latitude == 0) latitude = '39.90469'
    if (formData.longitude == 0) longitude = '116.40717'
    const center = new window.TMap.LatLng(latitude, longitude)
    // 定义map变量，调用 TMap.Map() 构造函数创建地图
    mapFn = new window.TMap.Map('TxMap', {
        center, // 设置地图中心点坐标
        zoom: 17, // 设置地图缩放级别
        viewMode: '2D', // 设置2D模式
        showControl: true // 去掉控件
    })

    mapFn.on('click', (evt:any) => {
        const evtModel = {
            lat: evt.latLng.getLat().toFixed(6),
            lng: evt.latLng.getLng().toFixed(6)
        }
        checkAddressInfo(evtModel.lat, evtModel.lng, 1)
        markerLayer.updateGeometries({
            id: 'shop',
            position: evt.latLng
        })
    })

    const markerLayer = new window.TMap.MultiMarker({
        id: 'marker-layer',
        map: mapFn, // 显示Marker图层的底图
        minimumClusterSize: 1
    })

    markerLayer.updateGeometries({
        id: 'shop',
        position: center
    })

    mapFn.on('idle', () => {
        watch(() => formData.latitude, (newVal, oldVal) => {
            const latLng = new window.TMap.LatLng(formData.latitude, formData.longitude)
            mapFn.panTo(latLng, 1)
            markerLayer.updateGeometries({
                id: 'shop',
                position: latLng
            })
        })
    })
}
onMounted(() => {
    const mapScript = document.createElement('script')
    getMap().then(res => {
        mapScript.type = 'text/javascript'
        mapScript.src = 'https://map.qq.com/api/gljs?v=1.exp&key=' + res.data.key
        document.body.appendChild(mapScript)
    })
    mapScript.onload = () => {
        // 加载完成后初始化地图
        setTimeout(() => {
            // 需要定时执行的代码
            initMap()
        }, 500)
    }
})

const checkAddressInfo = (lat:string, lng:string, type:number) => {
    getContraryAddress({
        location: lat + ',' + lng
    }).then(res => {
        if(res.data.result) {
            formData.province_name = res.data.result.address_component.province
            formData.city_name = res.data.result.address_component.city
            formData.district_name = res.data.result.address_component.district
            if (type == 1) {
                formData.address = res.data.result.formatted_addresses.recommend
                formData.full_address = formData.province_name + formData.city_name + formData.district_name + formData.address
                formData.latitude = lat
                formData.longitude = lng
            }
        }else{
            ElMessage({
                type: 'warning',
                message: res.data.message
            })
        }
    })
}

const back = () => {
    router.push('/shop/order/delivery/store')
}
</script>

<style lang="scss" scoped>
.fixed-footer {
    z-index: 1000 !important
}
</style>
