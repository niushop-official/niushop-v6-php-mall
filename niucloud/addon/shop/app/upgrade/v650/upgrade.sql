ALTER TABLE `shop_store`
    ADD COLUMN `store_no` VARCHAR(255) NOT NULL DEFAULT '' COMMENT '门店编号' AFTER store_id;

ALTER TABLE `shop_store`
    ADD COLUMN `contact_name` VARCHAR(255) NOT NULL DEFAULT '' COMMENT '联系人' AFTER store_mobile;

ALTER TABLE `shop_store`
    ADD COLUMN `support_delivery` TEXT DEFAULT NULL COMMENT '支持的配送服务商';

ALTER TABLE `shop_store`
    ADD COLUMN `extend_data` TEXT DEFAULT NULL COMMENT '配送服务商扩展数据';

ALTER TABLE `shop_store`
    ADD COLUMN `time_is_open` TINYINT NOT NULL DEFAULT 0 COMMENT '配送时间设置 0 关闭1 开启';

ALTER TABLE `shop_store`
    ADD COLUMN `support_local_delivery` TINYINT NOT NULL DEFAULT 0 COMMENT '支持同城配送';

ALTER TABLE `shop_store`
    ADD COLUMN `support_store` TINYINT NOT NULL DEFAULT 0 COMMENT '支持门店自提';

ALTER TABLE `shop_store`
    ADD COLUMN `status` TINYINT NOT NULL DEFAULT 0 COMMENT '开启状态（1开启，0关闭）';

ALTER TABLE `shop_store`
    ADD COLUMN `area` LONGTEXT DEFAULT NULL COMMENT '配送区域';

ALTER TABLE `shop_order_delivery`
    ADD COLUMN `local_delivery_order_id` VARCHAR(255) NOT NULL DEFAULT '' COMMENT '同城配送单号';

ALTER TABLE `shop_goods_evaluate`
    CHANGE COLUMN `member_name` `member_name` VARCHAR (255) NOT NULL DEFAULT '' COMMENT '会员名称';

ALTER TABLE `shop_goods`
    ADD COLUMN `goods_image_width` INT NOT NULL DEFAULT 800 COMMENT '商品详情首图宽' AFTER `goods_image`;
ALTER TABLE `shop_goods`
    ADD COLUMN `goods_image_height` INT NOT NULL DEFAULT 800 COMMENT '商品详情首图高' AFTER `goods_image_width`;


ALTER TABLE `shop_delivery_company`
    COMMENT = '物流公司表';


DROP TABLE IF EXISTS `shop_delivery_shop_delivery_order_log`;
CREATE TABLE `shop_delivery_shop_delivery_order_log`
(
    `id`           INT          NOT NULL AUTO_INCREMENT,
    `order_id`     INT          NOT NULL DEFAULT 0 COMMENT '订单id',
    `main_id`      INT          NOT NULL DEFAULT 0 COMMENT '操作人id',
    `main_type`    VARCHAR(255) NOT NULL DEFAULT '' COMMENT '操作人类型',
    `main_name`    VARCHAR(255) NOT NULL DEFAULT '' COMMENT '操作人名称',
    `operate`      VARCHAR(255) NOT NULL DEFAULT '' COMMENT '操作类型',
    `operate_desc` VARCHAR(500) NOT NULL DEFAULT '' COMMENT '操作描述',
    `status`       VARCHAR(255) NOT NULL DEFAULT '' COMMENT '操作后配送状态',
    `create_time`  INT          NOT NULL DEFAULT 0 COMMENT '配送时间',
    `remark`       VARCHAR(255) NOT NULL DEFAULT '' COMMENT '备注',
    PRIMARY KEY (`id`)
) ENGINE = INNODB,
CHARACTER SET utf8mb4,
COLLATE utf8mb4_general_ci,
COMMENT = '商家配送服务订单操作表';

DROP TABLE IF EXISTS `shop_delivery_shop_delivery_order`;
CREATE TABLE `shop_delivery_shop_delivery_order`
(
    `id`                         INT            NOT NULL AUTO_INCREMENT,
    `delivery_no`                VARCHAR(255)   NOT NULL DEFAULT '' COMMENT '配送单号',
    `out_delivery_no`            VARCHAR(255)   NOT NULL DEFAULT '' COMMENT '三方配送单号',
    `trade_type`                 VARCHAR(255)   NOT NULL DEFAULT '' COMMENT '交易类型，mall商城 supply供货商',
    `trade_no`                   VARCHAR(255)   NOT NULL DEFAULT '' COMMENT '交易单号',
    `trade_id`                   INT            NOT NULL DEFAULT 0 COMMENT '交易id',
    `delivery_money`             DECIMAL(10, 2) NOT NULL DEFAULT 0.00 COMMENT '配送费用',
    `delivery_start`             VARCHAR(255)   NOT NULL DEFAULT '' COMMENT '配送起点（发货点）',
    `delivery_start_province_id` INT            NOT NULL DEFAULT 0 COMMENT '配送起点省',
    `delivery_start_city_id`     INT            NOT NULL DEFAULT 0 COMMENT '配送起点市',
    `delivery_start_district_id` INT            NOT NULL DEFAULT 0 COMMENT '配送起点区',
    `delivery_start_lng`         VARCHAR(255)   NOT NULL DEFAULT '' COMMENT '配送起点经度',
    `delivery_start_lat`         VARCHAR(255)   NOT NULL DEFAULT '' COMMENT '配送起点纬度',
    `delivery_end`               VARCHAR(255)   NOT NULL DEFAULT '' COMMENT '配送终点（收货点）',
    `delivery_end_province_id`   INT            NOT NULL DEFAULT 0 COMMENT '配送终点省',
    `delivery_end_city_id`       INT            NOT NULL DEFAULT 0 COMMENT '配送终点市',
    `delivery_end_district_id`   INT            NOT NULL DEFAULT 0 COMMENT '配送终点区',
    `delivery_end_lng`           VARCHAR(255)   NOT NULL DEFAULT '' COMMENT '配送终点经度',
    `delivery_end_lat`           VARCHAR(255)   NOT NULL DEFAULT '' COMMENT '配送终点纬度',
    `delivery_distance`          DECIMAL(10, 4) NOT NULL DEFAULT 0.0000 COMMENT '配送距离',
    `deliver_id`                 INT            NOT NULL DEFAULT 0 COMMENT '配送员id',
    `deliver_name`               VARCHAR(255)   NOT NULL DEFAULT '' COMMENT '配送员姓名',
    `deliver_mobile`             VARCHAR(255)   NOT NULL DEFAULT '' COMMENT '配送员手机号',
    `status`                     VARCHAR(255)   NOT NULL DEFAULT '' COMMENT '配送状态',
    `create_time`                INT            NOT NULL DEFAULT 0 COMMENT '配送时间',
    `finish_time`                INT            NOT NULL DEFAULT 0 COMMENT '送达时间',
    `cancel_time`                INT            NOT NULL DEFAULT 0 COMMENT '取消时间',
    `remark`                     VARCHAR(255)   NOT NULL DEFAULT '' COMMENT '备注',
    `body`                       VARCHAR(1000)  NOT NULL DEFAULT '' COMMENT '商品信息',
    PRIMARY KEY (`id`)
) ENGINE = INNODB,
CHARACTER SET utf8mb4,
COLLATE utf8mb4_general_ci,
COMMENT = '商家配送服务订单表';

DROP TABLE IF EXISTS `shop_delivery_local_delivery_service`;
CREATE TABLE `shop_delivery_local_delivery_service`
(
    `id`          INT          NOT NULL AUTO_INCREMENT,
    `key`         VARCHAR(255) NOT NULL DEFAULT '' COMMENT '三方配送key',
    `name`        VARCHAR(255) NOT NULL DEFAULT '' COMMENT '三方配送名称',
    `config`      TEXT                  DEFAULT NULL COMMENT '三方配送接入参数',
    `is_merchant` TINYINT      NOT NULL DEFAULT 0 COMMENT '是否商家自配送',
    `is_use`      TINYINT      NOT NULL DEFAULT 0 COMMENT '是否启用（0否，1是）',
    PRIMARY KEY (`id`)
) ENGINE = INNODB,
CHARACTER SET utf8mb4,
COLLATE utf8mb4_general_ci,
COMMENT = '同城配送服务商表';


DROP TABLE IF EXISTS `shop_delivery_local_delivery_order_log`;
CREATE TABLE `shop_delivery_local_delivery_order_log`
(
    `id`           INT          NOT NULL AUTO_INCREMENT,
    `order_id`     INT          NOT NULL DEFAULT 0 COMMENT '订单id',
    `main_id`      INT          NOT NULL DEFAULT 0 COMMENT '操作人id',
    `main_type`    VARCHAR(255) NOT NULL DEFAULT '' COMMENT '操作人类型',
    `main_name`    VARCHAR(255) NOT NULL DEFAULT '' COMMENT '操作人名称',
    `operate`      VARCHAR(255) NOT NULL DEFAULT '' COMMENT '操作类型',
    `operate_desc` VARCHAR(500) NOT NULL DEFAULT '' COMMENT '操作描述',
    `status`       VARCHAR(255) NOT NULL DEFAULT '' COMMENT '操作后配送状态',
    `create_time`  INT          NOT NULL DEFAULT 0 COMMENT '配送时间',
    `remark`       VARCHAR(255) NOT NULL DEFAULT '' COMMENT '备注',
    PRIMARY KEY (`id`)
) ENGINE = INNODB,
CHARACTER SET utf8mb4,
COLLATE utf8mb4_general_ci,
COMMENT = '同城配送服务订单操作表';


DROP TABLE IF EXISTS `shop_delivery_local_delivery_order`;
CREATE TABLE `shop_delivery_local_delivery_order`
(
    `id`                         INT            NOT NULL AUTO_INCREMENT,
    `delivery_service`           VARCHAR(255)   NOT NULL DEFAULT '' COMMENT '配送方标识',
    `delivery_no`                VARCHAR(255)   NOT NULL DEFAULT '' COMMENT '配送单号',
    `out_delivery_no`            VARCHAR(255)   NOT NULL DEFAULT '' COMMENT '三方配送单号',
    `trade_type`                 VARCHAR(255)   NOT NULL DEFAULT '' COMMENT '交易类型，mall商城',
    `trade_no`                   VARCHAR(255)   NOT NULL DEFAULT '' COMMENT '交易单号',
    `trade_id`                   INT            NOT NULL DEFAULT 0 COMMENT '交易id',
    `delivery_money`             DECIMAL(10, 2) NOT NULL DEFAULT 0.00 COMMENT '配送费用',
    `delivery_start`             VARCHAR(1000)  NOT NULL DEFAULT '' COMMENT '配送起点（发货点）',
    `delivery_start_province_id` INT            NOT NULL DEFAULT 0 COMMENT '配送起点省',
    `delivery_start_city_id`     INT            NOT NULL DEFAULT 0 COMMENT '配送起点市',
    `delivery_start_district_id` INT            NOT NULL DEFAULT 0 COMMENT '配送起点区',
    `delivery_start_lng`         VARCHAR(255)   NOT NULL DEFAULT '' COMMENT '配送起点经度',
    `delivery_start_lat`         VARCHAR(255)   NOT NULL DEFAULT '' COMMENT '配送起点纬度',
    `delivery_end`               VARCHAR(1000)  NOT NULL DEFAULT '' COMMENT '配送终点（收货点）',
    `delivery_end_province_id`   INT            NOT NULL DEFAULT 0 COMMENT '配送终点省',
    `delivery_end_city_id`       INT            NOT NULL DEFAULT 0 COMMENT '配送终点市',
    `delivery_end_district_id`   INT            NOT NULL DEFAULT 0 COMMENT '配送终点区',
    `delivery_end_lng`           VARCHAR(255)   NOT NULL DEFAULT '' COMMENT '配送终点经度',
    `delivery_end_lat`           VARCHAR(255)   NOT NULL DEFAULT '' COMMENT '配送终点纬度',
    `delivery_distance`          DECIMAL(10, 4) NOT NULL DEFAULT 0.0000 COMMENT '配送距离',
    `status`                     VARCHAR(255)   NOT NULL DEFAULT '' COMMENT '配送状态',
    `out_status`                 VARCHAR(255)   NOT NULL DEFAULT '' COMMENT '三方配送状态',
    `create_time`                INT            NOT NULL DEFAULT 0 COMMENT '配送时间',
    `finish_time`                INT            NOT NULL DEFAULT 0 COMMENT '送达时间',
    `cancel_time`                INT            NOT NULL DEFAULT 0 COMMENT '取消时间',
    `rider_name`                 VARCHAR(255)   NOT NULL DEFAULT '' COMMENT '骑手姓名',
    `rider_mobile`               VARCHAR(255)   NOT NULL DEFAULT '' COMMENT '骑手电话',
    `deduct_money`               DECIMAL(10, 2) NOT NULL DEFAULT 0.00 COMMENT '违约金',
    `remark`                     VARCHAR(255)   NOT NULL DEFAULT '' COMMENT '备注',
    `body`                       VARCHAR(255)   NOT NULL DEFAULT '' COMMENT '配送产品信息',
    `actual_money`               DECIMAL(10, 2) NOT NULL DEFAULT 0.00 COMMENT '实际消费费用(订单取消或完成后实际定义)',
    PRIMARY KEY (`id`)
) ENGINE = INNODB,
CHARACTER SET utf8mb4,
COLLATE utf8mb4_general_ci,
COMMENT = '同城配送服务订单表';
