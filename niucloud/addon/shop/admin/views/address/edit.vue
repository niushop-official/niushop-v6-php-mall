<template>
	<div class="main-container">
        <el-card class="card !border-none mb-[15px]" shadow="never">
            <el-page-header :content="pageName" :icon="ArrowLeft" @back="back" />
        </el-card>
		<el-card class="box-card !border-none" shadow="never" v-loading="loading">
			<el-form :model="formData" label-width="90px" ref="formRef" :rules="formRules" class="page-form">
				<el-form-item :label="t('addressType')" prop="address_type">
					<div class="flex flex-col">
						<div>
							<el-checkbox v-model="formData.is_delivery_address" :label="t('deliveryAddress')" :true-label="1" :false-label="0"/>
							<el-checkbox v-model="formData.is_default_delivery" :label="t('defaultDeliveryAddress')" :true-label="1" :false-label="0" v-show="formData.is_delivery_address"/>
						</div>
						<div>
							<el-checkbox v-model="formData.is_refund_address" :label="t('refundAddress')" :true-label="1" :false-label="0"/>
							<el-checkbox v-model="formData.is_default_refund" :label="t('defaultRefundAddress')" :true-label="1" :false-label="0" v-show="formData.is_refund_address"/>
						</div>
					</div>
				</el-form-item>

				<el-form-item :label="t('contactName')" prop="contact_name">
					<el-input v-model.trim="formData.contact_name" clearable :placeholder="t('contactNamePlaceholder')" class="input-width" maxlength="10" />
				</el-form-item>

				<el-form-item :label="t('mobile')" prop="mobile">
					<el-input v-model.trim="formData.mobile" clearable :placeholder="t('mobilePlaceholder')" maxlength="11" class="input-width"  @keyup="filterNumber($event)" @blur="formData.mobile = $event.target.value"/>
				</el-form-item>

				<el-form-item :label="t('fullAddress')" prop="address_area">
					<el-select v-model="formData.province_id" value-key="id" clearable class="w-[200px]"  ref="provinceRef">
						<el-option :label="t('provincePlaceholder')" :value="0"/>
						<el-option v-for="(item, index) in areaList.province" :key="index" :label="item.name"  :value="item.id"/>
					</el-select>
					<el-select v-model="formData.city_id" value-key="id" clearable class="w-[200px] ml-3" ref="cityRef">
						<el-option :label="t('cityPlaceholder')" :value="0"/>
						<el-option v-for="(item, index) in areaList.city " :key="index" :label="item.name"  :value="item.id"/>
					</el-select>
					<el-select v-model="formData.district_id" value-key="id" clearable class="w-[200px] ml-3"  ref="districtRef">
						<el-option :label="t('districtPlaceholder')" :value="0"/>
						<el-option v-for="(item, index) in areaList.district " :key="index" :label="item.name"  :value="item.id"/>
					</el-select>
				</el-form-item>

				<el-form-item prop="address">
					<el-input v-model.trim="formData.address" clearable :placeholder="t('addressPlaceholder')" @input="areaChange()" class="input-width"/>
				</el-form-item>

				<el-form-item>
					<map-selector
						ref="mapSelectorRef"
						:longitude="formData.lng"
						:latitude="formData.lat"
						:zoom="14"
						:width="800"
                        :height="500"
						@locationChange="handleLocationChange"
					/>
				</el-form-item>
			</el-form>
		</el-card>
		<div class="fixed-footer-wrap">
			<div class="fixed-footer !z-[9999]">
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
import { ArrowLeft } from "@element-plus/icons-vue"
import { getShopAddressInfo, addShopAddress, editShopAddress } from '@/addon/shop/api/shop_address'
import { getAreaListByPid, getAreaByCode } from '@/app/api/sys'
import { useRoute } from 'vue-router'
import { filterNumber, debounce } from '@/utils/common'
import MapSelector from '@/components/map-selector/index.vue'

const route = useRoute()
const id: number = parseInt(route.query.id as string)
const loading = ref(false)
const pageName = route.meta.title
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
const mapSelectorRef = ref()

/**
 * 获取省
 */
getAreaListByPid(0).then(res => {
    areaList.province = res.data
})

onMounted(() => {
    // 地图组件会自动初始化
})

/**
* 处理地图位置变化
 */
const handleLocationChange = (data: { lat: number, lng: number, address: any }) => {
    formData.lat = data.lat
    formData.lng = data.lng
    
    // 处理地址信息
    if (data.address) {
        // 腾讯地图返回格式
        if (data.address.formatted_addresses && !data.address.addressComponent) {
            formData.address = data.address.formatted_addresses?.recommend || ''
            const addressStr = data.address.address || ''
            matchAreaFromAddress(addressStr)
        } else if (data.address.addressComponent) {
            // 天地图返回格式
            // 使用 formatted_addresses.recommend 或 formatted_address
            const detailAddr = data.address.formatted_addresses?.recommend || data.address.formatted_address || data.address.poi || ''
            formData.address = detailAddr
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
            formData.province_id = matchedProvince.id
            getAreaListByPid(matchedProvince.id).then((cityRes: any) => {
                areaList.city = cityRes.data || []
                
                if (cityMatch) {
                    const cityName = cityMatch[1]
                    const matchedCity = areaList.city.find(item => 
                        item.name.includes(cityName) || cityName.includes(item.name)
                    )
                    
                    if (matchedCity) {
                        formData.city_id = matchedCity.id
                        getAreaListByPid(matchedCity.id).then((districtRes: any) => {
                            areaList.district = districtRes.data || []
                            
                            if (districtMatches && districtMatches.length > 0) {
                                const districtName = districtMatches[districtMatches.length - 1]
                                const matchedDistrict = areaList.district.find(item => 
                                    item.name.includes(districtName) || districtName.includes(item.name)
                                )
                                if (matchedDistrict) {
                                    formData.district_id = matchedDistrict.id
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
            formData.province_id = matchedProvince.id
            getAreaListByPid(matchedProvince.id).then((cityRes: any) => {
                areaList.city = cityRes.data || []
                
                // 匹配市
                if (addressComp.city) {
                    const matchedCity = areaList.city.find(item => 
                        item.name.includes(addressComp.city) || addressComp.city.includes(item.name)
                    )
                    if (matchedCity) {
                        formData.city_id = matchedCity.id
                        getAreaListByPid(matchedCity.id).then((districtRes: any) => {
                            areaList.district = districtRes.data || []
                            
                            // 匹配区
                            if (addressComp.county) {
                                const matchedDistrict = areaList.district.find(item => 
                                    item.name.includes(addressComp.county) || addressComp.county.includes(item.name)
                                )
                                if (matchedDistrict) {
                                    formData.district_id = matchedDistrict.id
                                }
                            }
                        })
                    }
                }
            })
        }
    }
}

/**
* 表单数据
*/
const initialFormData = {
    id: 0,
    contact_name: '',
    mobile: '',
    province_id: 0,
    city_id: 0,
    district_id: 0,
    address: '',
    full_address: '',
    lat: 39.908626,
    lng: 116.397190,
    is_delivery_address: 0,
    is_refund_address: 0,
    is_default_delivery: 0,
    is_default_refund: 0
}

const formData: Record<string, any> = reactive({ ...initialFormData })

const setFormData = async (id: number = 0) => {
    loading.value = true
    Object.assign(formData, initialFormData)
    const data = await (await getShopAddressInfo(id)).data
    Object.keys(formData).forEach((key: string) => {
        if (data[key] != undefined) formData[key] = data[key]
    })
    loading.value = false
}
if (id) setFormData(id)

const formRef = ref<FormInstance>()

// 表单验证规则
const formRules = computed(() => {
    return {
        address_type: [
            {
                validator: (rule: any, value: any, callback: any) => {
                    if (!formData.is_delivery_address && !formData.is_refund_address) {
                        callback(new Error(t('addressTypeRequire')))
                    }
                    callback()
                }
            }
        ],
        contact_name: [
            { required: true, message: t('contactNamePlaceholder'), trigger: 'blur' }
        ],
        mobile: [
            { required: true, message: t('mobilePlaceholder'), trigger: 'blur' },
            {
                trigger: 'blur',
                validator: (rule: any, value: any, callback: any) => {
                    if (value && !/^1[3-9]\d{9}$/.test(value)) {
                        callback(new Error(t('mobileTips')))
                    }
                    callback()
                }
            }
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
    }
})

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

const areaChange = debounce(async () => {
    setTimeout(async () => {
        // 获取省名称：找到第一个id匹配的省，取它的name；没找到则返回空字符串
        let province = areaList.province.find(item => item.id == formData.province_id)?.name || '';
        // 获取市名称
        let city = areaList.city.find(item => item.id == formData.city_id)?.name || '';
        // 获取区名称
        let district = areaList.district.find(item => item.id == formData.district_id)?.name || '';

        const fullAddress = [
            formData.province_id ? (provinceRef.value?.selectedLabel || province) : '',
            formData.city_id ? (cityRef.value?.selectedLabel || city) : '',
            formData.district_id ? (districtRef.value?.selectedLabel || district) : '',
            formData.address
        ].join('')
        
        if (fullAddress && mapSelectorRef.value) {
            try {
                const result = await mapSelectorRef.value.searchAddress(fullAddress)
                formData.lat = result.lat
                formData.lng = result.lng
            } catch (error) {
                console.error('地址搜索失败:', error)
            }
        }
    }, 500)
}, 500)

// 地图点选获取区域信息已在handleLocationChange中处理

const onSave = async (formEl: FormInstance | undefined) => {
    if (loading.value || !formEl) return
    await formEl.validate(async (valid) => {
        if (valid) {
            loading.value = true

            const data = formData
            let province = areaList.province.find(item => item.id == formData.province_id)?.name || '';
            // 获取市名称：逻辑同上
            let city = areaList.city.find(item => item.id == formData.city_id)?.name || '';
            // 获取区名称：逻辑同上
            let district = areaList.district.find(item => item.id == formData.district_id)?.name || '';

            const address = [
                data.province_id ? (provinceRef.value.selectedLabel || province) : '',
                data.city_id ? (cityRef.value.selectedLabel || city) : '',
                data.district_id ? (districtRef.value.selectedLabel || district) : '',
                data.address
            ]
            data.full_address = address.join('')

            const save = id ? editShopAddress : addShopAddress
            save(data).then(res => {
                loading.value = false
                history.back()
            }).catch(() => {
                loading.value = false
            })
        }
    })
}

const back = () => {
    history.back()
}
</script>

<style lang="scss" scoped></style>
