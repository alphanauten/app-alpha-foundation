<?php declare(strict_types=1);

namespace AlphaFoundation\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

/**
 * @internal
 */
class Migration1742638724BannerPropertyGroupOptions extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1742638724;
    }

    public function update(Connection $connection): void
    {
        $connection->executeStatement('
            ALTER TABLE `marketing_banner`
            ADD COLUMN `property_group_options` JSON NULL;
        ');
    }

    public function updateDestructive(Connection $connection): void
    {
    }
}
