<template>
    <el-config-provider :locale="locale">
        <NuxtLayout>
            <NuxtLoadingIndicator/>
            <NuxtPage/>
        </NuxtLayout>
    </el-config-provider>
</template>

<script lang="ts" setup>
import { reactive, ref, computed, watch } from 'vue'
import useConfigStore from '@/stores/config'
import zhCn from 'element-plus/dist/locale/zh-cn.mjs'
import en from 'element-plus/dist/locale/en.mjs'
import useSystemStore from '@/stores/system'
import useAppStore from '@/stores/app'
import useMemberStore from '@/stores/member'
// 引入全局样式
import '@/assets/styles/index.scss'
import { useRoute, useRouter } from 'vue-router'

const router = useRouter()
// 初始化设置语言
const systemStore = useSystemStore()
const locale = computed(() => (systemStore.lang === 'zh-cn' ? zhCn : en))

// 初始化查询一些配置
const configStore = useConfigStore()
configStore.getLoginConfig(router)

// 如果已登录
getToken() && useMemberStore().setToken(getToken())

const route = useRoute()
watch(route, (nval, oval) => {
    useAppStore().$patch(state => {
        state.route = route.path
    })

    // 设置页面title
    let path = route.path == '/' ? '/index' : route.path
    // 处理部署后不知道为什么url会自动拼接上 / 的问题
    if (path.slice(-1) == '/') path = path.slice(0, -1)
    path = !path.lastIndexOf('/') ? `${ path }/index` : path
    let key = path.replace('/', '').replaceAll('/', '.')

    setTimeout(() => {
        useHead({
            title: t(`pages.${ key }`)
        })
    }, !oval ? 500 : 0)
}, { immediate: true })

watch(() => systemStore.site, () => {
    const site = systemStore.site
    const siteTitle = site.meta_title || site.front_end_name || site.site_name
    useHead({
        titleTemplate: (title) => {
            if (title) {
                if (siteTitle) {
                    return `${ title } - ${ siteTitle }`;
                } else {
                    return title;
                }
            } else {
                return siteTitle;
            }
        },
        meta: [
            {
                name: 'description',
                content: site.meta_desc ? site.meta_desc : ''
            },
            {
                name: 'Keywords',
                content: site.meta_keyword ? site.meta_keyword : ''
            }
        ]
    })
}, { deep: true, immediate: true })
</script>
