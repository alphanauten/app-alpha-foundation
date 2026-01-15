<?php

declare(strict_types=1);

namespace AlphaFoundation\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1767883495ListingSetProductExtension extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1767883495;
    }

    public function update(Connection $connection): void
    {
        $sql = <<<SQL
CREATE TABLE IF NOT EXISTS `alpha_product_extension_listing_feature_set` (
`id` BINARY(16) NOT NULL,
`product_id` BINARY(16) NOT NULL,
`product_version_id` BINARY(16) NOT NULL,
`feature_set_id` BINARY(16) NOT NULL,
`created_at` DATETIME(3) NOT NULL,
`updated_at` DATETIME(3) NULL,
PRIMARY KEY (`id`),
CONSTRAINT `fk.alpha_product_extension_listing_feature_set.product_id` FOREIGN KEY (`product_id`, `product_version_id`)
REFERENCES `product` (`id`, `version_id`) ON DELETE CASCADE ON UPDATE CASCADE,
CONSTRAINT `fk.alpha_product_extension_listing_feature_set.feature_set_id` FOREIGN KEY (`feature_set_id`)
REFERENCES `product_feature_set` (id) ON DELETE CASCADE ON UPDATE CASCADE
)
ENGINE=InnoDB
DEFAULT
CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;
SQL;

        $connection->executeStatement($sql);
    }

    public function updateDestructive(Connection $connection): void
    {
    }
}