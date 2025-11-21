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

namespace app\service\core\applet;

use app\model\applet\AppletSiteVersion;
use app\model\applet\AppletVersion;
use core\base\BaseCoreService;
use core\exception\CommonException;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;

/**
 * 小程序包 站点
 */
class CoreAppletSiteVersionService extends BaseCoreService
{

    public function __construct()
    {
        parent::__construct();
        $this->model = new AppletSiteVersion();
    }


    /**
     * 版本升级列表
     * @param array $where
     * @return array
     */
    public function getPage(array $where = [])
    {
        $field = 'id, version_id, type, action, version, version_num, create_time';
        $search_model = $this->model->where($where)->field($field)->order('version_num desc')->with(['appletVersion']);
        return $this->pageQuery($search_model);
    }

    /**
     * 获取版本升级信息
     * @param int $id
     * @return array
     */
    public function getInfo(int $id)
    {
        $field = 'id, version_id, type, action, version, version_num, create_time';
        return $this->model->where([['id', '=', $id]])->field($field)->with(['appletVersion'])->findOrEmpty()->toArray();
    }

    /**
     * 添加版本升级记录
     * @param int $version_id
     * @param string $action
     * @return true
     */
    public function add(int $version_id, string $action)
    {
        $version_info = (new CoreAppletVersionService())->getInfo($version_id);
        if (empty($version_info)) throw new CommonException('APPLET_VERSION_NOT_EXISTS');
        $data['type'] = $version_info['type'];
        $data['create_time'] = time();
        $data['version_id'] = $version_info['id'];
        $data['action'] = $action;//操作方式
        $this->model->create($data);
        return true;

    }


    /**
     * 获取最后一个下载或升级的版本
     * @param string $type
     * @param string $action
     * @return mixed|string
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function getLastVersion(string $type, string $action = '')
    {
        $where = [ ['type', '=', $type]];
        $list = $this->model->where($where)->with(['appletVersion'])->select()->toArray();
        $list = array_column($list, null, 'version_num');
        ksort($list);
        $version = reset($list);
        return $version['version'] ?? '';
    }

    /**
     * 获取当前站点最新可升级的小程序版本
     * @param string $type
     * @return void
     */
    public function getUpgradeVersion(string $type)
    {
        //查询下一次升级或下载的版本
        $version = $this->getLastVersion($type);
        $where = [['type', '=', $type]];
        if (!$version) {
            $version_num = version_to_int($version);
            $where[] = ['version_num', '>', $version_num];
        }
        //查询比这个版本号大的版本号
        return (new AppletVersion())->where($where)->order('version_num desc')->findOrEmpty();
    }
}
