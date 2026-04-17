<?php

use app\dict\member\MemberAccountTypeDict;

return [
    MemberAccountTypeDict::POINT => [
        //兑换订单
        'account_point_exchange_close' => [
            //名称
            'name' => get_lang('dict_member.account_point_exchange_close'),
            //是否增加
            'inc' => 0,
            //是否减少
            'dec' => 1,
        ],
        //兑换订单关闭
        'account_point_exchange_order' => [
            //名称
            'name' => get_lang('dict_member.account_point_exchange_order'),
            //是否增加
            'inc' => 1,
            //是否减少
            'dec' => 0,
        ],
        //兑换订单维权
        'account_point_exchange_refund' => [
            //名称
            'name' => get_lang('dict_member.account_point_exchange_refund'),
            //是否增加
            'inc' => 1,
            //是否减少
            'dec' => 0,
        ],
        //消费奖励
        'consume_reward' => [
            //名称
            'name' => get_lang('dict_member.account_point_consume_reward'),
            //是否增加
            'inc' => 1,
            //是否减少
            'dec' => 0,
        ]
    ],
    MemberAccountTypeDict::BALANCE => [
    ]
];
