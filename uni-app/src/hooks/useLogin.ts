import { redirect, isWeixinBrowser, urlDeconstruction } from '@/utils/common'
import {
    weappLogin,
    updateWeappOpenid,
    wechatLogin,
    updateWechatOpenid,
    wechatUser,
    wechatUserLogin
} from '@/app/api/auth'
import { getWechatAuthCode } from '@/app/api/system'
import useMemberStore from '@/stores/member'
import useConfigStore from '@/stores/config'

export function useLogin() {
    /**
     * 设置登录返回页
     */
    const setLoginBack = (data: redirectOptions) => {
        uni.setStorage({ key: 'loginBack', data })
        setTimeout(() => {
            const config = useConfigStore()
            // #ifdef MP-WEIXIN
            if (!uni.getStorageSync('autoLoginLock') && uni.getStorageSync('openid') && config.login.is_bind_mobile) {
                uni.setStorageSync('isbindmobile', true)
                return
            }
            // #endif

            // #ifdef H5
            if (!uni.getStorageSync('autoLoginLock') && isWeixinBrowser() && uni.getStorageSync('openid') && config.login.is_bind_mobile) {
                uni.setStorageSync('isbindmobile', true)
                return
            }
            // #endif
            redirect({ url: '/app/pages/auth/login', mode: 'redirectTo' })
        })
    }

    /**
     * 执行登录后跳转
     */
    const handleLoginBack = () => {
        uni.removeStorageSync('autoLoginLock')
        uni.getStorage({
            key: 'loginBack',
            success: (data: AnyObject) => {
                data ? redirect(data.data) : redirect({ url: '/app/pages/index/index', mode: 'switchTab' })
            },
            fail: (res) => {
                redirect({ url: '/app/pages/index/index', mode: 'switchTab' })
            }
        })
    }

    /**
     * 授权登录
     */
    const authLogin = (code: string | null,backFlag = false,callback: any = null) => {
        let obj = {};

        // #ifdef MP-WEIXIN
        obj.code = code;
        uni.getStorageSync('pid') && (Object.assign(obj, { pid: uni.getStorageSync('pid') }))
        weappLogin(obj).then((res: AnyObject) => {
            if (res.data.token) {
                useMemberStore().setToken(res.data.token)
                const config = useConfigStore()
                setTimeout(() => {
                    const memberInfo = useMemberStore().info
                    memberInfo && memberInfo.weapp_openid && uni.setStorageSync('openid', memberInfo.weapp_openid)
                    // 开启绑定手机号标识
                    if (uni.getStorageSync('isbindmobile')) {
                        uni.removeStorageSync('isbindmobile');
                    }
                    if (config.login.is_bind_mobile && memberInfo && !memberInfo.mobile) {
                        uni.setStorageSync('isbindmobile', true)
                    }
                    if(backFlag) handleLoginBack() //一键登录返回
                }, 1000)
            } else {
                uni.setStorageSync('openid', res.data.openid)
                uni.setStorageSync('unionid', res.data.unionid)
                if(callback) callback({
                    openid: uni.getStorageSync('openid'),
                    unionid: uni.getStorageSync('unionid')
                })
            }
        }).catch((err) => {
            uni.showToast({ title: err.msg, icon: 'none' })
        })
        // #endif

        // #ifdef H5
        obj.code = code;
        uni.getStorageSync('pid') && (Object.assign(obj, { pid: uni.getStorageSync('pid') }))
        wechatUser(obj).then((user_res: AnyObject) => {
            if (user_res.data) {
                wechatUserLogin(user_res.data).then((res: AnyObject) => {
                    if (res.data.token) {
                        useMemberStore().setToken(res.data.token)
                        const config = useConfigStore()
                        setTimeout(() => {
                            const memberInfo = useMemberStore().info
                            memberInfo && memberInfo.wx_openid && uni.setStorageSync('openid', memberInfo.wx_openid)
                            // 开启绑定手机号标识
                            if (uni.getStorageSync('isbindmobile')) {
                                uni.removeStorageSync('isbindmobile');
                            }
                            if (config.login.is_bind_mobile && memberInfo && !memberInfo.mobile) {
                                uni.setStorageSync('isbindmobile', true)
                            }
                        }, 1000)
                    } else {
                        uni.setStorageSync('openid', res.data.openid)
                        uni.setStorageSync('unionid', res.data.unionid)
                    }
                })
            }
        }).catch((err) => {
            if (err.msg == -1) {
                getAuthCode('snsapi_userinfo')
            } else {
                uni.showToast({ title: err.msg, icon: 'none' })
            }
        })
        // #endif
    }
    /**
     * 登录普通账号后修改openid
     */
    const updateOpenid = (code: string | null) => {
        let obj = {};
        // #ifdef MP-WEIXIN
        obj.code = code;
        updateWeappOpenid(obj).then((res) => {
            useMemberStore().getMemberInfo()
            setTimeout(() => {
                const memberInfo = useMemberStore().info
                memberInfo && memberInfo.weapp_openid && uni.setStorageSync('openid', memberInfo.weapp_openid)
            }, 1000)
        })
        // #endif

        // #ifdef H5
        obj.code = code;
        updateWechatOpenid(obj).then((res) => {
            useMemberStore().getMemberInfo()
            setTimeout(() => {
                const memberInfo = useMemberStore().info
                memberInfo && memberInfo.wx_openid && uni.setStorageSync('openid', memberInfo.wx_openid)
            }, 1000)
        })
        // #endif
    }
    /**
     * 获取授权码
     * updateFlag：更新oppenid
     * backFlag 控制一键登录返回
     */
    const getAuthCode = (scopes: '' | 'snsapi_base' | 'snsapi_userinfo' = 'snsapi_base', updateFlag: boolean = false, backFlag: boolean = false,callback = null) => {
        // #ifdef MP-WEIXIN
        wx.login({
            success(res) {
                if (res.code) {
                    updateFlag ? updateOpenid(res.code) : authLogin(res.code,backFlag,callback)
                } else {
                    console.log('登录失败！' + res.errMsg)
                }
            }
        })
        // #endif

        // #ifdef H5
        let url = `${ location.origin }${ location.pathname }`
        let query = urlDeconstruction(location.href).query
        query.code && (delete query.code)
        Object.keys(query).length && (url += uni.$u.queryParams(query))

        getWechatAuthCode({
            url,
            scopes
        }).then((res: AnyObject) => {
            location.href = res.data.url
        })
        // #endif
    }

    return {
        setLoginBack,
        handleLoginBack,
        authLogin,
        updateOpenid,
        getAuthCode
    }
}
