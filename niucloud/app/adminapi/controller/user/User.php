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

namespace app\adminapi\controller\user;

use app\dict\sys\UserDict;
use app\service\admin\user\UserService;
use core\base\BaseAdminController;
use think\Response;

/**
 * 用户管理
 * Class User
 * @description 用户管理
 * @package app\adminapi\controller\user
 */
class User extends BaseAdminController
{
    /**
     * 用户列表
     * @description 用户列表
     * @return Response
     */
    public function lists()
    {
        $data = $this->request->params([
            ['username', ''],
            ['realname', ''],
            ['role', ''],
            ['create_time', []],
            ['real_name', ''],
            ['last_time', []]
        ]);

        $list = (new UserService())->getPage($data);
        return success($list);
    }

    /**
     * 用户详情
     * @description 用户详情
     * @param $uid
     * @return Response
     */
    public function info($uid)
    {
        return success((new UserService())->getInfo($uid));
    }

    /**
     * 获取用户列表
     * @description 获取用户列表
     * @return Response
     */
    public function getUserAll()
    {
        $data = $this->request->params([
            ['username', ''],
            ['realname', ''],
            ['create_time', []],
        ]);
        $list = (new UserService())->getUserAll($data);
        return success($list);
    }

    /**
     * 获取用户下拉框
     * @description 获取用户下拉框
     * @return Response
     */
    public function getUserSelect()
    {
        $data = $this->request->params([
            ['username', '']
        ]);
        $list = (new UserService())->getUserSelect($data);
        return success($list);
    }

    /**
     * 检查用户是否存在
     * @description 检查用户是否存在
     * @return Response
     * @throws \think\db\exception\DbException
     */
    public function checkUserIsExist() {
        $data = $this->request->params([
            ['username', ''],
        ]);
        $is_exist = (new UserService())->checkUsername($data['username']);
        return success(data:$is_exist);
    }

    /**
     * 添加用户
     * @description 添加用户
     * @return Response
     * @throws \Exception
     */
    public function add() {
        $data = $this->request->params([
            ['username', ''],
            ['password', ''],
            ['mobile', ''],
            ['real_name', ''],
            ['head_img', ''],
            ['status', UserDict::ON],
            ['role_ids', []]
        ]);
        (new UserService())->add($data);
        return success();
    }

    /**
     * 编辑用户
     * @description 编辑用户
     * @return Response
     * @throws \Exception
     */
    public function edit($uid) {
        $data = $this->request->params([
            ['password', ''],
            ['mobile', ''],
            ['real_name', ''],
            ['head_img', ''],
            ['role_ids', []],
        ]);
        (new UserService())->edit($uid, $data);
        return success();
    }

    /**
     * 删除用户
     * @description 删除用户
     * @param $uid
     * @return Response
     */
    public function del($uid) {
        (new UserService())->del($uid);
        return success("DELETE_SUCCESS");
    }
}
