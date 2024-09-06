import { defineStore } from 'pinia'
import { setToken, removeToken, redirect } from '@/utils/common'
import { getMemberInfo, getMemberLevel } from '@/app/api/member'
import { logout } from '@/app/api/auth'
import useConfigStore from '@/stores/config'
interface Member {
    token: string | null
    info: AnyObject | null
    levelList: Array<any> | null
}

const useMemberStore = defineStore('member', {
    state: (): Member => {
        return {
            token: uni.getStorageSync(import.meta.env.VITE_REQUEST_STORAGE_TOKEN_KEY),
            info: null,
            levelList: null
        }
    },
    actions: {
        async setToken(token: string) {
            this.token = token
            setToken(token)
            await this.getMemberInfo()
        },
        async getMemberInfo() {
            if (!this.token) return
            await getMemberInfo().then((res: any) => {
                this.info = res.data
                uni.setStorageSync('wap_member_info', this.info)
                uni.setStorageSync('wap_member_id', res.data.member_id)
            }).catch(async() => {
                await this.logout()
            })
        },
        async logout(isRedirect: boolean = false) {
            if (!this.token) return
            this.token = ''
            this.info = null
            if(useConfigStore().login.is_auth_register){
                uni.setStorageSync('autoLoginLock', true) // todo 普通账号退出登录,在进行三方账号登录不会自动登录
            }
            await logout().then(() => {
                removeToken()
                uni.removeStorageSync('wap_member_info');
                uni.removeStorageSync('openid');
                uni.removeStorageSync('isbindmobile');
                isRedirect && redirect({ url: '/app/pages/index/index', mode: 'switchTab' })
            }).catch(() => {
                removeToken()
                uni.removeStorageSync('wap_member_info');
                uni.removeStorageSync('openid');
                uni.removeStorageSync('isbindmobile');
                isRedirect && redirect({ url: '/app/pages/index/index', mode: 'switchTab' })
            })
        },
        getMemberLevel() {
            getMemberLevel().then((res: any) => {
                this.levelList = res.data
            })
        }
    }
})

export default useMemberStore
