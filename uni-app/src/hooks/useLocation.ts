import { onLoad } from '@dcloudio/uni-app';
import { isWeixinBrowser,openMapSelector } from '@/utils/common';
import { getAddressByLatlng } from '@/app/api/system';
import wechat from '@/utils/wechat'
import useSystemStore from '@/stores/system';

// 单例定位服务
// 使用全局 Promise 确保整个应用中只有一个定位请求在执行
let locationPromise: Promise<void> | null = null;

export function useLocation(isOpenLocation: any) {
    const systemStore = useSystemStore();

    // 表示，定位是否开启
    let isOpen = isOpenLocation;
    if (systemStore.mapConfig.is_open != 1) {
        isOpen = false;
    }

    const init = () => {
        if (!isOpen) return false;
        
        // 使用 locationPromise 作为唯一的锁源
        if (locationPromise) {
            return;
        }
        
        // 立即设置空 Promise 作为锁
        locationPromise = new Promise<void>(() => {});
        
        // 排除手动地图选择位置的情况
        if (!uni.getStorageSync('manually_select_location_from_map')) {
            // #ifdef H5
            if (isWeixinBrowser() && systemStore.mapConfig.is_open && !uni.getStorageSync('location_address')) {
                wechat.init(() => {
                    wechat.getLocation(res => {
                        locationPromise = null;
                        let latlng = res.latitude + ',' + res.longitude;
                        getAddressByLatlngFn(latlng);
                    });
                });
            } else {
                locationPromise = null;
            }
            // #endif

            // #ifndef H5
            if (systemStore.mapConfig.is_open && !uni.getStorageSync('location_address')) {
                uni.getLocation({
                    // #ifndef APP-PLUS
                    type: 'gcj02',
                    // #endif
                    success: res => {
                        locationPromise = null;
                        let latlng = res.latitude + ',' + res.longitude;
                        getAddressByLatlngFn(latlng);
                    },
                    fail: (res) => {
                        locationPromise = null;
                        // uni.showToast({ title: res.errMsg, icon: 'none', duration: 15000 });
                        systemStore.defaultPositionAddress = '定位失败';
                        if (res.errno) {
                            if (res.errno == 104) {
                                let msg = '用户未授权隐私权限，获取位置失败';
                                // uni.showToast({ title: msg, icon: 'none' });
                                console.log(msg)
                            } else if (res.errno == 112) {
                                let msg = '隐私协议中未声明，获取位置失败';
                                // uni.showToast({ title: msg, icon: 'none' });
                                console.log(msg)
                            }
                        }
                        if (res.errMsg) {
                            if (res.errMsg.indexOf('getLocation:fail') != -1 || res.errMsg.indexOf('deny') != -1 || res.errMsg.indexOf('denied') != -1) {
                                let msg = '用户未授权获取位置权限，将无法提供服务';
                                // uni.showToast({ title: msg, icon: 'none' });
                                console.log(msg)
                            } else {
                                // uni.showToast({ title: res.errMsg, icon: 'none' });
                                console.log(res.errMsg)
                            }
                        }
                    }
                });
            } else {
                locationPromise = null;
            }
            // #endif
        } else {
            locationPromise = null;
        }
    };

    // 监听页面加载
    const onLoadLifeCycle = (callback: any = '') => {
        onLoad((option: any) => {
            if (option && option.latng) {
                getAddressByLatlngFn(option.latng)
            }

            uni.removeStorageSync('manually_select_location_from_map');
            typeof callback == 'function' && callback(option);
        });
    }

    // 刷新页面时需要调用的
    const refreshLocation = () => {
        if (!isOpen) return false;
        
        // 使用 locationPromise 作为唯一的锁源
        // 如果已经有定位在进行，直接返回
        if (locationPromise) {
            return;
        }
        
        // 立即设置空 Promise 作为锁（这是关键！必须在任何检查之前设置）
        // 第二个调用者会在上面的检查中看到 locationPromise 已存在，直接返回
        locationPromise = new Promise<void>(() => {});
        
        // 只读取一次本地缓存，减少重复存储调用
        const locationCache = uni.getStorageSync('location_address');
        const manualSelect = uni.getStorageSync('manually_select_location_from_map');

        // 存在手动选点标记，直接终止逻辑
        if (manualSelect) {
            locationPromise = null;
            return;
        }

        // 有定位缓存
        if (locationCache) {
            const cacheInfo = locationStorage();
            // 缓存未过期，恢复定位信息
            if (!cacheInfo.is_expired) {
                // 检查经纬度是否有效（非空且非0）
                const hasValidLatLng = locationCache.latitude && locationCache.longitude && 
                                      locationCache.latitude !== 0 && locationCache.longitude !== 0;
                if (hasValidLatLng) {
                    systemStore.setAddressInfo(locationCache);
                    locationPromise = null;
                    return; // 有有效缓存，直接返回，不触发定位
                }
                // 经纬度无效，继续往下走重新定位
            }
            // 缓存过期或经纬度无效，删除缓存
            uni.removeStorageSync('location_address');
        }

        // 无缓存或缓存无效/过期，直接执行定位
        // 排除手动地图选择位置的情况
        if (!uni.getStorageSync('manually_select_location_from_map')) {
            // #ifdef H5
            if (isWeixinBrowser() && systemStore.mapConfig.is_open) {
                wechat.init(() => {
                    wechat.getLocation(res => {
                        locationPromise = null;
                        let latlng = res.latitude + ',' + res.longitude;
                        getAddressByLatlngFn(latlng);
                    });
                });
            } else {
                locationPromise = null;
            }
            // #endif

            // #ifndef H5
            if (systemStore.mapConfig.is_open) {
                uni.getLocation({
                    // #ifndef APP-PLUS
                    type: 'gcj02',
                    // #endif
                    success: res => {
                        locationPromise = null;
                        let latlng = res.latitude + ',' + res.longitude;
                        getAddressByLatlngFn(latlng);
                    },
                    fail: (res) => {
                        locationPromise = null;
                        // uni.showToast({ title: res.errMsg, icon: 'none', duration: 15000 });
                        systemStore.defaultPositionAddress = '定位失败';
                        if (res.errno) {
                            if (res.errno == 104) {
                                let msg = '用户未授权隐私权限，获取位置失败';
                                // uni.showToast({ title: msg, icon: 'none' });
                                console.log(msg)
                            } else if (res.errno == 112) {
                                let msg = '隐私协议中未声明，获取位置失败';
                                // uni.showToast({ title: msg, icon: 'none' });
                                console.log(msg)
                            }
                        }
                        if (res.errMsg) {
                            if (res.errMsg.indexOf('getLocation:fail') != -1 || res.errMsg.indexOf('deny') != -1 || res.errMsg.indexOf('denied') != -1) {
                                let msg = '用户未授权获取位置权限，将无法提供服务';
                                // uni.showToast({ title: msg, icon: 'none' });
                                console.log(msg)
                            } else {
                                // uni.showToast({ title: res.errMsg, icon: 'none' });
                                console.log(res.errMsg)
                            }
                        }
                    }
                });
            } else {
                locationPromise = null;
            }
            // #endif
        } else {
            locationPromise = null;
        }
    };

    // 通过经纬度获取地址信息
    const getAddressByLatlngFn = (latlng: any = '') => {
        if (!isOpen) return false;
        let data = { latlng: '' };
        if (latlng) {
            data.latlng = latlng;
        } else {
            data.latlng = systemStore.diyAddressInfo.latitude + ',' + systemStore.diyAddressInfo.longitude;
        }
        getAddressByLatlng(data).then((res: any) => {
            if (res.data && Object.keys(res.data).length) {
                let obj: any = {};

                let latlngArr = data.latlng.split(',');

                obj.latitude = latlngArr[0];
                obj.longitude = latlngArr[1];
                obj.full_address = res.data.province != undefined ? res.data.province : '';
                obj.full_address += res.data.city != undefined ? res.data.city : '';
                obj.full_address += res.data.district != undefined ? res.data.district : '';
                obj.full_address += res.data.community != undefined ? res.data.community : '';

                obj.province_id = res.data.province_id;
                obj.province = res.data.province;
                obj.city_id = res.data.city_id;
                obj.city = res.data.city;
                obj.district_id = res.data.district_id;
                obj.district = res.data.district;
                obj.community = res.data.community;
                obj.formatted_addresses = res.data.formatted_addresses;
                systemStore.setAddressInfo(obj)
            } else {
                systemStore.setAddressInfo()
            }

            // 手动选择地图位置后，清除标识
            setTimeout(() => {
                // 由于异步请求速度影响，需要延迟删除
                uni.removeStorageSync('manually_select_location_from_map');
            }, 500);
        })
    }


    // 打开地图重新选择位置
    const reposition = () => {
        if (!isOpen) return false;

        let latitude = systemStore.diyAddressInfo ? systemStore.diyAddressInfo.latitude : '';
        let longitude = systemStore.diyAddressInfo ? systemStore.diyAddressInfo.longitude : '';

        // #ifndef H5
        uni.chooseLocation({
            latitude,
            longitude,
            success: (res) => {
                uni.setStorageSync('manually_select_location_from_map', true)
                let latng = res.latitude + ',' + res.longitude;
                getAddressByLatlngFn(latng)
            },
            fail: (res) => {
                // 在隐私协议中没有声明chooseLocation:fail api作用域
                if (res.errMsg && res.errno) {
                    if (res.errno == 104) {
                        let msg = '用户未授权隐私权限，选择位置失败';
                        // uni.showToast({ title: msg, icon: 'none' })
                        console.log(msg)
                    } else if (res.errno == 112) {
                        let msg = '隐私协议中未声明，打开地图选择位置失败';
                        // uni.showToast({ title: msg, icon: 'none' })
                        console.log(msg)
                    } else {
                        // uni.showToast({ title: res.errMsg, icon: 'none' })
                        console.log(res.errMsg)
                    }
                }
            }
        });
        // #endif

        // #ifdef H5
        uni.setStorageSync('manually_select_location_from_map', true)
        let backurl = location.origin + location.pathname;
        let params = ''
        if (latitude && longitude) {
            params = `latng=${ latitude },${ longitude }`
        }
        openMapSelector(backurl, params);
        // #endif
    }

    /**
     * 定位信息（缓存）
     */
    const locationStorage = () => {
        let data = uni.getStorageSync('location_address');
        if (data) {
            const date = new Date();
            if (systemStore.mapConfig.valid_time > 0) {
                data.is_expired = (date.getTime() / 1000) > data.valid_time; // 是否过期
            } else {
                data.is_expired = false;
            }
        } else {
            data = {
                is_expired: false
            };
        }
        return data;
    }

    return {
        init: init,
        onLoad: onLoadLifeCycle,
        refresh: refreshLocation,
        reposition: reposition
    }
}
