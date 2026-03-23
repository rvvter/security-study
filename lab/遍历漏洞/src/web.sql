

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for info


-- ----------------------------
-- Table structure for score
-- ----------------------------
DROP TABLE IF EXISTS `score`;
CREATE TABLE `score` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `number` varchar(255) DEFAULT NULL,
  `math` varchar(255) DEFAULT NULL,
  `english` varchar(255) DEFAULT NULL,
  `chinese` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of score
-- ----------------------------
BEGIN;
INSERT INTO `score` VALUES (1, '20190504035XA01', '95', '95', '95');
INSERT INTO `score` VALUES (2, '20190504035XA02', '91', '95', '95');
INSERT INTO `score` VALUES (3, '20190504035XA03', '91', '93', '91');
INSERT INTO `score` VALUES (4, '20190504035XA04', '93', '95', '92');
INSERT INTO `score` VALUES (5, '20190504035XA05', '94', '93', '93');
INSERT INTO `score` VALUES (6, '20190504035XA06', '91', '93', '100');
INSERT INTO `score` VALUES (7, '20190504035XA07', '93', '92', '92');
INSERT INTO `score` VALUES (8, '20190504035XA08', '83', '93', '91');
COMMIT;

SET FOREIGN_KEY_CHECKS = 1;
