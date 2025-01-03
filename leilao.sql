-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 04/12/2024 às 02:04
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `leilao`
--
CREATE DATABASE IF NOT EXISTS `leilao` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `leilao`;

-- --------------------------------------------------------

--
-- Estrutura para tabela `itens`
--

CREATE TABLE `itens` (
  `id` int(11) NOT NULL,
  `nome` varchar(255) DEFAULT NULL,
  `imagem` varchar(255) DEFAULT NULL,
  `minimo` decimal(10,2) DEFAULT NULL,
  `estado` enum('aberto','encerrado') DEFAULT 'aberto',
  `vencedor` int(11) DEFAULT NULL,
  `usuario_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



CREATE TABLE `lances` (
  `id` int(11) NOT NULL,
  `id_item` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `valor` decimal(10,2) NOT NULL,
  `data_lance` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `lances`
--

INSERT INTO `lances` (`id`, `id_item`, `id_usuario`, `valor`, `data_lance`) VALUES
(1, 14, 2, 667.00, '2024-12-04 00:49:19');

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nomeUsuario` varchar(255) NOT NULL,
  `senhaUsuario` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


--
-- Índices de tabela `itens`
--
ALTER TABLE `itens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `vencedor` (`vencedor`);

--
-- Índices de tabela `lances`
--
ALTER TABLE `lances`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_item` (`id_item`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Índices de tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `itens`
--
ALTER TABLE `itens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de tabela `lances`
--
ALTER TABLE `lances`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `itens`
--
ALTER TABLE `itens`
  ADD CONSTRAINT `itens_ibfk_1` FOREIGN KEY (`vencedor`) REFERENCES `usuarios` (`id`);

--
-- Restrições para tabelas `lances`
--
ALTER TABLE `lances`
  ADD CONSTRAINT `lances_ibfk_1` FOREIGN KEY (`id_item`) REFERENCES `itens` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `lances_ibfk_2` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
