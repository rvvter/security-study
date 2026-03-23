

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for info
-- ----------------------------
DROP TABLE IF EXISTS `info`;
CREATE TABLE `info` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) DEFAULT NULL,
  `xm` varchar(255) DEFAULT NULL,
  `xb` varchar(255) DEFAULT NULL,
  `sj` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of info
-- ----------------------------
BEGIN;
INSERT INTO `info` VALUES (1, 'user1', 'jack', 'male', '13888881111');
INSERT INTO `info` VALUES (2, 'user2', 'jackson', 'female', '13999999999');
INSERT INTO `info` VALUES (3, 'admin1', 'jacket', 'male', '13312341234');
INSERT INTO `info` VALUES (4, 'admin2', 'jackly', 'female', '18012344321');
COMMIT;

-- ----------------------------
-- Table structure for user1
-- ----------------------------
DROP TABLE IF EXISTS `user1`;
CREATE TABLE `user1` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `is_ad` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of user1
-- ----------------------------
BEGIN;
INSERT INTO `user1` VALUES (1, 'admin1', 'admin1', '1');
INSERT INTO `user1` VALUES (2, 'admin2', 'admin2', '1');
INSERT INTO `user1` VALUES (3, 'user1', 'user1', '0');
INSERT INTO `user1` VALUES (4, 'user2', 'user2', '0');
COMMIT;

SET FOREIGN_KEY_CHECKS = 1;
