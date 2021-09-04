CREATE DATABASE IF NOT EXISTS `ci_base_core` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `ci_base_core`;
CREATE USER 'cibase'@'%' IDENTIFIED BY 'Gqh#GoZsRG_e';
GRANT SELECT, INSERT, UPDATE, DELETE, CREATE, DROP, ALTER, CREATE TEMPORARY TABLES, LOCK TABLES ON ci_base_core.* TO 'cibase'@'%';
FLUSH PRIVILEGES;
