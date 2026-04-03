<?php

namespace app\service\core\notice;

use app\dict\notice\BindMerchantDict;
use app\service\core\sys\CoreConfigService;
use core\base\BaseCoreService;

class CoreNoticeBindMerchantService extends BaseCoreService
{

    public function __construct()
    {
        parent::__construct();
    }

    public function getInfo()
    {
        return (new CoreConfigService())->getConfigValue(BindMerchantDict::KEY);
    }

    public function bindAccount($value, $type = BindMerchantDict::WEAPP, $extends = [])
    {
        if ($type == BindMerchantDict::WECHAT) {
            $openid_column = 'wechat_openid';
        } else if ($type == BindMerchantDict::SMS) {
            $openid_column = 'mobile';
        } else {
            $openid_column = 'weapp_openid';
        }
        $bind_info = $this->getInfo();
        if (empty($bind_info)) {
            if (!empty($extends)) {
                $extends[$type] = $extends;
            }
            $res = (new CoreConfigService())->setConfig(BindMerchantDict::KEY,[
                'extends' => $extends,
                $openid_column => $value,
            ]);
        } else {
            $db_extends = $bind_info['extends'];
            if (!empty($extends)) {
                $db_extends[$type] = $extends;
            }
            $bind_info['extends'] = $db_extends;
            $bind_info[$openid_column] = $value;
            $res = (new CoreConfigService())->setConfig(BindMerchantDict::KEY,$bind_info);
        }
        return $res;
    }

    public function unBindAccount($data)
    {
        if (in_array(BindMerchantDict::WECHAT, $data['unbind_type']) && in_array(BindMerchantDict::WEAPP, $data['unbind_type']) && in_array(BindMerchantDict::SMS, $data['unbind_type'])) {
            return (new CoreConfigService())->clearConfig(BindMerchantDict::KEY);
        }
        $bind_info = $this->getInfo();
        if (in_array(BindMerchantDict::WECHAT, $data['unbind_type'])) {
            $bind_info['wechat_openid']= '';

        }
        if (in_array(BindMerchantDict::WEAPP, $data['unbind_type'])) {
            $bind_info['weapp_openid']= '';

        }
        if (in_array(BindMerchantDict::SMS, $data['unbind_type'])) {
            $bind_info['mobile']= '';
        }
        (new CoreConfigService())->setConfig(BindMerchantDict::KEY,$bind_info);
        return true;
    }
}