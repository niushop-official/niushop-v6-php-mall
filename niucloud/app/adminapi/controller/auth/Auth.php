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

namespace app\adminapi\controller\auth;

use app\service\admin\auth\AuthService;
use app\service\admin\auth\AuthSiteService;
use core\base\BaseAdminController;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use think\Response;


/**
 * 用户管理
 * Class Auth
 * @description 用户管理
 * @package app\adminapi\controller\auth
 */
class Auth extends BaseAdminController
{

    /**
     * 登录用户菜单列表的接口
     * @description 登录用户菜单列表
     */
    public function authMenuList()
    {
        return success((new AuthService())->getAuthMenuList(1, 1));
    }

    /**
     * 获取登录用户信息
     * @description 获取登录用户信息
     * @return Response
     */
    public function get()
    {
        return success(( new AuthService() )->getAuthInfo());
    }

    /**
     * 修改登录用户信息
     * @description 修改登录用户信息
     * @param $field
     * @return Response
     */
    public function modify($field)
    {
        $data = $this->request->params([
            [ 'value', '' ],
            [ 'field', $field ]
        ]);
//        $this->validate($data, 'app\validate\sys\User.modify');
        ( new AuthService() )->modifyAuth($field, $data[ 'value' ]);
        return success('MODIFY_SUCCESS');
    }

    /**
     * 更新用户
     * @description 更新用户
     */
    public function edit()
    {
        $data = $this->request->params([
            [ 'real_name', '' ],
            [ 'head_img', '' ],
            [ 'password', '' ],
            [ 'original_password', '' ]
        ]);
        ( new AuthService() )->editAuth($data);
        return success('MODIFY_SUCCESS');
    }
}
