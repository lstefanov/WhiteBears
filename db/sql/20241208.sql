ALTER TABLE `purchase_by_document` ADD `credit_notice` TINYINT(1) NULL DEFAULT '0' AFTER `created_at`;

ALTER TABLE `pbd_sting_invoice_items` ADD `credit_notice` TINYINT(1) NULL DEFAULT '0' AFTER `batch`;


INSERT INTO `nomenclatures_entities` (`id`, `code_number`, `code_name`, `name`, `price_from`, `price_to`) VALUES (NULL, '901', 'Y', 'Услуги', '0.00', '99999.99')