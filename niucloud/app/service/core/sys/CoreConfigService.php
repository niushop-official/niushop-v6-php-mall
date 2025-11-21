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

use app\model\sys\SysConfig;
use core\base\BaseCoreService;
use think\facade\Cache;
use think\Model;

/**
 * 配置服务层
 * Class BaseService
 * @package app\service
 */
class CoreConfigService extends BaseCoreService
{
    public static $cache_tag_name = 'sys_config';

    public function __construct()
    {
        parent::__construct();
        $this->model = new SysConfig();
    }

    /**
     * 获取配置信息
     * @param string $key
     * @return array
     */
    public function getConfig(string $key)
    {
        $cache_name = 'config_cache';
        $where = array(
            [ 'config_key', '=', $key ],
        );
        // 缓存清理
        $info = cache_remember(
            $cache_name . $key,
            function() use ($where) {
                $data = $this->model->where($where)->field('id,config_key,value,status,create_time,update_time')->findOrEmpty()->toArray();
                //数据库中无数据返回-1
                if (empty($data)) {
                    return -1;
                }
                return $data;
            },
            self::$cache_tag_name
        );
        // 检测缓存-1 返回空数据
        if ($info == -1) {
            return [];
        }
        return $info;
    }

    /**
     * 设置配置
     * @param string $key
     * @param array $value
     * @return SysConfig|bool|Model
     */
    public function setConfig(string $key, array $value)
    {
        $where = array(
            ['config_key', '=', $key]
        );
        $data = array(
            'config_key' => $key,
            'value' => $value,
        );
        $info = $this->getConfig($key);
        if (empty($info)) {
            $data[ 'create_time' ] = time();
            $res = $this->model->create($data);
        } else {
            $data[ 'update_time' ] = time();
            $res = $this->model->where($where)->save($data);
        }

        Cache::tag(self::$cache_tag_name )->clear();
        return $res;
    }

    /**
     * 修改设置状态
     * @param int $status
     * @param string $key
     * @return bool
     */
    public function modifyStatus( int $status, string $key)
    {
        $where = array(
            [ 'config_key', '=', $key ],
        );
        $data = array(
            'status' => $status,
        );
        return $this->model->where($where)->save($data);
    }

    /**
     * 返回config信息
     * @param string $key
     * @return array|mixed
     */
    public function getConfigValue( string $key)
    {
        $config_info = $this->getConfig( $key);
        if (empty($config_info)) {
            return [];
        }
        return $config_info[ 'value' ];
    }

    /**
     * 清除配置
     * @param string $key
     * @param array $value
     * @return SysConfig|bool|Model
     */
    public function clearConfig(string $key)
    {
        $where = array(
            [ 'config_key', '=', $key ],
        );
        $this->model->where($where)->delete();
        Cache::tag(self::$cache_tag_name)->clear();
    }
}
