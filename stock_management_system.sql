/*
 Navicat Premium Dump SQL

 Source Server         : mysql
 Source Server Type    : MySQL
 Source Server Version : 100432 (10.4.32-MariaDB)
 Source Host           : localhost:3306
 Source Schema         : stock_management_system

 Target Server Type    : MySQL
 Target Server Version : 100432 (10.4.32-MariaDB)
 File Encoding         : 65001

 Date: 03/11/2026 07:27:06
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for accounting_periods
-- ----------------------------
DROP TABLE IF EXISTS `accounting_periods`;
CREATE TABLE `accounting_periods`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `fiscal_year_id` bigint UNSIGNED NOT NULL,
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `period_number` tinyint UNSIGNED NOT NULL,
  `start_date_ad` date NOT NULL,
  `start_date_bs` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `end_date_ad` date NOT NULL,
  `end_date_bs` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `is_closed` tinyint(1) NOT NULL DEFAULT 0,
  `closed_by` bigint UNSIGNED NULL DEFAULT NULL,
  `closed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `accounting_periods_fiscal_year_id_period_number_unique`(`fiscal_year_id` ASC, `period_number` ASC) USING BTREE,
  INDEX `accounting_periods_closed_by_foreign`(`closed_by` ASC) USING BTREE,
  INDEX `accounting_periods_is_closed_index`(`is_closed` ASC) USING BTREE,
  CONSTRAINT `accounting_periods_closed_by_foreign` FOREIGN KEY (`closed_by`) REFERENCES `admins` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT,
  CONSTRAINT `accounting_periods_fiscal_year_id_foreign` FOREIGN KEY (`fiscal_year_id`) REFERENCES `fiscal_years` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 13 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of accounting_periods
-- ----------------------------
INSERT INTO `accounting_periods` VALUES (1, 1, 'Shrawan 2081', 1, '2024-07-16', '2081-04-01', '2024-08-16', '2081-04-32', 0, NULL, NULL, '2026-10-18 21:44:40', '2026-10-18 21:44:40');
INSERT INTO `accounting_periods` VALUES (2, 1, 'Bhadra 2081', 2, '2024-08-17', '2081-05-01', '2024-09-16', '2081-05-31', 0, NULL, NULL, '2026-10-18 21:44:40', '2026-10-18 21:44:40');
INSERT INTO `accounting_periods` VALUES (3, 1, 'Ashwin 2081', 3, '2024-09-17', '2081-06-01', '2024-10-16', '2081-06-30', 0, NULL, NULL, '2026-10-18 21:44:40', '2026-10-18 21:44:40');
INSERT INTO `accounting_periods` VALUES (4, 1, 'Kartik 2081', 4, '2024-10-17', '2081-07-01', '2024-11-15', '2081-07-30', 0, NULL, NULL, '2026-10-18 21:44:40', '2026-10-18 21:44:40');
INSERT INTO `accounting_periods` VALUES (5, 1, 'Mangsir 2081', 5, '2024-11-16', '2081-08-01', '2024-12-15', '2081-08-29', 0, NULL, NULL, '2026-10-18 21:44:40', '2026-10-18 21:44:40');
INSERT INTO `accounting_periods` VALUES (6, 1, 'Poush 2081', 6, '2024-12-16', '2081-09-01', '2025-01-14', '2081-09-30', 0, NULL, NULL, '2026-10-18 21:44:40', '2026-10-18 21:44:40');
INSERT INTO `accounting_periods` VALUES (7, 1, 'Magh 2081', 7, '2025-01-15', '2081-10-01', '2025-02-12', '2081-10-29', 0, NULL, NULL, '2026-10-18 21:44:40', '2026-10-18 21:44:40');
INSERT INTO `accounting_periods` VALUES (8, 1, 'Falgun 2081', 8, '2025-02-13', '2081-11-01', '2025-03-14', '2081-11-30', 0, NULL, NULL, '2026-10-18 21:44:40', '2026-10-18 21:44:40');
INSERT INTO `accounting_periods` VALUES (9, 1, 'Chaitra 2081', 9, '2025-03-15', '2081-12-01', '2025-04-13', '2081-12-30', 0, NULL, NULL, '2026-10-18 21:44:40', '2026-10-18 21:44:40');
INSERT INTO `accounting_periods` VALUES (10, 1, 'Baisakh 2082', 10, '2025-04-14', '2082-01-01', '2025-05-14', '2082-01-31', 0, NULL, NULL, '2026-10-18 21:44:40', '2026-10-18 21:44:40');
INSERT INTO `accounting_periods` VALUES (11, 1, 'Jestha 2082', 11, '2025-05-15', '2082-02-01', '2025-06-14', '2082-02-31', 0, NULL, NULL, '2026-10-18 21:44:40', '2026-10-18 21:44:40');
INSERT INTO `accounting_periods` VALUES (12, 1, 'Ashadh 2082', 12, '2025-06-15', '2082-03-01', '2025-07-15', '2082-03-31', 0, NULL, NULL, '2026-10-18 21:44:40', '2026-10-18 21:44:40');

-- ----------------------------
-- Table structure for admin_warehouse
-- ----------------------------
DROP TABLE IF EXISTS `admin_warehouse`;
CREATE TABLE `admin_warehouse`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `admin_id` bigint UNSIGNED NOT NULL,
  `warehouse_id` bigint UNSIGNED NOT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `admin_warehouse_admin_id_warehouse_id_unique`(`admin_id` ASC, `warehouse_id` ASC) USING BTREE,
  INDEX `admin_warehouse_warehouse_id_foreign`(`warehouse_id` ASC) USING BTREE,
  CONSTRAINT `admin_warehouse_admin_id_foreign` FOREIGN KEY (`admin_id`) REFERENCES `admins` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `admin_warehouse_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 11 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of admin_warehouse
-- ----------------------------
INSERT INTO `admin_warehouse` VALUES (1, 2, 1, 1, '2026-10-18 23:15:48', '2026-10-18 23:15:48');
INSERT INTO `admin_warehouse` VALUES (2, 3, 1, 1, '2026-10-18 23:15:49', '2026-10-18 23:15:49');
INSERT INTO `admin_warehouse` VALUES (3, 4, 1, 1, '2026-10-18 23:15:49', '2026-10-18 23:15:49');
INSERT INTO `admin_warehouse` VALUES (4, 5, 1, 1, '2026-10-18 23:15:49', '2026-10-18 23:15:49');
INSERT INTO `admin_warehouse` VALUES (5, 6, 1, 1, '2026-10-18 23:15:50', '2026-10-18 23:15:50');
INSERT INTO `admin_warehouse` VALUES (6, 7, 1, 1, '2026-10-18 23:15:50', '2026-10-18 23:15:50');
INSERT INTO `admin_warehouse` VALUES (7, 8, 1, 1, '2026-10-18 23:15:50', '2026-10-18 23:15:50');
INSERT INTO `admin_warehouse` VALUES (8, 9, 1, 1, '2026-10-18 23:15:50', '2026-10-18 23:15:50');
INSERT INTO `admin_warehouse` VALUES (9, 10, 1, 1, '2026-10-18 23:15:51', '2026-10-18 23:15:51');
INSERT INTO `admin_warehouse` VALUES (10, 11, 1, 1, '2026-10-18 23:15:51', '2026-10-18 23:15:51');

-- ----------------------------
-- Table structure for admins
-- ----------------------------
DROP TABLE IF EXISTS `admins`;
CREATE TABLE `admins`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `role_id` bigint UNSIGNED NULL DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `phone` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `username` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `password_reference` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '0 = Inactive, 1 = Active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `admins_username_unique`(`username` ASC) USING BTREE,
  INDEX `admins_role_id_foreign`(`role_id` ASC) USING BTREE,
  CONSTRAINT `admins_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB AUTO_INCREMENT = 12 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of admins
-- ----------------------------
INSERT INTO `admins` VALUES (2, 1, 'Super Administrator', NULL, 'info@nhpc.gov.np', 'admin', '$2y$12$TGFq0n0x0eEt/wiXW7B.m.fRZafgSUdEAxKXqldyW.Q3MJEKTibUi', 'admin@2025', 1, '2026-10-18 23:00:29', '2026-10-18 23:15:48');
INSERT INTO `admins` VALUES (3, 3, 'Business Owner', NULL, 'owner@pharmadistributors.com.np', 'business_owner', '$2y$12$zGbBdpmci0pFVpVI3pmk/.hVdbyHerM8x7rvb0idK2ChxGUdzdR1G', 'owner@2025', 1, '2026-10-18 23:15:49', '2026-10-18 23:15:49');
INSERT INTO `admins` VALUES (4, 4, 'System Administrator', NULL, 'sysadmin@pharmadistributors.com.np', 'sysadmin', '$2y$12$p.ZR3dBQeivpvX2WolHrVeZSqADrs0LfjhU/4LkrILeJ8xpTj28Zq', 'sysadmin@2025', 1, '2026-10-18 23:15:49', '2026-10-18 23:15:49');
INSERT INTO `admins` VALUES (5, 5, 'Inventory Manager', NULL, 'inventory@pharmadistributors.com.np', 'inventory_mgr', '$2y$12$GgabtzSEq0iXVv9HqeGeHOA8ckU9/QDCM3re64JBsOAsnomSTl9Su', 'inventory@2025', 1, '2026-10-18 23:15:49', '2026-10-18 23:15:49');
INSERT INTO `admins` VALUES (6, 6, 'Purchase Officer', NULL, 'purchase@pharmadistributors.com.np', 'purchase_officer', '$2y$12$F1MDaTXx3gKU4zq53PNDLODcbKNymGOvkH0Ff10NLrVeaOYaG72ZW', 'purchase@2025', 1, '2026-10-18 23:15:50', '2026-10-18 23:15:50');
INSERT INTO `admins` VALUES (7, 7, 'Sales Officer', NULL, 'sales@pharmadistributors.com.np', 'sales_officer', '$2y$12$zml2LHIoOe8T0rHwgj9Aqu4iCiJf/XvPISM2QQfpn2QlrOOaL/3nm', 'sales@2025', 1, '2026-10-18 23:15:50', '2026-10-18 23:15:50');
INSERT INTO `admins` VALUES (8, 8, 'Warehouse Staff', NULL, 'warehouse@pharmadistributors.com.np', 'warehouse_staff', '$2y$12$1dL3GohFFNrL9hkLibv1FOkSflMTskm0O3wA9T/Vo1BDunVaT4W4G', 'warehouse@2025', 1, '2026-10-18 23:15:50', '2026-10-18 23:15:50');
INSERT INTO `admins` VALUES (9, 9, 'Accountant', NULL, 'accountant@pharmadistributors.com.np', 'accountant', '$2y$12$GqHrKz2VREGWAt5f7R4txOi2pf0iVSoERcx5.pUpufd2lkbf/kdKq', 'accountant@2025', 1, '2026-10-18 23:15:50', '2026-10-18 23:15:50');
INSERT INTO `admins` VALUES (10, 10, 'Cashier', NULL, 'cashier@pharmadistributors.com.np', 'cashier', '$2y$12$cB7YXSjhkqkmuRPNIRJp3ukSumpX8/DXWqw4Aaleym5cIzc14/X/i', 'cashier@2025', 1, '2026-10-18 23:15:51', '2026-10-18 23:15:51');
INSERT INTO `admins` VALUES (11, 11, 'External Auditor', NULL, 'auditor@pharmadistributors.com.np', 'auditor', '$2y$12$R4vwRX..pHYV8qTEL88Im.2Cn/53mpG0H6E9mSiwaVvB1FvmcrRc.', 'auditor@2025', 1, '2026-10-18 23:15:51', '2026-10-18 23:15:51');

-- ----------------------------
-- Table structure for admit_cards
-- ----------------------------
DROP TABLE IF EXISTS `admit_cards`;
CREATE TABLE `admit_cards`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `professional_id` bigint UNSIGNED NULL DEFAULT NULL,
  `applicant_id` bigint UNSIGNED NULL DEFAULT NULL,
  `symbol_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `is_taken` tinyint(1) NULL DEFAULT NULL,
  `created_by` bigint UNSIGNED NULL DEFAULT NULL,
  `updated_by` bigint UNSIGNED NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `admit_cards_professional_id_foreign`(`professional_id` ASC) USING BTREE,
  INDEX `admit_cards_applicant_id_foreign`(`applicant_id` ASC) USING BTREE,
  INDEX `admit_cards_created_by_foreign`(`created_by` ASC) USING BTREE,
  INDEX `admit_cards_updated_by_foreign`(`updated_by` ASC) USING BTREE,
  CONSTRAINT `admit_cards_applicant_id_foreign` FOREIGN KEY (`applicant_id`) REFERENCES `applicants` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `admit_cards_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `admins` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `admit_cards_professional_id_foreign` FOREIGN KEY (`professional_id`) REFERENCES `professionals` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `admit_cards_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `admins` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of admit_cards
-- ----------------------------

-- ----------------------------
-- Table structure for applicant_logs
-- ----------------------------
DROP TABLE IF EXISTS `applicant_logs`;
CREATE TABLE `applicant_logs`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `professional_id` bigint UNSIGNED NULL DEFAULT NULL,
  `admin_id` bigint UNSIGNED NULL DEFAULT NULL,
  `applicant_id` bigint UNSIGNED NULL DEFAULT NULL,
  `remarks` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `state` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT 'operator',
  `status` enum('pending','progress','accepted','rejected','re-exam','onhold','re-check') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `applicant_logs_professional_id_foreign`(`professional_id` ASC) USING BTREE,
  INDEX `applicant_logs_admin_id_foreign`(`admin_id` ASC) USING BTREE,
  INDEX `applicant_logs_applicant_id_foreign`(`applicant_id` ASC) USING BTREE,
  CONSTRAINT `applicant_logs_admin_id_foreign` FOREIGN KEY (`admin_id`) REFERENCES `admins` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `applicant_logs_applicant_id_foreign` FOREIGN KEY (`applicant_id`) REFERENCES `applicants` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `applicant_logs_professional_id_foreign` FOREIGN KEY (`professional_id`) REFERENCES `professionals` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of applicant_logs
-- ----------------------------

-- ----------------------------
-- Table structure for applicants
-- ----------------------------
DROP TABLE IF EXISTS `applicants`;
CREATE TABLE `applicants`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `exam_id` bigint UNSIGNED NULL DEFAULT NULL,
  `professional_id` bigint UNSIGNED NULL DEFAULT NULL,
  `voucher_image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `biometric_photo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `remarks` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL,
  `subject_committee_count` int NULL DEFAULT NULL,
  `level_id` bigint UNSIGNED NULL DEFAULT NULL,
  `program_id` bigint UNSIGNED NULL DEFAULT NULL,
  `attempt` int NOT NULL DEFAULT 0,
  `is_admit_card_generate` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0 = No, 1 = Yes',
  `is_certificate_generate` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0 = No, 1 = Yes',
  `is_passed` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0 = No, 1 = Yes',
  `rejected` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0 = No, 1 = Yes',
  `payment_by` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'Bank' COMMENT 'Bank or Khalti',
  `payment_success` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0 = No, 1 = Yes',
  `state` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT 'operator',
  `status` enum('pending','progress','accepted','rejected','re-exam','onhold','re-check') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `applicants_exam_id_foreign`(`exam_id` ASC) USING BTREE,
  INDEX `applicants_professional_id_foreign`(`professional_id` ASC) USING BTREE,
  INDEX `applicants_level_id_foreign`(`level_id` ASC) USING BTREE,
  INDEX `applicants_program_id_foreign`(`program_id` ASC) USING BTREE,
  CONSTRAINT `applicants_exam_id_foreign` FOREIGN KEY (`exam_id`) REFERENCES `exams` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `applicants_level_id_foreign` FOREIGN KEY (`level_id`) REFERENCES `levels` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `applicants_professional_id_foreign` FOREIGN KEY (`professional_id`) REFERENCES `professionals` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `applicants_program_id_foreign` FOREIGN KEY (`program_id`) REFERENCES `programs` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of applicants
-- ----------------------------

-- ----------------------------
-- Table structure for audit_logs
-- ----------------------------
DROP TABLE IF EXISTS `audit_logs`;
CREATE TABLE `audit_logs`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED NULL DEFAULT NULL,
  `user_type` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'admin',
  `auditable_type` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `auditable_id` bigint UNSIGNED NULL DEFAULT NULL,
  `event` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `old_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL,
  `new_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL,
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `audit_logs_user_id_foreign`(`user_id` ASC) USING BTREE,
  INDEX `audit_logs_auditable_type_index`(`auditable_type` ASC) USING BTREE,
  INDEX `audit_logs_auditable_id_index`(`auditable_id` ASC) USING BTREE,
  INDEX `audit_logs_event_index`(`event` ASC) USING BTREE,
  INDEX `audit_logs_created_at_index`(`created_at` ASC) USING BTREE,
  CONSTRAINT `audit_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `admins` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 43 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of audit_logs
-- ----------------------------
INSERT INTO `audit_logs` VALUES (1, NULL, 'admin', 'App\\Models\\Product', 1, 'created', NULL, '{\"code\":\"PRD-NIKO-500\",\"name\":\"Niko 500mg Tablet\",\"generic_name\":\"Paracetamol\",\"brand\":\"Niko\",\"category_id\":1,\"manufacturer_id\":1,\"dosage_form\":\"Tablet\",\"strength\":\"500mg\",\"base_unit_id\":9,\"tax_category_id\":1,\"mrp\":2,\"purchase_price\":1.2,\"selling_price\":1.6,\"wholesale_price\":1.5,\"min_stock\":500,\"reorder_level\":1000,\"max_level\":10000,\"storage_requirement\":\"room_temperature\",\"is_active\":true,\"updated_at\":\"2026-10-18 22:06:40\",\"created_at\":\"2026-10-18 22:06:40\",\"id\":1}', '127.0.0.1', 'Symfony', '2026-10-18 22:06:40');
INSERT INTO `audit_logs` VALUES (2, NULL, 'admin', 'App\\Models\\Product', 2, 'created', NULL, '{\"code\":\"PRD-CIPRO-500\",\"name\":\"Cipro-500 Tablet\",\"generic_name\":\"Ciprofloxacin Hydrochloride\",\"brand\":\"Cipro\",\"category_id\":2,\"manufacturer_id\":2,\"dosage_form\":\"Tablet\",\"strength\":\"500mg\",\"base_unit_id\":9,\"tax_category_id\":1,\"mrp\":15,\"purchase_price\":9.5,\"selling_price\":12,\"wholesale_price\":11.2,\"min_stock\":200,\"reorder_level\":500,\"is_active\":true,\"updated_at\":\"2026-10-18 22:06:40\",\"created_at\":\"2026-10-18 22:06:40\",\"id\":2}', '127.0.0.1', 'Symfony', '2026-10-18 22:06:40');
INSERT INTO `audit_logs` VALUES (3, NULL, 'admin', 'App\\Models\\Product', 3, 'created', NULL, '{\"code\":\"PRD-INSULIN-40\",\"name\":\"Human Insulin 40IU\\/ml 10ml Vial\",\"generic_name\":\"Human Recombinant Insulin\",\"brand\":\"Insugen-R\",\"category_id\":4,\"manufacturer_id\":2,\"dosage_form\":\"Injection (Vial)\",\"strength\":\"40 IU\\/ml, 10ml\",\"base_unit_id\":5,\"tax_category_id\":1,\"mrp\":450,\"purchase_price\":320,\"selling_price\":395,\"wholesale_price\":380,\"min_stock\":50,\"reorder_level\":100,\"storage_requirement\":\"cold_chain_2_8\",\"is_active\":true,\"updated_at\":\"2026-10-18 22:06:40\",\"created_at\":\"2026-10-18 22:06:40\",\"id\":3}', '127.0.0.1', 'Symfony', '2026-10-18 22:06:40');
INSERT INTO `audit_logs` VALUES (4, NULL, 'admin', 'App\\Models\\Batch', 1, 'created', NULL, '{\"product_id\":1,\"batch_number\":\"NK-2024-01\",\"manufacturer_id\":1,\"mfg_date_ad\":\"2024-01-01 00:00:00\",\"expiry_date_ad\":\"2027-01-18 00:00:00\",\"mrp\":2,\"cost_price\":1.2,\"is_quarantined\":false,\"is_recalled\":false,\"mfg_date_bs\":\"2080-09-16\",\"expiry_date_bs\":\"2083-10-04\",\"updated_at\":\"2026-10-18 22:06:40\",\"created_at\":\"2026-10-18 22:06:40\",\"id\":1}', '127.0.0.1', 'Symfony', '2026-10-18 22:06:40');
INSERT INTO `audit_logs` VALUES (5, NULL, 'admin', 'App\\Models\\Batch', 2, 'created', NULL, '{\"product_id\":1,\"batch_number\":\"NK-2024-02\",\"manufacturer_id\":1,\"mfg_date_ad\":\"2024-06-01 00:00:00\",\"expiry_date_ad\":\"2028-04-18 00:00:00\",\"mrp\":2,\"cost_price\":1.25,\"is_quarantined\":false,\"is_recalled\":false,\"mfg_date_bs\":\"2081-02-19\",\"expiry_date_bs\":\"2085-01-06\",\"updated_at\":\"2026-10-18 22:06:40\",\"created_at\":\"2026-10-18 22:06:40\",\"id\":2}', '127.0.0.1', 'Symfony', '2026-10-18 22:06:40');
INSERT INTO `audit_logs` VALUES (6, NULL, 'admin', 'App\\Models\\Batch', 3, 'created', NULL, '{\"product_id\":1,\"batch_number\":\"NK-2023-EX\",\"manufacturer_id\":1,\"mfg_date_ad\":\"2023-01-01 00:00:00\",\"expiry_date_ad\":\"2026-09-18 00:00:00\",\"mrp\":2,\"cost_price\":1.1,\"is_quarantined\":false,\"is_recalled\":false,\"mfg_date_bs\":\"2079-09-17\",\"expiry_date_bs\":\"2083-06-02\",\"updated_at\":\"2026-10-18 22:06:40\",\"created_at\":\"2026-10-18 22:06:40\",\"id\":3}', '127.0.0.1', 'Symfony', '2026-10-18 22:06:40');
INSERT INTO `audit_logs` VALUES (7, NULL, 'admin', 'App\\Models\\Batch', 4, 'created', NULL, '{\"product_id\":1,\"batch_number\":\"NK-2024-QR\",\"manufacturer_id\":1,\"mfg_date_ad\":\"2024-05-01 00:00:00\",\"expiry_date_ad\":\"2027-10-18 00:00:00\",\"mrp\":2,\"cost_price\":1.2,\"is_quarantined\":true,\"is_recalled\":false,\"notes\":\"Quality seal check pending\",\"mfg_date_bs\":\"2081-01-19\",\"expiry_date_bs\":\"2084-07-02\",\"updated_at\":\"2026-10-18 22:06:40\",\"created_at\":\"2026-10-18 22:06:40\",\"id\":4}', '127.0.0.1', 'Symfony', '2026-10-18 22:06:40');
INSERT INTO `audit_logs` VALUES (8, NULL, 'admin', 'App\\Models\\Batch', 5, 'created', NULL, '{\"product_id\":3,\"batch_number\":\"INS-2024-09\",\"manufacturer_id\":2,\"mfg_date_ad\":\"2024-09-01 00:00:00\",\"expiry_date_ad\":\"2027-10-18 00:00:00\",\"mrp\":450,\"cost_price\":320,\"is_quarantined\":false,\"is_recalled\":false,\"mfg_date_bs\":\"2081-05-16\",\"expiry_date_bs\":\"2084-07-02\",\"updated_at\":\"2026-10-18 22:06:40\",\"created_at\":\"2026-10-18 22:06:40\",\"id\":5}', '127.0.0.1', 'Symfony', '2026-10-18 22:06:40');
INSERT INTO `audit_logs` VALUES (9, NULL, 'admin', 'App\\Models\\StockMovement', 1, 'stock_movement', '{\"on_hand_before\":0}', '{\"on_hand_after\":2000,\"movement_type\":\"OPENING_STOCK\",\"qty\":2000}', '127.0.0.1', 'Symfony', '2026-10-18 22:06:40');
INSERT INTO `audit_logs` VALUES (10, NULL, 'admin', 'App\\Models\\StockMovement', 2, 'stock_movement', '{\"on_hand_before\":0}', '{\"on_hand_after\":5000,\"movement_type\":\"OPENING_STOCK\",\"qty\":5000}', '127.0.0.1', 'Symfony', '2026-10-18 22:06:40');
INSERT INTO `audit_logs` VALUES (11, NULL, 'admin', 'App\\Models\\StockMovement', 3, 'stock_movement', '{\"on_hand_before\":0}', '{\"on_hand_after\":150,\"movement_type\":\"OPENING_STOCK\",\"qty\":150}', '127.0.0.1', 'Symfony', '2026-10-18 22:06:40');
INSERT INTO `audit_logs` VALUES (12, NULL, 'admin', 'App\\Models\\Supplier', 1, 'created', NULL, '{\"code\":\"SUP-DJPL-DIST\",\"name\":\"Deurali-Janta Distribution Division\",\"contact_person\":\"Ramesh Shrestha\",\"phone\":\"+977-1-4351122\",\"email\":\"distribution@deuralijanta.com\",\"pan_vat_number\":\"300123456\",\"dda_license_number\":\"DDA-W-012\\/075\",\"address\":\"Dhapasi, Kathmandu\",\"credit_period_days\":45,\"credit_limit\":2000000,\"opening_balance\":0,\"current_balance\":0,\"is_active\":true,\"updated_at\":\"2026-10-18 22:17:02\",\"created_at\":\"2026-10-18 22:17:02\",\"id\":1}', '127.0.0.1', 'Symfony', '2026-10-18 22:17:02');
INSERT INTO `audit_logs` VALUES (13, NULL, 'admin', 'App\\Models\\Supplier', 2, 'created', NULL, '{\"code\":\"SUP-NPL-SALES\",\"name\":\"NPL Pharma Logistics & Sales\",\"contact_person\":\"Suman Adhikari\",\"phone\":\"+977-1-4275566\",\"email\":\"orders@npl.com.np\",\"pan_vat_number\":\"300987654\",\"dda_license_number\":\"DDA-W-034\\/076\",\"address\":\"Kalanki, Kathmandu\",\"credit_period_days\":30,\"credit_limit\":1500000,\"opening_balance\":0,\"current_balance\":0,\"is_active\":true,\"updated_at\":\"2026-10-18 22:17:02\",\"created_at\":\"2026-10-18 22:17:02\",\"id\":2}', '127.0.0.1', 'Symfony', '2026-10-18 22:17:02');
INSERT INTO `audit_logs` VALUES (14, NULL, 'admin', 'App\\Models\\Supplier', 3, 'created', NULL, '{\"code\":\"SUP-MEDISALES\",\"name\":\"Medisales International Nepal Pvt. Ltd.\",\"contact_person\":\"Bikram Thapa\",\"phone\":\"+977-1-4433221\",\"email\":\"sales@medisales.com.np\",\"pan_vat_number\":\"601876543\",\"dda_license_number\":\"DDA-W-056\\/077\",\"address\":\"Tripureshwor, Kathmandu\",\"credit_period_days\":60,\"credit_limit\":3000000,\"opening_balance\":0,\"current_balance\":0,\"is_active\":true,\"updated_at\":\"2026-10-18 22:17:02\",\"created_at\":\"2026-10-18 22:17:02\",\"id\":3}', '127.0.0.1', 'Symfony', '2026-10-18 22:17:02');
INSERT INTO `audit_logs` VALUES (15, NULL, 'admin', 'App\\Models\\PurchaseOrder', 1, 'created', NULL, '{\"po_number\":\"PO-8182-00001\",\"fiscal_year_id\":1,\"supplier_id\":1,\"warehouse_id\":1,\"order_date_ad\":\"2026-10-18 00:00:00\",\"order_date_bs\":\"2083-07-01\",\"expected_delivery_date_ad\":null,\"expected_delivery_date_bs\":null,\"status\":\"draft\",\"subtotal\":0,\"discount_amount\":0,\"taxable_amount\":0,\"tax_amount\":0,\"total_amount\":0,\"notes\":\"Monthly bulk replenishment order\",\"created_by\":null,\"updated_at\":\"2026-10-18 22:17:02\",\"created_at\":\"2026-10-18 22:17:02\",\"id\":1}', '127.0.0.1', 'Symfony', '2026-10-18 22:17:02');
INSERT INTO `audit_logs` VALUES (16, NULL, 'admin', 'App\\Models\\PurchaseOrder', 1, 'updated', '{\"subtotal\":\"0.00\",\"discount_amount\":\"0.00\",\"taxable_amount\":\"0.00\",\"total_amount\":\"0.00\"}', '{\"subtotal\":\"6000.00\",\"discount_amount\":\"300.00\",\"taxable_amount\":\"5700.00\",\"total_amount\":\"5700.00\"}', '127.0.0.1', 'Symfony', '2026-10-18 22:17:02');
INSERT INTO `audit_logs` VALUES (17, NULL, 'admin', 'App\\Models\\PurchaseOrder', 2, 'created', NULL, '{\"po_number\":\"PO-8182-00002\",\"fiscal_year_id\":1,\"supplier_id\":1,\"warehouse_id\":1,\"order_date_ad\":\"2026-10-18 00:00:00\",\"order_date_bs\":\"2083-07-01\",\"expected_delivery_date_ad\":null,\"expected_delivery_date_bs\":null,\"status\":\"draft\",\"subtotal\":0,\"discount_amount\":0,\"taxable_amount\":0,\"tax_amount\":0,\"total_amount\":0,\"notes\":\"Monthly bulk replenishment order\",\"created_by\":1,\"updated_at\":\"2026-10-18 22:17:53\",\"created_at\":\"2026-10-18 22:17:53\",\"id\":2}', '127.0.0.1', 'Symfony', '2026-10-18 22:17:53');
INSERT INTO `audit_logs` VALUES (18, NULL, 'admin', 'App\\Models\\PurchaseOrder', 2, 'updated', '{\"subtotal\":\"0.00\",\"discount_amount\":\"0.00\",\"taxable_amount\":\"0.00\",\"total_amount\":\"0.00\"}', '{\"subtotal\":\"6000.00\",\"discount_amount\":\"300.00\",\"taxable_amount\":\"5700.00\",\"total_amount\":\"5700.00\"}', '127.0.0.1', 'Symfony', '2026-10-18 22:17:53');
INSERT INTO `audit_logs` VALUES (19, NULL, 'admin', 'App\\Models\\PurchaseOrder', 2, 'updated', '{\"status\":\"draft\",\"approved_by\":null,\"approved_at\":null}', '{\"status\":\"approved\",\"approved_by\":1,\"approved_at\":\"2026-10-18 22:17:53\"}', '127.0.0.1', 'Symfony', '2026-10-18 22:17:53');
INSERT INTO `audit_logs` VALUES (20, NULL, 'admin', 'App\\Models\\PurchaseOrder', 2, 'po_approved', NULL, '{\"status\":\"approved\"}', '127.0.0.1', 'Symfony', '2026-10-18 22:17:53');
INSERT INTO `audit_logs` VALUES (21, NULL, 'admin', 'App\\Models\\GoodsReceipt', 1, 'created', NULL, '{\"grn_number\":\"GRN-8182-00001\",\"fiscal_year_id\":1,\"purchase_order_id\":2,\"supplier_id\":1,\"warehouse_id\":1,\"supplier_invoice_number\":\"DJ-INV-2026-089\",\"supplier_invoice_date_ad\":\"2026-10-18 00:00:00\",\"supplier_invoice_date_bs\":\"2083-07-01\",\"receipt_date_ad\":\"2026-10-18 00:00:00\",\"receipt_date_bs\":\"2083-07-01\",\"freight_amount\":\"300\",\"handling_charges\":\"50\",\"other_charges\":\"0.00\",\"subtotal\":0,\"discount_amount\":0,\"taxable_amount\":0,\"tax_amount\":0,\"total_amount\":0,\"total_landed_cost\":0,\"status\":\"draft\",\"created_by\":null,\"notes\":\"Received in good condition via courier delivery\",\"updated_at\":\"2026-10-18 22:17:53\",\"created_at\":\"2026-10-18 22:17:53\",\"id\":1}', '127.0.0.1', 'Symfony', '2026-10-18 22:17:53');
INSERT INTO `audit_logs` VALUES (22, NULL, 'admin', 'App\\Models\\GoodsReceipt', 1, 'updated', '{\"subtotal\":\"0.00\",\"discount_amount\":\"0.00\",\"taxable_amount\":\"0.00\",\"total_amount\":\"0.00\",\"total_landed_cost\":\"0.00\"}', '{\"subtotal\":\"6000.00\",\"discount_amount\":\"300.00\",\"taxable_amount\":\"5700.00\",\"total_amount\":\"5700.00\",\"total_landed_cost\":\"6050.00\"}', '127.0.0.1', 'Symfony', '2026-10-18 22:17:53');
INSERT INTO `audit_logs` VALUES (23, NULL, 'admin', 'App\\Models\\Batch', 6, 'created', NULL, '{\"product_id\":1,\"batch_number\":\"NK-2026-GRN1\",\"goods_receipt_id\":1,\"manufacturer_id\":1,\"supplier_id\":1,\"mfg_date_ad\":\"2026-09-18 00:00:00\",\"mfg_date_bs\":\"2083-06-02\",\"expiry_date_ad\":\"2028-10-18 00:00:00\",\"expiry_date_bs\":\"2085-07-02\",\"cost_price\":\"1.2065\",\"mrp\":\"2.0000\",\"updated_at\":\"2026-10-18 22:17:53\",\"created_at\":\"2026-10-18 22:17:53\",\"id\":6}', '127.0.0.1', 'Symfony', '2026-10-18 22:17:53');
INSERT INTO `audit_logs` VALUES (24, NULL, 'admin', 'App\\Models\\StockMovement', 4, 'stock_movement', '{\"on_hand_before\":0}', '{\"on_hand_after\":5000,\"movement_type\":\"PURCHASE_RECEIPT\",\"qty\":5000}', '127.0.0.1', 'Symfony', '2026-10-18 22:17:53');
INSERT INTO `audit_logs` VALUES (25, NULL, 'admin', 'App\\Models\\PurchaseOrder', 2, 'updated', '{\"status\":\"approved\"}', '{\"status\":\"received\"}', '127.0.0.1', 'Symfony', '2026-10-18 22:17:53');
INSERT INTO `audit_logs` VALUES (26, NULL, 'admin', 'App\\Models\\Supplier', 1, 'updated', '{\"current_balance\":\"0.00\",\"updated_at\":\"2026-10-18T16:32:02.000000Z\"}', '{\"current_balance\":\"5700.00\",\"updated_at\":\"2026-10-18 22:17:53\"}', '127.0.0.1', 'Symfony', '2026-10-18 22:17:53');
INSERT INTO `audit_logs` VALUES (27, NULL, 'admin', 'App\\Models\\GoodsReceipt', 1, 'updated', '{\"status\":\"draft\",\"posted_by\":null,\"posted_at\":null}', '{\"status\":\"posted\",\"posted_by\":1,\"posted_at\":\"2026-10-18 22:17:53\"}', '127.0.0.1', 'Symfony', '2026-10-18 22:17:53');
INSERT INTO `audit_logs` VALUES (28, NULL, 'admin', 'App\\Models\\GoodsReceipt', 1, 'grn_posted', NULL, '{\"status\":\"posted\"}', '127.0.0.1', 'Symfony', '2026-10-18 22:17:53');
INSERT INTO `audit_logs` VALUES (29, NULL, 'admin', 'App\\Models\\Customer', 1, 'created', NULL, '{\"code\":\"CUST-ALKA-PHARM\",\"name\":\"Alka Hospital Pharmacy & Diagnostic Center\",\"contact_person\":\"Sunil Manandhar\",\"phone\":\"+977-1-5551234\",\"email\":\"pharmacy@alkahospital.com\",\"pan_vat_number\":\"301223344\",\"dda_license_number\":\"DDA-RET-0912\\/078\",\"address\":\"Jawalakhel, Lalitpur\",\"credit_period_days\":30,\"credit_limit\":500000,\"credit_stop\":false,\"opening_balance\":0,\"current_balance\":0,\"is_active\":true,\"updated_at\":\"2026-10-18 22:32:24\",\"created_at\":\"2026-10-18 22:32:24\",\"id\":1}', '127.0.0.1', 'Symfony', '2026-10-18 22:32:24');
INSERT INTO `audit_logs` VALUES (30, NULL, 'admin', 'App\\Models\\Customer', 2, 'created', NULL, '{\"code\":\"CUST-TUTH-PHARM\",\"name\":\"Tribhuvan University Teaching Hospital Pharmacy\",\"contact_person\":\"Dr. Pravin Sharma\",\"phone\":\"+977-1-4412345\",\"email\":\"tuthpharm@iom.edu.np\",\"pan_vat_number\":\"300112233\",\"dda_license_number\":\"DDA-RET-0415\\/074\",\"address\":\"Maharajgunj, Kathmandu\",\"credit_period_days\":45,\"credit_limit\":1000000,\"credit_stop\":false,\"opening_balance\":0,\"current_balance\":0,\"is_active\":true,\"updated_at\":\"2026-10-18 22:32:24\",\"created_at\":\"2026-10-18 22:32:24\",\"id\":2}', '127.0.0.1', 'Symfony', '2026-10-18 22:32:24');
INSERT INTO `audit_logs` VALUES (31, NULL, 'admin', 'App\\Models\\Customer', 3, 'created', NULL, '{\"code\":\"CUST-NORVIC-PHARM\",\"name\":\"Norvic International Hospital Pharmacy\",\"contact_person\":\"Rita Maharjan\",\"phone\":\"+977-1-4258555\",\"email\":\"norvicpharm@norvichospital.com\",\"pan_vat_number\":\"302334455\",\"dda_license_number\":\"DDA-RET-0881\\/077\",\"address\":\"Thapathali, Kathmandu\",\"credit_period_days\":30,\"credit_limit\":800000,\"credit_stop\":false,\"opening_balance\":0,\"current_balance\":0,\"is_active\":true,\"updated_at\":\"2026-10-18 22:32:24\",\"created_at\":\"2026-10-18 22:32:24\",\"id\":3}', '127.0.0.1', 'Symfony', '2026-10-18 22:32:24');
INSERT INTO `audit_logs` VALUES (32, NULL, 'admin', 'App\\Models\\Customer', 4, 'created', NULL, '{\"code\":\"CUST-MEDPLUS-CLINIC\",\"name\":\"MedPlus Polyclinic & Retail Chemist\",\"contact_person\":\"Kiran Bajracharya\",\"phone\":\"+977-1-5534433\",\"email\":\"medplusnepal@gmail.com\",\"pan_vat_number\":\"601998877\",\"dda_license_number\":\"DDA-RET-1520\\/079\",\"address\":\"Kumaripati, Lalitpur\",\"credit_period_days\":15,\"credit_limit\":200000,\"credit_stop\":false,\"opening_balance\":0,\"current_balance\":0,\"is_active\":true,\"updated_at\":\"2026-10-18 22:32:24\",\"created_at\":\"2026-10-18 22:32:24\",\"id\":4}', '127.0.0.1', 'Symfony', '2026-10-18 22:32:24');
INSERT INTO `audit_logs` VALUES (33, NULL, 'admin', 'App\\Models\\Customer', 5, 'created', NULL, '{\"code\":\"CUST-BHAKTAPUR-CARE\",\"name\":\"Bhaktapur Care & Community Pharmacy\",\"contact_person\":\"Raju Prajapati\",\"phone\":\"+977-1-6612211\",\"email\":\"bhaktapurcare@gmail.com\",\"pan_vat_number\":\"303445566\",\"dda_license_number\":\"DDA-RET-0723\\/076\",\"address\":\"Sallaghari, Bhaktapur\",\"credit_period_days\":30,\"credit_limit\":400000,\"credit_stop\":false,\"opening_balance\":0,\"current_balance\":0,\"is_active\":true,\"updated_at\":\"2026-10-18 22:32:24\",\"created_at\":\"2026-10-18 22:32:24\",\"id\":5}', '127.0.0.1', 'Symfony', '2026-10-18 22:32:24');
INSERT INTO `audit_logs` VALUES (34, NULL, 'admin', 'App\\Models\\SupplierPayment', 1, 'created', NULL, '{\"payment_number\":\"SPMT-8182-00001\",\"fiscal_year_id\":1,\"supplier_id\":1,\"cash_bank_account_id\":2,\"payment_date_ad\":\"2026-10-18 00:00:00\",\"payment_date_bs\":\"2083-07-01\",\"payment_method\":\"bank_transfer\",\"amount\":\"1000\",\"allocated_amount\":\"1000.00\",\"unallocated_amount\":\"0.00\",\"cheque_number\":null,\"cheque_date\":null,\"bank_name\":null,\"transaction_reference\":\"NABIL-VOUCHER-0012\",\"status\":\"draft\",\"created_by\":1,\"notes\":\"Pharma Seeded Demo Payment\",\"updated_at\":\"2026-10-18 22:40:32\",\"created_at\":\"2026-10-18 22:40:32\",\"id\":1}', '127.0.0.1', 'Symfony', '2026-10-18 22:40:32');
INSERT INTO `audit_logs` VALUES (35, NULL, 'admin', 'App\\Models\\Supplier', 1, 'updated', '{\"current_balance\":\"5700.00\",\"updated_at\":\"2026-10-18T16:32:53.000000Z\"}', '{\"current_balance\":\"4700.00\",\"updated_at\":\"2026-10-18 22:40:32\"}', '127.0.0.1', 'Symfony', '2026-10-18 22:40:32');
INSERT INTO `audit_logs` VALUES (36, NULL, 'admin', 'App\\Models\\SupplierPayment', 1, 'updated', '{\"status\":\"draft\",\"posted_by\":null,\"posted_at\":null}', '{\"status\":\"posted\",\"posted_by\":1,\"posted_at\":\"2026-10-18 22:40:32\"}', '127.0.0.1', 'Symfony', '2026-10-18 22:40:32');
INSERT INTO `audit_logs` VALUES (37, NULL, 'admin', 'App\\Models\\SupplierPayment', 1, 'supplier_payment_posted', NULL, '{\"status\":\"posted\"}', '127.0.0.1', 'Symfony', '2026-10-18 22:40:32');
INSERT INTO `audit_logs` VALUES (38, 2, 'admin', 'App\\Models\\Customer', 6, 'created', NULL, '{\"name\":\"Shila Pharmacy\",\"code\":\"CUST-DC295D\",\"contact_person\":null,\"phone\":null,\"email\":null,\"pan_vat_number\":null,\"dda_license_number\":null,\"address\":null,\"credit_period_days\":\"15\",\"credit_limit\":\"50000.00\",\"opening_balance\":\"0.00\",\"credit_stop\":false,\"is_active\":true,\"current_balance\":\"0.00\",\"updated_at\":\"2026-10-28 20:36:57\",\"created_at\":\"2026-10-28 20:36:57\",\"id\":6}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-28 20:36:57');
INSERT INTO `audit_logs` VALUES (39, 2, 'admin', 'App\\Models\\Customer', 6, 'updated', '{\"code\":\"CUST-DC295D\",\"updated_at\":\"2026-10-28T14:51:57.000000Z\"}', '{\"code\":\"CUST-Shila\",\"updated_at\":\"2026-10-28 20:37:19\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-28 20:37:19');
INSERT INTO `audit_logs` VALUES (40, 2, 'admin', 'App\\Models\\Customer', 6, 'updated', '{\"code\":\"CUST-Shila\",\"updated_at\":\"2026-10-28T14:52:19.000000Z\"}', '{\"code\":\"CUST-SHILA\",\"updated_at\":\"2026-10-28 20:37:33\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-28 20:37:33');
INSERT INTO `audit_logs` VALUES (41, 2, 'admin', 'App\\Models\\Customer', 6, 'updated', '{\"credit_stop\":false,\"updated_at\":\"2026-10-28T14:52:33.000000Z\"}', '{\"credit_stop\":true,\"updated_at\":\"2026-10-28 20:37:41\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-28 20:37:42');
INSERT INTO `audit_logs` VALUES (42, 2, 'admin', 'App\\Models\\Customer', 6, 'updated', '{\"credit_stop\":true,\"updated_at\":\"2026-10-28T14:52:41.000000Z\"}', '{\"credit_stop\":false,\"updated_at\":\"2026-10-28 20:37:52\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-28 20:37:52');

-- ----------------------------
-- Table structure for batch_recalls
-- ----------------------------
DROP TABLE IF EXISTS `batch_recalls`;
CREATE TABLE `batch_recalls`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `batch_id` bigint UNSIGNED NOT NULL,
  `recall_reference` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `issued_by` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `recall_date_ad` date NOT NULL,
  `recall_date_bs` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `reason` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `action_taken` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL,
  `quarantined_quantity` int UNSIGNED NOT NULL DEFAULT 0,
  `status` enum('active','resolved') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'active',
  `created_by` bigint UNSIGNED NULL DEFAULT NULL,
  `resolved_by` bigint UNSIGNED NULL DEFAULT NULL,
  `resolved_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `batch_recalls_recall_reference_unique`(`recall_reference` ASC) USING BTREE,
  INDEX `batch_recalls_batch_id_foreign`(`batch_id` ASC) USING BTREE,
  INDEX `batch_recalls_created_by_foreign`(`created_by` ASC) USING BTREE,
  INDEX `batch_recalls_resolved_by_foreign`(`resolved_by` ASC) USING BTREE,
  INDEX `batch_recalls_recall_date_ad_index`(`recall_date_ad` ASC) USING BTREE,
  INDEX `batch_recalls_recall_date_bs_index`(`recall_date_bs` ASC) USING BTREE,
  INDEX `batch_recalls_status_index`(`status` ASC) USING BTREE,
  CONSTRAINT `batch_recalls_batch_id_foreign` FOREIGN KEY (`batch_id`) REFERENCES `batches` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `batch_recalls_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `admins` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT,
  CONSTRAINT `batch_recalls_resolved_by_foreign` FOREIGN KEY (`resolved_by`) REFERENCES `admins` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of batch_recalls
-- ----------------------------

-- ----------------------------
-- Table structure for batches
-- ----------------------------
DROP TABLE IF EXISTS `batches`;
CREATE TABLE `batches`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `product_id` bigint UNSIGNED NOT NULL,
  `batch_number` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `manufacturer_id` bigint UNSIGNED NULL DEFAULT NULL,
  `supplier_id` bigint UNSIGNED NULL DEFAULT NULL,
  `goods_receipt_id` bigint UNSIGNED NULL DEFAULT NULL,
  `mfg_date_ad` date NULL DEFAULT NULL,
  `mfg_date_bs` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `expiry_date_ad` date NOT NULL,
  `expiry_date_bs` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `mrp` decimal(15, 4) NOT NULL DEFAULT 0.0000,
  `selling_price` decimal(15, 4) NOT NULL DEFAULT 0.0000,
  `wholesale_price` decimal(15, 4) NOT NULL DEFAULT 0.0000,
  `cost_price` decimal(15, 4) NOT NULL DEFAULT 0.0000,
  `is_quarantined` tinyint(1) NOT NULL DEFAULT 0,
  `is_recalled` tinyint(1) NOT NULL DEFAULT 0,
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `batches_manufacturer_id_foreign`(`manufacturer_id` ASC) USING BTREE,
  INDEX `batches_product_id_batch_number_index`(`product_id` ASC, `batch_number` ASC) USING BTREE,
  INDEX `batches_product_id_expiry_date_ad_index`(`product_id` ASC, `expiry_date_ad` ASC) USING BTREE,
  INDEX `batches_batch_number_index`(`batch_number` ASC) USING BTREE,
  INDEX `batches_supplier_id_index`(`supplier_id` ASC) USING BTREE,
  INDEX `batches_goods_receipt_id_index`(`goods_receipt_id` ASC) USING BTREE,
  INDEX `batches_expiry_date_ad_index`(`expiry_date_ad` ASC) USING BTREE,
  INDEX `batches_is_quarantined_index`(`is_quarantined` ASC) USING BTREE,
  INDEX `batches_is_recalled_index`(`is_recalled` ASC) USING BTREE,
  CONSTRAINT `batches_manufacturer_id_foreign` FOREIGN KEY (`manufacturer_id`) REFERENCES `manufacturers` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT,
  CONSTRAINT `batches_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 7 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of batches
-- ----------------------------
INSERT INTO `batches` VALUES (1, 1, 'NK-2024-01', 1, NULL, NULL, '2024-01-01', '2080-09-16', '2027-01-18', '2083-10-04', 2.0000, 0.0000, 0.0000, 1.2000, 0, 0, NULL, '2026-10-18 22:06:40', '2026-10-18 22:06:40', NULL);
INSERT INTO `batches` VALUES (2, 1, 'NK-2024-02', 1, NULL, NULL, '2024-06-01', '2081-02-19', '2028-04-18', '2085-01-06', 2.0000, 0.0000, 0.0000, 1.2500, 0, 0, NULL, '2026-10-18 22:06:40', '2026-10-18 22:06:40', NULL);
INSERT INTO `batches` VALUES (3, 1, 'NK-2023-EX', 1, NULL, NULL, '2023-01-01', '2079-09-17', '2026-09-18', '2083-06-02', 2.0000, 0.0000, 0.0000, 1.1000, 0, 0, NULL, '2026-10-18 22:06:40', '2026-10-18 22:06:40', NULL);
INSERT INTO `batches` VALUES (4, 1, 'NK-2024-QR', 1, NULL, NULL, '2024-05-01', '2081-01-19', '2027-10-18', '2084-07-02', 2.0000, 0.0000, 0.0000, 1.2000, 1, 0, 'Quality seal check pending', '2026-10-18 22:06:40', '2026-10-18 22:06:40', NULL);
INSERT INTO `batches` VALUES (5, 3, 'INS-2024-09', 2, NULL, NULL, '2024-09-01', '2081-05-16', '2027-10-18', '2084-07-02', 450.0000, 0.0000, 0.0000, 320.0000, 0, 0, NULL, '2026-10-18 22:06:40', '2026-10-18 22:06:40', NULL);
INSERT INTO `batches` VALUES (6, 1, 'NK-2026-GRN1', 1, 1, 1, '2026-09-18', '2083-06-02', '2028-10-18', '2085-07-02', 2.0000, 0.0000, 0.0000, 1.2065, 0, 0, NULL, '2026-10-18 22:17:53', '2026-10-18 22:17:53', NULL);

-- ----------------------------
-- Table structure for cache
-- ----------------------------
DROP TABLE IF EXISTS `cache`;
CREATE TABLE `cache`  (
  `key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `value` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of cache
-- ----------------------------

-- ----------------------------
-- Table structure for cache_locks
-- ----------------------------
DROP TABLE IF EXISTS `cache_locks`;
CREATE TABLE `cache_locks`  (
  `key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `owner` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of cache_locks
-- ----------------------------

-- ----------------------------
-- Table structure for cash_bank_accounts
-- ----------------------------
DROP TABLE IF EXISTS `cash_bank_accounts`;
CREATE TABLE `cash_bank_accounts`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `code` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `name` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `type` enum('cash','bank','other') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'cash',
  `account_number` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `bank_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `branch` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `opening_balance` decimal(15, 2) NOT NULL DEFAULT 0.00,
  `current_balance` decimal(15, 2) NOT NULL DEFAULT 0.00,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `cash_bank_accounts_code_unique`(`code` ASC) USING BTREE,
  INDEX `cash_bank_accounts_is_active_index`(`is_active` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of cash_bank_accounts
-- ----------------------------
INSERT INTO `cash_bank_accounts` VALUES (1, 'ACC-CASH-MAIN', 'Main Cash Drawer', 'cash', NULL, NULL, NULL, 100000.00, 100000.00, 1, '2026-10-18 21:44:41', '2026-10-18 22:40:32', NULL);
INSERT INTO `cash_bank_accounts` VALUES (2, 'ACC-BANK-NABIL', 'Nabil Bank Operating Account', 'bank', '01201017500123', 'Nabil Bank Ltd.', 'Tripureshwor', 500000.00, 499000.00, 1, '2026-10-18 21:44:41', '2026-10-18 22:40:32', NULL);

-- ----------------------------
-- Table structure for cash_bank_transactions
-- ----------------------------
DROP TABLE IF EXISTS `cash_bank_transactions`;
CREATE TABLE `cash_bank_transactions`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `fiscal_year_id` bigint UNSIGNED NOT NULL,
  `cash_bank_account_id` bigint UNSIGNED NOT NULL,
  `transaction_date_ad` date NOT NULL,
  `transaction_date_bs` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `transaction_type` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `reference_type` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `reference_id` bigint UNSIGNED NULL DEFAULT NULL,
  `reference_number` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `deposit_amount` decimal(15, 2) NOT NULL DEFAULT 0.00,
  `withdrawal_amount` decimal(15, 2) NOT NULL DEFAULT 0.00,
  `running_balance` decimal(15, 2) NOT NULL,
  `payment_method` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `cheque_number` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `cheque_date` date NULL DEFAULT NULL,
  `bank_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `status` enum('cleared','pending','bounced','void') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'cleared',
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL,
  `created_by` bigint UNSIGNED NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `cash_bank_transactions_fiscal_year_id_foreign`(`fiscal_year_id` ASC) USING BTREE,
  INDEX `cash_bank_transactions_cash_bank_account_id_foreign`(`cash_bank_account_id` ASC) USING BTREE,
  INDEX `cash_bank_transactions_created_by_foreign`(`created_by` ASC) USING BTREE,
  INDEX `cash_bank_transactions_transaction_date_ad_index`(`transaction_date_ad` ASC) USING BTREE,
  INDEX `cash_bank_transactions_transaction_date_bs_index`(`transaction_date_bs` ASC) USING BTREE,
  INDEX `cash_bank_transactions_transaction_type_index`(`transaction_type` ASC) USING BTREE,
  INDEX `cash_bank_transactions_reference_type_index`(`reference_type` ASC) USING BTREE,
  INDEX `cash_bank_transactions_reference_id_index`(`reference_id` ASC) USING BTREE,
  CONSTRAINT `cash_bank_transactions_cash_bank_account_id_foreign` FOREIGN KEY (`cash_bank_account_id`) REFERENCES `cash_bank_accounts` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `cash_bank_transactions_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `admins` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT,
  CONSTRAINT `cash_bank_transactions_fiscal_year_id_foreign` FOREIGN KEY (`fiscal_year_id`) REFERENCES `fiscal_years` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of cash_bank_transactions
-- ----------------------------
INSERT INTO `cash_bank_transactions` VALUES (1, 1, 2, '2026-10-18', '2083-07-01', 'SUPPLIER_PAYMENT', 'SupplierPayment', 1, 'SPMT-8182-00001', 0.00, 1000.00, 499000.00, 'bank_transfer', NULL, NULL, NULL, 'cleared', 'Supplier Payment #SPMT-8182-00001 to Deurali-Janta Distribution Division', NULL, '2026-10-18 22:40:32');

-- ----------------------------
-- Table structure for categories
-- ----------------------------
DROP TABLE IF EXISTS `categories`;
CREATE TABLE `categories`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `parent_id` bigint UNSIGNED NULL DEFAULT NULL,
  `code` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `name` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `categories_code_unique`(`code` ASC) USING BTREE,
  INDEX `categories_parent_id_foreign`(`parent_id` ASC) USING BTREE,
  INDEX `categories_is_active_index`(`is_active` ASC) USING BTREE,
  CONSTRAINT `categories_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 5 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of categories
-- ----------------------------
INSERT INTO `categories` VALUES (1, NULL, 'CAT-ANALGESIC', 'Analgesics & Antipyretics', NULL, 1, '2026-10-18 22:06:40', '2026-10-18 22:06:40', NULL);
INSERT INTO `categories` VALUES (2, NULL, 'CAT-ANTIBIOTIC', 'Antibiotics & Antimicrobials', NULL, 1, '2026-10-18 22:06:40', '2026-10-18 22:06:40', NULL);
INSERT INTO `categories` VALUES (3, NULL, 'CAT-GASTRO', 'Gastrointestinal Agents', NULL, 1, '2026-10-18 22:06:40', '2026-10-18 22:06:40', NULL);
INSERT INTO `categories` VALUES (4, NULL, 'CAT-ENDOCRINE', 'Endocrine & Diabetes Care', NULL, 1, '2026-10-18 22:06:40', '2026-10-18 22:06:40', NULL);

-- ----------------------------
-- Table structure for certificate_renew_requests
-- ----------------------------
DROP TABLE IF EXISTS `certificate_renew_requests`;
CREATE TABLE `certificate_renew_requests`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `professional_id` bigint UNSIGNED NULL DEFAULT NULL,
  `certificate_id` bigint UNSIGNED NULL DEFAULT NULL,
  `approved_by` bigint UNSIGNED NULL DEFAULT NULL,
  `level_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `program_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `cert_registration_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `voucher_image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0 = inactive, 1 = active',
  `transaction_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `current_working_institute` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `approved_at` datetime NULL DEFAULT NULL,
  `renewed_certificate_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `state` enum('operator','registrar','student') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'operator',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `certificate_renew_requests_transaction_id_unique`(`transaction_id` ASC) USING BTREE,
  INDEX `certificate_renew_requests_professional_id_foreign`(`professional_id` ASC) USING BTREE,
  INDEX `certificate_renew_requests_certificate_id_foreign`(`certificate_id` ASC) USING BTREE,
  INDEX `certificate_renew_requests_approved_by_foreign`(`approved_by` ASC) USING BTREE,
  CONSTRAINT `certificate_renew_requests_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `admins` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `certificate_renew_requests_certificate_id_foreign` FOREIGN KEY (`certificate_id`) REFERENCES `certificates` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `certificate_renew_requests_professional_id_foreign` FOREIGN KEY (`professional_id`) REFERENCES `professionals` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of certificate_renew_requests
-- ----------------------------

-- ----------------------------
-- Table structure for certificates
-- ----------------------------
DROP TABLE IF EXISTS `certificates`;
CREATE TABLE `certificates`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `professional_id` bigint UNSIGNED NULL DEFAULT NULL,
  `printed_by` bigint UNSIGNED NULL DEFAULT NULL,
  `issued_by` bigint UNSIGNED NULL DEFAULT NULL,
  `level_id` bigint UNSIGNED NULL DEFAULT NULL,
  `program_id` bigint UNSIGNED NULL DEFAULT NULL,
  `registration_id` bigint UNSIGNED NULL DEFAULT NULL,
  `srn` bigint UNSIGNED NOT NULL,
  `program_certificate_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `cert_registration_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `registrar` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `date_of_birth` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'zone, district, vdc, ward_no',
  `qualification` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `decision_date` date NOT NULL,
  `issued_year` int NULL DEFAULT NULL,
  `issued_date` date NULL DEFAULT NULL,
  `valid_till` date NULL DEFAULT NULL,
  `certificate` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'new',
  `type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `remarks` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `is_printed` tinyint(1) NOT NULL DEFAULT 0,
  `printed_date` date NULL DEFAULT NULL,
  `is_edited` tinyint(1) NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `certificates_professional_id_foreign`(`professional_id` ASC) USING BTREE,
  INDEX `certificates_level_id_foreign`(`level_id` ASC) USING BTREE,
  INDEX `certificates_program_id_foreign`(`program_id` ASC) USING BTREE,
  CONSTRAINT `certificates_level_id_foreign` FOREIGN KEY (`level_id`) REFERENCES `levels` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `certificates_professional_id_foreign` FOREIGN KEY (`professional_id`) REFERENCES `professionals` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `certificates_program_id_foreign` FOREIGN KEY (`program_id`) REFERENCES `programs` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of certificates
-- ----------------------------

-- ----------------------------
-- Table structure for colleges
-- ----------------------------
DROP TABLE IF EXISTS `colleges`;
CREATE TABLE `colleges`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '0 = hide, 1 = show',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of colleges
-- ----------------------------

-- ----------------------------
-- Table structure for customer_ledger_entries
-- ----------------------------
DROP TABLE IF EXISTS `customer_ledger_entries`;
CREATE TABLE `customer_ledger_entries`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `fiscal_year_id` bigint UNSIGNED NOT NULL,
  `customer_id` bigint UNSIGNED NOT NULL,
  `entry_date_ad` date NOT NULL,
  `entry_date_bs` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `transaction_type` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `reference_type` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `reference_id` bigint UNSIGNED NOT NULL,
  `reference_number` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `debit_amount` decimal(15, 2) NOT NULL DEFAULT 0.00,
  `credit_amount` decimal(15, 2) NOT NULL DEFAULT 0.00,
  `running_balance` decimal(15, 2) NOT NULL DEFAULT 0.00,
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL,
  `created_by` bigint UNSIGNED NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `customer_ledger_entries_fiscal_year_id_foreign`(`fiscal_year_id` ASC) USING BTREE,
  INDEX `customer_ledger_entries_created_by_foreign`(`created_by` ASC) USING BTREE,
  INDEX `cle_cust_created_idx`(`customer_id` ASC, `created_at` ASC) USING BTREE,
  INDEX `customer_ledger_entries_entry_date_ad_index`(`entry_date_ad` ASC) USING BTREE,
  INDEX `customer_ledger_entries_transaction_type_index`(`transaction_type` ASC) USING BTREE,
  INDEX `customer_ledger_entries_reference_type_index`(`reference_type` ASC) USING BTREE,
  INDEX `customer_ledger_entries_reference_id_index`(`reference_id` ASC) USING BTREE,
  INDEX `customer_ledger_entries_reference_number_index`(`reference_number` ASC) USING BTREE,
  INDEX `customer_ledger_entries_created_at_index`(`created_at` ASC) USING BTREE,
  CONSTRAINT `customer_ledger_entries_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `admins` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT,
  CONSTRAINT `customer_ledger_entries_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `customer_ledger_entries_fiscal_year_id_foreign` FOREIGN KEY (`fiscal_year_id`) REFERENCES `fiscal_years` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of customer_ledger_entries
-- ----------------------------

-- ----------------------------
-- Table structure for customer_receipt_allocations
-- ----------------------------
DROP TABLE IF EXISTS `customer_receipt_allocations`;
CREATE TABLE `customer_receipt_allocations`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `customer_receipt_id` bigint UNSIGNED NOT NULL,
  `sale_invoice_id` bigint UNSIGNED NOT NULL,
  `allocated_amount` decimal(15, 2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `customer_receipt_allocations_customer_receipt_id_foreign`(`customer_receipt_id` ASC) USING BTREE,
  INDEX `customer_receipt_allocations_sale_invoice_id_foreign`(`sale_invoice_id` ASC) USING BTREE,
  CONSTRAINT `customer_receipt_allocations_customer_receipt_id_foreign` FOREIGN KEY (`customer_receipt_id`) REFERENCES `customer_receipts` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `customer_receipt_allocations_sale_invoice_id_foreign` FOREIGN KEY (`sale_invoice_id`) REFERENCES `sale_invoices` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of customer_receipt_allocations
-- ----------------------------

-- ----------------------------
-- Table structure for customer_receipts
-- ----------------------------
DROP TABLE IF EXISTS `customer_receipts`;
CREATE TABLE `customer_receipts`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `receipt_number` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `fiscal_year_id` bigint UNSIGNED NOT NULL,
  `customer_id` bigint UNSIGNED NOT NULL,
  `cash_bank_account_id` bigint UNSIGNED NOT NULL,
  `receipt_date_ad` date NOT NULL,
  `receipt_date_bs` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `payment_method` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'cash',
  `amount` decimal(15, 2) NOT NULL,
  `allocated_amount` decimal(15, 2) NOT NULL DEFAULT 0.00,
  `unallocated_amount` decimal(15, 2) NOT NULL DEFAULT 0.00,
  `cheque_number` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `cheque_date` date NULL DEFAULT NULL,
  `bank_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `transaction_reference` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `status` enum('draft','posted','reversed') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'draft',
  `created_by` bigint UNSIGNED NULL DEFAULT NULL,
  `posted_by` bigint UNSIGNED NULL DEFAULT NULL,
  `posted_at` timestamp NULL DEFAULT NULL,
  `reversed_by` bigint UNSIGNED NULL DEFAULT NULL,
  `reversed_at` timestamp NULL DEFAULT NULL,
  `reversal_reason` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL,
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `customer_receipts_receipt_number_unique`(`receipt_number` ASC) USING BTREE,
  INDEX `customer_receipts_fiscal_year_id_foreign`(`fiscal_year_id` ASC) USING BTREE,
  INDEX `customer_receipts_customer_id_foreign`(`customer_id` ASC) USING BTREE,
  INDEX `customer_receipts_cash_bank_account_id_foreign`(`cash_bank_account_id` ASC) USING BTREE,
  INDEX `customer_receipts_created_by_foreign`(`created_by` ASC) USING BTREE,
  INDEX `customer_receipts_posted_by_foreign`(`posted_by` ASC) USING BTREE,
  INDEX `customer_receipts_reversed_by_foreign`(`reversed_by` ASC) USING BTREE,
  INDEX `customer_receipts_receipt_date_ad_index`(`receipt_date_ad` ASC) USING BTREE,
  INDEX `customer_receipts_receipt_date_bs_index`(`receipt_date_bs` ASC) USING BTREE,
  INDEX `customer_receipts_status_index`(`status` ASC) USING BTREE,
  CONSTRAINT `customer_receipts_cash_bank_account_id_foreign` FOREIGN KEY (`cash_bank_account_id`) REFERENCES `cash_bank_accounts` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `customer_receipts_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `admins` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT,
  CONSTRAINT `customer_receipts_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `customer_receipts_fiscal_year_id_foreign` FOREIGN KEY (`fiscal_year_id`) REFERENCES `fiscal_years` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `customer_receipts_posted_by_foreign` FOREIGN KEY (`posted_by`) REFERENCES `admins` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT,
  CONSTRAINT `customer_receipts_reversed_by_foreign` FOREIGN KEY (`reversed_by`) REFERENCES `admins` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of customer_receipts
-- ----------------------------

-- ----------------------------
-- Table structure for customers
-- ----------------------------
DROP TABLE IF EXISTS `customers`;
CREATE TABLE `customers`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `code` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `contact_person` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `phone` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `pan_vat_number` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `dda_license_number` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `address` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL,
  `credit_period_days` int UNSIGNED NOT NULL DEFAULT 30,
  `credit_limit` decimal(15, 2) NOT NULL DEFAULT 0.00,
  `credit_stop` tinyint(1) NOT NULL DEFAULT 0,
  `opening_balance` decimal(15, 2) NOT NULL DEFAULT 0.00,
  `current_balance` decimal(15, 2) NOT NULL DEFAULT 0.00,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `customers_code_unique`(`code` ASC) USING BTREE,
  INDEX `customers_name_index`(`name` ASC) USING BTREE,
  INDEX `customers_pan_vat_number_index`(`pan_vat_number` ASC) USING BTREE,
  INDEX `customers_credit_stop_index`(`credit_stop` ASC) USING BTREE,
  INDEX `customers_is_active_index`(`is_active` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 7 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of customers
-- ----------------------------
INSERT INTO `customers` VALUES (1, 'CUST-ALKA-PHARM', 'Alka Hospital Pharmacy & Diagnostic Center', 'Sunil Manandhar', '+977-1-5551234', 'pharmacy@alkahospital.com', '301223344', 'DDA-RET-0912/078', 'Jawalakhel, Lalitpur', 30, 500000.00, 0, 0.00, 0.00, 1, '2026-10-18 22:32:24', '2026-10-18 22:32:24', NULL);
INSERT INTO `customers` VALUES (2, 'CUST-TUTH-PHARM', 'Tribhuvan University Teaching Hospital Pharmacy', 'Dr. Pravin Sharma', '+977-1-4412345', 'tuthpharm@iom.edu.np', '300112233', 'DDA-RET-0415/074', 'Maharajgunj, Kathmandu', 45, 1000000.00, 0, 0.00, 0.00, 1, '2026-10-18 22:32:24', '2026-10-18 22:32:24', NULL);
INSERT INTO `customers` VALUES (3, 'CUST-NORVIC-PHARM', 'Norvic International Hospital Pharmacy', 'Rita Maharjan', '+977-1-4258555', 'norvicpharm@norvichospital.com', '302334455', 'DDA-RET-0881/077', 'Thapathali, Kathmandu', 30, 800000.00, 0, 0.00, 0.00, 1, '2026-10-18 22:32:24', '2026-10-18 22:32:24', NULL);
INSERT INTO `customers` VALUES (4, 'CUST-MEDPLUS-CLINIC', 'MedPlus Polyclinic & Retail Chemist', 'Kiran Bajracharya', '+977-1-5534433', 'medplusnepal@gmail.com', '601998877', 'DDA-RET-1520/079', 'Kumaripati, Lalitpur', 15, 200000.00, 0, 0.00, 0.00, 1, '2026-10-18 22:32:24', '2026-10-18 22:32:24', NULL);
INSERT INTO `customers` VALUES (5, 'CUST-BHAKTAPUR-CARE', 'Bhaktapur Care & Community Pharmacy', 'Raju Prajapati', '+977-1-6612211', 'bhaktapurcare@gmail.com', '303445566', 'DDA-RET-0723/076', 'Sallaghari, Bhaktapur', 30, 400000.00, 0, 0.00, 0.00, 1, '2026-10-18 22:32:24', '2026-10-18 22:32:24', NULL);
INSERT INTO `customers` VALUES (6, 'CUST-SHILA', 'Shila Pharmacy', NULL, NULL, NULL, NULL, NULL, NULL, 15, 50000.00, 0, 0.00, 0.00, 1, '2026-10-28 20:36:57', '2026-10-28 20:37:52', NULL);

-- ----------------------------
-- Table structure for districts
-- ----------------------------
DROP TABLE IF EXISTS `districts`;
CREATE TABLE `districts`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `province_id` bigint UNSIGNED NULL DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '0 = hide, 1 = show',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `districts_province_id_foreign`(`province_id` ASC) USING BTREE,
  CONSTRAINT `districts_province_id_foreign` FOREIGN KEY (`province_id`) REFERENCES `provinces` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of districts
-- ----------------------------

-- ----------------------------
-- Table structure for document_sequences
-- ----------------------------
DROP TABLE IF EXISTS `document_sequences`;
CREATE TABLE `document_sequences`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `fiscal_year_id` bigint UNSIGNED NOT NULL,
  `series` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `prefix` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `suffix` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `last_number` int UNSIGNED NOT NULL DEFAULT 0,
  `pad_length` tinyint UNSIGNED NOT NULL DEFAULT 5,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `document_sequences_series_fiscal_year_id_unique`(`series` ASC, `fiscal_year_id` ASC) USING BTREE,
  INDEX `document_sequences_fiscal_year_id_foreign`(`fiscal_year_id` ASC) USING BTREE,
  CONSTRAINT `document_sequences_fiscal_year_id_foreign` FOREIGN KEY (`fiscal_year_id`) REFERENCES `fiscal_years` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 11 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of document_sequences
-- ----------------------------
INSERT INTO `document_sequences` VALUES (1, 1, 'PO', 'PO-8182-', NULL, 2, 5, '2026-10-18 21:44:40', '2026-10-18 22:17:53');
INSERT INTO `document_sequences` VALUES (2, 1, 'GRN', 'GRN-8182-', NULL, 1, 5, '2026-10-18 21:44:40', '2026-10-18 22:17:53');
INSERT INTO `document_sequences` VALUES (3, 1, 'INV', 'INV-8182-', NULL, 0, 5, '2026-10-18 21:44:40', '2026-10-18 21:44:40');
INSERT INTO `document_sequences` VALUES (4, 1, 'PINV', 'PINV-8182-', NULL, 0, 5, '2026-10-18 21:44:40', '2026-10-18 21:44:40');
INSERT INTO `document_sequences` VALUES (5, 1, 'SRTN', 'SRTN-8182-', NULL, 0, 5, '2026-10-18 21:44:40', '2026-10-18 21:44:40');
INSERT INTO `document_sequences` VALUES (6, 1, 'PRTN', 'PRTN-8182-', NULL, 0, 5, '2026-10-18 21:44:40', '2026-10-18 21:44:40');
INSERT INTO `document_sequences` VALUES (7, 1, 'ADJ', 'ADJ-8182-', NULL, 0, 5, '2026-10-18 21:44:40', '2026-10-18 21:44:40');
INSERT INTO `document_sequences` VALUES (8, 1, 'TRF', 'TRF-8182-', NULL, 0, 5, '2026-10-18 21:44:40', '2026-10-18 21:44:40');
INSERT INTO `document_sequences` VALUES (9, 1, 'CRCPT', 'CRCPT-8182-', NULL, 0, 5, '2026-10-18 21:44:40', '2026-10-18 21:44:40');
INSERT INTO `document_sequences` VALUES (10, 1, 'SPMT', 'SPMT-8182-', NULL, 1, 5, '2026-10-18 21:44:40', '2026-10-18 22:40:32');

-- ----------------------------
-- Table structure for eligibility_marks
-- ----------------------------
DROP TABLE IF EXISTS `eligibility_marks`;
CREATE TABLE `eligibility_marks`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `level_id` bigint UNSIGNED NULL DEFAULT NULL,
  `min_physics_marks` int NOT NULL DEFAULT 0,
  `min_chemistry_marks` int NOT NULL DEFAULT 0,
  `min_bio_marks` int NOT NULL DEFAULT 0,
  `min_percentage` double NOT NULL DEFAULT 0,
  `min_gpa` double NOT NULL DEFAULT 0,
  `total_days_passport` int NOT NULL DEFAULT 0,
  `total_days_internship` int NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `eligibility_marks_level_id_foreign`(`level_id` ASC) USING BTREE,
  CONSTRAINT `eligibility_marks_level_id_foreign` FOREIGN KEY (`level_id`) REFERENCES `levels` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of eligibility_marks
-- ----------------------------

-- ----------------------------
-- Table structure for exams
-- ----------------------------
DROP TABLE IF EXISTS `exams`;
CREATE TABLE `exams`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `exam_number` int NULL DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `name_nep` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `opening_date` date NULL DEFAULT NULL,
  `closing_date` date NULL DEFAULT NULL,
  `exam_fee` double NULL DEFAULT NULL,
  `late_fee_start_date` date NULL DEFAULT NULL,
  `late_fee` double NULL DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL,
  `admit_card_generated` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0 = No, 1 = Yes',
  `result_published` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0 = No, 1 = Yes',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '0 = hide, 1 = show',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of exams
-- ----------------------------

-- ----------------------------
-- Table structure for failed_jobs
-- ----------------------------
DROP TABLE IF EXISTS `failed_jobs`;
CREATE TABLE `failed_jobs`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `connection` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `queue` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `exception` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `failed_jobs_uuid_unique`(`uuid` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of failed_jobs
-- ----------------------------

-- ----------------------------
-- Table structure for fiscal_years
-- ----------------------------
DROP TABLE IF EXISTS `fiscal_years`;
CREATE TABLE `fiscal_years`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `start_date_ad` date NOT NULL,
  `start_date_bs` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `end_date_ad` date NOT NULL,
  `end_date_bs` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `is_current` tinyint(1) NOT NULL DEFAULT 0,
  `is_closed` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `fiscal_years_name_unique`(`name` ASC) USING BTREE,
  INDEX `fiscal_years_is_current_index`(`is_current` ASC) USING BTREE,
  INDEX `fiscal_years_is_closed_index`(`is_closed` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of fiscal_years
-- ----------------------------
INSERT INTO `fiscal_years` VALUES (1, '2081/82', '2024-07-16', '2081-04-01', '2025-07-15', '2082-03-31', 1, 0, '2026-10-18 21:44:40', '2026-10-18 21:44:40', NULL);

-- ----------------------------
-- Table structure for goods_receipt_lines
-- ----------------------------
DROP TABLE IF EXISTS `goods_receipt_lines`;
CREATE TABLE `goods_receipt_lines`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `goods_receipt_id` bigint UNSIGNED NOT NULL,
  `purchase_order_line_id` bigint UNSIGNED NULL DEFAULT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  `batch_id` bigint UNSIGNED NULL DEFAULT NULL,
  `batch_number` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `mfg_date_ad` date NULL DEFAULT NULL,
  `mfg_date_bs` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `expiry_date_ad` date NOT NULL,
  `expiry_date_bs` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `location_id` bigint UNSIGNED NOT NULL,
  `unit_id` bigint UNSIGNED NOT NULL,
  `received_qty` decimal(12, 4) NOT NULL,
  `conversion_factor` decimal(12, 4) NOT NULL DEFAULT 1.0000,
  `received_base_qty` bigint UNSIGNED NOT NULL,
  `rejected_base_qty` bigint UNSIGNED NOT NULL DEFAULT 0,
  `unit_cost` decimal(15, 4) NOT NULL,
  `unit_cost_base` decimal(15, 4) NOT NULL,
  `mrp` decimal(15, 4) NOT NULL DEFAULT 0.0000,
  `selling_price` decimal(15, 4) NOT NULL DEFAULT 0.0000,
  `wholesale_price` decimal(15, 4) NOT NULL DEFAULT 0.0000,
  `discount_pct` decimal(8, 4) NOT NULL DEFAULT 0.0000,
  `discount_amount` decimal(15, 2) NOT NULL DEFAULT 0.00,
  `tax_category_id` bigint UNSIGNED NULL DEFAULT NULL,
  `tax_rate` decimal(8, 4) NOT NULL DEFAULT 0.0000,
  `tax_amount` decimal(15, 2) NOT NULL DEFAULT 0.00,
  `line_total` decimal(15, 2) NOT NULL DEFAULT 0.00,
  `landed_cost_share` decimal(15, 2) NOT NULL DEFAULT 0.00,
  `effective_unit_cost` decimal(15, 4) NOT NULL DEFAULT 0.0000,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `goods_receipt_lines_purchase_order_line_id_foreign`(`purchase_order_line_id` ASC) USING BTREE,
  INDEX `goods_receipt_lines_product_id_foreign`(`product_id` ASC) USING BTREE,
  INDEX `goods_receipt_lines_batch_id_foreign`(`batch_id` ASC) USING BTREE,
  INDEX `goods_receipt_lines_location_id_foreign`(`location_id` ASC) USING BTREE,
  INDEX `goods_receipt_lines_unit_id_foreign`(`unit_id` ASC) USING BTREE,
  INDEX `goods_receipt_lines_tax_category_id_foreign`(`tax_category_id` ASC) USING BTREE,
  INDEX `goods_receipt_lines_goods_receipt_id_product_id_index`(`goods_receipt_id` ASC, `product_id` ASC) USING BTREE,
  CONSTRAINT `goods_receipt_lines_batch_id_foreign` FOREIGN KEY (`batch_id`) REFERENCES `batches` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT,
  CONSTRAINT `goods_receipt_lines_goods_receipt_id_foreign` FOREIGN KEY (`goods_receipt_id`) REFERENCES `goods_receipts` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `goods_receipt_lines_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `warehouse_locations` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `goods_receipt_lines_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `goods_receipt_lines_purchase_order_line_id_foreign` FOREIGN KEY (`purchase_order_line_id`) REFERENCES `purchase_order_lines` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT,
  CONSTRAINT `goods_receipt_lines_tax_category_id_foreign` FOREIGN KEY (`tax_category_id`) REFERENCES `tax_categories` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT,
  CONSTRAINT `goods_receipt_lines_unit_id_foreign` FOREIGN KEY (`unit_id`) REFERENCES `units` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of goods_receipt_lines
-- ----------------------------
INSERT INTO `goods_receipt_lines` VALUES (1, 1, 2, 1, 6, 'NK-2026-GRN1', '2026-09-18', '2083-06-02', '2028-10-18', '2085-07-02', 1, 9, 5000.0000, 1.0000, 5000, 0, 1.2000, 1.2000, 0.0000, 0.0000, 0.0000, 5.0000, 300.00, NULL, 0.0000, 0.00, 5700.00, 332.50, 1.2065, '2026-10-18 22:17:53', '2026-10-18 22:17:53');

-- ----------------------------
-- Table structure for goods_receipts
-- ----------------------------
DROP TABLE IF EXISTS `goods_receipts`;
CREATE TABLE `goods_receipts`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `grn_number` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `fiscal_year_id` bigint UNSIGNED NOT NULL,
  `purchase_order_id` bigint UNSIGNED NULL DEFAULT NULL,
  `supplier_id` bigint UNSIGNED NOT NULL,
  `warehouse_id` bigint UNSIGNED NOT NULL,
  `supplier_invoice_number` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `supplier_invoice_date_ad` date NULL DEFAULT NULL,
  `supplier_invoice_date_bs` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `receipt_date_ad` date NOT NULL,
  `receipt_date_bs` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `freight_amount` decimal(15, 2) NOT NULL DEFAULT 0.00,
  `handling_charges` decimal(15, 2) NOT NULL DEFAULT 0.00,
  `other_charges` decimal(15, 2) NOT NULL DEFAULT 0.00,
  `subtotal` decimal(15, 2) NOT NULL DEFAULT 0.00,
  `discount_amount` decimal(15, 2) NOT NULL DEFAULT 0.00,
  `taxable_amount` decimal(15, 2) NOT NULL DEFAULT 0.00,
  `tax_amount` decimal(15, 2) NOT NULL DEFAULT 0.00,
  `total_amount` decimal(15, 2) NOT NULL DEFAULT 0.00,
  `total_landed_cost` decimal(15, 2) NOT NULL DEFAULT 0.00,
  `status` enum('draft','posted','cancelled') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'draft',
  `posted_by` bigint UNSIGNED NULL DEFAULT NULL,
  `posted_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint UNSIGNED NULL DEFAULT NULL,
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL,
  `attachment_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `goods_receipts_grn_number_unique`(`grn_number` ASC) USING BTREE,
  INDEX `goods_receipts_fiscal_year_id_foreign`(`fiscal_year_id` ASC) USING BTREE,
  INDEX `goods_receipts_purchase_order_id_foreign`(`purchase_order_id` ASC) USING BTREE,
  INDEX `goods_receipts_warehouse_id_foreign`(`warehouse_id` ASC) USING BTREE,
  INDEX `goods_receipts_posted_by_foreign`(`posted_by` ASC) USING BTREE,
  INDEX `goods_receipts_created_by_foreign`(`created_by` ASC) USING BTREE,
  INDEX `goods_receipts_supplier_id_receipt_date_ad_index`(`supplier_id` ASC, `receipt_date_ad` ASC) USING BTREE,
  INDEX `goods_receipts_supplier_invoice_number_index`(`supplier_invoice_number` ASC) USING BTREE,
  INDEX `goods_receipts_receipt_date_ad_index`(`receipt_date_ad` ASC) USING BTREE,
  INDEX `goods_receipts_status_index`(`status` ASC) USING BTREE,
  CONSTRAINT `goods_receipts_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `admins` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT,
  CONSTRAINT `goods_receipts_fiscal_year_id_foreign` FOREIGN KEY (`fiscal_year_id`) REFERENCES `fiscal_years` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `goods_receipts_posted_by_foreign` FOREIGN KEY (`posted_by`) REFERENCES `admins` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT,
  CONSTRAINT `goods_receipts_purchase_order_id_foreign` FOREIGN KEY (`purchase_order_id`) REFERENCES `purchase_orders` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT,
  CONSTRAINT `goods_receipts_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `goods_receipts_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of goods_receipts
-- ----------------------------
INSERT INTO `goods_receipts` VALUES (1, 'GRN-8182-00001', 1, 2, 1, 1, 'DJ-INV-2026-089', '2026-10-18', '2083-07-01', '2026-10-18', '2083-07-01', 300.00, 50.00, 0.00, 6000.00, 300.00, 5700.00, 0.00, 5700.00, 6050.00, 'posted', NULL, '2026-10-18 22:17:53', NULL, 'Received in good condition via courier delivery', NULL, '2026-10-18 22:17:53', '2026-10-18 22:17:53', NULL);

-- ----------------------------
-- Table structure for inventory_balances
-- ----------------------------
DROP TABLE IF EXISTS `inventory_balances`;
CREATE TABLE `inventory_balances`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `warehouse_id` bigint UNSIGNED NOT NULL,
  `location_id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  `batch_id` bigint UNSIGNED NOT NULL,
  `quantity_on_hand` bigint NOT NULL DEFAULT 0,
  `quantity_reserved` bigint NOT NULL DEFAULT 0,
  `quantity_damaged` bigint NOT NULL DEFAULT 0,
  `quantity_quarantined` bigint NOT NULL DEFAULT 0,
  `quantity_in_transit_out` bigint NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `inv_balance_wh_loc_prod_batch_unique`(`warehouse_id` ASC, `location_id` ASC, `product_id` ASC, `batch_id` ASC) USING BTREE,
  INDEX `inventory_balances_location_id_foreign`(`location_id` ASC) USING BTREE,
  INDEX `inventory_balances_batch_id_foreign`(`batch_id` ASC) USING BTREE,
  INDEX `inv_balance_wh_prod_idx`(`warehouse_id` ASC, `product_id` ASC) USING BTREE,
  INDEX `inv_balance_prod_batch_idx`(`product_id` ASC, `batch_id` ASC) USING BTREE,
  CONSTRAINT `inventory_balances_batch_id_foreign` FOREIGN KEY (`batch_id`) REFERENCES `batches` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `inventory_balances_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `warehouse_locations` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `inventory_balances_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `inventory_balances_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 5 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of inventory_balances
-- ----------------------------
INSERT INTO `inventory_balances` VALUES (1, 1, 1, 1, 1, 2000, 0, 0, 0, 0, '2026-10-18 22:06:40', '2026-10-18 22:06:40');
INSERT INTO `inventory_balances` VALUES (2, 1, 1, 1, 2, 5000, 0, 0, 0, 0, '2026-10-18 22:06:40', '2026-10-18 22:06:40');
INSERT INTO `inventory_balances` VALUES (3, 1, 4, 3, 5, 150, 0, 0, 0, 0, '2026-10-18 22:06:40', '2026-10-18 22:06:40');
INSERT INTO `inventory_balances` VALUES (4, 1, 1, 1, 6, 5000, 0, 0, 0, 0, '2026-10-18 22:17:53', '2026-10-18 22:17:53');

-- ----------------------------
-- Table structure for job_batches
-- ----------------------------
DROP TABLE IF EXISTS `job_batches`;
CREATE TABLE `job_batches`  (
  `id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `options` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL,
  `cancelled_at` int NULL DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of job_batches
-- ----------------------------

-- ----------------------------
-- Table structure for jobs
-- ----------------------------
DROP TABLE IF EXISTS `jobs`;
CREATE TABLE `jobs`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `attempts` tinyint UNSIGNED NOT NULL,
  `queued_at` timestamp NOT NULL DEFAULT current_timestamp,
  `reserved_at` int UNSIGNED NULL DEFAULT NULL,
  `available_at` int UNSIGNED NOT NULL,
  `created_at` int UNSIGNED NOT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `jobs_queue_index`(`queue` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of jobs
-- ----------------------------

-- ----------------------------
-- Table structure for kycs
-- ----------------------------
DROP TABLE IF EXISTS `kycs`;
CREATE TABLE `kycs`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `professional_id` bigint UNSIGNED NULL DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `dob` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `registration_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `symbol_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `profile_img` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `kycs_professional_id_foreign`(`professional_id` ASC) USING BTREE,
  CONSTRAINT `kycs_professional_id_foreign` FOREIGN KEY (`professional_id`) REFERENCES `professionals` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of kycs
-- ----------------------------

-- ----------------------------
-- Table structure for levels
-- ----------------------------
DROP TABLE IF EXISTS `levels`;
CREATE TABLE `levels`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `short_name_english` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `short_name_nepali` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `order` int NULL DEFAULT NULL,
  `route_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '0 = hide, 1 = yes',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of levels
-- ----------------------------

-- ----------------------------
-- Table structure for manufacturers
-- ----------------------------
DROP TABLE IF EXISTS `manufacturers`;
CREATE TABLE `manufacturers`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `code` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `name` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `country` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'Nepal',
  `contact_person` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `phone` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `address` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL,
  `dda_registration_no` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `manufacturers_code_unique`(`code` ASC) USING BTREE,
  INDEX `manufacturers_name_index`(`name` ASC) USING BTREE,
  INDEX `manufacturers_is_active_index`(`is_active` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of manufacturers
-- ----------------------------
INSERT INTO `manufacturers` VALUES (1, 'MFG-DJPL', 'Deurali-Janta Pharmaceuticals Pvt. Ltd.', 'Nepal', 'Supply Chain Manager', '+977-1-4356789', 'info@deuralijanta.com', NULL, 'DDA-MFG-001/2047', 1, '2026-10-18 22:06:40', '2026-10-18 22:06:40', NULL);
INSERT INTO `manufacturers` VALUES (2, 'MFG-NPL', 'Nepal Pharmaceuticals Laboratory (NPL)', 'Nepal', 'Distribution Officer', '+977-1-4271234', 'sales@npl.com.np', NULL, 'DDA-MFG-002/2048', 1, '2026-10-18 22:06:40', '2026-10-18 22:06:40', NULL);

-- ----------------------------
-- Table structure for migrations
-- ----------------------------
DROP TABLE IF EXISTS `migrations`;
CREATE TABLE `migrations`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 106 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of migrations
-- ----------------------------
INSERT INTO `migrations` VALUES (1, '0001_01_01_000000_create_session_table', 1);
INSERT INTO `migrations` VALUES (2, '0001_01_01_000001_create_cache_table', 1);
INSERT INTO `migrations` VALUES (3, '0001_01_01_000002_create_jobs_table', 1);
INSERT INTO `migrations` VALUES (4, '2025_11_05_111421_create_roles_table', 1);
INSERT INTO `migrations` VALUES (5, '2025_11_05_111846_create_modules_table', 1);
INSERT INTO `migrations` VALUES (6, '2025_11_05_112148_create_permissions_table', 1);
INSERT INTO `migrations` VALUES (7, '2025_11_05_112348_create_verification_steps_table', 1);
INSERT INTO `migrations` VALUES (8, '2025_11_05_113445_create_admins_table', 1);
INSERT INTO `migrations` VALUES (9, '2025_11_05_211211_create_colleges_table', 1);
INSERT INTO `migrations` VALUES (10, '2025_11_05_211224_create_universities_table', 1);
INSERT INTO `migrations` VALUES (11, '2025_11_06_201928_create_provinces_table', 1);
INSERT INTO `migrations` VALUES (12, '2025_11_06_201940_create_districts_table', 1);
INSERT INTO `migrations` VALUES (13, '2025_11_06_201957_create_municipalities_table', 1);
INSERT INTO `migrations` VALUES (14, '2025_11_08_090216_create_levels_table', 1);
INSERT INTO `migrations` VALUES (15, '2025_11_08_090358_create_subject_committees_table', 1);
INSERT INTO `migrations` VALUES (16, '2025_11_08_090414_create_subject_committee_users_table', 1);
INSERT INTO `migrations` VALUES (17, '2025_11_08_090614_create_programs_table', 1);
INSERT INTO `migrations` VALUES (18, '2025_11_08_125932_create_professionals_table', 1);
INSERT INTO `migrations` VALUES (19, '2025_11_08_130107_create_professional_infos_table', 1);
INSERT INTO `migrations` VALUES (20, '2025_11_08_130350_create_professional_qualifications_table', 1);
INSERT INTO `migrations` VALUES (21, '2025_11_08_131849_create_kycs_table', 1);
INSERT INTO `migrations` VALUES (22, '2025_11_08_132100_create_exams_table', 1);
INSERT INTO `migrations` VALUES (23, '2025_11_16_100156_create_applicants_table', 1);
INSERT INTO `migrations` VALUES (24, '2025_11_16_100205_create_applicant_logs_table', 1);
INSERT INTO `migrations` VALUES (25, '2025_11_22_213930_create_admit_cards_table', 1);
INSERT INTO `migrations` VALUES (26, '2025_12_11_213234_create_eligibility_marks_table', 1);
INSERT INTO `migrations` VALUES (27, '2025_12_11_214234_create_certificates_table', 1);
INSERT INTO `migrations` VALUES (28, '2026_03_20_160329_create_certificate_renew_requests_table', 1);
INSERT INTO `migrations` VALUES (42, '2026_10_18_000001_create_settings_table', 2);
INSERT INTO `migrations` VALUES (43, '2026_10_18_000002_create_audit_logs_table', 2);
INSERT INTO `migrations` VALUES (44, '2026_10_18_000003_create_fiscal_years_table', 2);
INSERT INTO `migrations` VALUES (45, '2026_10_18_000004_create_accounting_periods_table', 2);
INSERT INTO `migrations` VALUES (46, '2026_10_18_000005_create_document_sequences_table', 2);
INSERT INTO `migrations` VALUES (47, '2026_10_18_000006_create_warehouses_table', 2);
INSERT INTO `migrations` VALUES (48, '2026_10_18_000007_create_admin_warehouse_table', 2);
INSERT INTO `migrations` VALUES (49, '2026_10_18_000008_create_warehouse_locations_table', 2);
INSERT INTO `migrations` VALUES (50, '2026_10_18_000009_create_categories_table', 2);
INSERT INTO `migrations` VALUES (51, '2026_10_18_000010_create_manufacturers_table', 2);
INSERT INTO `migrations` VALUES (52, '2026_10_18_000011_create_units_table', 2);
INSERT INTO `migrations` VALUES (53, '2026_10_18_000012_create_tax_categories_table', 2);
INSERT INTO `migrations` VALUES (54, '2026_10_18_000013_create_cash_bank_accounts_table', 2);
INSERT INTO `migrations` VALUES (61, '2026_10_18_000014_create_products_table', 3);
INSERT INTO `migrations` VALUES (62, '2026_10_18_000015_create_product_packaging_levels_table', 3);
INSERT INTO `migrations` VALUES (63, '2026_10_18_000016_create_product_barcodes_table', 3);
INSERT INTO `migrations` VALUES (64, '2026_10_18_000017_create_batches_table', 3);
INSERT INTO `migrations` VALUES (65, '2026_10_18_000018_create_inventory_balances_table', 3);
INSERT INTO `migrations` VALUES (66, '2026_10_18_000019_create_stock_movements_table', 3);
INSERT INTO `migrations` VALUES (75, '2026_10_18_000020_create_suppliers_table', 4);
INSERT INTO `migrations` VALUES (76, '2026_10_18_000021_create_purchase_orders_table', 4);
INSERT INTO `migrations` VALUES (77, '2026_10_18_000022_create_purchase_order_lines_table', 4);
INSERT INTO `migrations` VALUES (78, '2026_10_18_000023_create_goods_receipts_table', 4);
INSERT INTO `migrations` VALUES (79, '2026_10_18_000024_create_goods_receipt_lines_table', 4);
INSERT INTO `migrations` VALUES (80, '2026_10_18_000025_create_supplier_ledger_entries_table', 4);
INSERT INTO `migrations` VALUES (81, '2026_10_18_000026_create_purchase_returns_table', 4);
INSERT INTO `migrations` VALUES (82, '2026_10_18_000027_create_purchase_return_lines_table', 4);
INSERT INTO `migrations` VALUES (89, '2026_10_18_000028_create_customers_table', 5);
INSERT INTO `migrations` VALUES (90, '2026_10_18_000029_create_sale_invoices_table', 5);
INSERT INTO `migrations` VALUES (91, '2026_10_18_000030_create_sale_invoice_lines_table', 5);
INSERT INTO `migrations` VALUES (92, '2026_10_18_000031_create_customer_ledger_entries_table', 5);
INSERT INTO `migrations` VALUES (93, '2026_10_18_000032_create_sale_returns_table', 5);
INSERT INTO `migrations` VALUES (94, '2026_10_18_000033_create_sale_return_lines_table', 5);
INSERT INTO `migrations` VALUES (95, '2026_10_18_180001_create_cash_bank_transactions_table', 6);
INSERT INTO `migrations` VALUES (96, '2026_10_18_180002_create_customer_receipts_table', 6);
INSERT INTO `migrations` VALUES (97, '2026_10_18_180003_create_customer_receipt_allocations_table', 6);
INSERT INTO `migrations` VALUES (98, '2026_10_18_180004_create_supplier_payments_table', 6);
INSERT INTO `migrations` VALUES (99, '2026_10_18_180005_create_supplier_payment_allocations_table', 6);
INSERT INTO `migrations` VALUES (100, '2026_10_18_190001_create_stock_transfers_table', 7);
INSERT INTO `migrations` VALUES (101, '2026_10_18_190002_create_stock_transfer_lines_table', 7);
INSERT INTO `migrations` VALUES (102, '2026_10_18_190003_create_stock_adjustments_table', 7);
INSERT INTO `migrations` VALUES (103, '2026_10_18_190004_create_stock_adjustment_lines_table', 7);
INSERT INTO `migrations` VALUES (104, '2026_10_18_190005_create_batch_recalls_table', 7);
INSERT INTO `migrations` VALUES (105, '2026_10_28_000001_add_pricing_fields_to_goods_receipt_lines_and_batches_table', 8);

-- ----------------------------
-- Table structure for modules
-- ----------------------------
DROP TABLE IF EXISTS `modules`;
CREATE TABLE `modules`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `modules_name_unique`(`name` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 27 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of modules
-- ----------------------------
INSERT INTO `modules` VALUES (1, 'Warehouse', '2026-10-18 21:44:22', '2026-10-18 21:44:22');
INSERT INTO `modules` VALUES (2, 'WarehouseLocation', '2026-10-18 21:44:22', '2026-10-18 21:44:22');
INSERT INTO `modules` VALUES (3, 'Category', '2026-10-18 21:44:22', '2026-10-18 21:44:22');
INSERT INTO `modules` VALUES (4, 'Manufacturer', '2026-10-18 21:44:22', '2026-10-18 21:44:22');
INSERT INTO `modules` VALUES (5, 'Unit', '2026-10-18 21:44:22', '2026-10-18 21:44:22');
INSERT INTO `modules` VALUES (6, 'TaxCategory', '2026-10-18 21:44:22', '2026-10-18 21:44:22');
INSERT INTO `modules` VALUES (7, 'CashBankAccount', '2026-10-18 21:44:22', '2026-10-18 21:44:22');
INSERT INTO `modules` VALUES (8, 'FiscalYear', '2026-10-18 21:44:22', '2026-10-18 21:44:22');
INSERT INTO `modules` VALUES (9, 'Setting', '2026-10-18 21:44:22', '2026-10-18 21:44:22');
INSERT INTO `modules` VALUES (10, 'AuditLog', '2026-10-18 21:44:22', '2026-10-18 21:44:22');
INSERT INTO `modules` VALUES (11, 'Product', '2026-10-18 21:44:22', '2026-10-18 21:44:22');
INSERT INTO `modules` VALUES (12, 'Batch', '2026-10-18 21:44:22', '2026-10-18 21:44:22');
INSERT INTO `modules` VALUES (13, 'Supplier', '2026-10-18 21:44:22', '2026-10-18 21:44:22');
INSERT INTO `modules` VALUES (14, 'Customer', '2026-10-18 21:44:22', '2026-10-18 21:44:22');
INSERT INTO `modules` VALUES (15, 'PurchaseOrder', '2026-10-18 21:44:22', '2026-10-18 21:44:22');
INSERT INTO `modules` VALUES (16, 'GoodsReceipt', '2026-10-18 21:44:22', '2026-10-18 21:44:22');
INSERT INTO `modules` VALUES (17, 'PurchaseReturn', '2026-10-18 21:44:22', '2026-10-18 21:44:22');
INSERT INTO `modules` VALUES (18, 'SaleInvoice', '2026-10-18 21:44:22', '2026-10-18 21:44:22');
INSERT INTO `modules` VALUES (19, 'SaleReturn', '2026-10-18 21:44:22', '2026-10-18 21:44:22');
INSERT INTO `modules` VALUES (20, 'CustomerPayment', '2026-10-18 21:44:22', '2026-10-18 21:44:22');
INSERT INTO `modules` VALUES (21, 'SupplierPayment', '2026-10-18 21:44:22', '2026-10-18 21:44:22');
INSERT INTO `modules` VALUES (22, 'StockTransfer', '2026-10-18 21:44:22', '2026-10-18 21:44:22');
INSERT INTO `modules` VALUES (23, 'StockAdjustment', '2026-10-18 21:44:22', '2026-10-18 21:44:22');
INSERT INTO `modules` VALUES (24, 'StockRelocation', '2026-10-18 21:44:22', '2026-10-18 21:44:22');
INSERT INTO `modules` VALUES (25, 'Recall', '2026-10-18 21:44:22', '2026-10-18 21:44:22');
INSERT INTO `modules` VALUES (26, 'Report', '2026-10-18 21:44:22', '2026-10-18 21:44:22');

-- ----------------------------
-- Table structure for municipalities
-- ----------------------------
DROP TABLE IF EXISTS `municipalities`;
CREATE TABLE `municipalities`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `district_id` bigint UNSIGNED NULL DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '0 = hide, 1 = show',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `municipalities_district_id_foreign`(`district_id` ASC) USING BTREE,
  CONSTRAINT `municipalities_district_id_foreign` FOREIGN KEY (`district_id`) REFERENCES `districts` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of municipalities
-- ----------------------------

-- ----------------------------
-- Table structure for password_reset_tokens
-- ----------------------------
DROP TABLE IF EXISTS `password_reset_tokens`;
CREATE TABLE `password_reset_tokens`  (
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `count` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of password_reset_tokens
-- ----------------------------

-- ----------------------------
-- Table structure for permissions
-- ----------------------------
DROP TABLE IF EXISTS `permissions`;
CREATE TABLE `permissions`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `role_id` bigint UNSIGNED NULL DEFAULT NULL,
  `module_id` bigint UNSIGNED NULL DEFAULT NULL,
  `name` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `permissions_role_id_foreign`(`role_id` ASC) USING BTREE,
  INDEX `permissions_module_id_foreign`(`module_id` ASC) USING BTREE,
  CONSTRAINT `permissions_module_id_foreign` FOREIGN KEY (`module_id`) REFERENCES `modules` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB AUTO_INCREMENT = 189 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of permissions
-- ----------------------------
INSERT INTO `permissions` VALUES (1, 1, 1, '\"[\\\"view\\\",\\\"create\\\",\\\"edit\\\",\\\"delete\\\",\\\"approve\\\",\\\"post\\\",\\\"cancel\\\"]\"', '2026-10-18 21:44:22', '2026-10-18 21:44:22');
INSERT INTO `permissions` VALUES (2, 2, 1, '\"[\\\"view\\\",\\\"create\\\",\\\"edit\\\"]\"', '2026-10-18 21:44:22', '2026-10-18 21:44:22');
INSERT INTO `permissions` VALUES (3, 1, 2, '\"[\\\"view\\\",\\\"create\\\",\\\"edit\\\",\\\"delete\\\",\\\"approve\\\",\\\"post\\\",\\\"cancel\\\"]\"', '2026-10-18 21:44:22', '2026-10-18 21:44:22');
INSERT INTO `permissions` VALUES (4, 2, 2, '\"[\\\"view\\\",\\\"create\\\",\\\"edit\\\"]\"', '2026-10-18 21:44:22', '2026-10-18 21:44:22');
INSERT INTO `permissions` VALUES (5, 1, 3, '\"[\\\"view\\\",\\\"create\\\",\\\"edit\\\",\\\"delete\\\",\\\"approve\\\",\\\"post\\\",\\\"cancel\\\"]\"', '2026-10-18 21:44:22', '2026-10-18 21:44:22');
INSERT INTO `permissions` VALUES (6, 2, 3, '\"[\\\"view\\\",\\\"create\\\",\\\"edit\\\"]\"', '2026-10-18 21:44:22', '2026-10-18 21:44:22');
INSERT INTO `permissions` VALUES (7, 1, 4, '\"[\\\"view\\\",\\\"create\\\",\\\"edit\\\",\\\"delete\\\",\\\"approve\\\",\\\"post\\\",\\\"cancel\\\"]\"', '2026-10-18 21:44:22', '2026-10-18 21:44:22');
INSERT INTO `permissions` VALUES (8, 2, 4, '\"[\\\"view\\\",\\\"create\\\",\\\"edit\\\"]\"', '2026-10-18 21:44:22', '2026-10-18 21:44:22');
INSERT INTO `permissions` VALUES (9, 1, 5, '\"[\\\"view\\\",\\\"create\\\",\\\"edit\\\",\\\"delete\\\",\\\"approve\\\",\\\"post\\\",\\\"cancel\\\"]\"', '2026-10-18 21:44:22', '2026-10-18 21:44:22');
INSERT INTO `permissions` VALUES (10, 2, 5, '\"[\\\"view\\\",\\\"create\\\",\\\"edit\\\"]\"', '2026-10-18 21:44:22', '2026-10-18 21:44:22');
INSERT INTO `permissions` VALUES (11, 1, 6, '\"[\\\"view\\\",\\\"create\\\",\\\"edit\\\",\\\"delete\\\",\\\"approve\\\",\\\"post\\\",\\\"cancel\\\"]\"', '2026-10-18 21:44:22', '2026-10-18 21:44:22');
INSERT INTO `permissions` VALUES (12, 2, 6, '\"[\\\"view\\\",\\\"create\\\",\\\"edit\\\"]\"', '2026-10-18 21:44:22', '2026-10-18 21:44:22');
INSERT INTO `permissions` VALUES (13, 1, 7, '\"[\\\"view\\\",\\\"create\\\",\\\"edit\\\",\\\"delete\\\",\\\"approve\\\",\\\"post\\\",\\\"cancel\\\"]\"', '2026-10-18 21:44:22', '2026-10-18 21:44:22');
INSERT INTO `permissions` VALUES (14, 2, 7, '\"[\\\"view\\\",\\\"create\\\",\\\"edit\\\"]\"', '2026-10-18 21:44:22', '2026-10-18 21:44:22');
INSERT INTO `permissions` VALUES (15, 1, 8, '\"[\\\"view\\\",\\\"create\\\",\\\"edit\\\",\\\"delete\\\",\\\"approve\\\",\\\"post\\\",\\\"cancel\\\"]\"', '2026-10-18 21:44:22', '2026-10-18 21:44:22');
INSERT INTO `permissions` VALUES (16, 2, 8, '\"[\\\"view\\\",\\\"create\\\",\\\"edit\\\"]\"', '2026-10-18 21:44:22', '2026-10-18 21:44:22');
INSERT INTO `permissions` VALUES (17, 1, 9, '\"[\\\"view\\\",\\\"create\\\",\\\"edit\\\",\\\"delete\\\",\\\"approve\\\",\\\"post\\\",\\\"cancel\\\"]\"', '2026-10-18 21:44:22', '2026-10-18 21:44:22');
INSERT INTO `permissions` VALUES (18, 2, 9, '\"[\\\"view\\\",\\\"create\\\",\\\"edit\\\"]\"', '2026-10-18 21:44:22', '2026-10-18 21:44:22');
INSERT INTO `permissions` VALUES (19, 1, 10, '\"[\\\"view\\\",\\\"create\\\",\\\"edit\\\",\\\"delete\\\",\\\"approve\\\",\\\"post\\\",\\\"cancel\\\"]\"', '2026-10-18 21:44:22', '2026-10-18 21:44:22');
INSERT INTO `permissions` VALUES (20, 2, 10, '\"[\\\"view\\\",\\\"create\\\",\\\"edit\\\"]\"', '2026-10-18 21:44:22', '2026-10-18 21:44:22');
INSERT INTO `permissions` VALUES (21, 1, 11, '\"[\\\"view\\\",\\\"create\\\",\\\"edit\\\",\\\"delete\\\",\\\"approve\\\",\\\"post\\\",\\\"cancel\\\"]\"', '2026-10-18 21:44:22', '2026-10-18 21:44:22');
INSERT INTO `permissions` VALUES (22, 2, 11, '\"[\\\"view\\\",\\\"create\\\",\\\"edit\\\"]\"', '2026-10-18 21:44:22', '2026-10-18 21:44:22');
INSERT INTO `permissions` VALUES (23, 1, 12, '\"[\\\"view\\\",\\\"create\\\",\\\"edit\\\",\\\"delete\\\",\\\"approve\\\",\\\"post\\\",\\\"cancel\\\"]\"', '2026-10-18 21:44:22', '2026-10-18 21:44:22');
INSERT INTO `permissions` VALUES (24, 2, 12, '\"[\\\"view\\\",\\\"create\\\",\\\"edit\\\"]\"', '2026-10-18 21:44:22', '2026-10-18 21:44:22');
INSERT INTO `permissions` VALUES (25, 1, 13, '\"[\\\"view\\\",\\\"create\\\",\\\"edit\\\",\\\"delete\\\",\\\"approve\\\",\\\"post\\\",\\\"cancel\\\"]\"', '2026-10-18 21:44:22', '2026-10-18 21:44:22');
INSERT INTO `permissions` VALUES (26, 2, 13, '\"[\\\"view\\\",\\\"create\\\",\\\"edit\\\"]\"', '2026-10-18 21:44:22', '2026-10-18 21:44:22');
INSERT INTO `permissions` VALUES (27, 1, 14, '\"[\\\"view\\\",\\\"create\\\",\\\"edit\\\",\\\"delete\\\",\\\"approve\\\",\\\"post\\\",\\\"cancel\\\"]\"', '2026-10-18 21:44:22', '2026-10-18 21:44:22');
INSERT INTO `permissions` VALUES (28, 2, 14, '\"[\\\"view\\\",\\\"create\\\",\\\"edit\\\"]\"', '2026-10-18 21:44:22', '2026-10-18 21:44:22');
INSERT INTO `permissions` VALUES (29, 1, 15, '\"[\\\"view\\\",\\\"create\\\",\\\"edit\\\",\\\"delete\\\",\\\"approve\\\",\\\"post\\\",\\\"cancel\\\"]\"', '2026-10-18 21:44:22', '2026-10-18 21:44:22');
INSERT INTO `permissions` VALUES (30, 2, 15, '\"[\\\"view\\\",\\\"create\\\",\\\"edit\\\"]\"', '2026-10-18 21:44:22', '2026-10-18 21:44:22');
INSERT INTO `permissions` VALUES (31, 1, 16, '\"[\\\"view\\\",\\\"create\\\",\\\"edit\\\",\\\"delete\\\",\\\"approve\\\",\\\"post\\\",\\\"cancel\\\"]\"', '2026-10-18 21:44:22', '2026-10-18 21:44:22');
INSERT INTO `permissions` VALUES (32, 2, 16, '\"[\\\"view\\\",\\\"create\\\",\\\"edit\\\"]\"', '2026-10-18 21:44:22', '2026-10-18 21:44:22');
INSERT INTO `permissions` VALUES (33, 1, 17, '\"[\\\"view\\\",\\\"create\\\",\\\"edit\\\",\\\"delete\\\",\\\"approve\\\",\\\"post\\\",\\\"cancel\\\"]\"', '2026-10-18 21:44:22', '2026-10-18 21:44:22');
INSERT INTO `permissions` VALUES (34, 2, 17, '\"[\\\"view\\\",\\\"create\\\",\\\"edit\\\"]\"', '2026-10-18 21:44:22', '2026-10-18 21:44:22');
INSERT INTO `permissions` VALUES (35, 1, 18, '\"[\\\"view\\\",\\\"create\\\",\\\"edit\\\",\\\"delete\\\",\\\"approve\\\",\\\"post\\\",\\\"cancel\\\"]\"', '2026-10-18 21:44:22', '2026-10-18 21:44:22');
INSERT INTO `permissions` VALUES (36, 2, 18, '\"[\\\"view\\\",\\\"create\\\",\\\"edit\\\"]\"', '2026-10-18 21:44:22', '2026-10-18 21:44:22');
INSERT INTO `permissions` VALUES (37, 1, 19, '\"[\\\"view\\\",\\\"create\\\",\\\"edit\\\",\\\"delete\\\",\\\"approve\\\",\\\"post\\\",\\\"cancel\\\"]\"', '2026-10-18 21:44:22', '2026-10-18 21:44:22');
INSERT INTO `permissions` VALUES (38, 2, 19, '\"[\\\"view\\\",\\\"create\\\",\\\"edit\\\"]\"', '2026-10-18 21:44:22', '2026-10-18 21:44:22');
INSERT INTO `permissions` VALUES (39, 1, 20, '\"[\\\"view\\\",\\\"create\\\",\\\"edit\\\",\\\"delete\\\",\\\"approve\\\",\\\"post\\\",\\\"cancel\\\"]\"', '2026-10-18 21:44:22', '2026-10-18 21:44:22');
INSERT INTO `permissions` VALUES (40, 2, 20, '\"[\\\"view\\\",\\\"create\\\",\\\"edit\\\"]\"', '2026-10-18 21:44:22', '2026-10-18 21:44:22');
INSERT INTO `permissions` VALUES (41, 1, 21, '\"[\\\"view\\\",\\\"create\\\",\\\"edit\\\",\\\"delete\\\",\\\"approve\\\",\\\"post\\\",\\\"cancel\\\"]\"', '2026-10-18 21:44:22', '2026-10-18 21:44:22');
INSERT INTO `permissions` VALUES (42, 2, 21, '\"[\\\"view\\\",\\\"create\\\",\\\"edit\\\"]\"', '2026-10-18 21:44:22', '2026-10-18 21:44:22');
INSERT INTO `permissions` VALUES (43, 1, 22, '\"[\\\"view\\\",\\\"create\\\",\\\"edit\\\",\\\"delete\\\",\\\"approve\\\",\\\"post\\\",\\\"cancel\\\"]\"', '2026-10-18 21:44:22', '2026-10-18 21:44:22');
INSERT INTO `permissions` VALUES (44, 2, 22, '\"[\\\"view\\\",\\\"create\\\",\\\"edit\\\"]\"', '2026-10-18 21:44:22', '2026-10-18 21:44:22');
INSERT INTO `permissions` VALUES (45, 1, 23, '\"[\\\"view\\\",\\\"create\\\",\\\"edit\\\",\\\"delete\\\",\\\"approve\\\",\\\"post\\\",\\\"cancel\\\"]\"', '2026-10-18 21:44:22', '2026-10-18 21:44:22');
INSERT INTO `permissions` VALUES (46, 2, 23, '\"[\\\"view\\\",\\\"create\\\",\\\"edit\\\"]\"', '2026-10-18 21:44:22', '2026-10-18 21:44:22');
INSERT INTO `permissions` VALUES (47, 1, 24, '\"[\\\"view\\\",\\\"create\\\",\\\"edit\\\",\\\"delete\\\",\\\"approve\\\",\\\"post\\\",\\\"cancel\\\"]\"', '2026-10-18 21:44:22', '2026-10-18 21:44:22');
INSERT INTO `permissions` VALUES (48, 2, 24, '\"[\\\"view\\\",\\\"create\\\",\\\"edit\\\"]\"', '2026-10-18 21:44:22', '2026-10-18 21:44:22');
INSERT INTO `permissions` VALUES (49, 1, 25, '\"[\\\"view\\\",\\\"create\\\",\\\"edit\\\",\\\"delete\\\",\\\"approve\\\",\\\"post\\\",\\\"cancel\\\"]\"', '2026-10-18 21:44:22', '2026-10-18 21:44:22');
INSERT INTO `permissions` VALUES (50, 2, 25, '\"[\\\"view\\\",\\\"create\\\",\\\"edit\\\"]\"', '2026-10-18 21:44:22', '2026-10-18 21:44:22');
INSERT INTO `permissions` VALUES (51, 1, 26, '\"[\\\"view\\\",\\\"create\\\",\\\"edit\\\",\\\"delete\\\",\\\"approve\\\",\\\"post\\\",\\\"cancel\\\"]\"', '2026-10-18 21:44:22', '2026-10-18 21:44:22');
INSERT INTO `permissions` VALUES (52, 2, 26, '\"[\\\"view\\\",\\\"create\\\",\\\"edit\\\"]\"', '2026-10-18 21:44:22', '2026-10-18 21:44:22');
INSERT INTO `permissions` VALUES (53, 3, 1, '\"[\\\"view\\\"]\"', '2026-10-18 23:15:48', '2026-10-18 23:15:48');
INSERT INTO `permissions` VALUES (54, 3, 2, '\"[\\\"view\\\"]\"', '2026-10-18 23:15:48', '2026-10-18 23:15:48');
INSERT INTO `permissions` VALUES (55, 3, 3, '\"[\\\"view\\\"]\"', '2026-10-18 23:15:48', '2026-10-18 23:15:48');
INSERT INTO `permissions` VALUES (56, 3, 4, '\"[\\\"view\\\"]\"', '2026-10-18 23:15:48', '2026-10-18 23:15:48');
INSERT INTO `permissions` VALUES (57, 3, 5, '\"[\\\"view\\\"]\"', '2026-10-18 23:15:48', '2026-10-18 23:15:48');
INSERT INTO `permissions` VALUES (58, 3, 6, '\"[\\\"view\\\"]\"', '2026-10-18 23:15:48', '2026-10-18 23:15:48');
INSERT INTO `permissions` VALUES (59, 3, 7, '\"[\\\"view\\\"]\"', '2026-10-18 23:15:48', '2026-10-18 23:15:48');
INSERT INTO `permissions` VALUES (60, 3, 8, '\"[\\\"view\\\"]\"', '2026-10-18 23:15:48', '2026-10-18 23:15:48');
INSERT INTO `permissions` VALUES (61, 3, 9, '\"[\\\"view\\\"]\"', '2026-10-18 23:15:48', '2026-10-18 23:15:48');
INSERT INTO `permissions` VALUES (62, 3, 10, '\"[\\\"view\\\"]\"', '2026-10-18 23:15:48', '2026-10-18 23:15:48');
INSERT INTO `permissions` VALUES (63, 3, 11, '\"[\\\"view\\\"]\"', '2026-10-18 23:15:48', '2026-10-18 23:15:48');
INSERT INTO `permissions` VALUES (64, 3, 12, '\"[\\\"view\\\"]\"', '2026-10-18 23:15:48', '2026-10-18 23:15:48');
INSERT INTO `permissions` VALUES (65, 3, 13, '\"[\\\"view\\\"]\"', '2026-10-18 23:15:48', '2026-10-18 23:15:48');
INSERT INTO `permissions` VALUES (66, 3, 14, '\"[\\\"view\\\"]\"', '2026-10-18 23:15:48', '2026-10-18 23:15:48');
INSERT INTO `permissions` VALUES (67, 3, 15, '\"[\\\"view\\\",\\\"approve\\\",\\\"cancel\\\"]\"', '2026-10-18 23:15:48', '2026-10-18 23:15:48');
INSERT INTO `permissions` VALUES (68, 3, 16, '\"[\\\"view\\\"]\"', '2026-10-18 23:15:48', '2026-10-18 23:15:48');
INSERT INTO `permissions` VALUES (69, 3, 17, '\"[\\\"view\\\",\\\"approve\\\"]\"', '2026-10-18 23:15:48', '2026-10-18 23:15:48');
INSERT INTO `permissions` VALUES (70, 3, 18, '\"[\\\"view\\\",\\\"approve\\\"]\"', '2026-10-18 23:15:49', '2026-10-18 23:15:49');
INSERT INTO `permissions` VALUES (71, 3, 19, '\"[\\\"view\\\",\\\"approve\\\"]\"', '2026-10-18 23:15:49', '2026-10-18 23:15:49');
INSERT INTO `permissions` VALUES (72, 3, 20, '\"[\\\"view\\\",\\\"approve\\\"]\"', '2026-10-18 23:15:49', '2026-10-18 23:15:49');
INSERT INTO `permissions` VALUES (73, 3, 21, '\"[\\\"view\\\",\\\"approve\\\"]\"', '2026-10-18 23:15:49', '2026-10-18 23:15:49');
INSERT INTO `permissions` VALUES (74, 3, 22, '\"[\\\"view\\\",\\\"approve\\\"]\"', '2026-10-18 23:15:49', '2026-10-18 23:15:49');
INSERT INTO `permissions` VALUES (75, 3, 23, '\"[\\\"view\\\",\\\"approve\\\",\\\"post\\\"]\"', '2026-10-18 23:15:49', '2026-10-18 23:15:49');
INSERT INTO `permissions` VALUES (76, 3, 24, '\"[\\\"view\\\"]\"', '2026-10-18 23:15:49', '2026-10-18 23:15:49');
INSERT INTO `permissions` VALUES (77, 3, 25, '\"[\\\"view\\\",\\\"approve\\\"]\"', '2026-10-18 23:15:49', '2026-10-18 23:15:49');
INSERT INTO `permissions` VALUES (78, 3, 26, '\"[\\\"view\\\"]\"', '2026-10-18 23:15:49', '2026-10-18 23:15:49');
INSERT INTO `permissions` VALUES (79, 4, 1, '\"[\\\"view\\\",\\\"create\\\",\\\"edit\\\",\\\"delete\\\"]\"', '2026-10-18 23:15:49', '2026-10-18 23:15:49');
INSERT INTO `permissions` VALUES (80, 4, 2, '\"[\\\"view\\\",\\\"create\\\",\\\"edit\\\",\\\"delete\\\"]\"', '2026-10-18 23:15:49', '2026-10-18 23:15:49');
INSERT INTO `permissions` VALUES (81, 4, 3, '\"[\\\"view\\\",\\\"create\\\",\\\"edit\\\",\\\"delete\\\"]\"', '2026-10-18 23:15:49', '2026-10-18 23:15:49');
INSERT INTO `permissions` VALUES (82, 4, 4, '\"[\\\"view\\\",\\\"create\\\",\\\"edit\\\",\\\"delete\\\"]\"', '2026-10-18 23:15:49', '2026-10-18 23:15:49');
INSERT INTO `permissions` VALUES (83, 4, 5, '\"[\\\"view\\\",\\\"create\\\",\\\"edit\\\",\\\"delete\\\"]\"', '2026-10-18 23:15:49', '2026-10-18 23:15:49');
INSERT INTO `permissions` VALUES (84, 4, 6, '\"[\\\"view\\\",\\\"create\\\",\\\"edit\\\",\\\"delete\\\"]\"', '2026-10-18 23:15:49', '2026-10-18 23:15:49');
INSERT INTO `permissions` VALUES (85, 4, 7, '\"[\\\"view\\\",\\\"create\\\",\\\"edit\\\",\\\"delete\\\"]\"', '2026-10-18 23:15:49', '2026-10-18 23:15:49');
INSERT INTO `permissions` VALUES (86, 4, 8, '\"[\\\"view\\\",\\\"create\\\",\\\"edit\\\",\\\"delete\\\"]\"', '2026-10-18 23:15:49', '2026-10-18 23:15:49');
INSERT INTO `permissions` VALUES (87, 4, 9, '\"[\\\"view\\\",\\\"create\\\",\\\"edit\\\"]\"', '2026-10-18 23:15:49', '2026-10-18 23:15:49');
INSERT INTO `permissions` VALUES (88, 4, 10, '\"[\\\"view\\\",\\\"delete\\\"]\"', '2026-10-18 23:15:49', '2026-10-18 23:15:49');
INSERT INTO `permissions` VALUES (89, 4, 11, '\"[\\\"view\\\",\\\"create\\\",\\\"edit\\\"]\"', '2026-10-18 23:15:49', '2026-10-18 23:15:49');
INSERT INTO `permissions` VALUES (90, 4, 12, '\"[\\\"view\\\"]\"', '2026-10-18 23:15:49', '2026-10-18 23:15:49');
INSERT INTO `permissions` VALUES (91, 4, 13, '\"[\\\"view\\\"]\"', '2026-10-18 23:15:49', '2026-10-18 23:15:49');
INSERT INTO `permissions` VALUES (92, 4, 14, '\"[\\\"view\\\"]\"', '2026-10-18 23:15:49', '2026-10-18 23:15:49');
INSERT INTO `permissions` VALUES (93, 4, 15, '\"[\\\"view\\\"]\"', '2026-10-18 23:15:49', '2026-10-18 23:15:49');
INSERT INTO `permissions` VALUES (94, 4, 16, '\"[\\\"view\\\"]\"', '2026-10-18 23:15:49', '2026-10-18 23:15:49');
INSERT INTO `permissions` VALUES (95, 4, 17, '\"[\\\"view\\\"]\"', '2026-10-18 23:15:49', '2026-10-18 23:15:49');
INSERT INTO `permissions` VALUES (96, 4, 18, '\"[\\\"view\\\"]\"', '2026-10-18 23:15:49', '2026-10-18 23:15:49');
INSERT INTO `permissions` VALUES (97, 4, 19, '\"[\\\"view\\\"]\"', '2026-10-18 23:15:49', '2026-10-18 23:15:49');
INSERT INTO `permissions` VALUES (98, 4, 20, '\"[\\\"view\\\"]\"', '2026-10-18 23:15:49', '2026-10-18 23:15:49');
INSERT INTO `permissions` VALUES (99, 4, 21, '\"[\\\"view\\\"]\"', '2026-10-18 23:15:49', '2026-10-18 23:15:49');
INSERT INTO `permissions` VALUES (100, 4, 22, '\"[\\\"view\\\"]\"', '2026-10-18 23:15:49', '2026-10-18 23:15:49');
INSERT INTO `permissions` VALUES (101, 4, 23, '\"[\\\"view\\\"]\"', '2026-10-18 23:15:49', '2026-10-18 23:15:49');
INSERT INTO `permissions` VALUES (102, 4, 24, '\"[\\\"view\\\"]\"', '2026-10-18 23:15:49', '2026-10-18 23:15:49');
INSERT INTO `permissions` VALUES (103, 4, 25, '\"[\\\"view\\\"]\"', '2026-10-18 23:15:49', '2026-10-18 23:15:49');
INSERT INTO `permissions` VALUES (104, 4, 26, '\"[\\\"view\\\"]\"', '2026-10-18 23:15:49', '2026-10-18 23:15:49');
INSERT INTO `permissions` VALUES (105, 5, 11, '\"[\\\"view\\\",\\\"create\\\",\\\"edit\\\"]\"', '2026-10-18 23:15:49', '2026-10-18 23:15:49');
INSERT INTO `permissions` VALUES (106, 5, 12, '\"[\\\"view\\\",\\\"create\\\",\\\"edit\\\"]\"', '2026-10-18 23:15:49', '2026-10-18 23:15:49');
INSERT INTO `permissions` VALUES (107, 5, 3, '\"[\\\"view\\\",\\\"create\\\",\\\"edit\\\"]\"', '2026-10-18 23:15:49', '2026-10-18 23:15:49');
INSERT INTO `permissions` VALUES (108, 5, 4, '\"[\\\"view\\\",\\\"create\\\",\\\"edit\\\"]\"', '2026-10-18 23:15:49', '2026-10-18 23:15:49');
INSERT INTO `permissions` VALUES (109, 5, 5, '\"[\\\"view\\\",\\\"create\\\",\\\"edit\\\"]\"', '2026-10-18 23:15:49', '2026-10-18 23:15:49');
INSERT INTO `permissions` VALUES (110, 5, 1, '\"[\\\"view\\\"]\"', '2026-10-18 23:15:49', '2026-10-18 23:15:49');
INSERT INTO `permissions` VALUES (111, 5, 2, '\"[\\\"view\\\",\\\"create\\\",\\\"edit\\\"]\"', '2026-10-18 23:15:49', '2026-10-18 23:15:49');
INSERT INTO `permissions` VALUES (112, 5, 16, '\"[\\\"view\\\"]\"', '2026-10-18 23:15:49', '2026-10-18 23:15:49');
INSERT INTO `permissions` VALUES (113, 5, 17, '\"[\\\"view\\\"]\"', '2026-10-18 23:15:49', '2026-10-18 23:15:49');
INSERT INTO `permissions` VALUES (114, 5, 19, '\"[\\\"view\\\"]\"', '2026-10-18 23:15:49', '2026-10-18 23:15:49');
INSERT INTO `permissions` VALUES (115, 5, 22, '\"[\\\"view\\\",\\\"create\\\",\\\"edit\\\",\\\"approve\\\",\\\"post\\\",\\\"cancel\\\"]\"', '2026-10-18 23:15:49', '2026-10-18 23:15:49');
INSERT INTO `permissions` VALUES (116, 5, 23, '\"[\\\"view\\\",\\\"create\\\",\\\"edit\\\",\\\"approve\\\",\\\"post\\\",\\\"cancel\\\"]\"', '2026-10-18 23:15:49', '2026-10-18 23:15:49');
INSERT INTO `permissions` VALUES (117, 5, 24, '\"[\\\"view\\\",\\\"create\\\",\\\"edit\\\",\\\"post\\\"]\"', '2026-10-18 23:15:49', '2026-10-18 23:15:49');
INSERT INTO `permissions` VALUES (118, 5, 25, '\"[\\\"view\\\",\\\"create\\\",\\\"edit\\\",\\\"approve\\\",\\\"cancel\\\"]\"', '2026-10-18 23:15:49', '2026-10-18 23:15:49');
INSERT INTO `permissions` VALUES (119, 5, 26, '\"[\\\"view\\\"]\"', '2026-10-18 23:15:49', '2026-10-18 23:15:49');
INSERT INTO `permissions` VALUES (120, 6, 13, '\"[\\\"view\\\",\\\"create\\\",\\\"edit\\\"]\"', '2026-10-18 23:15:49', '2026-10-18 23:15:49');
INSERT INTO `permissions` VALUES (121, 6, 15, '\"[\\\"view\\\",\\\"create\\\",\\\"edit\\\",\\\"approve\\\",\\\"cancel\\\"]\"', '2026-10-18 23:15:49', '2026-10-18 23:15:49');
INSERT INTO `permissions` VALUES (122, 6, 16, '\"[\\\"view\\\",\\\"create\\\",\\\"edit\\\",\\\"approve\\\"]\"', '2026-10-18 23:15:49', '2026-10-18 23:15:49');
INSERT INTO `permissions` VALUES (123, 6, 17, '\"[\\\"view\\\",\\\"create\\\",\\\"edit\\\",\\\"approve\\\",\\\"cancel\\\"]\"', '2026-10-18 23:15:49', '2026-10-18 23:15:49');
INSERT INTO `permissions` VALUES (124, 6, 11, '\"[\\\"view\\\"]\"', '2026-10-18 23:15:49', '2026-10-18 23:15:49');
INSERT INTO `permissions` VALUES (125, 6, 12, '\"[\\\"view\\\"]\"', '2026-10-18 23:15:49', '2026-10-18 23:15:49');
INSERT INTO `permissions` VALUES (126, 6, 5, '\"[\\\"view\\\"]\"', '2026-10-18 23:15:49', '2026-10-18 23:15:49');
INSERT INTO `permissions` VALUES (127, 6, 1, '\"[\\\"view\\\"]\"', '2026-10-18 23:15:49', '2026-10-18 23:15:49');
INSERT INTO `permissions` VALUES (128, 6, 21, '\"[\\\"view\\\"]\"', '2026-10-18 23:15:49', '2026-10-18 23:15:49');
INSERT INTO `permissions` VALUES (129, 6, 26, '\"[\\\"view\\\"]\"', '2026-10-18 23:15:49', '2026-10-18 23:15:49');
INSERT INTO `permissions` VALUES (130, 7, 14, '\"[\\\"view\\\",\\\"create\\\",\\\"edit\\\"]\"', '2026-10-18 23:15:50', '2026-10-18 23:15:50');
INSERT INTO `permissions` VALUES (131, 7, 18, '\"[\\\"view\\\",\\\"create\\\",\\\"edit\\\",\\\"approve\\\",\\\"cancel\\\"]\"', '2026-10-18 23:15:50', '2026-10-18 23:15:50');
INSERT INTO `permissions` VALUES (132, 7, 19, '\"[\\\"view\\\",\\\"create\\\",\\\"edit\\\",\\\"approve\\\",\\\"cancel\\\"]\"', '2026-10-18 23:15:50', '2026-10-18 23:15:50');
INSERT INTO `permissions` VALUES (133, 7, 11, '\"[\\\"view\\\"]\"', '2026-10-18 23:15:50', '2026-10-18 23:15:50');
INSERT INTO `permissions` VALUES (134, 7, 12, '\"[\\\"view\\\"]\"', '2026-10-18 23:15:50', '2026-10-18 23:15:50');
INSERT INTO `permissions` VALUES (135, 7, 5, '\"[\\\"view\\\"]\"', '2026-10-18 23:15:50', '2026-10-18 23:15:50');
INSERT INTO `permissions` VALUES (136, 7, 1, '\"[\\\"view\\\"]\"', '2026-10-18 23:15:50', '2026-10-18 23:15:50');
INSERT INTO `permissions` VALUES (137, 7, 20, '\"[\\\"view\\\"]\"', '2026-10-18 23:15:50', '2026-10-18 23:15:50');
INSERT INTO `permissions` VALUES (138, 7, 26, '\"[\\\"view\\\"]\"', '2026-10-18 23:15:50', '2026-10-18 23:15:50');
INSERT INTO `permissions` VALUES (139, 8, 1, '\"[\\\"view\\\"]\"', '2026-10-18 23:15:50', '2026-10-18 23:15:50');
INSERT INTO `permissions` VALUES (140, 8, 2, '\"[\\\"view\\\"]\"', '2026-10-18 23:15:50', '2026-10-18 23:15:50');
INSERT INTO `permissions` VALUES (141, 8, 11, '\"[\\\"view\\\"]\"', '2026-10-18 23:15:50', '2026-10-18 23:15:50');
INSERT INTO `permissions` VALUES (142, 8, 12, '\"[\\\"view\\\"]\"', '2026-10-18 23:15:50', '2026-10-18 23:15:50');
INSERT INTO `permissions` VALUES (143, 8, 16, '\"[\\\"view\\\",\\\"create\\\"]\"', '2026-10-18 23:15:50', '2026-10-18 23:15:50');
INSERT INTO `permissions` VALUES (144, 8, 22, '\"[\\\"view\\\",\\\"create\\\",\\\"edit\\\"]\"', '2026-10-18 23:15:50', '2026-10-18 23:15:50');
INSERT INTO `permissions` VALUES (145, 8, 24, '\"[\\\"view\\\",\\\"create\\\",\\\"edit\\\",\\\"post\\\"]\"', '2026-10-18 23:15:50', '2026-10-18 23:15:50');
INSERT INTO `permissions` VALUES (146, 8, 23, '\"[\\\"view\\\",\\\"create\\\"]\"', '2026-10-18 23:15:50', '2026-10-18 23:15:50');
INSERT INTO `permissions` VALUES (147, 8, 25, '\"[\\\"view\\\"]\"', '2026-10-18 23:15:50', '2026-10-18 23:15:50');
INSERT INTO `permissions` VALUES (148, 9, 7, '\"[\\\"view\\\",\\\"create\\\",\\\"edit\\\"]\"', '2026-10-18 23:15:50', '2026-10-18 23:15:50');
INSERT INTO `permissions` VALUES (149, 9, 8, '\"[\\\"view\\\",\\\"create\\\",\\\"edit\\\"]\"', '2026-10-18 23:15:50', '2026-10-18 23:15:50');
INSERT INTO `permissions` VALUES (150, 9, 6, '\"[\\\"view\\\",\\\"create\\\",\\\"edit\\\"]\"', '2026-10-18 23:15:50', '2026-10-18 23:15:50');
INSERT INTO `permissions` VALUES (151, 9, 20, '\"[\\\"view\\\",\\\"create\\\",\\\"edit\\\",\\\"approve\\\",\\\"post\\\",\\\"cancel\\\"]\"', '2026-10-18 23:15:50', '2026-10-18 23:15:50');
INSERT INTO `permissions` VALUES (152, 9, 21, '\"[\\\"view\\\",\\\"create\\\",\\\"edit\\\",\\\"approve\\\",\\\"post\\\",\\\"cancel\\\"]\"', '2026-10-18 23:15:50', '2026-10-18 23:15:50');
INSERT INTO `permissions` VALUES (153, 9, 18, '\"[\\\"view\\\",\\\"post\\\"]\"', '2026-10-18 23:15:50', '2026-10-18 23:15:50');
INSERT INTO `permissions` VALUES (154, 9, 15, '\"[\\\"view\\\"]\"', '2026-10-18 23:15:50', '2026-10-18 23:15:50');
INSERT INTO `permissions` VALUES (155, 9, 16, '\"[\\\"view\\\"]\"', '2026-10-18 23:15:50', '2026-10-18 23:15:50');
INSERT INTO `permissions` VALUES (156, 9, 17, '\"[\\\"view\\\",\\\"post\\\"]\"', '2026-10-18 23:15:50', '2026-10-18 23:15:50');
INSERT INTO `permissions` VALUES (157, 9, 19, '\"[\\\"view\\\",\\\"post\\\"]\"', '2026-10-18 23:15:50', '2026-10-18 23:15:50');
INSERT INTO `permissions` VALUES (158, 9, 26, '\"[\\\"view\\\"]\"', '2026-10-18 23:15:50', '2026-10-18 23:15:50');
INSERT INTO `permissions` VALUES (159, 10, 20, '\"[\\\"view\\\",\\\"create\\\"]\"', '2026-10-18 23:15:50', '2026-10-18 23:15:50');
INSERT INTO `permissions` VALUES (160, 10, 18, '\"[\\\"view\\\"]\"', '2026-10-18 23:15:50', '2026-10-18 23:15:50');
INSERT INTO `permissions` VALUES (161, 10, 7, '\"[\\\"view\\\"]\"', '2026-10-18 23:15:50', '2026-10-18 23:15:50');
INSERT INTO `permissions` VALUES (162, 10, 14, '\"[\\\"view\\\"]\"', '2026-10-18 23:15:50', '2026-10-18 23:15:50');
INSERT INTO `permissions` VALUES (163, 11, 1, '\"[\\\"view\\\"]\"', '2026-10-18 23:15:51', '2026-10-18 23:15:51');
INSERT INTO `permissions` VALUES (164, 11, 2, '\"[\\\"view\\\"]\"', '2026-10-18 23:15:51', '2026-10-18 23:15:51');
INSERT INTO `permissions` VALUES (165, 11, 3, '\"[\\\"view\\\"]\"', '2026-10-18 23:15:51', '2026-10-18 23:15:51');
INSERT INTO `permissions` VALUES (166, 11, 4, '\"[\\\"view\\\"]\"', '2026-10-18 23:15:51', '2026-10-18 23:15:51');
INSERT INTO `permissions` VALUES (167, 11, 5, '\"[\\\"view\\\"]\"', '2026-10-18 23:15:51', '2026-10-18 23:15:51');
INSERT INTO `permissions` VALUES (168, 11, 6, '\"[\\\"view\\\"]\"', '2026-10-18 23:15:51', '2026-10-18 23:15:51');
INSERT INTO `permissions` VALUES (169, 11, 7, '\"[\\\"view\\\"]\"', '2026-10-18 23:15:51', '2026-10-18 23:15:51');
INSERT INTO `permissions` VALUES (170, 11, 8, '\"[\\\"view\\\"]\"', '2026-10-18 23:15:51', '2026-10-18 23:15:51');
INSERT INTO `permissions` VALUES (171, 11, 9, '\"[\\\"view\\\"]\"', '2026-10-18 23:15:51', '2026-10-18 23:15:51');
INSERT INTO `permissions` VALUES (172, 11, 10, '\"[\\\"view\\\"]\"', '2026-10-18 23:15:51', '2026-10-18 23:15:51');
INSERT INTO `permissions` VALUES (173, 11, 11, '\"[\\\"view\\\"]\"', '2026-10-18 23:15:51', '2026-10-18 23:15:51');
INSERT INTO `permissions` VALUES (174, 11, 12, '\"[\\\"view\\\"]\"', '2026-10-18 23:15:51', '2026-10-18 23:15:51');
INSERT INTO `permissions` VALUES (175, 11, 13, '\"[\\\"view\\\"]\"', '2026-10-18 23:15:51', '2026-10-18 23:15:51');
INSERT INTO `permissions` VALUES (176, 11, 14, '\"[\\\"view\\\"]\"', '2026-10-18 23:15:51', '2026-10-18 23:15:51');
INSERT INTO `permissions` VALUES (177, 11, 15, '\"[\\\"view\\\"]\"', '2026-10-18 23:15:51', '2026-10-18 23:15:51');
INSERT INTO `permissions` VALUES (178, 11, 16, '\"[\\\"view\\\"]\"', '2026-10-18 23:15:51', '2026-10-18 23:15:51');
INSERT INTO `permissions` VALUES (179, 11, 17, '\"[\\\"view\\\"]\"', '2026-10-18 23:15:51', '2026-10-18 23:15:51');
INSERT INTO `permissions` VALUES (180, 11, 18, '\"[\\\"view\\\"]\"', '2026-10-18 23:15:51', '2026-10-18 23:15:51');
INSERT INTO `permissions` VALUES (181, 11, 19, '\"[\\\"view\\\"]\"', '2026-10-18 23:15:51', '2026-10-18 23:15:51');
INSERT INTO `permissions` VALUES (182, 11, 20, '\"[\\\"view\\\"]\"', '2026-10-18 23:15:51', '2026-10-18 23:15:51');
INSERT INTO `permissions` VALUES (183, 11, 21, '\"[\\\"view\\\"]\"', '2026-10-18 23:15:51', '2026-10-18 23:15:51');
INSERT INTO `permissions` VALUES (184, 11, 22, '\"[\\\"view\\\"]\"', '2026-10-18 23:15:51', '2026-10-18 23:15:51');
INSERT INTO `permissions` VALUES (185, 11, 23, '\"[\\\"view\\\"]\"', '2026-10-18 23:15:51', '2026-10-18 23:15:51');
INSERT INTO `permissions` VALUES (186, 11, 24, '\"[\\\"view\\\"]\"', '2026-10-18 23:15:51', '2026-10-18 23:15:51');
INSERT INTO `permissions` VALUES (187, 11, 25, '\"[\\\"view\\\"]\"', '2026-10-18 23:15:51', '2026-10-18 23:15:51');
INSERT INTO `permissions` VALUES (188, 11, 26, '\"[\\\"view\\\"]\"', '2026-10-18 23:15:51', '2026-10-18 23:15:51');

-- ----------------------------
-- Table structure for product_barcodes
-- ----------------------------
DROP TABLE IF EXISTS `product_barcodes`;
CREATE TABLE `product_barcodes`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `product_id` bigint UNSIGNED NOT NULL,
  `barcode` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `type` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'EAN13',
  `packaging_level_id` bigint UNSIGNED NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `product_barcodes_barcode_unique`(`barcode` ASC) USING BTREE,
  INDEX `product_barcodes_product_id_foreign`(`product_id` ASC) USING BTREE,
  INDEX `product_barcodes_packaging_level_id_foreign`(`packaging_level_id` ASC) USING BTREE,
  CONSTRAINT `product_barcodes_packaging_level_id_foreign` FOREIGN KEY (`packaging_level_id`) REFERENCES `product_packaging_levels` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT,
  CONSTRAINT `product_barcodes_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of product_barcodes
-- ----------------------------
INSERT INTO `product_barcodes` VALUES (1, 1, '8901234560011', 'EAN13', NULL, '2026-10-18 22:06:40', '2026-10-18 22:06:40');

-- ----------------------------
-- Table structure for product_packaging_levels
-- ----------------------------
DROP TABLE IF EXISTS `product_packaging_levels`;
CREATE TABLE `product_packaging_levels`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `product_id` bigint UNSIGNED NOT NULL,
  `unit_id` bigint UNSIGNED NOT NULL,
  `level_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `level_order` tinyint UNSIGNED NOT NULL,
  `conversion_factor` decimal(12, 4) UNSIGNED NOT NULL,
  `allow_fractional_sale` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `product_packaging_levels_product_id_level_order_unique`(`product_id` ASC, `level_order` ASC) USING BTREE,
  UNIQUE INDEX `product_packaging_levels_product_id_unit_id_unique`(`product_id` ASC, `unit_id` ASC) USING BTREE,
  INDEX `product_packaging_levels_unit_id_foreign`(`unit_id` ASC) USING BTREE,
  CONSTRAINT `product_packaging_levels_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `product_packaging_levels_unit_id_foreign` FOREIGN KEY (`unit_id`) REFERENCES `units` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 4 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of product_packaging_levels
-- ----------------------------
INSERT INTO `product_packaging_levels` VALUES (1, 1, 9, 'Tablet (Base)', 1, 1.0000, 0, '2026-10-18 22:06:40', '2026-10-18 22:06:40');
INSERT INTO `product_packaging_levels` VALUES (2, 1, 2, 'Strip of 10', 2, 10.0000, 0, '2026-10-18 22:06:40', '2026-10-18 22:06:40');
INSERT INTO `product_packaging_levels` VALUES (3, 1, 3, 'Box of 100', 3, 100.0000, 0, '2026-10-18 22:06:40', '2026-10-18 22:06:40');

-- ----------------------------
-- Table structure for products
-- ----------------------------
DROP TABLE IF EXISTS `products`;
CREATE TABLE `products`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `code` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `generic_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `brand` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `category_id` bigint UNSIGNED NULL DEFAULT NULL,
  `manufacturer_id` bigint UNSIGNED NULL DEFAULT NULL,
  `dosage_form` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `strength` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `base_unit_id` bigint UNSIGNED NOT NULL,
  `tax_category_id` bigint UNSIGNED NULL DEFAULT NULL,
  `mrp` decimal(15, 4) NOT NULL DEFAULT 0.0000,
  `purchase_price` decimal(15, 4) NOT NULL DEFAULT 0.0000,
  `selling_price` decimal(15, 4) NOT NULL DEFAULT 0.0000,
  `wholesale_price` decimal(15, 4) NOT NULL DEFAULT 0.0000,
  `min_stock` bigint UNSIGNED NOT NULL DEFAULT 0,
  `reorder_level` bigint UNSIGNED NOT NULL DEFAULT 0,
  `max_level` bigint UNSIGNED NULL DEFAULT NULL,
  `storage_requirement` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'room_temperature',
  `narcotic_psychotropic` tinyint(1) NOT NULL DEFAULT 0,
  `prescription_required` tinyint(1) NOT NULL DEFAULT 1,
  `dda_registration_number` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `allow_fractional_base` tinyint(1) NOT NULL DEFAULT 0,
  `image_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `products_code_unique`(`code` ASC) USING BTREE,
  INDEX `products_category_id_foreign`(`category_id` ASC) USING BTREE,
  INDEX `products_manufacturer_id_foreign`(`manufacturer_id` ASC) USING BTREE,
  INDEX `products_base_unit_id_foreign`(`base_unit_id` ASC) USING BTREE,
  INDEX `products_tax_category_id_foreign`(`tax_category_id` ASC) USING BTREE,
  INDEX `products_name_index`(`name` ASC) USING BTREE,
  INDEX `products_generic_name_index`(`generic_name` ASC) USING BTREE,
  INDEX `products_narcotic_psychotropic_index`(`narcotic_psychotropic` ASC) USING BTREE,
  INDEX `products_is_active_index`(`is_active` ASC) USING BTREE,
  CONSTRAINT `products_base_unit_id_foreign` FOREIGN KEY (`base_unit_id`) REFERENCES `units` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `products_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT,
  CONSTRAINT `products_manufacturer_id_foreign` FOREIGN KEY (`manufacturer_id`) REFERENCES `manufacturers` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT,
  CONSTRAINT `products_tax_category_id_foreign` FOREIGN KEY (`tax_category_id`) REFERENCES `tax_categories` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 4 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of products
-- ----------------------------
INSERT INTO `products` VALUES (1, 'PRD-NIKO-500', 'Niko 500mg Tablet', 'Paracetamol', 'Niko', 1, 1, 'Tablet', '500mg', 9, 1, 2.0000, 1.2000, 1.6000, 1.5000, 500, 1000, 10000, 'room_temperature', 0, 1, NULL, 0, NULL, NULL, 1, '2026-10-18 22:06:40', '2026-10-18 22:06:40', NULL);
INSERT INTO `products` VALUES (2, 'PRD-CIPRO-500', 'Cipro-500 Tablet', 'Ciprofloxacin Hydrochloride', 'Cipro', 2, 2, 'Tablet', '500mg', 9, 1, 15.0000, 9.5000, 12.0000, 11.2000, 200, 500, NULL, 'room_temperature', 0, 1, NULL, 0, NULL, NULL, 1, '2026-10-18 22:06:40', '2026-10-18 22:06:40', NULL);
INSERT INTO `products` VALUES (3, 'PRD-INSULIN-40', 'Human Insulin 40IU/ml 10ml Vial', 'Human Recombinant Insulin', 'Insugen-R', 4, 2, 'Injection (Vial)', '40 IU/ml, 10ml', 5, 1, 450.0000, 320.0000, 395.0000, 380.0000, 50, 100, NULL, 'cold_chain_2_8', 0, 1, NULL, 0, NULL, NULL, 1, '2026-10-18 22:06:40', '2026-10-18 22:06:40', NULL);

-- ----------------------------
-- Table structure for professional_infos
-- ----------------------------
DROP TABLE IF EXISTS `professional_infos`;
CREATE TABLE `professional_infos`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `professional_id` bigint UNSIGNED NULL DEFAULT NULL,
  `program_id` bigint UNSIGNED NULL DEFAULT NULL,
  `level_id` bigint UNSIGNED NULL DEFAULT NULL,
  `first_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `middle_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `last_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `first_name_nep` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `middle_name_nep` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `last_name_nep` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `dob_eng` date NULL DEFAULT NULL,
  `dob_nep` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `gender` enum('male','female','other') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT 'male',
  `marital_status` enum('married','unmarried') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT 'unmarried',
  `ethinic` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `citizenship_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `citizenship_issue_date` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `citizenship_issue_district` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `father_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `father_name_nep` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `grandfather_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `grandfather_name_nep` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `mother_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `mother_name_nep` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `province_id` bigint UNSIGNED NULL DEFAULT NULL,
  `district_id` bigint UNSIGNED NULL DEFAULT NULL,
  `municipality_id` bigint UNSIGNED NULL DEFAULT NULL,
  `ward_no` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `profile_picture` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `citizenship_front` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `citizenship_back` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `signature_image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `profile_state` enum('student','operator','officer','registrar','subject_committee','exam_committee','council','onhold') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT 'student',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `professional_infos_professional_id_foreign`(`professional_id` ASC) USING BTREE,
  INDEX `professional_infos_program_id_foreign`(`program_id` ASC) USING BTREE,
  INDEX `professional_infos_level_id_foreign`(`level_id` ASC) USING BTREE,
  INDEX `professional_infos_province_id_foreign`(`province_id` ASC) USING BTREE,
  INDEX `professional_infos_district_id_foreign`(`district_id` ASC) USING BTREE,
  INDEX `professional_infos_municipality_id_foreign`(`municipality_id` ASC) USING BTREE,
  CONSTRAINT `professional_infos_district_id_foreign` FOREIGN KEY (`district_id`) REFERENCES `districts` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `professional_infos_level_id_foreign` FOREIGN KEY (`level_id`) REFERENCES `levels` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `professional_infos_municipality_id_foreign` FOREIGN KEY (`municipality_id`) REFERENCES `municipalities` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `professional_infos_professional_id_foreign` FOREIGN KEY (`professional_id`) REFERENCES `professionals` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `professional_infos_program_id_foreign` FOREIGN KEY (`program_id`) REFERENCES `programs` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `professional_infos_province_id_foreign` FOREIGN KEY (`province_id`) REFERENCES `provinces` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of professional_infos
-- ----------------------------

-- ----------------------------
-- Table structure for professional_qualifications
-- ----------------------------
DROP TABLE IF EXISTS `professional_qualifications`;
CREATE TABLE `professional_qualifications`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `professional_id` bigint UNSIGNED NULL DEFAULT NULL,
  `level_id` bigint UNSIGNED NULL DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `board_university` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `passed_year` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `admission_year` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `college_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `registration_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `result_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT 'gpa | percentage',
  `gpa` decimal(4, 2) NULL DEFAULT NULL,
  `percentage` decimal(5, 2) NOT NULL,
  `physics_marks` decimal(5, 2) NOT NULL,
  `chemistry_marks` decimal(5, 2) NOT NULL,
  `biology_marks` decimal(5, 2) NOT NULL,
  `average` decimal(5, 2) NOT NULL,
  `transcript_image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `provisional_image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `character_image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `ojt_image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `visa_image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `noc_image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `intership_image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `passport_image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `licence` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `council_registration_certificate` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `ojt_pcl_community_1_image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `ojt_pcl_community_2_image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `transcript_mas_marksheet` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `transcript_bac_1` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `transcript_bac_2` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `transcript_bac_3` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `transcript_bac_4` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `transcript_bac_5` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `transcript_bac_6` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `transcript_bac_7` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `transcript_bac_8` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `migration_image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `equivalence_certificate` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `professional_qualifications_professional_id_foreign`(`professional_id` ASC) USING BTREE,
  INDEX `professional_qualifications_level_id_foreign`(`level_id` ASC) USING BTREE,
  CONSTRAINT `professional_qualifications_level_id_foreign` FOREIGN KEY (`level_id`) REFERENCES `levels` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `professional_qualifications_professional_id_foreign` FOREIGN KEY (`professional_id`) REFERENCES `professionals` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of professional_qualifications
-- ----------------------------

-- ----------------------------
-- Table structure for professionals
-- ----------------------------
DROP TABLE IF EXISTS `professionals`;
CREATE TABLE `professionals`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `phone` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `password_reference` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `otp` int NULL DEFAULT NULL,
  `is_foreign` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0 = native, 1 = foreign',
  `is_verify` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0 = No, 1 = Yes',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '0 = inactive, 1 = active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `professionals_email_unique`(`email` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of professionals
-- ----------------------------

-- ----------------------------
-- Table structure for programs
-- ----------------------------
DROP TABLE IF EXISTS `programs`;
CREATE TABLE `programs`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `certificate_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `qualification` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `level_id` bigint UNSIGNED NULL DEFAULT NULL,
  `subject_committee_id` bigint UNSIGNED NULL DEFAULT NULL,
  `program_duration` int NULL DEFAULT NULL,
  `duration_type` enum('month','year') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT 'year',
  `program_type` enum('yearly','semester','trimester','bulk') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT 'yearly',
  `has_exam` tinyint(1) NULL DEFAULT NULL COMMENT '1=Yes 0=No',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '0 = hide, 1 = show',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `programs_level_id_foreign`(`level_id` ASC) USING BTREE,
  INDEX `programs_subject_committee_id_foreign`(`subject_committee_id` ASC) USING BTREE,
  CONSTRAINT `programs_level_id_foreign` FOREIGN KEY (`level_id`) REFERENCES `levels` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `programs_subject_committee_id_foreign` FOREIGN KEY (`subject_committee_id`) REFERENCES `subject_committees` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of programs
-- ----------------------------

-- ----------------------------
-- Table structure for provinces
-- ----------------------------
DROP TABLE IF EXISTS `provinces`;
CREATE TABLE `provinces`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '0 = hide, 1 = show',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of provinces
-- ----------------------------

-- ----------------------------
-- Table structure for purchase_order_lines
-- ----------------------------
DROP TABLE IF EXISTS `purchase_order_lines`;
CREATE TABLE `purchase_order_lines`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `purchase_order_id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  `unit_id` bigint UNSIGNED NOT NULL,
  `ordered_qty` decimal(12, 4) NOT NULL,
  `conversion_factor` decimal(12, 4) NOT NULL DEFAULT 1.0000,
  `ordered_base_qty` bigint UNSIGNED NOT NULL,
  `received_base_qty` bigint UNSIGNED NOT NULL DEFAULT 0,
  `unit_price` decimal(15, 4) NOT NULL,
  `discount_pct` decimal(8, 4) NOT NULL DEFAULT 0.0000,
  `discount_amount` decimal(15, 2) NOT NULL DEFAULT 0.00,
  `tax_category_id` bigint UNSIGNED NULL DEFAULT NULL,
  `tax_rate` decimal(8, 4) NOT NULL DEFAULT 0.0000,
  `tax_amount` decimal(15, 2) NOT NULL DEFAULT 0.00,
  `line_total` decimal(15, 2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `purchase_order_lines_product_id_foreign`(`product_id` ASC) USING BTREE,
  INDEX `purchase_order_lines_unit_id_foreign`(`unit_id` ASC) USING BTREE,
  INDEX `purchase_order_lines_tax_category_id_foreign`(`tax_category_id` ASC) USING BTREE,
  INDEX `purchase_order_lines_purchase_order_id_product_id_index`(`purchase_order_id` ASC, `product_id` ASC) USING BTREE,
  CONSTRAINT `purchase_order_lines_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `purchase_order_lines_purchase_order_id_foreign` FOREIGN KEY (`purchase_order_id`) REFERENCES `purchase_orders` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `purchase_order_lines_tax_category_id_foreign` FOREIGN KEY (`tax_category_id`) REFERENCES `tax_categories` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT,
  CONSTRAINT `purchase_order_lines_unit_id_foreign` FOREIGN KEY (`unit_id`) REFERENCES `units` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of purchase_order_lines
-- ----------------------------
INSERT INTO `purchase_order_lines` VALUES (1, 1, 1, 9, 5000.0000, 1.0000, 5000, 0, 1.2000, 5.0000, 300.00, NULL, 0.0000, 0.00, 5700.00, '2026-10-18 22:17:02', '2026-10-18 22:17:02');
INSERT INTO `purchase_order_lines` VALUES (2, 2, 1, 9, 5000.0000, 1.0000, 5000, 5000, 1.2000, 5.0000, 300.00, NULL, 0.0000, 0.00, 5700.00, '2026-10-18 22:17:53', '2026-10-18 22:17:53');

-- ----------------------------
-- Table structure for purchase_orders
-- ----------------------------
DROP TABLE IF EXISTS `purchase_orders`;
CREATE TABLE `purchase_orders`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `po_number` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `fiscal_year_id` bigint UNSIGNED NOT NULL,
  `supplier_id` bigint UNSIGNED NOT NULL,
  `warehouse_id` bigint UNSIGNED NOT NULL,
  `order_date_ad` date NOT NULL,
  `order_date_bs` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `expected_delivery_date_ad` date NULL DEFAULT NULL,
  `expected_delivery_date_bs` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `status` enum('draft','approved','partially_received','received','cancelled','closed') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'draft',
  `subtotal` decimal(15, 2) NOT NULL DEFAULT 0.00,
  `discount_amount` decimal(15, 2) NOT NULL DEFAULT 0.00,
  `taxable_amount` decimal(15, 2) NOT NULL DEFAULT 0.00,
  `tax_amount` decimal(15, 2) NOT NULL DEFAULT 0.00,
  `total_amount` decimal(15, 2) NOT NULL DEFAULT 0.00,
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL,
  `approved_by` bigint UNSIGNED NULL DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint UNSIGNED NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `purchase_orders_po_number_unique`(`po_number` ASC) USING BTREE,
  INDEX `purchase_orders_fiscal_year_id_foreign`(`fiscal_year_id` ASC) USING BTREE,
  INDEX `purchase_orders_warehouse_id_foreign`(`warehouse_id` ASC) USING BTREE,
  INDEX `purchase_orders_approved_by_foreign`(`approved_by` ASC) USING BTREE,
  INDEX `purchase_orders_created_by_foreign`(`created_by` ASC) USING BTREE,
  INDEX `purchase_orders_supplier_id_status_index`(`supplier_id` ASC, `status` ASC) USING BTREE,
  INDEX `purchase_orders_order_date_ad_index`(`order_date_ad` ASC) USING BTREE,
  INDEX `purchase_orders_status_index`(`status` ASC) USING BTREE,
  CONSTRAINT `purchase_orders_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `admins` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT,
  CONSTRAINT `purchase_orders_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `admins` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT,
  CONSTRAINT `purchase_orders_fiscal_year_id_foreign` FOREIGN KEY (`fiscal_year_id`) REFERENCES `fiscal_years` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `purchase_orders_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `purchase_orders_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of purchase_orders
-- ----------------------------
INSERT INTO `purchase_orders` VALUES (1, 'PO-8182-00001', 1, 1, 1, '2026-10-18', '2083-07-01', NULL, NULL, 'draft', 6000.00, 300.00, 5700.00, 0.00, 5700.00, 'Monthly bulk replenishment order', NULL, NULL, NULL, '2026-10-18 22:17:02', '2026-10-18 22:17:02', NULL);
INSERT INTO `purchase_orders` VALUES (2, 'PO-8182-00002', 1, 1, 1, '2026-10-18', '2083-07-01', NULL, NULL, 'received', 6000.00, 300.00, 5700.00, 0.00, 5700.00, 'Monthly bulk replenishment order', NULL, '2026-10-18 22:17:53', NULL, '2026-10-18 22:17:53', '2026-10-18 22:17:53', NULL);

-- ----------------------------
-- Table structure for purchase_return_lines
-- ----------------------------
DROP TABLE IF EXISTS `purchase_return_lines`;
CREATE TABLE `purchase_return_lines`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `purchase_return_id` bigint UNSIGNED NOT NULL,
  `goods_receipt_line_id` bigint UNSIGNED NULL DEFAULT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  `batch_id` bigint UNSIGNED NOT NULL,
  `location_id` bigint UNSIGNED NOT NULL,
  `unit_id` bigint UNSIGNED NOT NULL,
  `returned_qty` decimal(12, 4) NOT NULL,
  `conversion_factor` decimal(12, 4) NOT NULL DEFAULT 1.0000,
  `returned_base_qty` bigint UNSIGNED NOT NULL,
  `unit_price` decimal(15, 4) NOT NULL,
  `tax_amount` decimal(15, 2) NOT NULL DEFAULT 0.00,
  `line_total` decimal(15, 2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `purchase_return_lines_purchase_return_id_foreign`(`purchase_return_id` ASC) USING BTREE,
  INDEX `purchase_return_lines_goods_receipt_line_id_foreign`(`goods_receipt_line_id` ASC) USING BTREE,
  INDEX `purchase_return_lines_product_id_foreign`(`product_id` ASC) USING BTREE,
  INDEX `purchase_return_lines_batch_id_foreign`(`batch_id` ASC) USING BTREE,
  INDEX `purchase_return_lines_location_id_foreign`(`location_id` ASC) USING BTREE,
  INDEX `purchase_return_lines_unit_id_foreign`(`unit_id` ASC) USING BTREE,
  CONSTRAINT `purchase_return_lines_batch_id_foreign` FOREIGN KEY (`batch_id`) REFERENCES `batches` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `purchase_return_lines_goods_receipt_line_id_foreign` FOREIGN KEY (`goods_receipt_line_id`) REFERENCES `goods_receipt_lines` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT,
  CONSTRAINT `purchase_return_lines_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `warehouse_locations` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `purchase_return_lines_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `purchase_return_lines_purchase_return_id_foreign` FOREIGN KEY (`purchase_return_id`) REFERENCES `purchase_returns` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `purchase_return_lines_unit_id_foreign` FOREIGN KEY (`unit_id`) REFERENCES `units` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of purchase_return_lines
-- ----------------------------

-- ----------------------------
-- Table structure for purchase_returns
-- ----------------------------
DROP TABLE IF EXISTS `purchase_returns`;
CREATE TABLE `purchase_returns`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `return_number` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `fiscal_year_id` bigint UNSIGNED NOT NULL,
  `goods_receipt_id` bigint UNSIGNED NULL DEFAULT NULL,
  `supplier_id` bigint UNSIGNED NOT NULL,
  `warehouse_id` bigint UNSIGNED NOT NULL,
  `return_date_ad` date NOT NULL,
  `return_date_bs` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `subtotal` decimal(15, 2) NOT NULL DEFAULT 0.00,
  `tax_amount` decimal(15, 2) NOT NULL DEFAULT 0.00,
  `total_amount` decimal(15, 2) NOT NULL DEFAULT 0.00,
  `reason` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL,
  `status` enum('draft','posted','cancelled') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'draft',
  `posted_by` bigint UNSIGNED NULL DEFAULT NULL,
  `posted_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint UNSIGNED NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `purchase_returns_return_number_unique`(`return_number` ASC) USING BTREE,
  INDEX `purchase_returns_fiscal_year_id_foreign`(`fiscal_year_id` ASC) USING BTREE,
  INDEX `purchase_returns_goods_receipt_id_foreign`(`goods_receipt_id` ASC) USING BTREE,
  INDEX `purchase_returns_supplier_id_foreign`(`supplier_id` ASC) USING BTREE,
  INDEX `purchase_returns_warehouse_id_foreign`(`warehouse_id` ASC) USING BTREE,
  INDEX `purchase_returns_posted_by_foreign`(`posted_by` ASC) USING BTREE,
  INDEX `purchase_returns_created_by_foreign`(`created_by` ASC) USING BTREE,
  INDEX `purchase_returns_return_date_ad_index`(`return_date_ad` ASC) USING BTREE,
  INDEX `purchase_returns_status_index`(`status` ASC) USING BTREE,
  CONSTRAINT `purchase_returns_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `admins` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT,
  CONSTRAINT `purchase_returns_fiscal_year_id_foreign` FOREIGN KEY (`fiscal_year_id`) REFERENCES `fiscal_years` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `purchase_returns_goods_receipt_id_foreign` FOREIGN KEY (`goods_receipt_id`) REFERENCES `goods_receipts` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT,
  CONSTRAINT `purchase_returns_posted_by_foreign` FOREIGN KEY (`posted_by`) REFERENCES `admins` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT,
  CONSTRAINT `purchase_returns_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `purchase_returns_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of purchase_returns
-- ----------------------------

-- ----------------------------
-- Table structure for roles
-- ----------------------------
DROP TABLE IF EXISTS `roles`;
CREATE TABLE `roles`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `roles_name_unique`(`name` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 12 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of roles
-- ----------------------------
INSERT INTO `roles` VALUES (1, 'Super Admin', '2026-10-18 21:44:22', '2026-10-18 21:44:22');
INSERT INTO `roles` VALUES (2, 'Admin', '2026-10-18 21:44:22', '2026-10-18 21:44:22');
INSERT INTO `roles` VALUES (3, 'Business Owner', '2026-10-18 23:15:48', '2026-10-18 23:15:48');
INSERT INTO `roles` VALUES (4, 'System Administrator', '2026-10-18 23:15:49', '2026-10-18 23:15:49');
INSERT INTO `roles` VALUES (5, 'Inventory Manager', '2026-10-18 23:15:49', '2026-10-18 23:15:49');
INSERT INTO `roles` VALUES (6, 'Purchase Officer', '2026-10-18 23:15:49', '2026-10-18 23:15:49');
INSERT INTO `roles` VALUES (7, 'Sales Officer', '2026-10-18 23:15:50', '2026-10-18 23:15:50');
INSERT INTO `roles` VALUES (8, 'Warehouse Staff', '2026-10-18 23:15:50', '2026-10-18 23:15:50');
INSERT INTO `roles` VALUES (9, 'Accountant', '2026-10-18 23:15:50', '2026-10-18 23:15:50');
INSERT INTO `roles` VALUES (10, 'Cashier', '2026-10-18 23:15:50', '2026-10-18 23:15:50');
INSERT INTO `roles` VALUES (11, 'Auditor / Read-Only User', '2026-10-18 23:15:51', '2026-10-18 23:15:51');

-- ----------------------------
-- Table structure for sale_invoice_lines
-- ----------------------------
DROP TABLE IF EXISTS `sale_invoice_lines`;
CREATE TABLE `sale_invoice_lines`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `sale_invoice_id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  `batch_id` bigint UNSIGNED NOT NULL,
  `location_id` bigint UNSIGNED NOT NULL,
  `unit_id` bigint UNSIGNED NOT NULL,
  `product_code` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `product_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `batch_number` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `expiry_date_ad` date NOT NULL,
  `expiry_date_bs` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `unit_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `qty` decimal(12, 4) NOT NULL,
  `conversion_factor` decimal(12, 4) NOT NULL DEFAULT 1.0000,
  `base_qty` bigint UNSIGNED NOT NULL,
  `unit_price` decimal(15, 4) NOT NULL,
  `discount_pct` decimal(8, 4) NOT NULL DEFAULT 0.0000,
  `discount_amount` decimal(15, 2) NOT NULL DEFAULT 0.00,
  `tax_category_id` bigint UNSIGNED NULL DEFAULT NULL,
  `tax_rate` decimal(8, 4) NOT NULL DEFAULT 0.0000,
  `tax_amount` decimal(15, 2) NOT NULL DEFAULT 0.00,
  `line_total` decimal(15, 2) NOT NULL DEFAULT 0.00,
  `unit_cost` decimal(15, 4) NOT NULL DEFAULT 0.0000,
  `cogs_amount` decimal(15, 2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `sale_invoice_lines_product_id_foreign`(`product_id` ASC) USING BTREE,
  INDEX `sale_invoice_lines_batch_id_foreign`(`batch_id` ASC) USING BTREE,
  INDEX `sale_invoice_lines_location_id_foreign`(`location_id` ASC) USING BTREE,
  INDEX `sale_invoice_lines_unit_id_foreign`(`unit_id` ASC) USING BTREE,
  INDEX `sale_invoice_lines_tax_category_id_foreign`(`tax_category_id` ASC) USING BTREE,
  INDEX `sale_invoice_lines_sale_invoice_id_product_id_index`(`sale_invoice_id` ASC, `product_id` ASC) USING BTREE,
  CONSTRAINT `sale_invoice_lines_batch_id_foreign` FOREIGN KEY (`batch_id`) REFERENCES `batches` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `sale_invoice_lines_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `warehouse_locations` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `sale_invoice_lines_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `sale_invoice_lines_sale_invoice_id_foreign` FOREIGN KEY (`sale_invoice_id`) REFERENCES `sale_invoices` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `sale_invoice_lines_tax_category_id_foreign` FOREIGN KEY (`tax_category_id`) REFERENCES `tax_categories` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT,
  CONSTRAINT `sale_invoice_lines_unit_id_foreign` FOREIGN KEY (`unit_id`) REFERENCES `units` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of sale_invoice_lines
-- ----------------------------

-- ----------------------------
-- Table structure for sale_invoices
-- ----------------------------
DROP TABLE IF EXISTS `sale_invoices`;
CREATE TABLE `sale_invoices`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `invoice_number` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `fiscal_year_id` bigint UNSIGNED NOT NULL,
  `customer_id` bigint UNSIGNED NULL DEFAULT NULL,
  `customer_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `customer_pan` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `warehouse_id` bigint UNSIGNED NOT NULL,
  `invoice_date_ad` date NOT NULL,
  `invoice_date_bs` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `salesperson_id` bigint UNSIGNED NULL DEFAULT NULL,
  `payment_method` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'credit',
  `subtotal` decimal(15, 2) NOT NULL DEFAULT 0.00,
  `discount_amount` decimal(15, 2) NOT NULL DEFAULT 0.00,
  `taxable_amount` decimal(15, 2) NOT NULL DEFAULT 0.00,
  `non_taxable_amount` decimal(15, 2) NOT NULL DEFAULT 0.00,
  `tax_amount` decimal(15, 2) NOT NULL DEFAULT 0.00,
  `delivery_charge` decimal(15, 2) NOT NULL DEFAULT 0.00,
  `round_off` decimal(15, 2) NOT NULL DEFAULT 0.00,
  `total_amount` decimal(15, 2) NOT NULL DEFAULT 0.00,
  `paid_amount` decimal(15, 2) NOT NULL DEFAULT 0.00,
  `credit_amount` decimal(15, 2) NOT NULL DEFAULT 0.00,
  `cogs_amount` decimal(15, 2) NOT NULL DEFAULT 0.00,
  `status` enum('draft','posted','cancelled') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'draft',
  `posted_by` bigint UNSIGNED NULL DEFAULT NULL,
  `posted_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint UNSIGNED NULL DEFAULT NULL,
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `sale_invoices_invoice_number_unique`(`invoice_number` ASC) USING BTREE,
  INDEX `sale_invoices_fiscal_year_id_foreign`(`fiscal_year_id` ASC) USING BTREE,
  INDEX `sale_invoices_warehouse_id_foreign`(`warehouse_id` ASC) USING BTREE,
  INDEX `sale_invoices_salesperson_id_foreign`(`salesperson_id` ASC) USING BTREE,
  INDEX `sale_invoices_posted_by_foreign`(`posted_by` ASC) USING BTREE,
  INDEX `sale_invoices_created_by_foreign`(`created_by` ASC) USING BTREE,
  INDEX `sale_invoices_customer_id_invoice_date_ad_index`(`customer_id` ASC, `invoice_date_ad` ASC) USING BTREE,
  INDEX `sale_invoices_invoice_date_ad_index`(`invoice_date_ad` ASC) USING BTREE,
  INDEX `sale_invoices_status_index`(`status` ASC) USING BTREE,
  CONSTRAINT `sale_invoices_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `admins` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT,
  CONSTRAINT `sale_invoices_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT,
  CONSTRAINT `sale_invoices_fiscal_year_id_foreign` FOREIGN KEY (`fiscal_year_id`) REFERENCES `fiscal_years` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `sale_invoices_posted_by_foreign` FOREIGN KEY (`posted_by`) REFERENCES `admins` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT,
  CONSTRAINT `sale_invoices_salesperson_id_foreign` FOREIGN KEY (`salesperson_id`) REFERENCES `admins` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT,
  CONSTRAINT `sale_invoices_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of sale_invoices
-- ----------------------------

-- ----------------------------
-- Table structure for sale_return_lines
-- ----------------------------
DROP TABLE IF EXISTS `sale_return_lines`;
CREATE TABLE `sale_return_lines`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `sale_return_id` bigint UNSIGNED NOT NULL,
  `sale_invoice_line_id` bigint UNSIGNED NULL DEFAULT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  `batch_id` bigint UNSIGNED NOT NULL,
  `location_id` bigint UNSIGNED NOT NULL,
  `unit_id` bigint UNSIGNED NOT NULL,
  `returned_qty` decimal(12, 4) NOT NULL,
  `conversion_factor` decimal(12, 4) NOT NULL DEFAULT 1.0000,
  `returned_base_qty` bigint UNSIGNED NOT NULL,
  `condition` enum('saleable','damaged','expired','quarantined') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'saleable',
  `unit_price` decimal(15, 4) NOT NULL,
  `tax_amount` decimal(15, 2) NOT NULL DEFAULT 0.00,
  `line_total` decimal(15, 2) NOT NULL DEFAULT 0.00,
  `restocked` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `sale_return_lines_sale_return_id_foreign`(`sale_return_id` ASC) USING BTREE,
  INDEX `sale_return_lines_sale_invoice_line_id_foreign`(`sale_invoice_line_id` ASC) USING BTREE,
  INDEX `sale_return_lines_product_id_foreign`(`product_id` ASC) USING BTREE,
  INDEX `sale_return_lines_batch_id_foreign`(`batch_id` ASC) USING BTREE,
  INDEX `sale_return_lines_location_id_foreign`(`location_id` ASC) USING BTREE,
  INDEX `sale_return_lines_unit_id_foreign`(`unit_id` ASC) USING BTREE,
  CONSTRAINT `sale_return_lines_batch_id_foreign` FOREIGN KEY (`batch_id`) REFERENCES `batches` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `sale_return_lines_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `warehouse_locations` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `sale_return_lines_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `sale_return_lines_sale_invoice_line_id_foreign` FOREIGN KEY (`sale_invoice_line_id`) REFERENCES `sale_invoice_lines` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT,
  CONSTRAINT `sale_return_lines_sale_return_id_foreign` FOREIGN KEY (`sale_return_id`) REFERENCES `sale_returns` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `sale_return_lines_unit_id_foreign` FOREIGN KEY (`unit_id`) REFERENCES `units` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of sale_return_lines
-- ----------------------------

-- ----------------------------
-- Table structure for sale_returns
-- ----------------------------
DROP TABLE IF EXISTS `sale_returns`;
CREATE TABLE `sale_returns`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `return_number` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `fiscal_year_id` bigint UNSIGNED NOT NULL,
  `sale_invoice_id` bigint UNSIGNED NULL DEFAULT NULL,
  `customer_id` bigint UNSIGNED NULL DEFAULT NULL,
  `warehouse_id` bigint UNSIGNED NOT NULL,
  `return_date_ad` date NOT NULL,
  `return_date_bs` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `subtotal` decimal(15, 2) NOT NULL DEFAULT 0.00,
  `tax_amount` decimal(15, 2) NOT NULL DEFAULT 0.00,
  `total_amount` decimal(15, 2) NOT NULL DEFAULT 0.00,
  `reason` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL,
  `credit_note_issued` tinyint(1) NOT NULL DEFAULT 1,
  `status` enum('draft','posted','cancelled') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'draft',
  `posted_by` bigint UNSIGNED NULL DEFAULT NULL,
  `posted_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint UNSIGNED NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `sale_returns_return_number_unique`(`return_number` ASC) USING BTREE,
  INDEX `sale_returns_fiscal_year_id_foreign`(`fiscal_year_id` ASC) USING BTREE,
  INDEX `sale_returns_sale_invoice_id_foreign`(`sale_invoice_id` ASC) USING BTREE,
  INDEX `sale_returns_customer_id_foreign`(`customer_id` ASC) USING BTREE,
  INDEX `sale_returns_warehouse_id_foreign`(`warehouse_id` ASC) USING BTREE,
  INDEX `sale_returns_posted_by_foreign`(`posted_by` ASC) USING BTREE,
  INDEX `sale_returns_created_by_foreign`(`created_by` ASC) USING BTREE,
  INDEX `sale_returns_return_date_ad_index`(`return_date_ad` ASC) USING BTREE,
  INDEX `sale_returns_status_index`(`status` ASC) USING BTREE,
  CONSTRAINT `sale_returns_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `admins` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT,
  CONSTRAINT `sale_returns_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT,
  CONSTRAINT `sale_returns_fiscal_year_id_foreign` FOREIGN KEY (`fiscal_year_id`) REFERENCES `fiscal_years` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `sale_returns_posted_by_foreign` FOREIGN KEY (`posted_by`) REFERENCES `admins` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT,
  CONSTRAINT `sale_returns_sale_invoice_id_foreign` FOREIGN KEY (`sale_invoice_id`) REFERENCES `sale_invoices` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT,
  CONSTRAINT `sale_returns_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of sale_returns
-- ----------------------------

-- ----------------------------
-- Table structure for sessions
-- ----------------------------
DROP TABLE IF EXISTS `sessions`;
CREATE TABLE `sessions`  (
  `id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `user_id` bigint UNSIGNED NULL DEFAULT NULL,
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `sessions_user_id_index`(`user_id` ASC) USING BTREE,
  INDEX `sessions_last_activity_index`(`last_activity` ASC) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of sessions
-- ----------------------------
INSERT INTO `sessions` VALUES ('2wlCQkNrb4hzPnrC29yn3nYgR36mN1WXGQqqvV11', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiMzJUZkFJbzUxTkk5bk04QTNLdGtUTmwwcnlndTlkMVZYTDFtREZhQyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NjM6Imh0dHA6Ly9sb2NhbGhvc3Qvc3RvY2tfbWFuYWdlbWVudF9zeXN0ZW0vYWRtaW4vcGhhcm1hL3N1cHBsaWVycyI7czo1OiJyb3V0ZSI7czoyMjoiYWRtaW4ucGhhcm1hLnN1cHBsaWVycyI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1793198734);
INSERT INTO `sessions` VALUES ('fSuTCiBFEKEWzw8TTq6kzVE44KBZL28mKAVMlJOs', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiN3gzNGxYQ3BkMlVOSWpyZDV1a2c5R09YUnlLWjlxTVk5WHlGaGRZWiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NjM6Imh0dHA6Ly9sb2NhbGhvc3Qvc3RvY2tfbWFuYWdlbWVudF9zeXN0ZW0vYWRtaW4vcGhhcm1hL3N1cHBsaWVycyI7czo1OiJyb3V0ZSI7czoyMjoiYWRtaW4ucGhhcm1hLnN1cHBsaWVycyI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1793198734);
INSERT INTO `sessions` VALUES ('OZ03NuG8YA8rCfJIuPKtLoYHqonxz6GHCh4pckKQ', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiaUFNTmx4VWJZQkFlQkxqTElnYTVObGNtUmJCVFJyRVVOY0oydEN4NyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NzM6Imh0dHA6Ly9sb2NhbGhvc3Qvc3RvY2tfbWFuYWdlbWVudF9zeXN0ZW0vYWRtaW4vcGhhcm1hL3NhbGUtaW52b2ljZS9jcmVhdGUiO3M6NToicm91dGUiO3M6MzI6ImFkbWluLnBoYXJtYS5zYWxlLWludm9pY2UuY3JlYXRlIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MjoibG9naW5fYWRtaW5fNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToyO3M6MTk6InBhc3N3b3JkX2hhc2hfYWRtaW4iO3M6NjQ6IjMxNWJhZGQ5NGFlY2VjOTNlN2JjZjM1YmI2OGQwMzYzYzkyNTg1NzAwMGY5NDQ4N2Y1ODk4YjFmOTQ5MThiOTEiO30=', 1793203165);
INSERT INTO `sessions` VALUES ('zDDfS3MImocIVGkNqjxv2IdmO0chO6Lyuy9O5lEt', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiWkp5bjhEeHVQNGJtN0NBb1FqVE0xbTFOWHR3eVhCR0loRExCUkVNVyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NjM6Imh0dHA6Ly9sb2NhbGhvc3Qvc3RvY2tfbWFuYWdlbWVudF9zeXN0ZW0vYWRtaW4vcGhhcm1hL3N1cHBsaWVycyI7czo1OiJyb3V0ZSI7czoyMjoiYWRtaW4ucGhhcm1hLnN1cHBsaWVycyI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1793198734);

-- ----------------------------
-- Table structure for settings
-- ----------------------------
DROP TABLE IF EXISTS `settings`;
CREATE TABLE `settings`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `key` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `value` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL,
  `group` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'general',
  `type` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'string',
  `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `settings_key_unique`(`key` ASC) USING BTREE,
  INDEX `settings_group_index`(`group` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 13 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of settings
-- ----------------------------
INSERT INTO `settings` VALUES (1, 'company_name', 'PharmaDistributors Nepal Pvt. Ltd.', 'company', 'string', 'Registered Entity Name', '2026-10-18 21:44:40', '2026-10-18 21:44:40');
INSERT INTO `settings` VALUES (2, 'company_pan_vat', '601234567', 'company', 'string', 'PAN / VAT Registration Number', '2026-10-18 21:44:40', '2026-10-18 21:44:40');
INSERT INTO `settings` VALUES (3, 'company_dda_reg_no', 'DDA-D-10294/080', 'company', 'string', 'Department of Drug Administration License No.', '2026-10-18 21:44:40', '2026-10-18 21:44:40');
INSERT INTO `settings` VALUES (4, 'company_phone', '+977-1-4412345', 'company', 'string', 'Head Office Phone', '2026-10-18 21:44:40', '2026-10-18 21:44:40');
INSERT INTO `settings` VALUES (5, 'company_email', 'info@pharmadistributors.com.np', 'company', 'string', 'Official Email', '2026-10-18 21:44:40', '2026-10-18 21:44:40');
INSERT INTO `settings` VALUES (6, 'company_address', 'Tripureshwor, Kathmandu, Nepal', 'company', 'string', 'Physical Address', '2026-10-18 21:44:40', '2026-10-18 21:44:40');
INSERT INTO `settings` VALUES (7, 'currency_symbol', 'NPR', 'financial', 'string', 'Currency code/symbol', '2026-10-18 21:44:40', '2026-10-18 21:44:40');
INSERT INTO `settings` VALUES (8, 'enforce_fefo', 'true', 'inventory', 'boolean', 'Enforce First-Expiry-First-Out sales allocation', '2026-10-18 21:44:40', '2026-10-18 21:44:40');
INSERT INTO `settings` VALUES (9, 'allow_negative_stock', 'false', 'inventory', 'boolean', 'Strictly prevent negative stock balances', '2026-10-18 21:44:40', '2026-10-18 21:44:40');
INSERT INTO `settings` VALUES (10, 'near_expiry_alert_days', '90', 'inventory', 'integer', 'Days before expiry to trigger alert', '2026-10-18 21:44:40', '2026-10-18 21:44:40');
INSERT INTO `settings` VALUES (11, 'require_adjustment_maker_checker', 'true', 'inventory', 'boolean', 'Stock adjustments require two-person approval', '2026-10-18 21:44:40', '2026-10-18 21:44:40');
INSERT INTO `settings` VALUES (12, 'maker_checker_threshold_amount', '10000', 'inventory', 'integer', 'Value above which 2nd approver is mandatory', '2026-10-18 21:44:40', '2026-10-18 21:44:40');

-- ----------------------------
-- Table structure for stock_adjustment_lines
-- ----------------------------
DROP TABLE IF EXISTS `stock_adjustment_lines`;
CREATE TABLE `stock_adjustment_lines`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `stock_adjustment_id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  `batch_id` bigint UNSIGNED NOT NULL,
  `location_id` bigint UNSIGNED NOT NULL,
  `book_quantity` int NOT NULL,
  `counted_quantity` int NOT NULL,
  `variance_quantity` int NOT NULL,
  `unit_cost` decimal(15, 4) NOT NULL DEFAULT 0.0000,
  `variance_value` decimal(15, 2) NOT NULL DEFAULT 0.00,
  `reason` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `stock_adjustment_lines_stock_adjustment_id_foreign`(`stock_adjustment_id` ASC) USING BTREE,
  INDEX `stock_adjustment_lines_product_id_foreign`(`product_id` ASC) USING BTREE,
  INDEX `stock_adjustment_lines_batch_id_foreign`(`batch_id` ASC) USING BTREE,
  INDEX `stock_adjustment_lines_location_id_foreign`(`location_id` ASC) USING BTREE,
  CONSTRAINT `stock_adjustment_lines_batch_id_foreign` FOREIGN KEY (`batch_id`) REFERENCES `batches` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `stock_adjustment_lines_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `warehouse_locations` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `stock_adjustment_lines_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `stock_adjustment_lines_stock_adjustment_id_foreign` FOREIGN KEY (`stock_adjustment_id`) REFERENCES `stock_adjustments` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of stock_adjustment_lines
-- ----------------------------

-- ----------------------------
-- Table structure for stock_adjustments
-- ----------------------------
DROP TABLE IF EXISTS `stock_adjustments`;
CREATE TABLE `stock_adjustments`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `adjustment_number` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `fiscal_year_id` bigint UNSIGNED NOT NULL,
  `warehouse_id` bigint UNSIGNED NOT NULL,
  `adjustment_date_ad` date NOT NULL,
  `adjustment_date_bs` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `type` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'PHYSICAL_COUNT',
  `status` enum('draft','approved','rejected') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'draft',
  `total_variance_value` decimal(15, 2) NOT NULL DEFAULT 0.00,
  `reason` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL,
  `created_by` bigint UNSIGNED NULL DEFAULT NULL,
  `approved_by` bigint UNSIGNED NULL DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `rejection_reason` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `stock_adjustments_adjustment_number_unique`(`adjustment_number` ASC) USING BTREE,
  INDEX `stock_adjustments_fiscal_year_id_foreign`(`fiscal_year_id` ASC) USING BTREE,
  INDEX `stock_adjustments_warehouse_id_foreign`(`warehouse_id` ASC) USING BTREE,
  INDEX `stock_adjustments_created_by_foreign`(`created_by` ASC) USING BTREE,
  INDEX `stock_adjustments_approved_by_foreign`(`approved_by` ASC) USING BTREE,
  INDEX `stock_adjustments_adjustment_date_ad_index`(`adjustment_date_ad` ASC) USING BTREE,
  INDEX `stock_adjustments_adjustment_date_bs_index`(`adjustment_date_bs` ASC) USING BTREE,
  INDEX `stock_adjustments_type_index`(`type` ASC) USING BTREE,
  INDEX `stock_adjustments_status_index`(`status` ASC) USING BTREE,
  CONSTRAINT `stock_adjustments_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `admins` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT,
  CONSTRAINT `stock_adjustments_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `admins` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT,
  CONSTRAINT `stock_adjustments_fiscal_year_id_foreign` FOREIGN KEY (`fiscal_year_id`) REFERENCES `fiscal_years` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `stock_adjustments_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of stock_adjustments
-- ----------------------------

-- ----------------------------
-- Table structure for stock_movements
-- ----------------------------
DROP TABLE IF EXISTS `stock_movements`;
CREATE TABLE `stock_movements`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `fiscal_year_id` bigint UNSIGNED NULL DEFAULT NULL,
  `warehouse_id` bigint UNSIGNED NOT NULL,
  `location_id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  `batch_id` bigint UNSIGNED NOT NULL,
  `movement_type` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `quantity` bigint NOT NULL,
  `balance_after` bigint NULL DEFAULT NULL,
  `unit_cost` decimal(15, 4) NOT NULL DEFAULT 0.0000,
  `total_cost` decimal(15, 2) NOT NULL DEFAULT 0.00,
  `reference_type` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `reference_id` bigint UNSIGNED NULL DEFAULT NULL,
  `from_location_id` bigint UNSIGNED NULL DEFAULT NULL,
  `to_location_id` bigint UNSIGNED NULL DEFAULT NULL,
  `created_by` bigint UNSIGNED NULL DEFAULT NULL,
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `stock_movements_fiscal_year_id_foreign`(`fiscal_year_id` ASC) USING BTREE,
  INDEX `stock_movements_location_id_foreign`(`location_id` ASC) USING BTREE,
  INDEX `stock_movements_batch_id_foreign`(`batch_id` ASC) USING BTREE,
  INDEX `stock_movements_from_location_id_foreign`(`from_location_id` ASC) USING BTREE,
  INDEX `stock_movements_to_location_id_foreign`(`to_location_id` ASC) USING BTREE,
  INDEX `stock_movements_created_by_foreign`(`created_by` ASC) USING BTREE,
  INDEX `sm_wh_prod_created_idx`(`warehouse_id` ASC, `product_id` ASC, `created_at` ASC) USING BTREE,
  INDEX `sm_prod_batch_created_idx`(`product_id` ASC, `batch_id` ASC, `created_at` ASC) USING BTREE,
  INDEX `stock_movements_movement_type_index`(`movement_type` ASC) USING BTREE,
  INDEX `stock_movements_reference_type_index`(`reference_type` ASC) USING BTREE,
  INDEX `stock_movements_reference_id_index`(`reference_id` ASC) USING BTREE,
  INDEX `stock_movements_created_at_index`(`created_at` ASC) USING BTREE,
  CONSTRAINT `stock_movements_batch_id_foreign` FOREIGN KEY (`batch_id`) REFERENCES `batches` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `stock_movements_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `admins` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT,
  CONSTRAINT `stock_movements_fiscal_year_id_foreign` FOREIGN KEY (`fiscal_year_id`) REFERENCES `fiscal_years` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT,
  CONSTRAINT `stock_movements_from_location_id_foreign` FOREIGN KEY (`from_location_id`) REFERENCES `warehouse_locations` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT,
  CONSTRAINT `stock_movements_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `warehouse_locations` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `stock_movements_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `stock_movements_to_location_id_foreign` FOREIGN KEY (`to_location_id`) REFERENCES `warehouse_locations` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT,
  CONSTRAINT `stock_movements_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 5 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of stock_movements
-- ----------------------------
INSERT INTO `stock_movements` VALUES (1, 1, 1, 1, 1, 1, 'OPENING_STOCK', 2000, 2000, 1.2000, 2400.00, NULL, NULL, NULL, NULL, NULL, 'Initial inventory opening balance', '2026-10-18 22:06:40');
INSERT INTO `stock_movements` VALUES (2, 1, 1, 1, 1, 2, 'OPENING_STOCK', 5000, 5000, 1.2500, 6250.00, NULL, NULL, NULL, NULL, NULL, 'Initial inventory opening balance', '2026-10-18 22:06:40');
INSERT INTO `stock_movements` VALUES (3, 1, 1, 4, 3, 5, 'OPENING_STOCK', 150, 150, 320.0000, 48000.00, NULL, NULL, NULL, NULL, NULL, 'Cold chain opening stock', '2026-10-18 22:06:40');
INSERT INTO `stock_movements` VALUES (4, 1, 1, 1, 1, 6, 'PURCHASE_RECEIPT', 5000, 5000, 1.2065, 6032.50, 'GoodsReceipt', 1, NULL, NULL, NULL, 'GRN #GRN-8182-00001 (Supplier Inv: DJ-INV-2026-089)', '2026-10-18 22:17:53');

-- ----------------------------
-- Table structure for stock_transfer_lines
-- ----------------------------
DROP TABLE IF EXISTS `stock_transfer_lines`;
CREATE TABLE `stock_transfer_lines`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `stock_transfer_id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  `batch_id` bigint UNSIGNED NOT NULL,
  `from_location_id` bigint UNSIGNED NOT NULL,
  `to_location_id` bigint UNSIGNED NULL DEFAULT NULL,
  `quantity` int UNSIGNED NOT NULL,
  `unit_cost` decimal(15, 4) NOT NULL DEFAULT 0.0000,
  `status` enum('pending','in_transit','received','cancelled') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `stock_transfer_lines_stock_transfer_id_foreign`(`stock_transfer_id` ASC) USING BTREE,
  INDEX `stock_transfer_lines_product_id_foreign`(`product_id` ASC) USING BTREE,
  INDEX `stock_transfer_lines_batch_id_foreign`(`batch_id` ASC) USING BTREE,
  INDEX `stock_transfer_lines_from_location_id_foreign`(`from_location_id` ASC) USING BTREE,
  INDEX `stock_transfer_lines_to_location_id_foreign`(`to_location_id` ASC) USING BTREE,
  INDEX `stock_transfer_lines_status_index`(`status` ASC) USING BTREE,
  CONSTRAINT `stock_transfer_lines_batch_id_foreign` FOREIGN KEY (`batch_id`) REFERENCES `batches` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `stock_transfer_lines_from_location_id_foreign` FOREIGN KEY (`from_location_id`) REFERENCES `warehouse_locations` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `stock_transfer_lines_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `stock_transfer_lines_stock_transfer_id_foreign` FOREIGN KEY (`stock_transfer_id`) REFERENCES `stock_transfers` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `stock_transfer_lines_to_location_id_foreign` FOREIGN KEY (`to_location_id`) REFERENCES `warehouse_locations` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of stock_transfer_lines
-- ----------------------------

-- ----------------------------
-- Table structure for stock_transfers
-- ----------------------------
DROP TABLE IF EXISTS `stock_transfers`;
CREATE TABLE `stock_transfers`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `transfer_number` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `fiscal_year_id` bigint UNSIGNED NOT NULL,
  `from_warehouse_id` bigint UNSIGNED NOT NULL,
  `to_warehouse_id` bigint UNSIGNED NOT NULL,
  `transfer_date_ad` date NOT NULL,
  `transfer_date_bs` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `status` enum('draft','dispatched','received','cancelled') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'draft',
  `total_items` int UNSIGNED NOT NULL DEFAULT 0,
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL,
  `created_by` bigint UNSIGNED NULL DEFAULT NULL,
  `dispatched_by` bigint UNSIGNED NULL DEFAULT NULL,
  `dispatched_at` timestamp NULL DEFAULT NULL,
  `received_by` bigint UNSIGNED NULL DEFAULT NULL,
  `received_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `stock_transfers_transfer_number_unique`(`transfer_number` ASC) USING BTREE,
  INDEX `stock_transfers_fiscal_year_id_foreign`(`fiscal_year_id` ASC) USING BTREE,
  INDEX `stock_transfers_from_warehouse_id_foreign`(`from_warehouse_id` ASC) USING BTREE,
  INDEX `stock_transfers_to_warehouse_id_foreign`(`to_warehouse_id` ASC) USING BTREE,
  INDEX `stock_transfers_created_by_foreign`(`created_by` ASC) USING BTREE,
  INDEX `stock_transfers_dispatched_by_foreign`(`dispatched_by` ASC) USING BTREE,
  INDEX `stock_transfers_received_by_foreign`(`received_by` ASC) USING BTREE,
  INDEX `stock_transfers_transfer_date_ad_index`(`transfer_date_ad` ASC) USING BTREE,
  INDEX `stock_transfers_transfer_date_bs_index`(`transfer_date_bs` ASC) USING BTREE,
  INDEX `stock_transfers_status_index`(`status` ASC) USING BTREE,
  CONSTRAINT `stock_transfers_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `admins` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT,
  CONSTRAINT `stock_transfers_dispatched_by_foreign` FOREIGN KEY (`dispatched_by`) REFERENCES `admins` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT,
  CONSTRAINT `stock_transfers_fiscal_year_id_foreign` FOREIGN KEY (`fiscal_year_id`) REFERENCES `fiscal_years` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `stock_transfers_from_warehouse_id_foreign` FOREIGN KEY (`from_warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `stock_transfers_received_by_foreign` FOREIGN KEY (`received_by`) REFERENCES `admins` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT,
  CONSTRAINT `stock_transfers_to_warehouse_id_foreign` FOREIGN KEY (`to_warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of stock_transfers
-- ----------------------------

-- ----------------------------
-- Table structure for subject_committee_users
-- ----------------------------
DROP TABLE IF EXISTS `subject_committee_users`;
CREATE TABLE `subject_committee_users`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED NULL DEFAULT NULL,
  `subject_committee_id` bigint UNSIGNED NULL DEFAULT NULL,
  `signature_image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `coordinator` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `subject_committee_users_user_id_foreign`(`user_id` ASC) USING BTREE,
  INDEX `subject_committee_users_subject_committee_id_foreign`(`subject_committee_id` ASC) USING BTREE,
  CONSTRAINT `subject_committee_users_subject_committee_id_foreign` FOREIGN KEY (`subject_committee_id`) REFERENCES `subject_committees` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `subject_committee_users_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `admins` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of subject_committee_users
-- ----------------------------

-- ----------------------------
-- Table structure for subject_committees
-- ----------------------------
DROP TABLE IF EXISTS `subject_committees`;
CREATE TABLE `subject_committees`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '0 = hide, 1 = show',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of subject_committees
-- ----------------------------

-- ----------------------------
-- Table structure for supplier_ledger_entries
-- ----------------------------
DROP TABLE IF EXISTS `supplier_ledger_entries`;
CREATE TABLE `supplier_ledger_entries`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `fiscal_year_id` bigint UNSIGNED NOT NULL,
  `supplier_id` bigint UNSIGNED NOT NULL,
  `entry_date_ad` date NOT NULL,
  `entry_date_bs` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `transaction_type` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `reference_type` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `reference_id` bigint UNSIGNED NOT NULL,
  `reference_number` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `debit_amount` decimal(15, 2) NOT NULL DEFAULT 0.00,
  `credit_amount` decimal(15, 2) NOT NULL DEFAULT 0.00,
  `running_balance` decimal(15, 2) NOT NULL DEFAULT 0.00,
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL,
  `created_by` bigint UNSIGNED NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `supplier_ledger_entries_fiscal_year_id_foreign`(`fiscal_year_id` ASC) USING BTREE,
  INDEX `supplier_ledger_entries_created_by_foreign`(`created_by` ASC) USING BTREE,
  INDEX `sle_supplier_created_idx`(`supplier_id` ASC, `created_at` ASC) USING BTREE,
  INDEX `supplier_ledger_entries_entry_date_ad_index`(`entry_date_ad` ASC) USING BTREE,
  INDEX `supplier_ledger_entries_transaction_type_index`(`transaction_type` ASC) USING BTREE,
  INDEX `supplier_ledger_entries_reference_type_index`(`reference_type` ASC) USING BTREE,
  INDEX `supplier_ledger_entries_reference_id_index`(`reference_id` ASC) USING BTREE,
  INDEX `supplier_ledger_entries_reference_number_index`(`reference_number` ASC) USING BTREE,
  INDEX `supplier_ledger_entries_created_at_index`(`created_at` ASC) USING BTREE,
  CONSTRAINT `supplier_ledger_entries_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `admins` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT,
  CONSTRAINT `supplier_ledger_entries_fiscal_year_id_foreign` FOREIGN KEY (`fiscal_year_id`) REFERENCES `fiscal_years` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `supplier_ledger_entries_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of supplier_ledger_entries
-- ----------------------------
INSERT INTO `supplier_ledger_entries` VALUES (1, 1, 1, '2026-10-18', '2083-07-01', 'PURCHASE_INVOICE', 'GoodsReceipt', 1, 'GRN-8182-00001', 0.00, 5700.00, 5700.00, 'Invoice #DJ-INV-2026-089 received via GRN #GRN-8182-00001', NULL, '2026-10-18 22:17:53');
INSERT INTO `supplier_ledger_entries` VALUES (2, 1, 1, '2026-10-18', '2083-07-01', 'PAYMENT', 'SupplierPayment', 1, 'SPMT-8182-00001', 1000.00, 0.00, 4700.00, 'Payment #SPMT-8182-00001 (bank_transfer)', NULL, '2026-10-18 22:40:32');

-- ----------------------------
-- Table structure for supplier_payment_allocations
-- ----------------------------
DROP TABLE IF EXISTS `supplier_payment_allocations`;
CREATE TABLE `supplier_payment_allocations`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `supplier_payment_id` bigint UNSIGNED NOT NULL,
  `goods_receipt_id` bigint UNSIGNED NOT NULL,
  `allocated_amount` decimal(15, 2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `supplier_payment_allocations_supplier_payment_id_foreign`(`supplier_payment_id` ASC) USING BTREE,
  INDEX `supplier_payment_allocations_goods_receipt_id_foreign`(`goods_receipt_id` ASC) USING BTREE,
  CONSTRAINT `supplier_payment_allocations_goods_receipt_id_foreign` FOREIGN KEY (`goods_receipt_id`) REFERENCES `goods_receipts` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `supplier_payment_allocations_supplier_payment_id_foreign` FOREIGN KEY (`supplier_payment_id`) REFERENCES `supplier_payments` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of supplier_payment_allocations
-- ----------------------------
INSERT INTO `supplier_payment_allocations` VALUES (1, 1, 1, 1000.00, '2026-10-18 22:40:32', '2026-10-18 22:40:32');

-- ----------------------------
-- Table structure for supplier_payments
-- ----------------------------
DROP TABLE IF EXISTS `supplier_payments`;
CREATE TABLE `supplier_payments`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `payment_number` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `fiscal_year_id` bigint UNSIGNED NOT NULL,
  `supplier_id` bigint UNSIGNED NOT NULL,
  `cash_bank_account_id` bigint UNSIGNED NOT NULL,
  `payment_date_ad` date NOT NULL,
  `payment_date_bs` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `payment_method` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'bank_transfer',
  `amount` decimal(15, 2) NOT NULL,
  `allocated_amount` decimal(15, 2) NOT NULL DEFAULT 0.00,
  `unallocated_amount` decimal(15, 2) NOT NULL DEFAULT 0.00,
  `cheque_number` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `cheque_date` date NULL DEFAULT NULL,
  `bank_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `transaction_reference` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `status` enum('draft','posted','reversed') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'draft',
  `created_by` bigint UNSIGNED NULL DEFAULT NULL,
  `posted_by` bigint UNSIGNED NULL DEFAULT NULL,
  `posted_at` timestamp NULL DEFAULT NULL,
  `reversed_by` bigint UNSIGNED NULL DEFAULT NULL,
  `reversed_at` timestamp NULL DEFAULT NULL,
  `reversal_reason` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL,
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `supplier_payments_payment_number_unique`(`payment_number` ASC) USING BTREE,
  INDEX `supplier_payments_fiscal_year_id_foreign`(`fiscal_year_id` ASC) USING BTREE,
  INDEX `supplier_payments_supplier_id_foreign`(`supplier_id` ASC) USING BTREE,
  INDEX `supplier_payments_cash_bank_account_id_foreign`(`cash_bank_account_id` ASC) USING BTREE,
  INDEX `supplier_payments_created_by_foreign`(`created_by` ASC) USING BTREE,
  INDEX `supplier_payments_posted_by_foreign`(`posted_by` ASC) USING BTREE,
  INDEX `supplier_payments_reversed_by_foreign`(`reversed_by` ASC) USING BTREE,
  INDEX `supplier_payments_payment_date_ad_index`(`payment_date_ad` ASC) USING BTREE,
  INDEX `supplier_payments_payment_date_bs_index`(`payment_date_bs` ASC) USING BTREE,
  INDEX `supplier_payments_status_index`(`status` ASC) USING BTREE,
  CONSTRAINT `supplier_payments_cash_bank_account_id_foreign` FOREIGN KEY (`cash_bank_account_id`) REFERENCES `cash_bank_accounts` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `supplier_payments_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `admins` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT,
  CONSTRAINT `supplier_payments_fiscal_year_id_foreign` FOREIGN KEY (`fiscal_year_id`) REFERENCES `fiscal_years` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `supplier_payments_posted_by_foreign` FOREIGN KEY (`posted_by`) REFERENCES `admins` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT,
  CONSTRAINT `supplier_payments_reversed_by_foreign` FOREIGN KEY (`reversed_by`) REFERENCES `admins` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT,
  CONSTRAINT `supplier_payments_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of supplier_payments
-- ----------------------------
INSERT INTO `supplier_payments` VALUES (1, 'SPMT-8182-00001', 1, 1, 2, '2026-10-18', '2083-07-01', 'bank_transfer', 1000.00, 1000.00, 0.00, NULL, NULL, NULL, 'NABIL-VOUCHER-0012', 'posted', NULL, NULL, '2026-10-18 22:40:32', NULL, NULL, NULL, 'Pharma Seeded Demo Payment', '2026-10-18 22:40:32', '2026-10-18 22:40:32', NULL);

-- ----------------------------
-- Table structure for suppliers
-- ----------------------------
DROP TABLE IF EXISTS `suppliers`;
CREATE TABLE `suppliers`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `code` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `contact_person` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `phone` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `pan_vat_number` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `dda_license_number` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `address` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL,
  `credit_period_days` int UNSIGNED NOT NULL DEFAULT 30,
  `credit_limit` decimal(15, 2) NOT NULL DEFAULT 0.00,
  `opening_balance` decimal(15, 2) NOT NULL DEFAULT 0.00,
  `current_balance` decimal(15, 2) NOT NULL DEFAULT 0.00,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `suppliers_code_unique`(`code` ASC) USING BTREE,
  INDEX `suppliers_name_index`(`name` ASC) USING BTREE,
  INDEX `suppliers_pan_vat_number_index`(`pan_vat_number` ASC) USING BTREE,
  INDEX `suppliers_is_active_index`(`is_active` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 4 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of suppliers
-- ----------------------------
INSERT INTO `suppliers` VALUES (1, 'SUP-DJPL-DIST', 'Deurali-Janta Distribution Division', 'Ramesh Shrestha', '+977-1-4351122', 'distribution@deuralijanta.com', '300123456', 'DDA-W-012/075', 'Dhapasi, Kathmandu', 45, 2000000.00, 0.00, 4700.00, 1, '2026-10-18 22:17:02', '2026-10-18 22:40:32', NULL);
INSERT INTO `suppliers` VALUES (2, 'SUP-NPL-SALES', 'NPL Pharma Logistics & Sales', 'Suman Adhikari', '+977-1-4275566', 'orders@npl.com.np', '300987654', 'DDA-W-034/076', 'Kalanki, Kathmandu', 30, 1500000.00, 0.00, 0.00, 1, '2026-10-18 22:17:02', '2026-10-18 22:17:02', NULL);
INSERT INTO `suppliers` VALUES (3, 'SUP-MEDISALES', 'Medisales International Nepal Pvt. Ltd.', 'Bikram Thapa', '+977-1-4433221', 'sales@medisales.com.np', '601876543', 'DDA-W-056/077', 'Tripureshwor, Kathmandu', 60, 3000000.00, 0.00, 0.00, 1, '2026-10-18 22:17:02', '2026-10-18 22:17:02', NULL);

-- ----------------------------
-- Table structure for tax_categories
-- ----------------------------
DROP TABLE IF EXISTS `tax_categories`;
CREATE TABLE `tax_categories`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `code` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `rate` decimal(8, 4) NOT NULL DEFAULT 0.0000,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `tax_categories_code_unique`(`code` ASC) USING BTREE,
  INDEX `tax_categories_is_active_index`(`is_active` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of tax_categories
-- ----------------------------
INSERT INTO `tax_categories` VALUES (1, 'VAT-EXEMPT', 'VAT Exempt Medicines (Schedule 1)', 0.0000, 'Essential and life-saving pharmaceuticals exempt from VAT in Nepal.', 1, '2026-10-18 21:44:41', '2026-10-18 21:44:41');
INSERT INTO `tax_categories` VALUES (2, 'VAT-STANDARD', 'Standard VAT (Configurable)', 13.0000, 'Standard rate if product or non-medicine item is VAT taxable.', 1, '2026-10-18 21:44:41', '2026-10-18 21:44:41');

-- ----------------------------
-- Table structure for units
-- ----------------------------
DROP TABLE IF EXISTS `units`;
CREATE TABLE `units`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `abbreviation` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `units_abbreviation_unique`(`abbreviation` ASC) USING BTREE,
  INDEX `units_is_active_index`(`is_active` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 11 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of units
-- ----------------------------
INSERT INTO `units` VALUES (1, 'Piece', 'Pcs', 1, '2026-10-18 21:44:40', '2026-10-18 21:44:40');
INSERT INTO `units` VALUES (2, 'Strip', 'Str', 1, '2026-10-18 21:44:40', '2026-10-18 21:44:40');
INSERT INTO `units` VALUES (3, 'Box', 'Box', 1, '2026-10-18 21:44:40', '2026-10-18 21:44:40');
INSERT INTO `units` VALUES (4, 'Bottle', 'Btl', 1, '2026-10-18 21:44:40', '2026-10-18 21:44:40');
INSERT INTO `units` VALUES (5, 'Vial', 'Vial', 1, '2026-10-18 21:44:41', '2026-10-18 21:44:41');
INSERT INTO `units` VALUES (6, 'Ampoule', 'Amp', 1, '2026-10-18 21:44:41', '2026-10-18 21:44:41');
INSERT INTO `units` VALUES (7, 'Tube', 'Tube', 1, '2026-10-18 21:44:41', '2026-10-18 21:44:41');
INSERT INTO `units` VALUES (8, 'Sachet', 'Sachet', 1, '2026-10-18 21:44:41', '2026-10-18 21:44:41');
INSERT INTO `units` VALUES (9, 'Tablet', 'Tab', 1, '2026-10-18 21:44:41', '2026-10-18 21:44:41');
INSERT INTO `units` VALUES (10, 'Capsule', 'Cap', 1, '2026-10-18 21:44:41', '2026-10-18 21:44:41');

-- ----------------------------
-- Table structure for universities
-- ----------------------------
DROP TABLE IF EXISTS `universities`;
CREATE TABLE `universities`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '0 = hide, 1 = show',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of universities
-- ----------------------------

-- ----------------------------
-- Table structure for verification_steps
-- ----------------------------
DROP TABLE IF EXISTS `verification_steps`;
CREATE TABLE `verification_steps`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `role_id` bigint UNSIGNED NULL DEFAULT NULL,
  `step` int NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `verification_steps_role_id_foreign`(`role_id` ASC) USING BTREE,
  CONSTRAINT `verification_steps_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of verification_steps
-- ----------------------------

-- ----------------------------
-- Table structure for warehouse_locations
-- ----------------------------
DROP TABLE IF EXISTS `warehouse_locations`;
CREATE TABLE `warehouse_locations`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `warehouse_id` bigint UNSIGNED NOT NULL,
  `parent_id` bigint UNSIGNED NULL DEFAULT NULL,
  `code` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `name` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `type` enum('storage','receiving','dispatch','quarantine','damaged','expired_disposal','returns','cold_storage') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'storage',
  `depth` tinyint UNSIGNED NOT NULL DEFAULT 1,
  `is_sellable` tinyint(1) NOT NULL DEFAULT 1,
  `is_available` tinyint(1) NOT NULL DEFAULT 1,
  `storage_condition` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'room_temperature',
  `capacity` decimal(12, 2) NULL DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `warehouse_locations_warehouse_id_code_unique`(`warehouse_id` ASC, `code` ASC) USING BTREE,
  INDEX `warehouse_locations_parent_id_foreign`(`parent_id` ASC) USING BTREE,
  INDEX `warehouse_locations_warehouse_id_type_index`(`warehouse_id` ASC, `type` ASC) USING BTREE,
  INDEX `warehouse_locations_warehouse_id_is_available_is_sellable_index`(`warehouse_id` ASC, `is_available` ASC, `is_sellable` ASC) USING BTREE,
  INDEX `warehouse_locations_is_active_index`(`is_active` ASC) USING BTREE,
  CONSTRAINT `warehouse_locations_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `warehouse_locations` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT,
  CONSTRAINT `warehouse_locations_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 11 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of warehouse_locations
-- ----------------------------
INSERT INTO `warehouse_locations` VALUES (1, 1, NULL, 'LOC-STORAGE', 'General Ambient Storage (Rack A1-D10)', 'storage', 1, 1, 1, 'room_temperature', NULL, 1, '2026-10-18 21:44:40', '2026-10-18 21:44:40', NULL);
INSERT INTO `warehouse_locations` VALUES (2, 1, NULL, 'LOC-RECEIVING', 'Inbound Goods Staging Dock', 'receiving', 1, 0, 0, 'room_temperature', NULL, 1, '2026-10-18 21:44:40', '2026-10-18 21:44:40', NULL);
INSERT INTO `warehouse_locations` VALUES (3, 1, NULL, 'LOC-DISPATCH', 'Outbound Pack & Dispatch Bay', 'dispatch', 1, 0, 0, 'room_temperature', NULL, 1, '2026-10-18 21:44:40', '2026-10-18 21:44:40', NULL);
INSERT INTO `warehouse_locations` VALUES (4, 1, NULL, 'LOC-COLD-CHAIN', 'Cold Chain Walk-in Chiller (2°C - 8°C)', 'cold_storage', 1, 1, 1, 'cold_chain_2_8', NULL, 1, '2026-10-18 21:44:40', '2026-10-18 21:44:40', NULL);
INSERT INTO `warehouse_locations` VALUES (5, 1, NULL, 'LOC-QUARANTINE', 'Quality Quarantine & Hold Area', 'quarantine', 1, 0, 0, 'room_temperature', NULL, 1, '2026-10-18 21:44:40', '2026-10-18 21:44:40', NULL);
INSERT INTO `warehouse_locations` VALUES (6, 1, NULL, 'LOC-DAMAGED', 'Damaged Goods Isolation Rack', 'damaged', 1, 0, 0, 'room_temperature', NULL, 1, '2026-10-18 21:44:40', '2026-10-18 21:44:40', NULL);
INSERT INTO `warehouse_locations` VALUES (7, 1, NULL, 'LOC-EXPIRED', 'Expired Stock / DDA Disposal Staging', 'expired_disposal', 1, 0, 0, 'room_temperature', NULL, 1, '2026-10-18 21:44:40', '2026-10-18 21:44:40', NULL);
INSERT INTO `warehouse_locations` VALUES (8, 1, NULL, 'LOC-RETURNS', 'Customer Returns Inspection Area', 'returns', 1, 0, 0, 'room_temperature', NULL, 1, '2026-10-18 21:44:40', '2026-10-18 21:44:40', NULL);
INSERT INTO `warehouse_locations` VALUES (9, 2, NULL, 'LOC-PKR-STORE', 'Main Ambient Storage Rack', 'storage', 1, 1, 1, 'room_temperature', NULL, 1, '2026-10-18 22:46:57', '2026-10-18 22:46:57', NULL);
INSERT INTO `warehouse_locations` VALUES (10, 2, NULL, 'LOC-PKR-QUAR', 'Quarantine Bay', 'quarantine', 1, 0, 0, 'room_temperature', NULL, 1, '2026-10-18 22:46:57', '2026-10-18 22:46:57', NULL);

-- ----------------------------
-- Table structure for warehouses
-- ----------------------------
DROP TABLE IF EXISTS `warehouses`;
CREATE TABLE `warehouses`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `code` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `name` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `phone` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `address` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL,
  `is_primary` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `warehouses_code_unique`(`code` ASC) USING BTREE,
  INDEX `warehouses_is_active_index`(`is_active` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of warehouses
-- ----------------------------
INSERT INTO `warehouses` VALUES (1, 'WH-CENTRAL', 'Central Pharma Distribution Hub', '+977-1-4498765', 'centralwh@pharmadistributors.com.np', 'Balaju Industrial Area, Kathmandu', 1, 1, '2026-10-18 21:44:40', '2026-10-18 21:44:40', NULL);
INSERT INTO `warehouses` VALUES (2, 'WH-POKHARA-DEPOT', 'Pokhara Regional Distribution Depot', '+977-61-532100', 'pokharadepot@pharmadistributors.com.np', 'Nagdhunga, Pokhara, Kaski', 0, 1, '2026-10-18 22:46:57', '2026-10-18 22:46:57', NULL);

SET FOREIGN_KEY_CHECKS = 1;
