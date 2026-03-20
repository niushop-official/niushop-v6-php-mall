<?php

namespace addon\shop\core\local_delivery\sdk\dada;

class OrderCancelRequest extends BaseRequest
{
    private $request_url = '/api/order/formalCancel';
    private $cancel_reason;
    private $cancel_reason_id;
    private $order_id;

    public function setCancelReason(string $cancel_reason): self
    {
        $this->cancel_reason = $cancel_reason;
        return $this;
    }

    public function getCancelReason(): string
    {
        return $this->cancel_reason;
    }

    public function setCancelReasonId(int $cancel_reason_id): self
    {
        $this->cancel_reason_id = $cancel_reason_id;
        return $this;
    }

    public function getCancelReasonId(): int
    {
        return $this->cancel_reason_id;
    }

    public function setOrderId(string $order_id): self
    {
        $this->order_id = $order_id;
        return $this;
    }

    public function getOrderId(): string
    {
        return $this->order_id;
    }

    public function toArray(): array
    {
        return [
            'cancel_reason' => $this->cancel_reason,
            'cancel_reason_id' => $this->cancel_reason_id,
            'order_id' => $this->order_id,
        ];
    }

    public function toJson(): string
    {
        return json_encode($this->toArray(), JSON_UNESCAPED_UNICODE);
    }

    private function validate()
    {
        if (empty($this->order_id)) {
            throw new \Exception('订单编号不能为空');
        }
        if (empty($this->cancel_reason_id)) {
            throw new \Exception('取消原因ID不能为空');
        }
        if ($this->cancel_reason_id == 10000 && empty($this->cancel_reason)) {
            throw new \Exception('取消原因不能为空');
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
        return $this->request($body, $this->request_url);
    }
}