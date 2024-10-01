<?php declare(strict_types=1);

namespace AlphaFoundation\Migration;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Shopware\Core\Framework\Log\Package;
use Shopware\Core\Framework\Migration\MigrationStep;

/**
 * @internal
 */
#[Package('core')]
class Migration1721558462 extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1721558462;
    }

    /**
     * @throws Exception
     */
    public function update(Connection $connection): void
    {
        $connection->executeStatement('
            CREATE TABLE IF NOT EXISTS `marketing_banner` (
                `id` BINARY(16) NOT NULL,
                `name` VARCHAR(255) NOT NULL,
                `description` VARCHAR(255) NULL,
                `active` TINYINT(1)  NOT NULL DEFAULT 0,
                `banner_type` VARCHAR(255) NOT NULL,
                `categories` JSON NOT NULL,
                `text` LONGTEXT NULL,
                `background_color` VARCHAR(255) NULL,
                `text_color` VARCHAR(255) NULL,
                `border` VARCHAR(255) NULL,
                `css` LONGTEXT NULL,
                `media_id` BINARY(16) NULL,
                `type` VARCHAR(255) NOT NULL,
                `data` JSON NULL,
                `config` JSON NULL,
                `custom_fields` JSON NULL,
                `created_at` DATETIME(3) NOT NULL,
                `updated_at` DATETIME(3) NULL,
                `valid_from` DATETIME(3) NULL,
                `valid_until` DATETIME(3) NULL,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
            ');

        $sql = <<<SQL
            CREATE TABLE IF NOT EXISTS `marketing_banner_translation` (
                `marketing_banner_id` BINARY(16) NOT NULL,
                `language_id` BINARY(16) NOT NULL,
                `config` JSON NULL,
                `custom_fields` JSON NULL,
                `created_at` DATETIME(3) NOT NULL,
                `updated_at` DATETIME(3) NULL,
                PRIMARY KEY (`marketing_banner_id`, `language_id`),
                CONSTRAINT `json.marketing_banner_translation.config` CHECK(JSON_VALID(`config`)),
                CONSTRAINT `json.marketing_banner_translation.custom_fields` CHECK (JSON_VALID(`custom_fields`)),
                CONSTRAINT `fk.marketing_banner_translation.marketing_banner_id` FOREIGN KEY (`marketing_banner_id`)
                    REFERENCES `marketing_banner` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
                CONSTRAINT `fk.marketing_banner_translation.language_id` FOREIGN KEY (`language_id`)
                    REFERENCES `language` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
            SQL;
        $connection->executeStatement($sql);


        $connection->executeStatement('
            CREATE TABLE IF NOT EXISTS `marketing_banner_rule` (
                `banner_id` BINARY(16) NOT NULL,
                `rule_id` BINARY(16) NOT NULL,
                PRIMARY KEY (`banner_id`, `rule_id`),
                CONSTRAINT `fk.marketing_banner_rule.banner_id` FOREIGN KEY (`banner_id`)
                  REFERENCES `marketing_banner` (id) ON DELETE CASCADE ON UPDATE CASCADE,
                CONSTRAINT `fk.marketing_banner_rule.rule_id` FOREIGN KEY (`rule_id`)
                  REFERENCES `rule` (id) ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
       ');

        $sql = <<<SQL
            CREATE TABLE IF NOT EXISTS `alpha_cms_element_config` (
                `id` BINARY(16) NOT NULL,
                `name` VARCHAR(255) COLLATE utf8mb4_unicode_ci,
                `type` VARCHAR(255) COLLATE utf8mb4_unicode_ci,
                `config` JSON NULL,
                `data` JSON NULL,
                `custom_fields` JSON NULL,
                `created_at` DATETIME(3),
                `updated_at` DATETIME(3),
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
            SQL;
        $connection->executeStatement($sql);

    }

    /**
     * update destructive changes
     */
    public function updateDestructive(Connection $connection): void
    {
    }

}
