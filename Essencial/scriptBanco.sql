CREATE TABLE `acessos` (
  `id` int(11) NOT NULL,
  `motivo` varchar(255) NOT NULL,
  `imagem` mediumblob NOT NULL,
  `data_hora` timestamp NOT NULL DEFAULT current_timestamp(),
  `observacoes` varchar(20) DEFAULT NULL,
  `horaBaixa` timestamp NULL DEFAULT NULL
)