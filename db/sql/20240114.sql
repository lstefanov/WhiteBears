ALTER TABLE `purchase_by_document` ADD `document_type` TINYINT(2) NOT NULL DEFAULT '1' COMMENT '1 - FF (regular)\r\n2 - FF (Subscription)' AFTER `business_id`;

ALTER TABLE `pbd_fioniks_farma_invoice_price` ADD `taxable_value` DECIMAL(10,2) NOT NULL AFTER `total_price`;
