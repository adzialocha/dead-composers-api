SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

CREATE TABLE `dead_composers` (
  `id` int(11) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `birth_day` date DEFAULT NULL,
  `death_day` date NOT NULL,
  `public_domain_day` date NOT NULL,
  `public_domain_years` int(11) NOT NULL,
  `nationality` varchar(2) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `source_url` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

ALTER TABLE `dead_composers`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `dead_composers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
