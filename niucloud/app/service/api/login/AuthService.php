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

namespace app\service\api\login;

use app\dict\common\ChannelDict;
use app\model\member\Member;
use app\Request;
use app\service\api\member\MemberService;
use app\service\core\channel\CoreH5Service;
use app\service\core\channel\CorePcService;
use app\service\core\weapp\CoreWeappAuthService;
use core\base\BaseApiService;
use core\exception\ApiException;
use core\exception\AuthException;

/**
 * 登录服务层
 * Class AuthService
 * @package app\service\api\login
 */
class AuthService extends BaseApiService
{

    public function __construct()
    {
        parent::__construct();
        $this->model = new Member();
    }

    public function checkMember(Request $request)
    {
        //如果登录信息非法就报错
        if ($this->member_id > 0) {
            $member_service = new MemberService();
            $member_info = $member_service->findMemberInfo([ 'member_id' => $this->member_id ]);
            if ($member_info->isEmpty()) {
                throw new AuthException('MEMBER_NOT_EXIST', 401);
            }
        }
        return true;
    }

    /**
     * 校验渠道
     * @param Request $request
     * @return void
     */
    public function checkChannel(Request $request)
    {
        $channel = $request->getChannel();

        switch ($channel) {
            case ChannelDict::H5:
                $is_open = (int) ( new CoreH5Service() )->getH5()[ 'is_open' ];
                if (!$is_open) throw new AuthException('SITE_CLOSE_NOT_ALLOW', 402);
                break;
            case ChannelDict::PC:
                $is_open = (int) ( new CorePcService() )->getPc()[ 'is_open' ];
                if (!$is_open) throw new AuthException('SITE_CLOSE_NOT_ALLOW', 402);
                break;
        }
    }

    /**
     * 绑定手机号
     * @param string $mobile
     * @param string $mobile_code
     * @return array
     */
    public function bindMobile(string $mobile, string $mobile_code)
    {

        if (empty($mobile)) {
            $result = ( new CoreWeappAuthService() )->getUserPhoneNumber($mobile_code);
            if (empty($result)) throw new ApiException('WECHAT_EMPOWER_NOT_EXIST');
            if ($result[ 'errcode' ] != 0) throw new ApiException($result[ 'errmsg' ]);
            $phone_info = $result[ 'phone_info' ];
            $mobile = $phone_info[ 'purePhoneNumber' ];
            if (empty($mobile)) throw new ApiException('WECHAT_EMPOWER_NOT_EXIST');
        } else {
            //todo  校验手机号验证码
            ( new LoginService() )->checkMobileCode($mobile);
        }
        $member_service = new MemberService();
        $member = $member_service->findMemberInfo([ 'member_id' => $this->member_id ]);
        if ($member->isEmpty()) throw new AuthException('MEMBER_NOT_EXIST');

        $o_mobile = $member[ 'mobile' ];//原始手机号
        if (!empty($o_mobile) && $o_mobile == $mobile) throw new AuthException('MOBILE_NOT_CHANGE');

        $mobile_member = $member_service->findMemberInfo([ 'mobile' => $mobile ]);
        if (!$mobile_member->isEmpty()) throw new AuthException('MOBILE_IS_EXIST');

//        if(empty($mobile)) throw new AuthException('MOBILE_NEEDED');//必须填写
        $member->save([
            'mobile' => $mobile
        ]);
        return [
            'mobile' => $mobile
        ];
    }

    /**
     * 获取手机号
     * @param string $mobile_code
     * @return array
     */
    public function getMobile(string $mobile_code)
    {

        $result = ( new CoreWeappAuthService() )->getUserPhoneNumber($mobile_code);
        if (empty($result)) throw new ApiException('WECHAT_EMPOWER_NOT_EXIST');
        if ($result[ 'errcode' ] != 0) throw new ApiException($result[ 'errmsg' ]);
        $phone_info = $result[ 'phone_info' ];
        $mobile = $phone_info[ 'purePhoneNumber' ];
        if (empty($mobile)) throw new ApiException('WECHAT_EMPOWER_NOT_EXIST');

//        $member_service = new MemberService();

//        $mobile_member = $member_service->findMemberInfo([ 'mobile' => $mobile ]);
//        if (!$mobile_member->isEmpty()) throw new AuthException('MOBILE_IS_EXIST');

        return [
            'mobile' => $mobile
        ];
    }

}
