
ALTER TABLE `member` ADD COLUMN `invite_code` VARCHAR(20) NOT NULL DEFAULT '' COMMENT '邀请码' AFTER `member_no`;
ALTER TABLE `member` ADD UNIQUE INDEX `UK_invite_code` (`invite_code`) COMMENT '唯一邀请码索引';
