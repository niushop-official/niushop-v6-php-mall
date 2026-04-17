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

namespace app\service\admin\member;

use app\model\sys\SysConfig;
use app\service\core\member\CoreMemberConfigService;
use app\service\core\member\CoreMemberService;
use core\base\BaseAdminService;
use think\Model;

/**
 * 会员设置
 * Class MemberConfigService
 * @package app\service\admin\member
 */
class MemberConfigService extends BaseAdminService
{
    /**
     * 获取注册与登录设置
     */
    public function getLoginConfig(){

        return (new CoreMemberConfigService())->getLoginConfig();
    }

    /**
     * 注册登录设置
     * @param array $data
     * @return true
     */
    public function setLoginConfig(array $data){
        return (new CoreMemberConfigService())->setLoginConfig($data);
    }
    /**
     * 获取提现设置
     */
    public function getCashOutConfig(){

        return (new CoreMemberConfigService())->getCashOutConfig();
    }

    /**
     * 提现设置
     * @param array $data
     * @return true
     */
    public function setCashOutConfig(array $data){
        return (new CoreMemberConfigService())->setCashOutConfig($data);
    }

    /**
     * 获取会员设置
     */
    public function getMemberConfig(){
        return (new CoreMemberConfigService())->getMemberConfig();
    }

    /**
     * 会员设置
     * @param array $data
     * @return true
     */
    public function setMemberConfig(array $data){
        return (new CoreMemberConfigService())->setMemberConfig($data);
    }

    /**
     * 获取成长值规则配置
     */
    public function getGrowthRuleConfig(){
        $config = (new CoreMemberConfigService())->getGrowthRuleConfig();
        if (!empty($config)) {
            $config = CoreMemberService::getGrowthRuleContent($config);
        }
        return $config;
    }

    /**
     * 配置成长值规则
     * @param array $data
     * @return true
     */
    public function setGrowthRuleConfig(array $data){
        return (new CoreMemberConfigService())->setGrowthRuleConfig($data);
    }

    /**
     * 获取积分规则配置
     */
    public function getPointRuleConfig(){
        $config = (new CoreMemberConfigService())->getPointRuleConfig();
        if (!empty($config)) {
            if (isset($config['grant']) && !empty($config['grant'])) $config['grant'] = CoreMemberService::getPointGrantRuleContent($config['grant']);
            if (isset($config['consume']) && !empty($config['consume'])) $config['consume'] = CoreMemberService::getPointGrantRuleContent($config['consume']);
        }
        return $config;
    }

    /**
     * 配置积分规则
     * @param array $data
     * @return true
     */
    public function setPointRuleConfig(array $data){
        return (new CoreMemberConfigService())->setPointRuleConfig($data);
    }
}
