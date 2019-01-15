ALTER TABLE `yw_f_comment`
CHANGE COLUMN `play_id` `play_id`  int(11) NOT NULL COMMENT '评论剧目id(关联play表)' AFTER `create_time`;
INSERT INTO `yw_admin_menu` VALUES (176, 0, 1, 1, 5, 'adminyw', 'Comment', 'index', '', '评论管理', 'comment', '');