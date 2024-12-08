ALTER TABLE `purchase_by_document` ADD `credit_notice` TINYINT(1) NULL DEFAULT '0' AFTER `created_at`;

ALTER TABLE `pbd_sting_invoice_items` ADD `credit_notice` TINYINT(1) NULL DEFAULT '0' AFTER `batch`;