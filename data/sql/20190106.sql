ALTER TABLE `yw_tag`
MODIFY COLUMN `id`  int(11) NOT NULL AUTO_INCREMENT FIRST ,
ADD PRIMARY KEY (`id`);

ALTER TABLE `yw_country`
CHANGE COLUMN `pid` `parent_id`  int(11) NOT NULL DEFAULT 0 COMMENT '父级id' AFTER `name`;

INSERT INTO `yw_admin_menu` VALUES (174, 0, 1, 1, 2, 'adminyw', 'Tag', 'index', '', '标签管理', 'tag', '');
INSERT INTO `yw_admin_menu` VALUES (175, 0, 1, 1, 4, 'adminyw', 'Translator', 'index', '', '贡献人员管理', 'language', '');