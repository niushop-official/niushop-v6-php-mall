<?php

namespace addon\shop\core\local_delivery\sdk\dada;

class ShopAddRequest extends BaseRequest
{

    private $request_url = '/api/shop/add';

    private $station_name;

    private $business;

    private $station_address;

    private $lng;

    private $lat;

    private $contact_name;

    private $phone;

    private $origin_shop_id;

    private $id_card = null;

    private $username = null;

    private $password = null;

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

    public function setIdCard($id_card)
    {
        $this->id_card = $id_card;
        return $this;
    }

    public function getIdCard()
    {
        return $this->id_card;
    }

    public function setUsername($username)
    {
        $this->username = $username;
        return $this;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function setPassword($password)
    {
        $this->password = $password;
        return $this;
    }

    public function getPassword()
    {
        return $this->password;
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
            'id_card' => $this->id_card,
            'username' => $this->username,
            'password' => $this->password,
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
        if (empty($this->station_name)) {
            throw new \Exception('门店名称不能为空');
        }
        if (empty($this->business)) {
            throw new \Exception('门店所属行业类别不能为空');
        }
        if (empty($this->station_address)) {
            throw new \Exception('门店地址不能为空');
        }
        if (empty($this->lng) || empty($this->lat)) {
            throw new \Exception('门店经纬度不能为空');
        }
        if (empty($this->contact_name)) {
            throw new \Exception('联系人姓名不能为空');
        }
        if (empty($this->phone)) {
            throw new \Exception('联系人电话不能为空');
        }
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
        $body = [$body];
        return $this->request($body,$this->request_url);
    }
}