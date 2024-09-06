import { defineStore } from 'pinia'
import { getSiteInfo, getMap } from '@/app/api/system'

interface System {
    site: AnyObject | null,
    siteApps: string[],
    siteAddons: string[],
    currRoute: string,
    location: Object | null, // 定位信息
    mapConfig: Object
}

const useSystemStore = defineStore('system', {
    state: (): System => {
        return {
            site: null,
            siteApps: [],
            siteAddons: [],
            currRoute: '',
            location: null,
            mapConfig: {
                is_open: 1,
                valid_time: 0
            }
        }
    },
    actions: {
        async getSiteInfoFn() {
            await getSiteInfo().then((res: any) => {
                this.site = res.data
                const apps = [], addons = []
                Object.keys(res.data.site_addons).forEach((key:string) => {
                    const item = res.data.site_addons[key]
                    item.type == 'app' ? apps.push(key) : addons.push(key)
                })
                this.siteApps = apps
                this.siteAddons = addons
            }).catch((err) => {
            })
        },
        async getMapFn() {
            // 获取地图配置
            await getMap().then((res: any) => {
                this.mapConfig.is_open = res.data.is_open;
                this.mapConfig.valid_time = res.data.valid_time;
                uni.setStorageSync('mapConfig', this.mapConfig);
            })
        },
        setLocation(value: Object) {
            var date = new Date();
            date.setSeconds(60 * this.mapConfig.valid_time);
            value.valid_time = date.getTime() / 1000; // 定位信息 5分钟内有效，过期后将重新获取定位信息
            this.location = value;
            uni.setStorageSync('location', value); // 初始化数据调用
        }
    }
})

export default useSystemStore
