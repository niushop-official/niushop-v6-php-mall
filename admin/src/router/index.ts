import { createRouter, createWebHistory, RouteLocationRaw, RouteLocationNormalizedLoaded } from 'vue-router'
import NProgress from 'nprogress'
import 'nprogress/nprogress.css'
import { STATIC_ROUTES, NO_LOGIN_ROUTES, ROOT_ROUTER, ADMIN_ROUTE, findFirstValidRoute } from './routers'
import { language } from '@/lang'
import useSystemStore from '@/stores/modules/system'
import useUserStore from '@/stores/modules/user'
import { setWindowTitle, getAppType, urlToRouteRaw } from '@/utils/common'

const router = createRouter({
    history: createWebHistory(import.meta.env.BASE_URL),
    routes: [ADMIN_ROUTE, ...STATIC_ROUTES]
})

/**
 * 重写push方法
 */
const originPush = router.push
router.push = (to: RouteLocationRaw) => {
    const route: any = typeof to == 'string' ? urlToRouteRaw(to) : to
    if (route.path) {
        const paths = route.path.split('/').filter((item: string) => { return item })
        route.path = ['admin'].indexOf(paths[0]) == -1 ? `/${getAppType()}${route.path}` : route.path
    }
    return originPush(route)
}

/**
 * 重写resolve方法
 */
const originResolve = router.resolve
router.resolve = (to: RouteLocationRaw, currentLocation?: RouteLocationNormalizedLoaded) => {
    const route: any = typeof to == 'string' ? urlToRouteRaw(to) : to
    if (route.path) {
        const paths = route.path.split('/').filter((item: string) => { return item })
        route.path = ['admin'].indexOf(paths[0]) == -1 ? `/${getAppType()}${route.path}` : route.path
    }
    return originResolve(route, currentLocation)
}

// 全局前置守卫
router.beforeEach(async (to: any, from, next) => {
    NProgress.configure({ showSpinner: false })
    NProgress.start()

    to.redirectedFrom && (to.query = to.redirectedFrom.query)

    const userStore = useUserStore()
    const systemStore = useSystemStore()

    let title: string = to.meta.title ?? ''


    // 设置网站标题
    setWindowTitle(title)

    // 加载语言包
    await language.loadLocaleMessages(to.meta.addon || '', (to.meta.view || to.path), systemStore.lang);

    const loginPath = '/admin/login'

    // 判断是否需登录
    if (NO_LOGIN_ROUTES.includes(to.path)) {
        next()
    } else if (userStore.token) {
        // 如果已加载路由
        if (userStore.routers.length) {
            if (to.path === loginPath) {
                next(`/${getAppType()}`)
            } else {
                next()
            }
        } else {
            try {
                if (!systemStore.apps.length) {
                    await systemStore.getInstallAddons()
                }
                await userStore.getAuthMenusFn()

                // 设置首页路由
                let firstRoute: symbol | string | undefined = findFirstValidRoute(userStore.routers)
                for (let i = 0; i < systemStore.apps.length; i++) {
                    const item = systemStore.apps[i]
                    if (userStore.addonIndexRoute[item.key]) {
                        firstRoute = userStore.addonIndexRoute[item.key]
                        break
                    }
                }

                ROOT_ROUTER.redirect = { name: firstRoute }
                router.addRoute(ROOT_ROUTER)

                // 设置应用首页路由
                ADMIN_ROUTE.children[0].redirect = { name: firstRoute }
                router.addRoute(ADMIN_ROUTE)

                // 添加动态路由
                userStore.routers.forEach(route => {
                    if (!route.children) {
                        router.addRoute(ADMIN_ROUTE.name, route)
                        return
                    }

                    // 动态添加可访问路由表
                    router.addRoute(ADMIN_ROUTE.name, route)
                })
                next(to)
            } catch (err) {
                console.log(err)
                next({ path: loginPath, query: { redirect: to.fullPath } })
            }
        }
    } else {
        if (to.path === loginPath) {
            next()
        } else {
            next({ path: loginPath, query: { redirect: to.fullPath } })
        }
    }
})

// 全局后置钩子
router.afterEach(() => {
    NProgress.done()
})

export default router
