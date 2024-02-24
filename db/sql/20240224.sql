ALTER TABLE `vpj_sting_entities` ADD `export_date_full` DATE NULL DEFAULT NULL AFTER `export_date`;


CREATE TABLE `pbd_sting_delivery` (
                                      `id` int(11) NOT NULL,
                                      `purchase_by_document_id` int(11) NOT NULL,
                                      `pharmacist_in_charge` varchar(255) DEFAULT NULL,
                                      `place` varchar(255) DEFAULT NULL,
                                      `address` varchar(255) DEFAULT NULL,
                                      `route` varchar(255) DEFAULT NULL,
                                      `date` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `pbd_sting_invoice_items`
--

CREATE TABLE `pbd_sting_invoice_items` (
                                           `id` int(11) NOT NULL,
                                           `purchase_by_document_id` int(11) NOT NULL,
                                           `number` int(11) NOT NULL,
                                           `nzok` varchar(255) DEFAULT NULL,
                                           `designation` varchar(255) NOT NULL,
                                           `manufacturer` varchar(255) NOT NULL,
                                           `quantity` int(11) NOT NULL,
                                           `base_price` decimal(10,2) DEFAULT NULL,
                                           `trade_markup` decimal(10,2) DEFAULT NULL,
                                           `trade_discount` decimal(10,2) DEFAULT NULL,
                                           `wholesaler_price` decimal(10,2) DEFAULT NULL,
                                           `value` decimal(10,2) DEFAULT NULL,
                                           `price_with_vat` decimal(10,2) DEFAULT NULL,
                                           `certificate` varchar(255) NOT NULL,
                                           `expiry_date` varchar(255) NOT NULL,
                                           `recommended_price` decimal(10,2) DEFAULT NULL,
                                           `limit_price` varchar(255) DEFAULT NULL,
                                           `percent_a` varchar(255) DEFAULT NULL,
                                           `batch` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `pbd_sting_invoice_payment`
--

CREATE TABLE `pbd_sting_invoice_payment` (
                                             `id` int(11) NOT NULL,
                                             `purchase_by_document_id` int(11) NOT NULL,
                                             `payment_info` varchar(255) DEFAULT NULL,
                                             `tax_event_date` date DEFAULT NULL,
                                             `payer_bic` varchar(255) DEFAULT NULL,
                                             `payer_iban` varchar(255) DEFAULT NULL,
                                             `payer_bank` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `pbd_sting_invoice_price`
--

CREATE TABLE `pbd_sting_invoice_price` (
                                           `id` int(11) NOT NULL,
                                           `purchase_by_document_id` int(11) NOT NULL,
                                           `total_price` decimal(10,2) NOT NULL,
                                           `total_price_from_supplier` decimal(10,2) NOT NULL,
                                           `trade_discount` decimal(10,2) NOT NULL,
                                           `trade_discount_percent` varchar(255) DEFAULT NULL,
                                           `value_of_the_deal` decimal(10,2) DEFAULT NULL,
                                           `tax_20` decimal(10,2) NOT NULL,
                                           `tax_base` varchar(255) DEFAULT NULL,
                                           `total_price_with_tax` decimal(10,2) NOT NULL,
                                           `total_price_with_tax_in_words` varchar(255) NOT NULL,
                                           `note` varchar(255) DEFAULT NULL,
                                           `doc_number` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `pbd_sting_recipient`
--

CREATE TABLE `pbd_sting_recipient` (
                                       `id` int(11) NOT NULL,
                                       `purchase_by_document_id` int(11) NOT NULL,
                                       `name` varchar(255) DEFAULT NULL,
                                       `address` varchar(255) DEFAULT NULL,
                                       `address_reg` varchar(255) DEFAULT NULL,
                                       `in_number` varchar(255) DEFAULT NULL,
                                       `vat_number` varchar(255) DEFAULT NULL,
                                       `license` varchar(255) DEFAULT NULL,
                                       `opiates_license` varchar(255) DEFAULT NULL,
                                       `mol` varchar(255) DEFAULT NULL,
                                       `phone` varchar(255) DEFAULT NULL,
                                       `client_id` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `pbd_sting_supplier`
--

CREATE TABLE `pbd_sting_supplier` (
                                      `id` int(11) NOT NULL,
                                      `purchase_by_document_id` int(11) NOT NULL,
                                      `name` varchar(255) DEFAULT NULL,
                                      `address` varchar(255) DEFAULT NULL,
                                      `in_number` varchar(255) DEFAULT NULL,
                                      `vat_number` varchar(255) DEFAULT NULL,
                                      `license` varchar(255) DEFAULT NULL,
                                      `opiates_license` varchar(255) DEFAULT NULL,
                                      `controlling_person` varchar(255) DEFAULT NULL,
                                      `phone` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `pbd_sting_delivery`
--
ALTER TABLE `pbd_sting_delivery`
    ADD PRIMARY KEY (`id`),
    ADD KEY `purchase_by_document_id` (`purchase_by_document_id`);

--
-- Indexes for table `pbd_sting_invoice_items`
--
ALTER TABLE `pbd_sting_invoice_items`
    ADD PRIMARY KEY (`id`),
    ADD KEY `purchase_by_document_id` (`purchase_by_document_id`);

--
-- Indexes for table `pbd_sting_invoice_payment`
--
ALTER TABLE `pbd_sting_invoice_payment`
    ADD PRIMARY KEY (`id`),
    ADD KEY `purchase_by_document_id` (`purchase_by_document_id`);

--
-- Indexes for table `pbd_sting_invoice_price`
--
ALTER TABLE `pbd_sting_invoice_price`
    ADD PRIMARY KEY (`id`),
    ADD KEY `purchase_by_document_id` (`purchase_by_document_id`);

--
-- Indexes for table `pbd_sting_recipient`
--
ALTER TABLE `pbd_sting_recipient`
    ADD PRIMARY KEY (`id`),
    ADD KEY `purchase_by_document_id` (`purchase_by_document_id`);

--
-- Indexes for table `pbd_sting_supplier`
--
ALTER TABLE `pbd_sting_supplier`
    ADD PRIMARY KEY (`id`),
    ADD KEY `purchase_by_document_id` (`purchase_by_document_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `pbd_sting_delivery`
--
ALTER TABLE `pbd_sting_delivery`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pbd_sting_invoice_items`
--
ALTER TABLE `pbd_sting_invoice_items`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pbd_sting_invoice_payment`
--
ALTER TABLE `pbd_sting_invoice_payment`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pbd_sting_invoice_price`
--
ALTER TABLE `pbd_sting_invoice_price`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pbd_sting_recipient`
--
ALTER TABLE `pbd_sting_recipient`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pbd_sting_supplier`
--
ALTER TABLE `pbd_sting_supplier`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;