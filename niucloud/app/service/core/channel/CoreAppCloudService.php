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

namespace app\service\core\channel;

use app\dict\addon\AddonDict;
use app\model\addon\Addon;
use app\service\api\diy\DiyConfigService;
use app\service\core\addon\CoreAddonBaseService;
use app\service\core\addon\CoreAddonDevelopDownloadService;
use app\service\core\addon\WapTrait;
use app\service\core\niucloud\CoreCloudBaseService;
use app\service\core\niucloud\CoreModuleService;
use app\service\core\sys\CoreConfigService;
use app\service\core\sys\CoreSysConfigService;
use core\exception\CommonException;
use core\util\niucloud\BaseNiucloudClient;
use core\util\niucloud\CloudService;
use think\facade\Cache;

/**
 * 微信APP云服务
 * Class CoreWeappAuthService
 * @package app\service\core\weapp
 */
class CoreAppCloudService extends CoreCloudBaseService
{
    private $addon;

    private $addon_path;

    private $cache_key = 'app_cloud_build';

    use WapTrait;

    public function __construct()
    {
        parent::__construct();
        $this->root_path = dirname(root_path()) . DIRECTORY_SEPARATOR;
        $this->addon_path = root_path() . 'addon' . DIRECTORY_SEPARATOR;
    }

    /**
     * 上传APP
     * @param $addon
     */
    public function appCloudBuid(array $data)
    {

        $action_token = ( new CoreModuleService() )->getActionToken('appbuild', [ 'data' => [ 'product_key' => BaseNiucloudClient::PRODUCT ] ]);

        $app_config = (new CoreAppService())->getConfig();
        if (empty($app_config['app_name'])) throw new CommonException("请先配置应用名称");
        if (empty($app_config['uni_app_id'])) throw new CommonException("请先配置应用ID");
        if (empty($app_config['android_app_key'])) throw new CommonException("请先配置应用密钥");
        if (empty($app_config['application_id'])) throw new CommonException("请先配置应用包名");

        $wap_url = (new CoreSysConfigService())->getSceneDomain()['wap_url'];
        $map_config = ( new CoreConfigService() )->getConfigValue('MAPKEY');
        $build = [
            'app_name' => $app_config['app_name'],
            'uni_app_id' => $app_config['uni_app_id'],
            'wechat_app_id' => $app_config['wechat_app_id'],
            'wechat_app_secret' => $app_config['wechat_app_secret'],
            'android_app_key' => $app_config['android_app_key'],
            'application_id' => $app_config['application_id'],
            'privacy_agreement' => $wap_url . '/app/pages/auth/agreement?key=privacy&=',
            'service_agreement' => $wap_url . '/app/pages/auth/agreement?key=service&=',
            'qq_map_key' => $map_config['key'] ?? '',
            'amap_key' => $map_config['amap_key'] ?? '',
            'version_name' => $data['version_name'],
            'version_code' => $data['version_code'],
            'cert' => $data['cert']
        ];

        // 上传任务key
        $task_key = time();
        // 此次上传任务临时目录
        $temp_dir = runtime_path() . 'build_app' . DIRECTORY_SEPARATOR . $task_key;
        $package_dir = $temp_dir . DIRECTORY_SEPARATOR . 'package' . DIRECTORY_SEPARATOR;
        // uni
        $uni_dir = $package_dir . 'uni-app';

        dir_copy($this->root_path . 'uni-app', $uni_dir, exclude_dirs: [ 'node_modules', 'unpackage', 'dist', '.git' ]);

        // 替换env文件
        $this->weappEnvReplace($uni_dir . DIRECTORY_SEPARATOR . '.env.production');

        // 拷贝证书文件
        if ($data['cert']['type'] == 'private') {
            if (!file_exists($data['cert']['file'])) throw new CommonException('证书文件不存在');
            $cert_content = file_get_contents($data['cert']['file']);
            file_put_contents($package_dir . 'cert.jks', $cert_content);
        }

        // 拷贝icon文件
        file_copy($data['build']['icon'], $package_dir . 'drawable.zip');


        ( new CoreAddonBaseService() )->writeArrayToJsonFile($build, $package_dir . 'build.json');

        // 处理 mainifest.json
        $this->mergeManifestJson($uni_dir . DIRECTORY_SEPARATOR, [
            "name" => $app_config['app_name'],
            "appid" => $app_config['uni_app_id'],
            "versionName" => $data['version_name'],
            "versionCode" => $data['version_code'],
        ]);

        // 将临时目录下文件生成压缩包
        $zip_file = $temp_dir . DIRECTORY_SEPARATOR . 'app.zip';
        ( new CoreAddonDevelopDownloadService('') )->compressToZip($package_dir, $zip_file);


        $query = [
            'authorize_code' => $this->auth_code,
            'timestamp' => $task_key,
            'token' => $action_token[ 'data' ][ 'token' ] ?? ''
        ];
        $response = ( new CloudService(true, 'http://java.oss.niucloud.com/') )->httpPost('/cloud/appbuild?' . http_build_query($query), [
            'multipart' => [
                [
                    'name' => 'file',
                    'contents' => fopen($zip_file, 'r'),
                    'filename' => 'app.zip'
                ]
            ],
        ]);


        // 删除临时文件
        del_target_dir($temp_dir, true);

        if (isset($response[ 'code' ]) && $response[ 'code' ] == 0) throw new CommonException($response[ 'msg' ]);

        return [ 'key' => $query[ 'timestamp' ] ];
    }

    /**
     * 处理底部导航【暂时停止调用，存在APP兼容性问题】
     * @param $compile_path
     * @param $addon_arr
     * @return void
     */
    public function handleTabbar($compile_path, $addon_arr = [])
    {
        $bottomList = array_column(( new DiyConfigService() )->getBottomList(), null, 'key');
        $tabbarList = [];
        if (empty($addon_arr)) {
            foreach ($bottomList as $app_item) {
                array_push($tabbarList, ...$app_item[ 'value' ][ 'list' ]);
            }
        } else {
            foreach ($addon_arr as $addon) {
                if (isset($bottomList[ $addon ])) {
                    array_push($tabbarList, ...$bottomList[ $addon ][ 'value' ][ 'list' ]);
                }
            }
        }

        $tabbarList = array_map(function ($item) {
            if (strpos($item[ 'link' ][ 'url' ], '?') !== false) {
                $item[ 'link' ][ 'url' ] = explode('?', $item[ 'link' ][ 'url' ])[0];
            }
            $link = array_filter(explode('/', $item[ 'link' ][ 'url' ]));
            $item[ 'link' ] = $item[ 'link' ][ 'url' ];
            $item[ 'component' ] = implode('-', $link);
            $item[ 'name' ] = lcfirst(implode('', array_map(function ($str) {
                return ucfirst($str);
            }, $link)));
            return $item;
        }, $tabbarList);
        $tabbarList = array_column($tabbarList, null, 'name');

        if (isset($tabbarList[ 'appPagesIndexIndex' ])) unset($tabbarList[ 'appPagesIndexIndex' ]);
        if (isset($tabbarList[ 'appPagesMemberIndex' ])) unset($tabbarList[ 'appPagesMemberIndex' ]);

        // 处理vue文件
        $tpl = str_replace('/', DIRECTORY_SEPARATOR, public_path() . 'static/tpl/tabbar.tpl');
        $content = view($tpl, [ 'tabbarList' => $tabbarList ])->getContent();
        file_put_contents(str_replace('/', DIRECTORY_SEPARATOR, $compile_path . 'app/pages/index/tabbar.vue'), $content);

        // 处理tabbar.json
        file_put_contents($compile_path . 'tabbar.json', json_encode(array_column($tabbarList, 'link'), JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
    }

    /**
     * 小程序上传env文件处理
     * @param string $env_file
     * @return void
     */
    private function weappEnvReplace(string $env_file)
    {
        $env = file_get_contents($env_file);
        $env = str_replace("VITE_APP_BASE_URL=''", "VITE_APP_BASE_URL='" . (string) url('/', [], '', true) . 'api/' . "'", $env);
        $env = str_replace("VITE_IMG_DOMAIN=''", "VITE_IMG_DOMAIN='" . (string) url('/', [], '', true) . "'", $env);
        file_put_contents($env_file, $env);
    }

    /**
     * 获取APP编译日志
     * @param string $timestamp
     * @return void
     */
    public function getAppCompileLog(string $timestamp)
    {
        $result = [
            'status' => '',
            'build_log' => [],
            'file_path' => '',
            'fail_reason' => ''
        ];

        $query = [
            'authorize_code' => $this->auth_code,
            'timestamp' => $timestamp
        ];
        $build_log = ( new CloudService(true, 'http://java.oss.niucloud.com/') )->httpGet('cloud/get_appbuild_logs?' . http_build_query($query));
        $result['build_log'] = $build_log;

        if (isset($build_log['data']) && isset($build_log['data'][0]) && is_array($build_log['data'][0])) {
            $last = end($build_log['data'][0]);
            if ($last['code'] == 0) {
                $result['status'] = 'fail';
                $result['fail_reason'] = $last['msg'] ?? '';
                return $result;
            }
            if ($last['percent'] == 100) {
                $result = $this->buildSuccess($result, $timestamp);
            }
        }

        return $result;
    }

    public function buildSuccess(array $result, string $task_key)
    {
        try {
            $query = [
                'authorize_code' => $this->auth_code,
                'timestamp' => $task_key
            ];
            $chunk_size = 1 * 1024 * 1024;
            $temp_dir = 'upload' . DIRECTORY_SEPARATOR . 'download' . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . $task_key . DIRECTORY_SEPARATOR;
            if (!is_dir($temp_dir))  mkdirs($temp_dir);

            $build_task = Cache::get($this->cache_key . $task_key) ?? [];

            if (!isset($build_task[ 'index' ])) {
                $response = ( new CloudService(true, 'http://java.oss.niucloud.com/') )->request('HEAD', 'cloud/apk_download?' . http_build_query($query), [
                    'headers' => [ 'Range' => 'bytes=0-' ]
                ]);
                $length = $response->getHeader('Content-range');
                $length = (int) explode("/", $length[ 0 ])[ 1 ];
                $step = (int) ceil($length / $chunk_size);
                $filename = $response->getHeader('filename')[0];

                $build_task = array_merge($build_task, [ 'step' => $step, 'index' => 0, 'length' => $length, 'file_name' => $filename ]);
                Cache::set($this->cache_key . $task_key, $build_task);
            } else {
                $file = $temp_dir . $build_task[ 'file_name' ];
                $file_resource = fopen($file, 'a');

                if (( $build_task[ 'index' ] + 1 ) <= $build_task[ 'step' ]) {
                    $start = $build_task[ 'index' ] * $chunk_size;
                    $end = ( $build_task[ 'index' ] + 1 ) * $chunk_size;
                    $end = min($end, $build_task[ 'length' ]);

                    $response = ( new CloudService(true, 'http://java.oss.niucloud.com/') )->request('GET', 'cloud/apk_download?' . http_build_query($query), [
                        'headers' => [ 'Range' => "bytes={$start}-{$end}" ]
                    ]);
                    fwrite($file_resource, $response->getBody());
                    fclose($file_resource);

                    $build_task[ 'index' ] += 1;
                    Cache::set($this->cache_key . $task_key, $build_task);

                    $result['build_log'][] = [ 'code' => 1, 'action' => '安装包下载中,已下载' . round($build_task[ 'index' ] / $build_task[ 'step' ] * 100) . '%', 'percent' => '99' ];
                } else {
                    $result['build_log'][] = [ 'code' => 1, 'action' => '安装包下载中,已下载' . round($build_task[ 'index' ] / $build_task[ 'step' ] * 100) . '%', 'percent' => '100' ];
                    $result['status'] = 'success';
                    $result['file_path'] = $file;
                    Cache::set($this->cache_key . $task_key, null);
                }
            }
        } catch (\Exception $e) {
            $result['status'] = 'fail';
            $result['fail_reason'] = $e->getMessage().$e->getFile().$e->getLine();
            $result['build_log'][] = [ 'code' => 0, 'msg' => $e->getMessage(), 'action' => '', 'percent' => '100' ];
        }
        return $result;
    }

    public function generateSingCert(array $data) {
        $dname = "CN={$data['cn']}, OU={$data['ou']}, O={$data['o']}, L={$data['l']}, ST={$data['st']}, C={$data['c']}";
        $query = [
            'key_alias' => $data['key_alias'],
            'key_password' => $data['key_password'],
            'store_password' => $data['store_password'],
            'limit' => $data['limit'] * 365,
            'dname' => $dname
        ];
        $response = ( new CloudService(true, 'http://java.oss.niucloud.com/') )->request('GET', 'cloud/getcert?' . http_build_query($query));
        $content_type = $response->getHeaders()['Content-Type'][0];
        if ($content_type == 'application/json') {
            $content = json_decode($response->getBody()->getContents(), true);
            if (isset($content[ 'code' ]) && $content[ 'code' ] == 0) throw new CommonException($content[ 'msg' ]);
        }
        $dir = 'upload' . DIRECTORY_SEPARATOR . 'download' . DIRECTORY_SEPARATOR . 'cert' . DIRECTORY_SEPARATOR;
        if (!is_dir($dir))  mkdirs($dir);

        $file = $dir . $data['key_alias'] . '.zip';
        if (file_exists($file)) unlink($file);

        $file_resource = fopen($file, 'a');
        fwrite($file_resource, $response->getBody());
        fclose($file_resource);
        return $file;
    }
}
