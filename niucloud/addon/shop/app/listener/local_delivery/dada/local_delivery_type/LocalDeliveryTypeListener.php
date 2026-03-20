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

namespace addon\shop\app\listener\local_delivery\dada\local_delivery_type;

/**
 * 配送服务商-达达秒送
 * @package addon\shop\app\listener\local_delivery\dada\local_delivery_type
 */
class LocalDeliveryTypeListener
{

    public function handle($params)
    {
        return [
            'dada' => [
                'name' => '达达秒送',
                'key' => 'dada',
                //配置参数
                'params' => [
                    'app_key' => 'APP_KEY',
                    'app_secret' => 'APP_SECRET',
                    'source_id' => '商户SourceID',
                    'shop_no' => '门店编号',
                ],
                'encrypt_params' => ['app_key', 'app_secret', 'source_id', 'shop_no'],
                'component' => '/src/addon/shop/views/delivery/components/local-delivery-service-dada.vue',
                'business_list' => json_decode(file_get_contents(root_path() . '/addon/shop/app/dict/local_delivery/dada/json/shop_catetory_dict.json'), true),
                'extend_data' => [],
                //商家配送标识
                'is_merchant' => 0
            ],
        ];
    }
}
