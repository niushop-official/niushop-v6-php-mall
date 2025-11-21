ALTER TABLE `shop_invoice`
    CHANGE COLUMN trade_id trade_id VARCHAR(1000) NOT NULL DEFAULT '' COMMENT '业务id集';

ALTER TABLE `shop_invoice`
    ADD COLUMN pay_voucher TEXT DEFAULT NULL COMMENT '支付凭证';