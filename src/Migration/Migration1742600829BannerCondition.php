<?php declare(strict_types=1);

namespace AlphaFoundation\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

/**
 * @internal
 */
class Migration1742600829BannerCondition extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1742600829;
    }

    public function update(Connection $connection): void
    {
        $connection->executeStatement('
            ALTER TABLE `marketing_banner`
            ADD COLUMN `banner_condition` VARCHAR(255) NOT NULL;
        ');
    }

    public function updateDestructive(Connection $connection): void
    {
    }
}
