<?php

namespace app\upgrade\v150;

use app\model\member\Member;

class Upgrade
{

    public function handle()
    {
        $this->generateInviteCodeForExistingMembers();
    }

    /**
     * 为现有会员生成邀请码
     */
    private function generateInviteCodeForExistingMembers()
    {
        $member_model = new Member();
        $where = [
            ['invite_code', '=', '']
        ];
        $list = $member_model->where($where)->field('member_id')->select()->toArray();

        if (!empty($list)) {
            foreach ($list as $k => $v) {
                $invite_code = $this->generateUniqueInviteCode();
                $member_model->where([['member_id', '=', $v['member_id']]])->update(['invite_code' => $invite_code]);
            }
        }
    }

    /**
     * 生成唯一邀请码
     * @return string
     */
    private function generateUniqueInviteCode()
    {
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $code = '';
        for ($i = 0; $i < 8; $i++) {
            $code .= $chars[random_int(0, strlen($chars) - 1)];
        }

        $member_model = new Member();
        $exists = $member_model->where([['invite_code', '=', $code]])->findOrEmpty();

        if ($exists->isEmpty()) {
            return $code;
        } else {
            return $this->generateUniqueInviteCode();
        }
    }
}
