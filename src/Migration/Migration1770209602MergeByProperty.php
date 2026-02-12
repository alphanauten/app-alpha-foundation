<?php

declare(strict_types=1);

namespace AlphaFoundation\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1770209602MergeByProperty extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1770209602;
    }

    public function update(Connection $connection): void
    {
        $sql = <<<SQL
CREATE TABLE IF NOT EXISTS `alpha_property_review_pool_merge_extension` (
    `id` BINARY(16) NOT NULL,
    `property_group_id` BINARY(16) NULL,
    `merge_review_pools` BOOLEAN NULL,
    `created_at` DATETIME(3) NOT NULL,
    `updated_at` DATETIME(3) NULL,
    PRIMARY KEY (`id`),
    CONSTRAINT `unique.alpha_property_review_pool_merge_extension.property_group` UNIQUE (`property_group_id`),
    CONSTRAINT `fk.alpha_property_review_pool_merge_extension.property_group_id` FOREIGN KEY (`property_group_id`) REFERENCES `property_group` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL;
        $connection->executeStatement($sql);
    }

    public function updateDestructive(Connection $connection): void
    {
    }
}