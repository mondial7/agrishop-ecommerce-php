-- phpMyAdmin SQL Dump
-- version 4.6.5.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:8889
-- Creato il: Giu 09, 2017 alle 18:08
-- Versione del server: 5.6.35
-- Versione PHP: 7.1.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `agrishop`
--

-- --------------------------------------------------------

--
-- Struttura della tabella `agrishop__addresses`
--

CREATE TABLE `agrishop__addresses` (
  `id` int(11) NOT NULL,
  `profile_id` int(11) NOT NULL,
  `cap` varchar(10) NOT NULL,
  `street` varchar(150) NOT NULL,
  `name` varchar(150) NOT NULL,
  `city` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struttura della tabella `agrishop__areas`
--

CREATE TABLE `agrishop__areas` (
  `id` int(11) NOT NULL,
  `area` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struttura della tabella `agrishop__categories`
--

CREATE TABLE `agrishop__categories` (
  `id` int(11) NOT NULL,
  `category` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struttura della tabella `agrishop__farm`
--

CREATE TABLE `agrishop__farm` (
  `id` int(11) NOT NULL,
  `owner_name` varchar(255) NOT NULL,
  `owner_surname` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struttura della tabella `agrishop__production_areas`
--

CREATE TABLE `agrishop__production_areas` (
  `area` int(11) NOT NULL,
  `farm` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struttura della tabella `agrishop__production_categories`
--

CREATE TABLE `agrishop__production_categories` (
  `farm` int(11) NOT NULL,
  `category` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struttura della tabella `agrishop__products`
--

CREATE TABLE `agrishop__products` (
  `id` int(11) NOT NULL,
  `category` int(11) NOT NULL,
  `area` int(11) NOT NULL,
  `farm` int(11) NOT NULL,
  `quantity` decimal(10,3) NOT NULL,
  `name` varchar(255) NOT NULL,
  `price` decimal(10,0) NOT NULL,
  `produced` date NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struttura della tabella `agrishop__profile`
--

CREATE TABLE `agrishop__profile` (
  `id` int(11) NOT NULL,
  `username` varchar(150) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('farm','customer') NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struttura della tabella `agrishop__profile_logged`
--

CREATE TABLE `agrishop__profile_logged` (
  `id` int(11) NOT NULL,
  `profile_id` int(11) NOT NULL,
  `cookie_token` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struttura della tabella `agrishop__sales`
--

CREATE TABLE `agrishop__sales` (
  `id` int(11) NOT NULL,
  `product` int(11) NOT NULL,
  `profile` int(11) NOT NULL,
  `review` varchar(255) DEFAULT NULL,
  `payment_type` int(2) NOT NULL DEFAULT '1',
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Indici per le tabelle scaricate
--

--
-- Indici per le tabelle `agrishop__addresses`
--
ALTER TABLE `agrishop__addresses`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `profile_id` (`profile_id`,`cap`,`street`,`name`);

--
-- Indici per le tabelle `agrishop__areas`
--
ALTER TABLE `agrishop__areas`
  ADD PRIMARY KEY (`id`);

--
-- Indici per le tabelle `agrishop__categories`
--
ALTER TABLE `agrishop__categories`
  ADD PRIMARY KEY (`id`);

--
-- Indici per le tabelle `agrishop__farm`
--
ALTER TABLE `agrishop__farm`
  ADD PRIMARY KEY (`id`);

--
-- Indici per le tabelle `agrishop__production_areas`
--
ALTER TABLE `agrishop__production_areas`
  ADD UNIQUE KEY `area` (`area`,`farm`),
  ADD KEY `production_area_id` (`area`),
  ADD KEY `production_farm_id` (`farm`);

--
-- Indici per le tabelle `agrishop__production_categories`
--
ALTER TABLE `agrishop__production_categories`
  ADD UNIQUE KEY `farm` (`farm`,`category`),
  ADD KEY `production_category_id` (`category`),
  ADD KEY `production_farm_id_cat` (`farm`);

--
-- Indici per le tabelle `agrishop__products`
--
ALTER TABLE `agrishop__products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_category_id` (`category`),
  ADD KEY `product_area_id` (`area`),
  ADD KEY `product_farm_id` (`farm`);

--
-- Indici per le tabelle `agrishop__profile`
--
ALTER TABLE `agrishop__profile`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indici per le tabelle `agrishop__profile_logged`
--
ALTER TABLE `agrishop__profile_logged`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `cookie_token` (`cookie_token`),
  ADD KEY `logged_profile_id` (`profile_id`);

--
-- Indici per le tabelle `agrishop__sales`
--
ALTER TABLE `agrishop__sales`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `product` (`product`),
  ADD KEY `sale_customer_id` (`profile`);

--
-- AUTO_INCREMENT per le tabelle scaricate
--

--
-- AUTO_INCREMENT per la tabella `agrishop__addresses`
--
ALTER TABLE `agrishop__addresses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT per la tabella `agrishop__areas`
--
ALTER TABLE `agrishop__areas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
--
-- AUTO_INCREMENT per la tabella `agrishop__categories`
--
ALTER TABLE `agrishop__categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
--
-- AUTO_INCREMENT per la tabella `agrishop__products`
--
ALTER TABLE `agrishop__products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
--
-- AUTO_INCREMENT per la tabella `agrishop__profile`
--
ALTER TABLE `agrishop__profile`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;
--
-- AUTO_INCREMENT per la tabella `agrishop__profile_logged`
--
ALTER TABLE `agrishop__profile_logged`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT per la tabella `agrishop__sales`
--
ALTER TABLE `agrishop__sales`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
--
-- Limiti per le tabelle scaricate
--

--
-- Limiti per la tabella `agrishop__addresses`
--
ALTER TABLE `agrishop__addresses`
  ADD CONSTRAINT `address_profile_id` FOREIGN KEY (`profile_id`) REFERENCES `agrishop__profile` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limiti per la tabella `agrishop__farm`
--
ALTER TABLE `agrishop__farm`
  ADD CONSTRAINT `farm_id` FOREIGN KEY (`id`) REFERENCES `agrishop__profile` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limiti per la tabella `agrishop__production_areas`
--
ALTER TABLE `agrishop__production_areas`
  ADD CONSTRAINT `production_area_id` FOREIGN KEY (`area`) REFERENCES `agrishop__areas` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `production_farm_id` FOREIGN KEY (`farm`) REFERENCES `agrishop__farm` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limiti per la tabella `agrishop__production_categories`
--
ALTER TABLE `agrishop__production_categories`
  ADD CONSTRAINT `production_category_id` FOREIGN KEY (`category`) REFERENCES `agrishop__categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `production_farm_id_cat` FOREIGN KEY (`farm`) REFERENCES `agrishop__farm` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limiti per la tabella `agrishop__products`
--
ALTER TABLE `agrishop__products`
  ADD CONSTRAINT `product_area_id` FOREIGN KEY (`area`) REFERENCES `agrishop__areas` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `product_category_id` FOREIGN KEY (`category`) REFERENCES `agrishop__categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `product_farm_id` FOREIGN KEY (`farm`) REFERENCES `agrishop__farm` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limiti per la tabella `agrishop__profile_logged`
--
ALTER TABLE `agrishop__profile_logged`
  ADD CONSTRAINT `logged_profile_id` FOREIGN KEY (`profile_id`) REFERENCES `agrishop__profile` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limiti per la tabella `agrishop__sales`
--
ALTER TABLE `agrishop__sales`
  ADD CONSTRAINT `sale_customer_id` FOREIGN KEY (`profile`) REFERENCES `agrishop__profile` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `sale_product_id` FOREIGN KEY (`product`) REFERENCES `agrishop__products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
