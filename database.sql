-- phpMyAdmin SQL Dump
-- version 4.9.7
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Tempo de geração: 21-Jan-2022 às 18:01
-- Versão do servidor: 10.3.32-MariaDB
-- versão do PHP: 7.3.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `anderlan_vibbra`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `tb_auth_error_log`
--

CREATE TABLE `tb_auth_error_log` (
  `login` varchar(35) NOT NULL,
  `created` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estrutura da tabela `tb_projects`
--

CREATE TABLE `tb_projects` (
  `projectId` bigint(20) NOT NULL,
  `userId` bigint(20) NOT NULL,
  `status` varchar(25) NOT NULL COMMENT 'active, inactive, canceled',
  `title` varchar(75) NOT NULL,
  `description` text NOT NULL,
  `created` datetime NOT NULL,
  `deleted` varchar(20) NOT NULL DEFAULT '0' COMMENT 'datetime or 0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estrutura da tabela `tb_projects_users`
--

CREATE TABLE `tb_projects_users` (
  `projectId` bigint(20) NOT NULL,
  `userId` bigint(20) NOT NULL,
  `created` datetime NOT NULL,
  `deleted` varchar(20) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estrutura da tabela `tb_times`
--

CREATE TABLE `tb_times` (
  `timeId` bigint(20) NOT NULL,
  `projectId` bigint(20) NOT NULL,
  `userId` bigint(20) NOT NULL,
  `started` datetime NOT NULL,
  `ended` varchar(20) DEFAULT NULL,
  `seconds` bigint(20) DEFAULT 0,
  `description` text NOT NULL,
  `created` datetime NOT NULL,
  `deleted` varchar(20) NOT NULL DEFAULT '0' COMMENT 'datetime or 0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estrutura da tabela `tb_users`
--

CREATE TABLE `tb_users` (
  `userId` bigint(20) NOT NULL,
  `role` varchar(25) NOT NULL DEFAULT 'user' COMMENT 'user, admin',
  `login` varchar(35) NOT NULL,
  `status` varchar(25) NOT NULL DEFAULT 'active' COMMENT 'pending, active, suspended',
  `name` varchar(55) NOT NULL,
  `email` varchar(155) NOT NULL,
  `password` varchar(75) NOT NULL,
  `created` datetime NOT NULL,
  `deleted` varchar(20) NOT NULL DEFAULT '0' COMMENT 'datetime or 0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Extraindo dados da tabela `tb_users`
--

INSERT INTO `tb_users` (`userId`, `role`, `login`, `status`, `name`, `email`, `password`, `created`, `deleted`) VALUES
(1164277669585412113, 'admin', 'admin', 'pending', 'Admin', 'admin@email.com', '96cae35ce8a9b0244178bf28e4966c2ce1b8385723a96a6b838858cdd6ca0a1e', '2022-01-21 14:51:35', '0');

--
-- Índices para tabelas despejadas
--

--
-- Índices para tabela `tb_auth_error_log`
--
ALTER TABLE `tb_auth_error_log`
  ADD KEY `login` (`login`,`created`);

--
-- Índices para tabela `tb_projects`
--
ALTER TABLE `tb_projects`
  ADD PRIMARY KEY (`projectId`,`deleted`),
  ADD KEY `userId` (`userId`),
  ADD KEY `title` (`title`),
  ADD KEY `created` (`created`),
  ADD KEY `status` (`status`);

--
-- Índices para tabela `tb_projects_users`
--
ALTER TABLE `tb_projects_users`
  ADD PRIMARY KEY (`projectId`,`userId`,`deleted`);

--
-- Índices para tabela `tb_times`
--
ALTER TABLE `tb_times`
  ADD PRIMARY KEY (`timeId`,`deleted`),
  ADD KEY `projectId` (`projectId`),
  ADD KEY `userId` (`userId`),
  ADD KEY `started` (`started`),
  ADD KEY `ended` (`ended`),
  ADD KEY `created` (`created`);

--
-- Índices para tabela `tb_users`
--
ALTER TABLE `tb_users`
  ADD PRIMARY KEY (`userId`,`deleted`),
  ADD UNIQUE KEY `login` (`login`,`deleted`),
  ADD KEY `email` (`email`),
  ADD KEY `created` (`created`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
