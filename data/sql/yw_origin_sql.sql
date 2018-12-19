/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50505
Source Host           : localhost:3306
Source Database       : yiwantongren

Target Server Type    : MYSQL
Target Server Version : 50505
File Encoding         : 65001

Date: 2018-12-19 12:12:24
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `yw_ask_video`
-- ----------------------------
DROP TABLE IF EXISTS `yw_ask_video`;
CREATE TABLE `yw_ask_video` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `user_id` int(11) NOT NULL COMMENT '用户id',
  `video_name` varchar(64) NOT NULL COMMENT '片名',
  `description` varchar(255) NOT NULL COMMENT '片描述',
  `create_time` datetime NOT NULL COMMENT '求片时间',
  `is_deleted` tinyint(2) NOT NULL DEFAULT '0' COMMENT '是否被删除(0：否 1：是)',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='求片表\r\n登陆后才可以求片';

-- ----------------------------
-- Records of yw_ask_video
-- ----------------------------

-- ----------------------------
-- Table structure for `yw_click_record`
-- ----------------------------
DROP TABLE IF EXISTS `yw_click_record`;
CREATE TABLE `yw_click_record` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `number` varchar(32) NOT NULL COMMENT '点击量',
  `play_id` int(11) NOT NULL COMMENT '剧目id',
  `create_time` datetime NOT NULL COMMENT '创建时间',
  `update_time` datetime NOT NULL COMMENT '更新时间',
  `is_deleted` tinyint(2) NOT NULL DEFAULT '0' COMMENT '是否被删除（0：否 1：是）',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='点击量记录表\r\n点到剧目详情里时进行记录';

-- ----------------------------
-- Records of yw_click_record
-- ----------------------------

-- ----------------------------
-- Table structure for `yw_country`
-- ----------------------------
DROP TABLE IF EXISTS `yw_country`;
CREATE TABLE `yw_country` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `name` varchar(32) NOT NULL COMMENT '国家名称',
  `pid` int(11) NOT NULL DEFAULT '0' COMMENT '父级id',
  `language` varchar(32) NOT NULL COMMENT '语种',
  `listorder` int(11) NOT NULL DEFAULT '0' COMMENT '排序',
  `remark` varchar(255) DEFAULT NULL COMMENT '备注',
  `is_deleted` tinyint(2) NOT NULL COMMENT '是否被删除(0：否 1：是)',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='国家语种表\r\n欧美、日本、韩国、泰国；如点击欧美，查找父级为欧美的国家，出现法国，意大利，显示语种为法语，拉丁语等';

-- ----------------------------
-- Records of yw_country
-- ----------------------------

-- ----------------------------
-- Table structure for `yw_f_comment`
-- ----------------------------
DROP TABLE IF EXISTS `yw_f_comment`;
CREATE TABLE `yw_f_comment` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `user_id` int(11) NOT NULL DEFAULT '0' COMMENT '前台用户id（游客为0）',
  `name` varchar(32) NOT NULL DEFAULT '游客' COMMENT '评论者昵称',
  `content` longtext NOT NULL COMMENT '评论内容',
  `create_time` datetime NOT NULL COMMENT '评论时间',
  `play_id` int(11) NOT NULL COMMENT '评论剧目id(关联play表)',
  `comment_id` int(11) NOT NULL DEFAULT '0' COMMENT '回复评论id(直接评论剧目的为0，回复其他人的为其他人的评论id)',
  `is_deleted` tinyint(2) NOT NULL DEFAULT '0' COMMENT '是否被删除(0：否 1：是)',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='评论表\r\n游客也可以评论';

-- ----------------------------
-- Records of yw_f_comment
-- ----------------------------

-- ----------------------------
-- Table structure for `yw_f_user`
-- ----------------------------
DROP TABLE IF EXISTS `yw_f_user`;
CREATE TABLE `yw_f_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `name` varchar(32) NOT NULL COMMENT '昵称',
  `email` varchar(64) NOT NULL COMMENT '邮箱',
  `avatar` varchar(255) DEFAULT NULL COMMENT '头像',
  `last_login_time` datetime NOT NULL COMMENT '最近登陆时间',
  `create_time` datetime NOT NULL COMMENT '注册时间',
  `is_deleted` tinyint(2) NOT NULL DEFAULT '0' COMMENT '是否被删除(0：否 1：是)',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='前台用户表';

-- ----------------------------
-- Records of yw_f_user
-- ----------------------------

-- ----------------------------
-- Table structure for `yw_play`
-- ----------------------------
DROP TABLE IF EXISTS `yw_play`;
CREATE TABLE `yw_play` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `name` varchar(32) NOT NULL COMMENT '剧目名称',
  `type_id` int(11) NOT NULL COMMENT '分类id(关联分类表)',
  `listorder` int(11) NOT NULL DEFAULT '0' COMMENT '排序',
  `cover_img` varchar(255) NOT NULL COMMENT '封面图',
  `description` varchar(255) NOT NULL COMMENT '剧目描述',
  `content` longtext NOT NULL COMMENT '剧目内容（富文本）',
  `is_login` tinyint(2) NOT NULL DEFAULT '0' COMMENT '是否登陆才可见(0：否 1：是)',
  `is_show` tinyint(2) NOT NULL DEFAULT '1' COMMENT '前台是否可见(0：否 1：是)',
  `is_top` tinyint(2) NOT NULL DEFAULT '0' COMMENT '是否置顶（0：否 1：是）',
  `is_lunbo` tinyint(2) NOT NULL DEFAULT '0' COMMENT '是否轮播(0：否 1：是)',
  `is_deleted` tinyint(2) NOT NULL DEFAULT '0' COMMENT '是否被删除(0：否 1：是)',
  `create_time` datetime NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='剧目表\r\n某部电影，电视剧，动漫，分享链接在后台用富文本编辑';

-- ----------------------------
-- Records of yw_play
-- ----------------------------

-- ----------------------------
-- Table structure for `yw_play_country`
-- ----------------------------
DROP TABLE IF EXISTS `yw_play_country`;
CREATE TABLE `yw_play_country` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `play_id` int(11) NOT NULL COMMENT '剧目id(关联 play表)',
  `country_id` int(11) NOT NULL COMMENT '国家id(关联country表)',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='剧目国家关联表\r\n大多数剧属于1个国家，有些剧可能是多个国家合拍';

-- ----------------------------
-- Records of yw_play_country
-- ----------------------------

-- ----------------------------
-- Table structure for `yw_play_tag`
-- ----------------------------
DROP TABLE IF EXISTS `yw_play_tag`;
CREATE TABLE `yw_play_tag` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `play_id` int(11) NOT NULL COMMENT '剧目id(关联 play表)',
  `tag_id` int(11) NOT NULL COMMENT '标签id(关联tag表)',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='剧目标签关联表\r\n一个剧目可以有多个标签';

-- ----------------------------
-- Records of yw_play_tag
-- ----------------------------

-- ----------------------------
-- Table structure for `yw_play_translator`
-- ----------------------------
DROP TABLE IF EXISTS `yw_play_translator`;
CREATE TABLE `yw_play_translator` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `play_id` int(11) NOT NULL COMMENT '剧目id(关联 play表)',
  `translator_id` int(11) NOT NULL COMMENT '翻译者id(关联translator表)',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='剧目译者关联表\r\n一个翻译者可能翻译多部剧，每部剧有多个翻译者';

-- ----------------------------
-- Records of yw_play_translator
-- ----------------------------

-- ----------------------------
-- Table structure for `yw_tag`
-- ----------------------------
DROP TABLE IF EXISTS `yw_tag`;
CREATE TABLE `yw_tag` (
  `id` int(11) NOT NULL,
  `name` varchar(32) NOT NULL COMMENT '标签名',
  `listorder` int(11) NOT NULL DEFAULT '0' COMMENT '排序',
  `is_deleted` tinyint(2) NOT NULL DEFAULT '0' COMMENT '是否被删除(0：否 1：是)'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='标签表\r\n惊悚、悬疑、爱情、奇幻';

-- ----------------------------
-- Records of yw_tag
-- ----------------------------

-- ----------------------------
-- Table structure for `yw_translator`
-- ----------------------------
DROP TABLE IF EXISTS `yw_translator`;
CREATE TABLE `yw_translator` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL COMMENT '翻译者名',
  `description` varchar(255) NOT NULL COMMENT '描述',
  `url` varchar(255) DEFAULT NULL COMMENT '跳转链接',
  `create_time` datetime NOT NULL COMMENT '创建时间',
  `is_deleted` tinyint(2) NOT NULL DEFAULT '0' COMMENT '是否被删除(0：否 1：是)',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='翻译人员\r\n翻译人员的相关介绍';

-- ----------------------------
-- Records of yw_translator
-- ----------------------------

-- ----------------------------
-- Table structure for `yw_type`
-- ----------------------------
DROP TABLE IF EXISTS `yw_type`;
CREATE TABLE `yw_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `name` varchar(32) NOT NULL COMMENT '分类名称',
  `listorder` int(11) NOT NULL DEFAULT '0' COMMENT '排序',
  `remark` varchar(255) DEFAULT NULL,
  `is_deleted` tinyint(2) NOT NULL COMMENT '是否被删除(0：否 1：是)',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='剧种分类表\r\n电影，电视剧，纪录片，短片，剪辑以及动画还有游戏、漫画';

-- ----------------------------
-- Records of yw_type
-- ----------------------------
