<script setup lang="ts">
    import { onLaunch, onShow, onHide } from '@dcloudio/uni-app'
    import { launchInterceptor } from '@/utils/interceptor'
    import { getToken, isWeixinBrowser, currRoute } from '@/utils/common'
    import useMemberStore from '@/stores/member'
    import useConfigStore from '@/stores/config'
    import useSystemStore from '@/stores/system'
    import { useLogin } from '@/hooks/useLogin'
    import { useShare } from '@/hooks/useShare'

    onLaunch(async(data) => {

        // 添加初始化拦截器
        launchInterceptor()

        // #ifdef H5
        uni.getSystemInfoSync().platform == 'ios' && (uni.setStorageSync('initUrl', location.href))

        // 传输给后台数据
        window.parent.postMessage(JSON.stringify({
            type: 'appOnLaunch',
            message: '初始化加载完成'
        }), '*');

        // 监听父页面发来的消息
        window.addEventListener('message', event => {
            try {
                let data = {
                    type: ''
                };
                if (typeof event.data == 'string') {
                    data = JSON.parse(event.data)
                } else if (typeof event.data == 'object') {
                    data = event.data
                }
                if (data.type && data.type == 'appOnReady') {
                    window.parent.postMessage(JSON.stringify({
                        type: 'appOnReady',
                        message: '加载完成'
                    }), '*');
                }
            } catch (e) {
                console.log('uni-app App.vue 接受数据错误', e)
            }
        }, false);

        const { wechatInit } = useShare()
        wechatInit()
        // #endif

        // #ifdef MP
        const updateManager = uni.getUpdateManager();
        updateManager.onCheckForUpdate(function(res) {
            // 请求完新版本信息的回调
        });

        updateManager.onUpdateReady(function(res) {
            uni.showModal({
                title: '更新提示',
                content: '新版本已经准备好，是否重启应用？',
                success(res) {
                    if (res.confirm) {
                        // 新的版本已经下载好，调用 applyUpdate 应用新版本并重启
                        updateManager.applyUpdate();
                    }
                }
            });
        });

        updateManager.onUpdateFailed(function(res) {
            // 新的版本下载失败
        });
        // #endif

        const configStore = useConfigStore()
        await configStore.getTabbarConfig()
        await configStore.getLoginConfig()

        useSystemStore().getMapFn()
        useSystemStore().getSiteInfoFn()
        useMemberStore().getMemberLevel()
        try {
            // 隐藏tabbar
            uni.hideTabBar()
        } catch (e) {

        }

        // 判断在登录注册页面账号锁定后不进行请求三方登录注册
        let url = currRoute()
        if ((url == 'app/pages/auth/login' || url == 'app/pages/auth/register') && (configStore.login.is_username || configStore.login.is_mobile || configStore.login.is_bind_mobile)) {
            return false
        }

        // 判断是否已登录
        if (getToken()) {
            const memberStore = useMemberStore()
            await memberStore.setToken(getToken())

            setTimeout(() => {
                if (!uni.getStorageSync('openid')) {
                    const memberInfo = useMemberStore().info
                    const login = useLogin()
                    // #ifdef MP-WEIXIN
                    if (memberInfo && memberInfo.weapp_openid) {
                        uni.setStorageSync('openid', memberInfo.weapp_openid)
                    } else {
                        login.getAuthCode('', true)
                    }
                    // #endif
                    // #ifdef H5
                    if (isWeixinBrowser()) {
                        if (memberInfo && memberInfo.wx_openid) {
                            uni.setStorageSync('openid', memberInfo.wx_openid)
                        } else {
                            data.query.code ? login.updateOpenid(data.query.code) : login.getAuthCode('snsapi_userinfo')
                        }
                    }
                    // #endif
                }
                // 开启强制绑定手机号
                if (uni.getStorageSync('isbindmobile')) {
                    uni.removeStorageSync('isbindmobile');
                }
                if (configStore.login.is_bind_mobile && !memberStore.info.mobile) {
                    // 强制绑定手机号
                    uni.setStorageSync('isbindmobile', true)
                }
            }, 1000)
        }
        if (!getToken()) {
            // todo 退出后不进行自动登录
            // #ifdef MP
            if(uni.getStorageSync('autoLoginLock')){
                return false
            }
            // #endif
            // 判断是否开启第三方自动注册登录
            if (configStore.login.is_auth_register) {
                const login = useLogin()
                // 第三方平台自动登录
                // #ifdef MP
                login.getAuthCode()
                // #endif
                // #ifdef H5
                if (isWeixinBrowser()) {
                    data.query.code ? login.authLogin(data.query.code) : login.getAuthCode('snsapi_userinfo')
                }
                // #endif
            }
        }
    })

    onShow(() => {
    })

    onHide(() => {
    })
</script>

<style>
    uni-page-head {
        display: none !important;
    }
</style>
