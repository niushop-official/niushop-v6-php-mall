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

namespace app\service\admin\weapp;

use app\dict\sys\CloudDict;
use app\service\core\weapp\CoreWeappCloudService;
use app\service\core\weapp\CoreWeappService;
use core\base\BaseAdminService;
use app\model\weapp\WeappVersion;
use core\exception\CommonException;

/**
 * 小程序包版本发布
 */
class WeappVersionService extends BaseAdminService
{
    public function __construct()
    {
        parent::__construct();
        $this->model = new WeappVersion();
    }

    /**
     * 添加小程序版本
     * @param array $data
     */
    public function add(array $data)
    {
        $uploading = $this->model->where([ ['status', '=', 0]])->field('id')->findOrEmpty();
        if (!$uploading->isEmpty()) throw new CommonException('WEAPP_UPLOADING');
        if (empty($data['version'])) {
            $version_no = $this->model->where([['id', '>', 0]])->order('version_no desc')->field('version_no')->findOrEmpty()->toArray()['version_no'] ?? 0;
            $version_no += 1;
            $version = "1.0.{$version_no}";
        }else{
            $version_no = 0;//自定义的version_no标记为0 不因自定义版本打断默认的连续性
            $version = $data['version'];
        }
        $upload_res = (new CoreWeappCloudService())->uploadWeapp([
            'version' => $version,
            'desc' => $data['desc'] ?? ''
        ]);

        $res = $this->model->create([
            'version' => $version,
            'version_no' => $version_no,
            'desc' => $data['desc'] ?? '',
            'create_time' => time(),
            'task_key' => $upload_res['key']
        ]);
        return $res->id;
    }

    public function getPreviewImage()
    {
        try {
            $version = $this->model->where([['id', '>', 0]])->order('id desc')->findOrEmpty();
            if (!$version->isEmpty() || in_array($version['status'], [CloudDict::APPLET_UPLOAD_SUCCESS, CloudDict::APPLET_AUDITING])) {
                if ($version['from_type'] == 'cloud_build') {
                    return (new CoreWeappCloudService())->getWeappPreviewImage();
                } else {
                    return image_to_base64((new CoreWeappService())->getWeappPreviewImage(), true);
                }
            }
        } catch (\Exception $e) {
            return '';
        }
    }

    /**
     * 获取小程序版本列表
     * @param array $where
     * @return array
     */
    public function getPage(array $where = [])
    {
        $field = 'id, version, version_no, desc, create_time, status, fail_reason, task_key';
        $order = 'create_time desc';
        $where[] = ['id', '>', 0];
        $search_model = $this->model->where($where)->field($field)->order($order)->append(['status_name']);
        $list = $this->pageQuery($search_model);
        return $list;
    }

    /**
     * 编辑
     * @param int $id
     * @param array $data
     * @return true
     */
    public function edit(int $id, array $data)
    {
        $data['status'] = 0;
        $data['update_time'] = time();
        $this->model->where([['id', '=', $id]])->create($data);
        return true;
    }

    /**
     * 删除
     * @param int $id
     * @return true
     */
    public function del(int $id)
    {
        $this->model->where([['id', '=', $id]])->delete();
        return true;
    }

    /**
     * 获取小程序上传日志
     * @param string $key
     * @return null
     */
    public function getUploadLog(string $key)
    {
        $build_log = (new CoreWeappCloudService())->getWeappCompileLog($key);

        if (isset($build_log['data']) && isset($build_log['data'][0]) && is_array($build_log['data'][0])) {
            $last = end($build_log['data'][0]);
            if ($last['code'] == 0) {
                (new WeappVersion())->update(['status' => CloudDict::APPLET_UPLOAD_FAIL, 'fail_reason' => $last['msg'] ?? '', 'update_time' => time()], ['task_key' => $key]);
                return $build_log;
            }
            if ($last['percent'] == 100) {
                (new WeappVersion())->update(['status' => CloudDict::APPLET_UPLOAD_SUCCESS, 'update_time' => time()], ['task_key' => $key]);
            }
        }
        return $build_log;
    }
}
