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

namespace addon\shop\app\listener\local_delivery\dada\order;

use addon\shop\app\dict\local_delivery\dada\DadaDeliveryDict;
use addon\shop\app\dict\local_delivery\LocalDeliveryStatusDict;
use addon\shop\app\service\core\local_delivery\CoreLocalDeliveryService;
use addon\shop\core\local_delivery\LocalDeliveryLoader;
use app\model\member\Member;
use core\exception\CommonException;

/**
 * 查询配送订单的起始点和终点
 * Class GetLocalDeliveryTrack
 * @package addon\shop\app\listener\local_delivery\dada\order
 */
class GetLocalDeliveryTrack
{

    public function handle($params)
    {
        $status = $params['status'];
        $member_id = $params['member_id'];
        $delivery_no = $params['delivery_no'];
        $delivery_service = $params['delivery_service'];

        if ($delivery_service == DadaDeliveryDict::DADA) {
            $member_info = (new Member())->where([['member_id', '=', $member_id]])->findOrEmpty()->toArray();
            if (empty($member_info)) throw new CommonException('MEMBER_NOT_EXIST');
            $headimg = empty($member_info['headimg']) ? 'static/resource/images/default_headimg.png' : $member_info['headimg'];

            if (in_array($status, [LocalDeliveryStatusDict::PENDING_PICKUP, LocalDeliveryStatusDict::IN_DELIVERY])) {
                $config = (new CoreLocalDeliveryService())->getConfig($delivery_service);
                $loader = new LocalDeliveryLoader($delivery_service, $config);
                $data = [
                    'delivery_no' => $delivery_no,
                ];
                $response_data = $loader->getTransporterPosition($data);
            }

            switch ($status) {
                case LocalDeliveryStatusDict::ORDER_ACCEPTED:
                case LocalDeliveryStatusDict::PENDING_ACCEPTANCE:
                    return [
                        [
                            'points_type' => 'shop',
                            'latitude' => $params['delivery_start_lat'],
                            'longitude' => $params['delivery_start_lng'],
                            'icon_path' => 'addon/shop/local_delivery/in_stock.png',
                            'callout' => [
                                'content' => '备货中',
                            ],
                        ],
                        [
                            'points_type' => 'member',
                            'latitude' => $params['delivery_end_lat'],
                            'longitude' => $params['delivery_end_lng'],
                            'icon_path' => $headimg,
                            'callout' => [
                                'content' => '',
                            ],
                        ]
                    ];
                case LocalDeliveryStatusDict::PENDING_PICKUP:
                    if (!empty($response_data['rider_latitude']) && !empty($response_data['rider_longitude'])) {
                        return [
                            [
                                'points_type' => 'rider',
                                'latitude' => $response_data['rider_latitude'],
                                'longitude' => $response_data['rider_longitude'],
                                'icon_path' => 'addon/shop/local_delivery/in_delivery.png',
                                'callout' => [
                                    'content' => '骑手正赶往商家',
                                ],
                            ],
                            [
                                'points_type' => 'shop',
                                'latitude' => $params['delivery_start_lat'],
                                'longitude' => $params['delivery_start_lng'],
                                'icon_path' => 'addon/shop/local_delivery/in_stock.png',
                                'callout' => [
                                    'content' => '',
                                ],
                            ]
                        ];
                    } else {
                        return [
                            [
                                'points_type' => 'shop',
                                'latitude' => $params['delivery_start_lat'],
                                'longitude' => $params['delivery_start_lng'],
                                'icon_path' => 'addon/shop/local_delivery/in_stock.png',
                                'callout' => [
                                    'content' => '骑手正赶往商家',
                                ],
                            ]
                        ];
                    }
                case LocalDeliveryStatusDict::RIDER_ARRIVED:
                    return [
                        [
                            'points_type' => 'rider_arrived',
                            'latitude' => $params['delivery_start_lat'],
                            'longitude' => $params['delivery_start_lng'],
                            'icon_path' => 'addon/shop/local_delivery/picking_up.png',
                            'callout' => [
                                'content' => '骑手到店取餐中',
                            ],
                        ],
                        [
                            'points_type' => 'member',
                            'latitude' => $params['delivery_end_lat'],
                            'longitude' => $params['delivery_end_lng'],
                            'icon_path' => $headimg,
                            'callout' => [
                                'content' => '',
                            ],
                        ]
                    ];
                case LocalDeliveryStatusDict::IN_DELIVERY:
                    return [
                        [
                            'points_type' => 'rider',
                            'latitude' => $response_data['rider_latitude'] ?? $params['delivery_start_lat'],
                            'longitude' => $response_data['rider_longitude'] ?? $params['delivery_start_lng'],
                            'icon_path' => 'addon/shop/local_delivery/in_delivery.png',
                            'callout' => [
                                'content' => '骑手正在送餐',
                            ],
                        ],
                        [
                            'points_type' => 'member',
                            'latitude' => $params['delivery_end_lat'],
                            'longitude' => $params['delivery_end_lng'],
                            'icon_path' => $headimg,
                            'callout' => [
                                'content' => '',
                            ],
                        ]
                    ];
                default:
                    return [];
            }
        }
    }
}
