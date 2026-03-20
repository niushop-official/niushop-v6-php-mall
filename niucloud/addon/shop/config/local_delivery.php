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

use core\dict\DictLoader;

$system = [
    //默认驱动
    'default' => 'merchant',
    'drivers' => [
        //商家自配送
        'merchant' => [],
        //达达
        'dada' => [
            'driver' => 'addon\shop\core\local_delivery\Dada',  //反射类的名字
        ]
    ]
];
$system['drivers'] = (new DictLoader("Config"))->load(['data' => $system['drivers'], 'name' => 'local_delivery']);
return $system;
