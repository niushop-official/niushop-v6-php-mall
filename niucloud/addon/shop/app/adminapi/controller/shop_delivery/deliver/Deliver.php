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

namespace addon\shop\app\adminapi\controller\shop_delivery\deliver;

use addon\shop\app\service\admin\shop_delivery\deliver\DeliverService;
use core\base\BaseAdminController;
use think\Response;


/**
 * 配送员
 * @description 配送员
 * Class Deliver
 * @package addon\shop\app\adminapi\controller\shop_delivery\deliver
 */
class Deliver extends BaseAdminController
{
    /**
     * 获取配送员分页列表
     * @description 查看配送员列表
     * @return \think\Response
     */
    public function pages()
    {
        $data = $this->request->params([
            ['deliver_name', ''],
            ['deliver_mobile', '']
        ]);
        return success(( new DeliverService() )->getPage($data));
    }

    /**
     * 获取配送员列表
     * @description 查看配送员列表
     * @return \think\Response
     */
    public function lists()
    {
        $data = $this->request->params([
            ['deliver_name', ''],
            ['deliver_mobile', '']
        ]);
        return success(( new DeliverService() )->getList($data));
    }

    /**
     * 配送员详情
     * @description 查看配送员详情
     * @param int $id
     * @return \think\Response
     */
    public function info(int $id)
    {
        return success(( new DeliverService() )->getInfo($id));
    }

    /**
     * 添加配送员
     * @description 添加配送员
     * @return \think\Response
     */
    public function add()
    {
        $data = $this->request->params([
            ['deliver_name', ''],
            ['deliver_mobile', ''],
        ]);
        $id = ( new DeliverService() )->add($data);
        return success('ADD_SUCCESS', [ 'id' => $id ]);
    }

    /**
     * 配送员编辑
     * @description 编辑配送员
     * @param int $id 自提门店id
     * @return \think\Response
     */
    public function edit($id)
    {
        $data = $this->request->params([
            ['deliver_name', ''],
            ['deliver_mobile', ''],
        ]);
        ( new DeliverService() )->edit($id, $data);
        return success('EDIT_SUCCESS');
    }

    /**
     * 配送员删除
     * @description 删除配送员
     * @param int $id
     * @return Response
     */
    public function del(int $id)
    {
        ( new DeliverService() )->del($id);
        return success('DELETE_SUCCESS');
    }

}
