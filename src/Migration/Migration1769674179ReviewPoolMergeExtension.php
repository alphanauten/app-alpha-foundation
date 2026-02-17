<?php

declare(strict_types=1);

namespace AlphaFoundation\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1769674179ReviewPoolMergeExtension extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1769674179;
    }

    public function update(Connection $connection): void
    {
        $sql = <<<SQL
CREATE TABLE IF NOT EXISTS `alpha_review_pool_merge_extension` (
    `id` BINARY(16) NOT NULL,
    `category_id` BINARY(16) NULL,
    `category_version_id` BINARY(16) NOT NULL,
    `merge_review_pools` BOOLEAN NULL,
    `created_at` DATETIME(3) NOT NULL,
    `updated_at` DATETIME(3) NULL,
    PRIMARY KEY (`id`),
    CONSTRAINT `unique.alpha_review_pool_merge_extension.category` UNIQUE (`category_id`, `category_version_id`),
    CONSTRAINT `fk.alpha_review_pool_merge_extension.category_id` FOREIGN KEY (`category_id`, `category_version_id`) REFERENCES `category` (`id`, `version_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL;
        $connection->executeStatement($sql);
    }

    public function updateDestructive(Connection $connection): void
    {
    }
}