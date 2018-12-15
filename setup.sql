SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

CREATE TABLE `dead_composers` (
  `id` int(11) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `name` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `birth_day` date DEFAULT NULL,
  `death_day` date NOT NULL,
  `public_domain_day` date not null,
  `public_domain_years` int(11) not null,
  `nationality` varchar(2) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `source_url` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;

ALTER TABLE `dead_composers`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `dead_composers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
