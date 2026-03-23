

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for dd
-- ----------------------------
DROP TABLE IF EXISTS `dd`;
CREATE TABLE `dd` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `sp_name` varchar(255) DEFAULT NULL,
  `jiage` varchar(255) DEFAULT NULL,
  `geshu` varchar(255) DEFAULT NULL,
  `dizhi` varchar(255) DEFAULT NULL,
  `zongjia` varchar(255) DEFAULT NULL,
  `youhui` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of dd
-- ----------------------------
BEGIN;
INSERT INTO `dd` VALUES (1, '礼品卡1000面值', '1000', '2', '四川省xx市xxx区xxx街道', '', '5');
INSERT INTO `dd` VALUES (2, '礼品卡1000面值', '1000', '2', '四川省xx市xxx区xxx街道', '1995', '5');
INSERT INTO `dd` VALUES (3, '礼品卡1000面值', '1000', '2', '四川省xx市xxx区xxx街道', '1995', '5');
INSERT INTO `dd` VALUES (4, '礼品卡1000面值', '1000', '2', '四川省xx市xxx区xxx街道', '1995', '5');
INSERT INTO `dd` VALUES (5, '礼品卡1000面值', '1000', '1', '四川省xx市xxx区xxx街道', '990', '10');
COMMIT;

-- ----------------------------
-- Table structure for dingdan
-- ----------------------------
DROP TABLE IF EXISTS `dingdan`;
CREATE TABLE `dingdan` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `ddid` varchar(255) DEFAULT NULL,
  `sp_name` varchar(255) DEFAULT NULL,
  `geshu` int(20) DEFAULT NULL,
  `dizhi` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for dizhi
-- ----------------------------
DROP TABLE IF EXISTS `dizhi`;
CREATE TABLE `dizhi` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `dizhi` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of dizhi
-- ----------------------------
BEGIN;
INSERT INTO `dizhi` VALUES (1, '四川省xx市xxx区xxx街道');
INSERT INTO `dizhi` VALUES (2, '北京市xxx区xxx街道');
COMMIT;

-- ----------------------------
-- Table structure for sp
-- ----------------------------
DROP TABLE IF EXISTS `sp`;
CREATE TABLE `sp` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `sp_name` varchar(255) DEFAULT NULL,
  `jiage` varchar(255) DEFAULT NULL,
  `tp_path` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of sp
-- ----------------------------
BEGIN;
INSERT INTO `sp` VALUES (1, '礼品卡1000面值', '1000', 'static/97818639-3905-4ba0-b2ec-f935d2d9234b-B6D2G8mD.jpg');
INSERT INTO `sp` VALUES (2, '礼品卡500面值', '500', 'static/d4ba1816-d1a9-46a7-a23d-42993672df0b-2wO7bsGP.jpg');
INSERT INTO `sp` VALUES (3, '礼品卡200面值', '200', 'static/7ea05677-467b-4164-aad0-df2c69ee2975-JSQfMtzr.jpg');
COMMIT;

-- ----------------------------
-- Table structure for youhuiquan
-- ----------------------------
DROP TABLE IF EXISTS `youhuiquan`;
CREATE TABLE `youhuiquan` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `key2use` int(20) DEFAULT NULL,
  `youhui` int(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of youhuiquan
-- ----------------------------
BEGIN;
INSERT INTO `youhuiquan` VALUES (1, 100, 5);
INSERT INTO `youhuiquan` VALUES (2, 100, 10);
INSERT INTO `youhuiquan` VALUES (3, 100, 20);
INSERT INTO `youhuiquan` VALUES (4, 100, 50);
COMMIT;

SET FOREIGN_KEY_CHECKS = 1;
