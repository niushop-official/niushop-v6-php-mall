<template>
    <el-dialog v-model="showDialog" :title="t('editAddress')" width="700px" class="diy-dialog-wrap" :destroy-on-close="true" :before-close="handleClose">
        <div v-loading="loading">
            <el-form :model="formData" label-width="100px" ref="formRef" :rules="formRules" class="page-form mb-[30px]">
                <el-form-item :label="t('deliveryType')" prop="delivery_type">
                    <el-radio-group v-model="formData.delivery_type" @change="deliveryChange">
                        <el-radio :label="item.key" v-for="(item, index) in deliveryType" :key="index"  status>{{ item.name }}</el-radio>
                    </el-radio-group>
                </el-form-item>

                <!-- 物流配送/同城配送 -->
                <template v-if="changeDeliveryType == 'express' || changeDeliveryType =='local_delivery'">
                    <el-form-item :label="t('contacts')"  prop="taker_name">
                        <el-input v-model.trim="formData.taker_name" clearable :placeholder="t('contactsPlaceholder')" class="input-width" maxlength="10" />
                    </el-form-item>
                    <el-form-item :label="t('ContactInformation')"  prop="taker_mobile">
                        <el-input v-model.trim="formData.taker_mobile" clearable :placeholder="t('ContactInformationPlaceholder')" class="input-width" maxlength="15" />
                    </el-form-item>
                    <el-form-item :label="t('address')" prop="address_area">
                        <el-select v-model="formData.taker_province" value-key="id" clearable class="w-[150px]"  ref="provinceRef" :placeholder="t('province')" @change="addressChange">
                            <el-option :label="t('province')"  :value="0"/>
                            <el-option v-for="(item, index) in areaList.province" :key="index" :label="item.name"  :value="item.id"/>
                        </el-select>
                        <el-select v-model="formData.taker_city" value-key="id" clearable class="w-[150px] ml-3" ref="cityRef" :placeholder="t('city')" @change="addressChange">
                            <el-option :label="t('city')"  :value="0"/>
                            <el-option v-for="(item, index) in areaList.city " :key="index" :label="item.name"  :value="item.id"/>
                        </el-select>
                        <el-select v-model="formData.taker_district" value-key="id" clearable class="w-[150px] mt-[20px]"  ref="districtRef" :placeholder="t('area')" @change="addressChange">
                            <el-option :label="t('area')" :value="0"/>
                            <el-option v-for="(item, index) in areaList.district " :key="index" :label="item.name"  :value="item.id" />
                        </el-select>
                        <el-input v-model.trim="formData.taker_address" clearable :placeholder="t('detailedAddress')" class="!w-[214px] mt-[20px] ml-3" maxlength="30"/>
                    </el-form-item>

                </template>
                <el-form-item >
                    <!-- 地图display：none会渲染报错 -->
                    <div class="h-[0px] overflow-hidden" :class="{'!h-auto': changeDeliveryType == 'local_delivery'}">
                        <map-selector
                            ref="mapSelectorRef"
                            :longitude="formData.taker_longitude"
                            :latitude="formData.taker_latitude"
                            :zoom="14"
                            :container-style="'w-[500px] h-[270px] relative'"
                            @locationChange="handleLocationChange"
                        />
                    </div>
                    <div v-if="changeDeliveryType == 'local_delivery' && mapMessage" class="text-[#f52222] text-[12px] line-height-[15px]">{{ mapMessage }}</div>

                </el-form-item>
                <!-- 门店自提 -->
                <el-form-item :label="t('selfPickupStores')" v-if="changeDeliveryType == 'store'">
                    <el-select v-model="formData.take_store_id" value-key="store_id" @change="selectStore" >
                        <el-option v-for="(item, index) in deliveryList " :key="index" :label="item.store_name" :value="item.store_id"/>
                    </el-select>
                </el-form-item>

            </el-form>
        </div>
        <template #footer>
            <span class="dialog-footer">
                <el-button @click="showDialog = false">{{ t('cancel') }}</el-button>
                <el-button type="primary" :loading="loading" @click="confirm(formRef)">{{ t('confirm') }}</el-button>
            </span>
        </template>
    </el-dialog>
</template>

<script lang="ts" setup>
import { ref, reactive, computed, onMounted, watch, nextTick } from 'vue'
import { t } from '@/lang'
import { FormInstance } from 'element-plus'
import { getCompanyList, getShopDeliveryList } from '@/addon/shop/api/delivery'
import { getOrderEditAddress, orderEditAddress, getDeliveryList } from '@/addon/shop/api/order'
import { getIsTradeManaged } from '@/app/api/weapp'
import { getAreaListByPid, getAreaByCode } from '@/app/api/sys'
import { debounce, deepClone } from '@/utils/common'
import MapSelector from '@/components/map-selector/index.vue'

const showDialog = ref(false)
const loading = ref(false)
const mapSelectorRef = ref()

interface companyDataType{
    company_id: number,
    company_name: string,
    index: number
}
const companyData = ref<companyDataType[]>([])
const deliveryType = ref([])
const isTradeManaged = ref(false)
const deliveryList = ref([])

getCompanyList({}).then((data) => {
    companyData.value = data.data
})

getIsTradeManaged().then(res => {
    isTradeManaged.value = res.data.is_trade_managed
})

/**
 * 表单数据
 */
const initialFormData = {
    order_id: 0,
    delivery_type: '',
    taker_name: '',
    taker_mobile: '',
    taker_latitude: '',
    taker_longitude: '',
    taker_full_address: '',
    taker_address: '',
    taker_province: 0,
    taker_city: 0,
    taker_district: 0,
    is_delivery_address: 0,
    is_refund_address: 0,
    is_default_delivery: 0,
    is_default_refund: 0,
    take_store_id: ''
}
const formData: Record<string, any> = reactive({ ...initialFormData })

const formRef = ref<FormInstance>()
// 表单验证规则
const formRules = computed(() => {
    return {
        delivery_type: [
            { required: true, message: t('deliveryTypePlaceholder'), trigger: 'blur' }
        ],
        taker_name: [
            { required: true, validator: companyPass, trigger: 'blur' }
        ],
        taker_mobile: [
            { required: true, validator: ContactInformation, trigger: 'blur' }
        ],
        address_area: [
            {
                required: true,
                validator: (rule: any, value: any, callback: any) => {
                    if (!formData.taker_province) {
                        callback(new Error(t('provincePlaceholder')))
                    }
                    if (!formData.taker_city) {
                        callback(new Error(t('cityPlaceholder')))
                    }
                    if (areaList.district.length && !formData.taker_district) {
                        callback(new Error(t('districtPlaceholder')))
                    }
                    if (!formData.taker_address) {
                        callback(new Error(t('detailedAddress')))
                    }
                    callback()
                }
            }
        ]
    }
})

const companyPass = (rule: any, value: any, callback: any) => {
    if (formData.delivery_type == 'express' && value === '') {
        callback(new Error(t('contactsPlaceholder')))
    } else {
        callback()
    }
}

const ContactInformation = (rule: any, value: any, callback: any) => {
    if (formData.delivery_type == 'express' || formData.delivery_type == 'local_delivery') {
        const reg = /^1[3-9]\d{9}$/
        if (value === '') {
            callback(new Error(t('ContactInformationPlaceholder')))
        } else if (value.length != 11) {
            callback(new Error('请输入11位手机号'))
        } else if (!reg.test(value)) {
            callback(new Error('请输入正确的手机号'))
        }
    }
    callback()
}

const emit = defineEmits(['complete'])

const selectStore = (data:any) => {
    formData.take_store_id = data
}

const addressChange = () => {
    formData.taker_address = ''
}

/**
 * 确认
 * @param formEl
 */
const confirm = async (formEl: FormInstance | undefined) => {
    if (loading.value || !formEl) return
    await formEl.validate(async (valid) => {
        if (valid) {
            loading.value = true
            // 根据选择的配送方式进行不同的提交方法
            if (changeDeliveryType.value == 'express' || changeDeliveryType.value == 'local_delivery') {
                const address = [
                    formData.taker_province ? addressName(areaList.province, formData.taker_province) : '',
                    formData.taker_city ? addressName(areaList.city, formData.taker_city) : '',
                    formData.taker_district ? addressName(areaList.district, formData.taker_district) : '',
                    formData.taker_address
                ]
                formData.taker_full_address = address.join('')
                formData.delivery_type = changeDeliveryType.value

                orderEditAddress(formData).then(res => {
                    loading.value = false
                    showDialog.value = false
                    emit('complete')
                    initData()
                }).catch(() => {
                    loading.value = false
                })
            } else {
                const params = {
                    order_id: formData.order_id,
                    delivery_type: changeDeliveryType.value,
                    take_store_id: formData.take_store_id
                }
                orderEditAddress(params).then(res => {
                    loading.value = false
                    showDialog.value = false
                    emit('complete')
                }).catch(() => {
                    loading.value = false
                })
            }
        }
    })
}

const getAddressInfoFn = (data:any) => {
    getOrderEditAddress(data).then((res) => {
        for (const i in formData) {
            if (res.data[i]) {
                formData[i] = res.data[i]
            }
        }

        deliveryChange(formData.delivery_type)
        loading.value = false
    })
}

// 初始化
const setFormData = async (data: any) => {
    loading.value = true
    const params = {
        order_id: data.order_id,
        delivery_type: data.delivery_type
    }
    nextTick(() => {
        initMap()
        getAddressInfoFn(params)
    })
}

// 获取配送方式
const getShopDeliveryListFn = () => {
    getShopDeliveryList().then((res) => {
        const data = res.data ? deepClone(res.data) : []
        deliveryType.value = data.filter(item => item.status == 1)
    })
}
// 获取自提点列表
getShopDeliveryListFn()
const changeDeliveryType = ref()
const deliveryChange = (data) => {
    changeDeliveryType.value = data
    if (changeDeliveryType.value == 'local_delivery') areaChange()
}

// 门店自提列表
const deliveryListFn = () => {
    getDeliveryList().then((res) => {
        deliveryList.value = res.data
    })
}
deliveryListFn()

const mapMessage = ref('')

// 监听地图位置变化事件
const handleLocationChange = (data: { lat: number, lng: number, address: any }) => {
    formData.taker_latitude = data.lat
    formData.taker_longitude = data.lng
    // 处理地址信息
    if (data.address) {
        // 腾讯地图返回格式
        if (data.address.formatted_addresses && !data.address.addressComponent) {
            formData.taker_address = data.address.formatted_addresses?.recommend || ''
            const addressStr = data.address.address || ''
            matchAreaFromAddress(addressStr)
        } else if (data.address.addressComponent) {
            // 天地图返回格式
            const detailAddr = data.address.formatted_addresses?.recommend || data.address.formatted_address || data.address.poi || ''
            formData.taker_address = detailAddr
            matchAreaFromTianditu(data.address.addressComponent)
        }
    }
}

// 从地址字符串匹配省市区（腾讯地图）
const matchAreaFromAddress = (addressStr: string) => {
    const provinceMatch = addressStr.match(/^(.+?省|.+?市|.+?自治区)/)
    const cityMatch = addressStr.match(/(.+?市)/)
    const districtMatches = addressStr.match(/(.+?区|.+?县)/g)
    if (provinceMatch) {
        const provinceName = provinceMatch[1]
        const matchedProvince = areaList.province.find(item => 
            item.name.includes(provinceName) || provinceName.includes(item.name)
        )
        if (matchedProvince) {
            formData.taker_province = matchedProvince.id
            getAreaListByPid(formData.taker_province).then(res => {
                areaList.city = res.data || []
                if (cityMatch) {
                    const cityName = cityMatch[1]
                    const matchedCity = areaList.city.find(item => 
                        item.name.includes(cityName) || cityName.includes(item.name)
                    ) 
                    if (matchedCity) {
                        formData.taker_city = matchedCity.id
                        getAreaListByPid(formData.taker_city).then(res => {
                            areaList.district = res.data || []
                            
                            if (districtMatches && districtMatches.length > 0) {
                                const districtName = districtMatches[districtMatches.length - 1]
                                const matchedDistrict = areaList.district.find(item => 
                                    item.name.includes(districtName) || districtName.includes(item.name)
                                )
                                if (matchedDistrict) {
                                    formData.taker_district = matchedDistrict.id
                                }
                            }
                        })
                    }
                }
            })
        }
    }
}

// 从天地图地址组件匹配省市区
const matchAreaFromTianditu = (addressComp: any) => {
    if (!addressComp) return
    // 匹配省
    if (addressComp.province) {
        const matchedProvince = areaList.province.find(item => 
            item.name.includes(addressComp.province) || addressComp.province.includes(item.name)
        )
        if (matchedProvince) {
            formData.taker_province = matchedProvince.id
            getAreaListByPid(formData.taker_province).then(res => {
                areaList.city = res.data || []
                // 匹配市
                if (addressComp.city) {
                    const matchedCity = areaList.city.find(item => 
                        item.name.includes(addressComp.city) || addressComp.city.includes(item.name)
                    )
                    if (matchedCity) {
                        formData.taker_city = matchedCity.id
                        getAreaListByPid(formData.taker_city).then(res => {
                            areaList.district = res.data || []     
                            // 匹配区
                            if (addressComp.county) {
                                const matchedDistrict = areaList.district.find(item => 
                                    item.name.includes(addressComp.county) || addressComp.county.includes(item.name)
                                )
                                if (matchedDistrict) {
                                    formData.taker_district = matchedDistrict.id
                                }
                            }
                        })
                    }
                }
            })
        }
    }
}

// 初始化地图（现在使用组件，此函数保留但内容为空）
const initMap = () => {
    // 地图组件会自动初始化
}

onMounted(() => {
    // 地图组件会自动初始化
})

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
 * 获取市
 */
watch(() => formData.taker_province, (nval) => {
    if (nval) {
        getAreaListByPid(formData.taker_province).then(res => {
            areaList.city = res.data
            const cityId = formData.taker_city
            if (cityId) {
                let isExist = false
                for (let i = 0; i < res.data.length; i++) {
                    if (cityId == res.data[i].id) {
                        isExist = true
                        break
                    }
                }
                if (isExist) {
                    formData.taker_city = cityId
                    return
                }
            }
            formData.taker_city = 0

            if (changeDeliveryType.value == 'local_delivery') areaChange()
        })
    } else {
        formData.taker_city = 0
    }
})

/**
* 获取区
 */
watch(() => formData.taker_city, (nval) => {
    if (nval) {
        getAreaListByPid(formData.taker_city).then(res => {
            areaList.district = res.data

            const districtId = formData.taker_district
            if (districtId) {
                let isExist = false
                for (let i = 0; i < res.data.length; i++) {
                    if (districtId == res.data[i].id) {
                        isExist = true
                        break
                    }
                }
                if (isExist) {
                    formData.taker_district = districtId
                    return
                }
            }
            if (changeDeliveryType.value == 'local_delivery') areaChange()
            formData.taker_district = 0
        })
    } else {
        formData.taker_district = 0
    }
})

watch(() => formData.taker_district, (nval) => {
    if (nval) {
        if (changeDeliveryType.value == 'local_delivery') areaChange()
    }
})

const areaChange = debounce(async () => {
    setTimeout(async () => {
        const fullAddress = [
            formData.taker_province ? provinceRef.value?.states?.selectedLabel : '',
            formData.taker_city ? cityRef.value?.states?.selectedLabel : '',
            formData.taker_district ? districtRef.value?.states?.selectedLabel : '',
            formData.taker_address
        ].join('')
        
        if (fullAddress && mapSelectorRef.value) {
            try {
                const result = await mapSelectorRef.value.searchAddress(fullAddress)
                formData.taker_latitude = result.lat
                formData.taker_longitude = result.lng
            } catch (error) {
                console.error('地址搜索失败:', error)
            }
        }
    }, 500)
}, 500)

/**
 * 获取省
 */
getAreaListByPid(0).then(res => {
    areaList.province = res.data
})

const handleClose = (next:any) => {
    initData()
    next()
}
const initData = () => {
    const data = {
        order_id: 0,
        delivery_type: '',
        taker_name: '',
        taker_mobile: '',
        taker_latitude: '',
        taker_longitude: '',
        taker_full_address: '',
        taker_address: '',
        taker_province: 0,
        taker_city: 0,
        taker_district: 0,
        is_delivery_address: 0,
        is_refund_address: 0,
        is_default_delivery: 0,
        is_default_refund: 0,
        take_store_id: ''
    }
    Object.keys(formData).forEach((key: string) => {
        if (data[key] != undefined) formData[key] = data[key]
    })
}

const addressName = (data, id) => {
    const address = data.find(item => item.id === id)
    return address.name
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
