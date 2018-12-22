/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50505
Source Host           : localhost:3306
Source Database       : yiwantongren

Target Server Type    : MYSQL
Target Server Version : 50505
File Encoding         : 65001

Date: 2018-12-22 09:54:16
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `yw_jilu`
-- ----------------------------
DROP TABLE IF EXISTS `yw_jilu`;
CREATE TABLE `yw_jilu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` varchar(64) CHARACTER SET utf8 NOT NULL COMMENT '操作者',
  `remark` varchar(256) CHARACTER SET utf8 NOT NULL COMMENT '备注',
  `update_before` varchar(256) CHARACTER SET utf8 NOT NULL COMMENT '更新前数据',
  `update_after` varchar(256) CHARACTER SET utf8 NOT NULL COMMENT '更新后数据',
  `c_time` datetime NOT NULL COMMENT '创建日期',
  `object` varchar(64) CHARACTER SET utf8 NOT NULL COMMENT '表明',
  `object_id` int(11) NOT NULL COMMENT '某表对应id',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Records of yw_jilu
-- ----------------------------
INSERT INTO `yw_admin_menu` VALUES (170, 0, 1, 1, 10000, 'adminyw', 'Type', 'index', '', '剧种分类管理', 'th', '');
INSERT INTO `yw_admin_menu` VALUES (171, 0, 1, 1, 10000, 'adminyw', 'play', 'index', '', '剧目管理', 'tv', '');
INSERT INTO `yw_admin_menu` VALUES (172, 0, 1, 1, 10000, 'adminyw', 'record', 'index', '', '操作记录', 'clipboard', '');