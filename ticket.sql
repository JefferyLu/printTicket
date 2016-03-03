/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50508
Source Host           : localhost:3306
Source Database       : ticket

Target Server Type    : MYSQL
Target Server Version : 50508
File Encoding         : 65001

Date: 2016-03-03 00:56:27
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `pt_coupon`
-- ----------------------------
DROP TABLE IF EXISTS `pt_coupon`;
CREATE TABLE `pt_coupon` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键自增id',
  `type` enum('deliver','discount') DEFAULT 'discount' COMMENT 'discount:单价折扣类 deliver:买赠类',
  `title` varchar(70) DEFAULT NULL COMMENT '优惠标题',
  `description` text COMMENT '优惠规则描述',
  `rules` varchar(100) DEFAULT '0' COMMENT '规则比例',
  `create_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='优惠信息表';

-- ----------------------------
-- Records of pt_coupon
-- ----------------------------
INSERT INTO `pt_coupon` VALUES ('1', 'discount', '95折', '对应商品打95折', '0.95', '2016-03-02 17:15:23');
INSERT INTO `pt_coupon` VALUES ('2', 'deliver', '买二送一', '买两个相同的商品赠送一个相同的商品', '2:1', '2016-03-02 17:16:03');

-- ----------------------------
-- Table structure for `pt_goods`
-- ----------------------------
DROP TABLE IF EXISTS `pt_goods`;
CREATE TABLE `pt_goods` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键自增id',
  `barcode` varchar(32) DEFAULT NULL COMMENT '商品条形码 唯一索引',
  `cate_id` int(11) DEFAULT '0' COMMENT '商品分类id 外键 对应商品分类id号',
  `name` varchar(70) DEFAULT NULL COMMENT '商品名称 最大70个字',
  `unit` char(6) DEFAULT '个' COMMENT '数量单位 个、斤、件、瓶等',
  `price` decimal(11,2) DEFAULT '0.00' COMMENT '单价 保留两位小数',
  `update_time` timestamp NULL DEFAULT NULL COMMENT '最后更新时间',
  `create_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `barcode` (`barcode`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COMMENT='商品信息表';

-- ----------------------------
-- Records of pt_goods
-- ----------------------------
INSERT INTO `pt_goods` VALUES ('1', 'ITEM000001', '1', '羽毛球', '个', '1.00', null, '2016-03-02 11:32:11');
INSERT INTO `pt_goods` VALUES ('3', 'ITEM000003', '2', '苹果', '斤', '5.50', null, '2016-03-02 11:32:26');
INSERT INTO `pt_goods` VALUES ('4', 'ITEM000005', '3', '可口可乐', '瓶', '3.00', null, '2016-03-02 11:32:30');
INSERT INTO `pt_goods` VALUES ('6', 'ITEM000006', '2', '橙子', '个', '4.50', null, '2016-03-02 00:09:26');
INSERT INTO `pt_goods` VALUES ('7', 'ITEM000007', '3', '王老吉', '个', '4.00', null, '2016-03-02 00:19:59');
INSERT INTO `pt_goods` VALUES ('8', 'ITEM000008', '3', '雪碧', '个', '3.00', null, '2016-03-02 00:21:32');

-- ----------------------------
-- Table structure for `pt_goods_cate`
-- ----------------------------
DROP TABLE IF EXISTS `pt_goods_cate`;
CREATE TABLE `pt_goods_cate` (
  `cate_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '商品分类id 主键 自增id',
  `cate_name` varchar(30) DEFAULT NULL COMMENT '商品分类名称',
  `create_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '添加时间 默认当前时间',
  PRIMARY KEY (`cate_id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COMMENT='商品分类表';

-- ----------------------------
-- Records of pt_goods_cate
-- ----------------------------
INSERT INTO `pt_goods_cate` VALUES ('1', '运动用品', '2016-03-02 14:21:33');
INSERT INTO `pt_goods_cate` VALUES ('2', '水果', '2016-03-02 14:21:51');
INSERT INTO `pt_goods_cate` VALUES ('3', '饮料', '2016-03-02 14:22:14');

-- ----------------------------
-- Table structure for `pt_goods_coupon`
-- ----------------------------
DROP TABLE IF EXISTS `pt_goods_coupon`;
CREATE TABLE `pt_goods_coupon` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键自增id',
  `barcode` varchar(32) DEFAULT NULL COMMENT '商品唯一条形码',
  `coupon_id` int(11) DEFAULT '0' COMMENT '商品对应优惠活动id ',
  `level` int(11) DEFAULT '0' COMMENT '优惠优先级 数字越大级别越高',
  `create_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '添加时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `barcode_coupon` (`barcode`,`coupon_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COMMENT='商品优惠对应关系表';

-- ----------------------------
-- Records of pt_goods_coupon
-- ----------------------------
INSERT INTO `pt_goods_coupon` VALUES ('1', 'ITEM000001', '2', '0', '2016-03-02 14:24:24');
INSERT INTO `pt_goods_coupon` VALUES ('3', 'ITEM000003', '1', '0', '2016-03-02 14:24:59');
INSERT INTO `pt_goods_coupon` VALUES ('5', 'ITEM000005', '1', '0', '2016-03-02 14:25:12');
INSERT INTO `pt_goods_coupon` VALUES ('6', 'ITEM000005', '2', '1', '2016-03-02 14:25:45');
