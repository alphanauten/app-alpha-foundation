<?php declare(strict_types=1);

namespace AlphaFoundation;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Plugin;
use Shopware\Core\Framework\Plugin\Context\UninstallContext;

class AlphaFoundation extends Plugin
{
    public function uninstall(UninstallContext $uninstallContext): void
    {
        if ($uninstallContext->keepUserData()) {
            return;
        }

        /** @var Connection $connection */
        $connection = $this->container->get(Connection::class);
        try {
            $connection->executeStatement('ALTER TABLE `marketing_banner` DROP COLUMN `banner_condition`');
            $connection->executeStatement('ALTER TABLE `marketing_banner` DROP COLUMN `marketing_banner`');
            $connection->executeStatement('DROP TABLE `marketing_banner_property`');
        } catch (\Exception $e) {

        }
    }
}
