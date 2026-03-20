<?php
// +----------------------------------------------------------------------
// | Niucloud-admin 企业快速开发的多应用管理平台
// +----------------------------------------------------------------------
// | 官方网址：https://www.niucloud.com
// +----------------------------------------------------------------------
// | niucloud团队 版权所有 开源版本可自由商用
// +----------------------------------------------------------------------
// | Author: Niucloud Team
// +----------------------------------------------------------------------

namespace app\service\admin\addon;


use app\dict\addon\AddonDict;
use app\dict\menu\MenuDict;
use app\model\addon\Addon;
use app\model\sys\SysMenu;
use app\service\admin\auth\AuthService;
use app\service\core\addon\CoreAddonCloudService;
use app\service\core\addon\CoreAddonDownloadService;
use app\service\core\addon\CoreAddonInstallService;
use app\service\core\addon\CoreAddonService;
use core\base\BaseAdminService;
use core\exception\CommonException;
use think\db\exception\DbException;


/**
 * 消息管理服务层
 */
class AddonService extends BaseAdminService
{
    public static $cache_tag_name = 'addon_cache';

    public function __construct()
    {
        parent::__construct();
        $this->model = new Addon();

    }

    public function getList()
    {
        return ( new CoreAddonService() )->getLocalAddonList();
    }

    /**
     * 获取当前站点消息
     * @return array
     */
    public function getLocalAddonList()
    {
        return ( new CoreAddonService() )->getLocalAddonList();
    }

    /**
     * 安装插件
     * @param string $addon
     */
    public function install(string $addon)
    {
        return ( new CoreAddonInstallService($addon) )->install();
    }

    /**
     * 云安装插件
     * @param string $addon
     */
    public function cloudInstall(string $addon)
    {
        return ( new CoreAddonInstallService($addon) )->install('cloud');
    }

    /**
     * 云安装日志
     * @param string $addon
     * @return null
     */
    public function cloudInstallLog(string $addon)
    {
        return ( new CoreAddonCloudService() )->getBuildLog($addon);
    }

    /**
     * 获取安装任务
     * @return mixed
     */
    public function getInstallTask()
    {
        return ( new CoreAddonInstallService('') )->getInstallTask();
    }

    /**
     * 安装插件检测安装环境
     * @param string $addon
     */
    public function installCheck(string $addon)
    {
        return ( new CoreAddonInstallService($addon) )->installCheck();
    }

    /**
     * 取消安装任务
     * @param string $addon
     */
    public function cancleInstall(string $addon)
    {
        return ( new CoreAddonInstallService($addon) )->cancleInstall();
    }

    /**
     * @param string $addon
     * @return array|array[]|null
     */
    public function uninstallCheck(string $addon)
    {
        return ( new CoreAddonInstallService($addon) )->uninstallCheck();
    }

    /**
     * 卸载插件
     * @param string $addon
     * @return true
     */
    public function uninstall(string $addon)
    {
        return CoreAddonInstallService::instance($addon)->uninstall();
    }

    /**
     * 获取插件列表
     * @param array $where
     * @return array
     */
    public function getPage(array $where = [])
    {
        return ( new CoreAddonService() )->getPage($where);
    }

    /**
     * 获取插件信息
     * @param int $id
     * @return array
     */
    public function getInfo(int $id)
    {
        return ( new CoreAddonService() )->getInfo($id);
    }

    /**
     * 设置插件状态
     * @param int $id
     * @param int $status
     */
    public function setStatus(int $id, int $status)
    {
        return ( new CoreAddonService() )->setStatus($id, $status);
    }

    /**
     * 下载应用
     * @param string $app_key
     * @return true
     */
    public function download(string $app_key, string $version)
    {
        if (empty($version)) throw new CommonException('ADDON_DOWNLOAD_VERSION_EMPTY');
        return ( new CoreAddonDownloadService() )->download($app_key, $version);
    }


    /**
     * 查询已安装应用
     * @return array
     */
    public function getInstallList()
    {
        return ( new CoreAddonService() )->getInstallAddonList();
    }

    public function getAddonList()
    {
        $addon_list = $this->model->where([ [ 'status', '=', AddonDict::ON ], [ 'type', '=', 'addon' ] ])->append([ 'status_name' ])->column('title, icon, key, desc, status, type, support_app', 'key');
        return $addon_list;
    }

    /**
     * 应用key缓存
     * @param $keys
     * @return mixed|string
     * @throws DbException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getAddonListByKeys($keys)
    {
        sort($keys);
        $cache_name = 'addon_list' . implode('_', $keys);
        return cache_remember(
            $cache_name,
            function () use ($keys) {
                $where = [
                    [ 'key', 'in', $keys ],
                    [ 'status', '=', AddonDict::ON ]
                ];
                return $this->model->where($where)->field('title, icon, key, desc, status, cover')->select()->toArray();

            },
            self::$cache_tag_name
        );
    }

    /**
     * 获取插件信息
     * @param int $id
     * @return array
     */
    public function getInfoByKey(string $key)
    {
        return $this->model->where([ [ 'key', '=', $key ] ])->field('title, icon, key, desc, status, cover')->findOrEmpty()->toArray();
    }

    /**
     * @return array[]
     */
    public function showCustomer($is_sort = true)
    {
        $show_list = event('ShowCustomer', []);
        $addon_type_list = MenuDict::getAddonChildMenu();
        $return = [];
        foreach ($show_list as $item) {
            foreach ($addon_type_list as $key => $value) {
                if (!isset($return[ $key ])) {
                    $return[ $key ] = [
                        'title' => $value[ 'name' ],
                        'sort' => $value[ 'sort' ],
                        'list' => [],
                    ];
                }
                $return[ $key ][ 'list' ] = array_merge($return[ $key ][ 'list' ], $item[ $key ] ?? []);
            }
        }

        //防止有未实现对应事件的插件额外做一次查询 未实现的直接放到 addon_tool 里面
        $keys = [];
        foreach ($return as $item) {
            foreach ($item[ 'list' ] as $value) {
                $keys[] = $value[ 'key' ];
            }
        }
        $addon_list = $this->getAddonList();
        $menu_model = ( new SysMenu() );
        $addon_urls = $menu_model
            ->where([ [ 'addon', 'in', array_column($addon_list, 'key') ], [ 'addon', 'not in', $keys ], [ 'is_show', '=', 1 ], [ 'menu_type', '=', 1 ] ])
            ->order('id asc')
            ->group('addon')
            ->column('router_path', 'addon');
        if (!empty($addon_list)) {
            foreach ($addon_list as $k => $v) {
                if (in_array($v[ 'key' ], $keys)) {
                    continue;
                }
                $url = $addon_urls[ $v[ 'key' ] ] ?? '';
                $return[ 'addon_tool' ][ 'list' ][] = [
                    'title' => $v[ 'title' ],
                    'desc' => $v[ 'desc' ],
                    'icon' => $v[ 'icon' ],
                    'key' => $v[ 'key' ],
                    'url' => $url ? '/' . $url : ''
                ];
            }
        }
        if ($is_sort) {
            usort($return, function (array $a, array $b) {
                $sortA = isset($a[ 'sort' ]) ? (int) $a[ 'sort' ] : 0;
                $sortB = isset($b[ 'sort' ]) ? (int) $b[ 'sort' ] : 0;
                return $sortB <=> $sortA;
            });
        }
        return $return;
    }

    //生成菜单数据
    public function getSpecialMenuList()
    {
        $auth_menu_list = ( new AuthService() )->getAuthMenuList('all', 1);
        $auth_menu_list = array_column($auth_menu_list, null, 'menu_key');
        $auth_menu_list = $auth_menu_list[ 'addon' ][ 'children' ] ?? [];
        $list = $this->showCustomer(false);//获取对应的需要展示的key
        $addon_menu_list = MenuDict::getAddonChildMenu();
        $menu_list = [];
        foreach ($addon_menu_list as $item) {
            $menu_key_list = array_column($list[ $item[ 'key' ] ][ 'list' ] ?? [], 'key');
            $temp_menu = [
                'app_type' => 'admin',
                'menu_name' => $item[ 'name' ],
                'menu_key' => $item[ 'key' ],
                'menu_short_name' => $item[ 'short_name' ],
                'parent_key' => 'addon',
                'menu_type' => '0',
                'icon' => 'iconfont iconzhuangxiu3',
                'api_url' => '',
                'router_path' => 'app/index',
                'view_path' => 'app/index',
                'methods' => 'get',
                'sort' => $item[ 'sort' ],
                'status' => '1',
                'is_show' => '1',
            ];
            $children = [];
            if (!empty($auth_menu_list)) {
                foreach ($auth_menu_list as $datum_item) {
                    if (in_array($datum_item[ 'menu_key' ], $menu_key_list)) {
                        $children[] = $datum_item;
                    }
                }
            }
            $temp_menu[ 'children' ] = $children;
            $menu_list[] = $temp_menu;
        }
        usort($menu_list, function (array $a, array $b) {
            $sortA = isset($a[ 'sort' ]) ? (int) $a[ 'sort' ] : 0;
            $sortB = isset($b[ 'sort' ]) ? (int) $b[ 'sort' ] : 0;
            return $sortB <=> $sortA;
        });
        return [
            'parent_key' => 'addon',
            'list' => $menu_list
        ];
    }

}
