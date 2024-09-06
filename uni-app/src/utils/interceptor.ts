import { language } from '@/locale'
import { checkNeedLogin } from '@/utils/auth'
import { getToken } from '@/utils/common'
import { memberLog } from '@/app/api/auth'
import useConfigStore from "@/stores/config";
import { useShare } from '@/hooks/useShare'

/**
 * 页面跳转拦截器
 */
export const redirectInterceptor = (route: { path: string, query: object }) => {
    route.path = `/${ route.path }`

    // 检测当前访问的是系统（app）还是插件
    setAddonName(route.path)

    // #ifdef MP
    route.path.indexOf('addon') != -1 && language.loadAllLocaleMessages('addon', uni.getLocale())
    // #endif

    // 校验是否需要登录
    checkNeedLogin(route)

    loadShare()

    // 添加会员访问日志
    if (getToken()) memberLog({
        route: route.path,
        params: JSON.stringify(route.query),
        pre_route: getCurrentPages()[0]?.route
    })
}

/**
 * 应用初始化拦截器
 */
export const launchInterceptor = () => {
    const launch = uni.getLaunchOptionsSync()
    launch.path = `/${ launch.path }`

    // 检测当前访问的是系统（app）还是插件
    setAddonName(launch.path);

    // 加载语言包
    language.loadAllLocaleMessages('app', uni.getLocale())

    // #ifdef H5
    language.loadAllLocaleMessages('addon', uni.getLocale())
    // #endif

    // 校验是否需要登录
    checkNeedLogin(launch)

    // 存储分享会员id
    if (launch.query && launch.query.mid) {
        uni.setStorageSync('pid', launch.query.mid)
    }

    loadShare()

    // 添加会员访问日志
    if (getToken()) memberLog({ route: launch.path, params: JSON.stringify(launch.query || {}), pre_route: '' })
}

/**
 * 检测当前访问的是系统（app）还是插件
 * 设置插件的底部导航
 * 设置插件应用的主色调
 * @param path
 */
const setAddonName = async (path: string) => {
    let pathArr = path.split('/')
    let route = pathArr[1] == 'addon' ? pathArr[2] : 'app';

    // 设置底部导航
    const configStore = useConfigStore()
    if (configStore.addon != route) {
        configStore.addon = route;
    }

    // 设置插件应用的主色调，排除系统
    if (route != 'app') {
        try {
            const theme = await import(`../addon/${ route }/utils/theme.json`)
            configStore.themeColor = theme.default
            uni.setStorageSync('current_theme_color', JSON.stringify(theme.default));
        } catch (e) {
            // 设置插件应用的主色调发生错误，若不存在则使用最后有效的主色调
        }

    }

}

// 加载分享
const loadShare = () => {
    const { setShare } = useShare()
    setShare()
}