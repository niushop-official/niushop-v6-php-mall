<?php

namespace addon\shop\core\local_delivery\sdk\dada;

class ConfigRequest extends BaseRequest
{
    private $cancel_reason_url = '/api/order/cancel/reasons';
    public function getCancelReasonList()
    {
        return $this->request([], $this->cancel_reason_url);
    }
}