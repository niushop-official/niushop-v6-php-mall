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

namespace addon\shop\app\adminapi\controller\delivery_store;

use addon\shop\app\service\admin\delivery_store\DeliveryStoreService;
use app\upgrade\v158\Upgrade;
use core\base\BaseAdminController;
use think\Response;

/**
 * 提货点控制器
 * Class DeliveryStore
 * @package addon\shop\app\adminapi\controller\delivery_store
 */
class DeliveryStore extends BaseAdminController
{
    /**
     * 获取展示用信息
     * @description 获取添加/编辑展示用信息
     * @return Response
     */
    public function init()
    {
        return success(( new DeliveryStoreService() )->getInitInfo());
    }

    /**
     * 获取提货点列表
     * @description 查看提货点列表详情
     * @return \think\Response
     */
    public function lists()
    {
        $data = $this->request->params([
            ['store_name', ''],
            ['pick_up_type', ''],//提货类型
            ['create_time', [] ]
        ]);
        return success(( new DeliveryStoreService() )->getPage($data));
    }

    /**
     * 提货点详情
     * @param int $id
     * @return \think\Response
     */
    public function info(int $id)
    {
        return success(( new DeliveryStoreService() )->getInfo($id));
    }


    /**
     * 添加提货点
     * @description 添加提货点
     * @return \think\Response
     */
    public function add()
    {
        $data = $this->request->params([
            ['pick_up_type', ''],//提货类型
            ['store_name', ''],//门店名称
            ['store_logo', ''],//门店logo
            ['contact_name', ''],
            ['store_mobile', ''],
            [ 'province_id',0 ],
            [ 'city_id',0 ],
            [ 'district_id',0 ],
            ['address', ''],
            ['full_address', ''],
            ['longitude', ''],
            ['latitude', ''],
            ['trade_time', ''],
            ['time_is_open', ''],
            ['time_week', [] ],//周数组
            ['time_interval', ''],//间隔时间段 单位分钟
            ['trade_time_json', [] ],//营业时间
            [ 'area', [] ],//配送区域
            ['status', 0 ]
        ]);
        $this->validate($data, 'addon\shop\app\validate\delivery\Store.add');
        $id = ( new DeliveryStoreService() )->add($data);
        return success('ADD_SUCCESS', [ 'id' => $id ]);
    }

    /**
     * 提货点编辑
     * @description 编辑提货点
     * @param int $id 提货点id
     * @return \think\Response
     */
    public function edit(int $id)
    {
        $data = $this->request->params([
            ['pick_up_type', ''],//提货类型
            ['store_name', ''],//门店名称
            ['store_logo', ''],//门店logo
            ['contact_name', ''],
            ['store_mobile', ''],
            [ 'province_id',0 ],
            [ 'city_id',0 ],
            [ 'district_id',0 ],
            ['address', ''],
            ['full_address', ''],
            ['longitude', ''],
            ['latitude', ''],
            ['trade_time', ''],
            ['time_is_open', ''],
            ['time_week', [] ],//周数组
            ['time_interval', ''],//间隔时间段 单位分钟
            ['trade_time_json', [] ],//营业时间
            [ 'area', [] ],//配送区域
            ['status', 0 ]
        ]);
        $this->validate($data, 'addon\shop\app\validate\delivery\Store.edit');
        ( new DeliveryStoreService() )->edit($id, $data);
        return success('EDIT_SUCCESS');
    }

    /**
     * 提货点删除
     * @description 删除提货点
     * @param int $id 提货点id
     * @return \think\Response
     */
    public function del(int $id)
    {
        ( new DeliveryStoreService() )->del($id);
        return success('DELETE_SUCCESS');
    }

    /**
     * @description 查看提货点列表-全部
     * @return \think\Response
     */
    public function getList()
    {
        $data = $this->request->params([
            ['keyword', ''],
            ['pick_up_type', '' ]
        ]);
        return success(( new DeliveryStoreService() )->getList($data));
    }

    /**
     * @description 提货点提货类型
     * @return \think\Response
     */
    public function getPickUpType()
    {
        return success(( new DeliveryStoreService() )->getPickUpType());
    }

    /**
     * 修改社区分类状态
     * @return \think\Response
     */
    public function modifyStatus()
    {
        $data = $this->request->params([
            [ 'store_id', '' ],
            [ 'status', '' ],
        ]);
        ( new DeliveryStoreService() )->modifyStatus($data);
        return success('SUCCESS');
    }

}
