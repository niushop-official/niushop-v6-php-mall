import { getTabbarPages } from './pages'
import useDiyStore from '@/app/stores/diy'
import useMemberStore from '@/stores/member'
import useSystemStore from '@/stores/system'
import useConfigStore from '@/stores/config'
import { getNeedLoginPages } from '@/utils/pages'

/**
* 跳转页面
*/
export const redirect = (redirect : redirectOptions) => {
	// 装修模式禁止跳转
	if (useDiyStore().mode == 'decorate') return

	let { url, mode, param, success, fail, complete } = redirect
	
	const config = useConfigStore()
	// 如果未开启普通账号登录注册,不展示登录注册页面
	if(!getToken() && getNeedLoginPages().indexOf(url) != -1 && !config.login.is_username && !config.login.is_mobile && !config.login.is_bind_mobile){
		uni.showToast({ title: '商家未开启普通账号登录注册', icon: 'none' })
		return
	}

	mode = mode || 'navigateTo'
	const tabBar = getTabbarPages()
	tabBar.includes(url) && (mode = 'switchTab')

	mode != 'switchTab' && param && Object.keys(param).length && (url += uni.$u.queryParams(param))

	switch (mode) {
		case 'switchTab':
			uni.switchTab({
				url,
				success: () => { success && success() },
				fail: () => { fail && fail() },
				complete: () => { complete && complete() }
			})
			break;
		case 'navigateTo':
			uni.navigateTo({
				url,
				success: () => { success && success() },
				fail: () => { fail && fail() },
				complete: () => { complete && complete() }
			})
			break;
		case 'reLaunch':
			uni.reLaunch({
				url,
				success: () => { success && success() },
				fail: () => { fail && fail() },
				complete: () => { complete && complete() }
			})
			break;
		case 'redirectTo':
			uni.redirectTo({
				url,
				success: () => { success && success() },
				fail: () => { fail && fail() },
				complete: () => { complete && complete() }
			})
			break;
	}
}

/**
* 自定义跳转链接
* @param {Object} link
*/
export const diyRedirect = (link: any) => {
	const diyStore = useDiyStore();
	// 装修模式禁止跳转
	if (diyStore.mode == 'decorate') return;

	if (link == null || Object.keys(link).length == 1) return;

	// 外部链接
	if (link.url && (link.url.indexOf('https') != -1 || link.url.indexOf('http') != -1)) {

		// #ifdef H5
		window.location.href = link.url;
		// #endif

		// #ifdef MP
		redirect({
			url: '/app/pages/webview/index',
			param: { src: encodeURIComponent(link.url) }
		});
		// #endif
	} else if (link.appid) {
		// 跳转其他小程序

		// #ifdef MP
		uni.navigateToMiniProgram({
			appId: link.appid,
			path: link.page
		})
		// #endif
	} else if (link.name == 'DIY_MAKE_PHONE_CALL' && link.mobile) {
		// 拨打电话

		uni.makePhoneCall({
			phoneNumber: link.mobile,
			success: (res) => {},
			fail: (res) => {}
		});

	} else {
		redirect({ url: link.url });
	}
}

/**
* 获取当前路由
*/
export const currRoute = () => {
	const pages = getCurrentPages()
	const route = pages[pages.length - 1]
	return route ? route.route : ''
}

// 获取分享路由
export const currShareRoute = () => {
	const pages = getCurrentPages()
	if (pages.length == 0) {
		return {
			path: '/',
			params: {}
		}
	}
	let currentRoute = pages[pages.length - 1].route //获取当前页面路由

	// #ifdef H5
	let currentParam = pages[pages.length - 1].$page.options; //获取路由参数
	// #endif

	// #ifdef MP
	let currentParam = pages[pages.length - 1].options || {}; //获取路由参数
	// #endif

	// 拼接参数
	let params = {};
	for (let key in currentParam) {
		params[key] = currentParam[key]
	}
	let currentPath = '/' + currentRoute;

	return {
		path: currentPath,
		params: params
	}
}

/**
* 获取token
* @returns
*/
export function getToken() : null | string {
    return useMemberStore().token
}

/**
* 设置token
* @param token
* @returns
*/
export function setToken(token : string) : void {
	uni.setStorageSync(import.meta.env.VITE_REQUEST_STORAGE_TOKEN_KEY, token)
}

/**
* 移除token
* @returns
*/
export function removeToken() : void {
	uni.removeStorageSync(import.meta.env.VITE_REQUEST_STORAGE_TOKEN_KEY)
}

/**
* 将url 解构为 { path: ***, query: {} }
*/
export function urlDeconstruction(url : string) {
	const query = {}
	const [path, param] = url.split('?')

	param && param.split('&').forEach((str : string) => {
		let [name, value] = str.split('=')
		query[name] = value
	})

	return { path, query }
}

/**
* 判断是否是url
* @param str
* @returns
*/
export function isUrl(str : string) : boolean {
	return str && (str.indexOf('http://') != -1 || str.indexOf('https://') != -1) || false
}

/**
* 图片输出
* @param path
* @returns
*/
export function img(path : string) : string {
    // #ifdef H5
	return isUrl(path) ? path : `${import.meta.env.VITE_IMG_DOMAIN || location.origin}/${path}`
    // #endif

    // #ifndef H5
    return isUrl(path) ? path : `${import.meta.env.VITE_IMG_DOMAIN}/${path}`
    // #endif
}

/**
 * 手机号隐藏
 */
export function mobileHide(mobile: string) {
	return mobile.replace(/(\d{3})\d{4}(\d{4})/, '$1****$2')
}

/**
* 判断是否是微信浏览器
*/
export function isWeixinBrowser() : boolean {
	// #ifndef H5
	return false
	// #endif
	let ua = navigator.userAgent.toLowerCase()
	return /micromessenger/.test(ua) ? true : false
}

/**
* 获取应用场景值
*/
export function getAppChannel() : string {
	// #ifdef APP-PLUS
	return 'app'
	// #endif
	// #ifdef MP-WEIXIN
	return 'weapp'
	// #endif
	// #ifdef H5
	return isWeixinBrowser() ? 'wechat' : 'h5'
	// #endif
}

/**
* 金额格式化
*/
export function moneyFormat(money : string) : string {
	return isNaN(parseFloat(money)) ? money : parseFloat(money).toFixed(2)
}

/**
 * 手机号隐藏
 */
export function mobileConceal(mobile : string) : string {
	return mobile.substring(0, 3) + "****" + mobile.substr(mobile.length - 4);
}

/**
 * 时间戳转日期格式
 * @param {Object} timeStamp
 */
export function timeStampTurnTime(timeStamp, type = "") {
	if (timeStamp != undefined && timeStamp != "" && timeStamp > 0) {
		var date = new Date();
		date.setTime(timeStamp * 1000);
		var y = date.getFullYear();
		var m = date.getMonth() + 1;
		m = m < 10 ? ('0' + m) : m;
		var d = date.getDate();
		d = d < 10 ? ('0' + d) : d;
		var h = date.getHours();
		h = h < 10 ? ('0' + h) : h;
		var minute = date.getMinutes();
		var second = date.getSeconds();
		minute = minute < 10 ? ('0' + minute) : minute;
		second = second < 10 ? ('0' + second) : second;
		if (type) {
			if (type == 'yearMonthDay') {
				return y + '年' + m + '月' + d + '日';
			}
			return y + '-' + m + '-' + d;
		} else {
			return y + '-' + m + '-' + d + ' ' + h + ':' + minute + ':' + second;
		}

	} else {
		return "";
	}
}

/**
 * 复制
 * @param {Object} value
 * @param {Object} callback
 */
export function copy(value, callback) {
	// #ifdef H5
	var oInput = document.createElement('input'); //创建一个隐藏input（重要！）
	oInput.value = value; //赋值
	oInput.setAttribute("readonly", "readonly");
	document.body.appendChild(oInput);
	oInput.select(); // 选择对象
	document.execCommand("Copy"); // 执行浏览器复制命令
	oInput.className = 'oInput';
	oInput.style.display = 'none';
	uni.hideKeyboard();
	uni.showToast({
		title: '复制成功',
		icon: 'none'
	});

	typeof callback == 'function' && callback();
	// #endif

	// #ifdef MP || APP-PLUS
	uni.setClipboardData({
		data: value,
		success: () => {
			typeof callback == 'function' && callback();
		},
		fail: (res)=>{
			// 在隐私协议中没有声明chooseLocation:fail api作用域
			if(res.errMsg && res.errno) {
				if(res.errno == 104){
				    let msg = '用户未授权隐私权限，设置剪贴板数据失败';
				    uni.showToast({title: msg, icon: 'none'})
				}else if(res.errno == 112){
					let msg = '隐私协议中未声明，设置剪贴板数据失败';
					uni.showToast({title: msg, icon: 'none'})
				}else {
					uni.showToast({title: res.errMsg, icon: 'none'})
				}
			}
		}
	});
	// #endif
}

/**
 * 处理onLoad传递的参数
 * @param option
 */
export function handleOnloadParams(option:any) {
	let params: any = {};

	// 处理小程序扫码进入的场景值参数
	if (option.scene) {
		var sceneParams = decodeURIComponent(option.scene).split('&');
		if (sceneParams.length) {
			sceneParams.forEach(item => {
				let arr = item.split('-');
				params[arr[0]] = arr[1];
				if(arr[0] == 'mid'){
					uni.setStorageSync('pid', arr[1])
				}
			});
		}
	} else {
		params = option;
	}
	return params;
}

/**
 * 获取定位信息
 */
export function getLocation(param = {}) {
	uni.getLocation({
		type: param.type ?? 'gcj02',
		success: res => {
		const systemStore = useSystemStore()
		systemStore.setLocation(res);
			typeof param.success == 'function' && param.success(res);
		},
		fail: res => {
			typeof param.fail == 'function' && param.fail(res);
		},
		complete: res => {
			typeof param.complete == 'function' && param.complete(res);
		}
	});
}

/**
 * 定位信息（缓存）
 */
export function locationStorage () {
	let data = uni.getStorageSync('location');
	let mapConfig = uni.getStorageSync('mapConfig');
	if (data) {
		var date = new Date();
		if (mapConfig.valid_time > 0) {
			data.is_expired = (date.getTime() / 1000) > data.valid_time; // 是否过期
		} else {
			data.is_expired = false;
		}
	}
	return data;
}

/**
 * @description 深度克隆
 * @param {object} obj 需要深度克隆的对象
 * @returns {*} 克隆后的对象或者原值（不是对象）
 */
export function deepClone(obj: object) {
    // 对常见的“非”值，直接返回原来值
    if ([null, undefined, NaN, false].includes(obj)) return obj
    if (typeof obj !== 'object' && typeof obj !== 'function') {
        // 原始类型直接返回
        return obj
    }
    const o = isArray(obj) ? [] : {}
    for (const i in obj) {
        if (obj.hasOwnProperty(i)) {
            o[i] = typeof obj[i] === 'object' ? deepClone(obj[i]) : obj[i]
        }
    }
    return o
}

/**
 * 防抖函数
 * @param fn
 * @param delay
 * @returns
 */
export function debounce(fn: (args?: any) => any, delay: number = 300) {
    let timer: null | number = null
    return function (...args) {
        if (timer != null) {
            clearTimeout(timer)
            timer = null
        }
        timer = setTimeout(() => {
            fn.call(this, ...args)
        }, delay);
    }
}

const isArray = (value: any) => {
    if (typeof Array.isArray === 'function') {
        return Array.isArray(value)
    }
    return Object.prototype.toString.call(value) === '[object Array]'
}

// px转rpx
export function pxToRpx(px: any) {
    const screenWidth = uni.getSystemInfoSync().screenWidth;
    return (750 * Number.parseInt(px)) / screenWidth;
}
