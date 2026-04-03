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

namespace app\service\core\sys;

use app\dict\sys\StorageDict;
use app\model\sys\SysAttachment;
use app\service\core\upload\CoreFileService;
use core\base\BaseCoreService;
use core\exception\AdminException;
use core\exception\UploadFileException;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;

/**
 * 素材管理服务层
 * Class CoreAttachmentService
 * @package app\service\core\sys
 */
class CoreAttachmentService extends BaseCoreService
{
    public function __construct()
    {
        parent::__construct();
        $this->model = new SysAttachment();
    }

    /**
     * 新增素材
     * @param array $data
     * @return mixed
     */
    public function add(array $data){
        $attachment = $this->model->create($data);
        if(!$attachment->att_id)
            throw new AdminException('ADD_FAIL');//创建失败
        return $attachment->att_id;
    }

    /**
     * 素材库模型对象
     * @param int $att_id
     * @return mixed
     */
    public function find(int $att_id){
        $where = array(
            ['att_id', '=', $att_id]
        );
        $user = $this->model->where($where)->findOrEmpty();
        if ($user->isEmpty())
            throw new AdminException('ATTACHMENT_NOE_EXIST');
        return $user;
    }

    /**
     * 图片列表
     * @param array|null $data
     * @return array
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function getList(?array $data = []){
        $where = array();
        $att_ids = $data['att_ids'] ?? [];
        if(!empty($att_ids)){
            $where[] = ['att_id', 'in', $att_ids];
        }
        return $this->model->where($where)->select()->toArray();
    }

    /**
     * 编辑素材
     * @param int $att_id
     * @param array $data
     * @return SysAttachment
     */
    public function edit(int $att_id, array $data){
        $where = array(
            ['att_id', '=', $att_id]
        );
        return $this->model->update($data, $where);
    }

    /**
     * 删除素材
     * @param int $att_id
     * @return mixed
     */
    public function del(int $att_id){
        //查询是否有下级菜单或按钮
        $menu = $this->find($att_id);
        return $menu->delete();

    }


    /**
     * 删除附件库中的文件
     * @param array $att_ids
     * @return true
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function delAll(array $att_ids){
        $core_attachment_service = new CoreAttachmentService();
        $list = $core_attachment_service->getList(compact('att_ids'));
        if(empty($list))
            throw new UploadFileException('PLEASE_SELECT_IMAGE');

        $del_success_ids = [];
        foreach($list as $v){
            try {
                $file_driver = (new CoreFileService())->driver($v['storage_type']);
                //读取上传附件的信息用于后续得校验和数据写入,删除失败直接通过
                $file_driver->delete($v['path']);
            } catch (\Exception $e) {
                // 如果附件在云存储中删除失败后 尝试该资源是否是在平台云存储中
                if ($v['storage_type'] != StorageDict::LOCAL) {
                    try {
                        $file_driver = (new CoreFileService())->driver(0, $v['storage_type']);
                        $file_driver->delete($v['path']);
                    } catch (\Exception $e) {
                        if (!empty($del_success_ids)) {
                            $this->model->destroy($del_success_ids);
                        }
                        throw new UploadFileException($e->getMessage());
                    }
                }
            }
            $del_success_ids[] = $v['att_id'];
        }
        $this->model->destroy($del_success_ids);
        return true;
    }
}
