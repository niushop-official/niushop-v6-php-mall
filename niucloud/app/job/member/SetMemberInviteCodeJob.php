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

namespace app\job\member;

use app\service\core\member\CoreMemberService;
use core\base\BaseJob;

class SetMemberInviteCodeJob extends BaseJob
{
    public function doJob($member_id)
    {
        CoreMemberService::createInviteCode($member_id);
        return true;
    }
}
