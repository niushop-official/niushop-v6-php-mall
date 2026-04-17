ALTER TABLE `member` ADD COLUMN `invite_code` varchar(20) NOT NULL DEFAULT '' COMMENT '邀请码' AFTER `member_no`;
ALTER TABLE `member` ADD UNIQUE INDEX `idx_invite_code` (`invite_code`);

ALTER TABLE `{{prefix}}shop_order_goods` ADD COLUMN `invite_code` varchar(20) NOT NULL DEFAULT '' COMMENT '邀请码' AFTER `extend`;
