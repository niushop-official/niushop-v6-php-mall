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

namespace app\service\admin\auth;

use app\Request;
use app\service\admin\sys\MenuService;
use app\service\admin\sys\RoleService;
use app\service\admin\user\UserService;
use app\service\core\niucloud\CoreAuthService;
use core\base\BaseAdminService;
use core\exception\AuthException;
use core\exception\CommonException;
use Exception;

/**
 * 用户服务层
 * Class AuthService
 * @package app\service\admin\auth
 */
class AuthService extends BaseAdminService
{

    /**
     * 校验权限
     * @param Request $request
     * @return bool
     * @throws Exception
     */
    public function checkRole(Request $request)
    {
//        $this->checkAuthinfo($request);

        $rule = strtolower(trim($request->rule()->getRule()));
        $method = strtolower(trim($request->method()));

        if($method != 'get'){
//            throw new AuthException('演示站禁止操作');
        }

        $menu_service = new MenuService();
        $all_menu_list = $menu_service->getAllApiList();
        //先判断当前访问的接口是否收到权限的限制
        $method_menu_list = $all_menu_list[ $method ] ?? [];
        if (!in_array($rule, $method_menu_list))
            return true;

        $auth_role_list = $this->getAuthApiList();
        if (!empty($auth_role_list[ $method ]) && in_array($rule, $auth_role_list[ $method ]))
            return true;

        throw new AuthException('NO_PERMISSION');

    }

    public function checkAuthinfo(Request $request) {
        $rule = strtolower(trim($request->rule()->getRule()));
        $method = strtolower(trim($request->method()));

        if ($method == 'get') return;

        $ignore = ['niucloud/authinfo', 'upgrade', 'niucloud/build', 'sys/cache/clear'];
        foreach ($ignore as $item) {
            if (strpos($rule, $item) !== false) return;
        }

        $authinfo = (new CoreAuthService())->getAuthInfo()['data'] ?? [];;
        if (empty($authinfo)) return;

        if (!$this->isCheckDomain()) return;

        $site_address = $authinfo['site_address'] ?? '';
        $domain = request()->domain();
        if (!empty($site_address) && strpos($domain, $site_address) !== false) return;

        throw new CommonException("授权域名校验失败！请确保当前访问域名与授权码绑定的域名一致");
    }

    private function isCheckDomain() {
        return !(request()->ip() == '127.0.0.1' || request()->host() == 'localhost');
    }

    /**
     * 当前授权用户接口权限
     * @return array
     */
    public function getAuthApiList()
    {
        $user_info = ( new UserService() )->getUserCache($this->uid);
        if (empty($user_info))
            return [];

        $is_admin = $user_info[ 'is_admin' ];//是否是超级管理员组
        $menu_service = new MenuService();
        if ($is_admin) {//查询全部启用的权限
            //获取站点信息
            return ( new MenuService() )->getAllApiList(1);
        } else {
            $user_role_ids = $user_info[ 'role_ids' ];
            $role_service = new RoleService();
            $menu_keys = $role_service->getMenuKeysByRoleIds($user_role_ids ?? []);
            return $menu_service->getApiListByMenuKeys($menu_keys);
        }
    }

    /**
     * 当前授权用户菜单权限
     * @return array
     */
    public function getAuthMenuList($status = 'all', int $is_tree = 0, int $is_button = 1)
    {
        $user_info = ( new UserService() )->getUserCache($this->uid);
        if (empty($user_info))
            return [];
        $is_admin = $user_info[ 'is_admin' ];//是否是超级管理员组
        $menu_service = new MenuService();
        if ($is_admin) {//查询全部启用的权限
            return ( new MenuService() )->getAllMenuList($status, $is_tree, $is_button);
        } else {
            $user_role_ids = $user_info[ 'role_ids' ];
            $role_service = new RoleService();
            $menu_keys = $role_service->getMenuKeysByRoleIds($user_role_ids ?? []);
            return $menu_service->getMenuListByMenuKeys($menu_keys, $is_tree, is_button:$is_button);
        }
    }

    /**
     * 获取授权用户信息
     */
    public function getAuthInfo()
    {
        return ( new UserService() )->getUserCache($this->uid);
    }

    /**
     * 修改用户
     * @param array $data
     * @return true
     */
    public function editAuth(array $data)
    {
        if (!empty($data[ 'password' ])) {
            //检测原始密码是否正确
            $user = ( new UserService() )->find($this->uid);
            if (!check_password($data[ 'original_password' ], $user->password))
                throw new AuthException('OLD_PASSWORD_ERROR');

        }
        return ( new UserService() )->edit($this->uid, $data);
    }
}
