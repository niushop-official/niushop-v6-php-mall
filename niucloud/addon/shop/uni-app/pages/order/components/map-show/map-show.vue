<template>
    <view  class="container" v-if="!loading">
        <!-- 微信小程序地图组件 -->
		<map  id="mapContainer"  :latitude="marker.center.latitude" :longitude="marker.center.longitude" :polyline="polyline" :include-points="marker.center" :markers="marker.points" :scale="scale" :min-scale="10" :max-scale="20" :style="mapStyle" enable-zoom></map>
    </view>
</template>

<script setup lang="ts">
import { computed, onMounted, ref } from 'vue';
import { img } from '@/utils/common';
import { getTrackOfLocal } from '@/addon/shop/api/order'
const prop = defineProps({
    tradeNo:{
      type: String,
      default: ''
    },
    height: {
        type: String,
        default: 'calc(50vh  + 150rpx)'
    }
})
const mapStyle = computed(() => {
    let style = ''
    style += `width:100%;`
    style += `height:${prop.height};`
    return style
})

const emit = defineEmits(['comfirm']);

// 查询地图
let marker = ref<any>({
    center: { 
        latitude: 39.909, 
        longitude: 116.39742 
    },
})
const loading = ref<any>(true)
let polyline = ref<any>(null)
let scale = ref<any>(12)
const getTrackOfLocalFn = () => {
    loading.value = true;
    getTrackOfLocal({ trade_no: prop.tradeNo }) .then((res: any) => {
        marker.value = res.data;
        if(marker.value){
            marker.value.points?.forEach((item: any) => {
                item.id = getRandomInt(1, 200)
                item.iconPath = img(item.icon_path)
                item.width = 40
                item.height = 40
                if(item.callout.content){
                    item.callout.padding = 11
                    item.callout.display = 'ALWAYS'
                    item.callout.bgColor = '#fa5050'
                    item.callout.color = '#fff'
                    item.callout.borderRadius = 6
                    item.callout.fontSize = 12
                }
            })
            processRouteData(marker.value.driving)
            scale.value = estimateZoomLevel(haversineDistance(marker.value.points[0].latitude, marker.value.points[0].longitude, marker.value.points[1].latitude, marker.value.points[marker.value.points.length - 1].longitude))
        }
        emit('comfirm', res.data)
        loading.value = false;
    }).catch(() => {
        loading.value = false;
    })
}
const  getRandomInt = (min: Number, max: Number) => {
    min = Math.ceil(min);
    max = Math.floor(max);
    return Math.floor(Math.random() * (max - min + 1)) + min;
}

onMounted(() => {
   getTrackOfLocalFn()
})
const  processRouteData = (result: any) => {
    if (!result) {
        return false
    }
    
    const route = result.routes[0];
    const coords = route.polyline;
    
    // 坐标解压（腾讯地图采用的压缩算法）
    const kr = 1000000;
    for (let i = 2; i < coords.length; i++) {
        coords[i] = Number(coords[i - 2]) + Number(coords[i]) / kr;
    }
    
    // 转换为polyline数据格式
    const points = [];
    for (let i = 0; i < coords.length; i += 2) {
        points.push({
            latitude: coords[i],
            longitude: coords[i + 1]
        });
    }
    
    // 更新地图路线
    polyline.value = [{
        points: points,
        color: '#f48aa2',
        width: 4,
        arrowLine: true // 显示方向箭头
    }];
}
const  haversineDistance = (lat1: any, lon1: any, lat2: any, lon2: any) => {
    var R = 6371; // 地球半径，单位为公里
    var dLat = deg2rad(lat2 - lat1);
    var dLon = deg2rad(lon2 - lon1);
    var a = 
      Math.sin(dLat / 2) * Math.sin(dLat / 2) +
      Math.cos(deg2rad(lat1)) * Math.cos(deg2rad(lat2)) * 
      Math.sin(dLon / 2) * Math.sin(dLon / 2);
    var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
    var distance = R * c; // 距离，单位为公里
    return distance * 1000;
}

const deg2rad = (deg: any) => {
    return deg * (Math.PI / 180);
}


const estimateZoomLevel = (distance: any) => {
    let zoomLevel;
    if (distance > 1000000) {
    zoomLevel = 5 - ((distance - 1000000) / 3000000) * 10;
    } else if (distance > 100000 && distance <= 1000000) {
    zoomLevel = 7 + ((distance - 100000) / 900000) * 6;
    } else if (distance > 50000 && distance <= 100000) {
    zoomLevel = 10 + (distance / 1000000) * 9;
    } else if (distance > 10000 && distance <= 50000) {
    zoomLevel = 11 + (distance / 1000000) * 6.5;
    } else if (distance > 5000 && distance <= 10000) {
    zoomLevel = 13 + (distance / 100000) * 6;
    } else if (distance > 900 && distance <= 5000) {
    zoomLevel = 14 + (distance / 100000) * 8;
    } else if (distance > 500 && distance <= 900) {
    zoomLevel = 16 + (distance / 10000) * 3;
    } else if (distance > 100 && distance <= 500) {
    zoomLevel = 16.5 + (distance / 10000) * 2;
    } else {
    zoomLevel = 17 + (distance / 10000) * 2;
    }
    // 限制缩放级别在合理范围内，假设缩放级别范围是3 - 17
    return parseInt(Math.max(3, Math.min(19, zoomLevel)) - 1);
}

</script>

<style lang="scss" scoped>

</style>