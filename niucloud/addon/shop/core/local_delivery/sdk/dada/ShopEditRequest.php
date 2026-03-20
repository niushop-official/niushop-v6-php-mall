<?php

namespace addon\shop\core\local_delivery\sdk\dada;

class ShopEditRequest extends BaseRequest
{

    private $request_url = '/api/shop/update';

    private $station_name;

    private $business;

    private $station_address;

    private $lng;

    private $lat;

    private $contact_name;

    private $phone;

    private $origin_shop_id;

    private $settlement_type = 0;

    public function setStationName($station_name)
    {
        $this->station_name = $station_name;
        return $this;
    }

    public function getStationName()
    {
        return $this->station_name;
    }

    public function setBusiness($business)
    {
        $this->business = $business;
        return $this;
    }

    public function getBusiness()
    {
        return $this->business;
    }

    public function setStationAddress($station_address)
    {
        $this->station_address = $station_address;
        return $this;
    }

    public function getStationAddress()
    {
        return $this->station_address;
    }

    public function setLng($lng)
    {
        $this->lng = $lng;
        return $this;
    }

    public function getLng()
    {
        return $this->lng;
    }

    public function setLat($lat)
    {
        $this->lat = $lat;
        return $this;
    }

    public function getLat()
    {
        return $this->lat;
    }

    public function setContactName($contact_name)
    {
        $this->contact_name = $contact_name;
        return $this;
    }

    public function getContactName()
    {
        return $this->contact_name;
    }

    public function setPhone($phone)
    {
        $this->phone = $phone;
        return $this;
    }

    public function getPhone()
    {
        return $this->phone;
    }

    public function setOriginShopId($origin_shop_id)
    {
        $this->origin_shop_id = $origin_shop_id;
        return $this;
    }

    public function getOriginShopId()
    {
        return $this->origin_shop_id;
    }

    public function setSettlementType($settlement_type)
    {
        $this->settlement_type = $settlement_type;
        return $this;
    }

    public function getSettlementType()
    {
        return $this->settlement_type;
    }

    // 将对象转换为数组
    public function toArray()
    {
        return [
            'station_name' => $this->station_name,
            'business' => $this->business,
            'station_address' => $this->station_address,
            'lng' => $this->lng,
            'lat' => $this->lat,
            'contact_name' => $this->contact_name,
            'phone' => $this->phone,
            'origin_shop_id' => $this->origin_shop_id,
            'settlement_type' => $this->settlement_type,
        ];
    }

    // 将对象转换为JSON
    public function toJson()
    {
        return json_encode($this->toArray(), JSON_UNESCAPED_UNICODE);
    }

    private function validate()
    {
        if (empty($this->origin_shop_id)) {
            throw new \Exception('门店编号不能为空');
        }
    }

    public function sendRequest()
    {
        $this->validate();
        $body = $this->toArray();
        foreach ($body as $key => $value) {
            if (is_null($value)) {
                unset($body[$key]);
            }
        }
        return $this->request($body,$this->request_url);
    }
}