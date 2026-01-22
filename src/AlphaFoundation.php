<?php declare(strict_types=1);

namespace AlphaFoundation;

use AlphaFoundation\Util\Lifecycle\CustomFieldInstaller;
use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\Plugin;
use Shopware\Core\Framework\Plugin\Context\ActivateContext;
use Shopware\Core\Framework\Plugin\Context\DeactivateContext;
use Shopware\Core\Framework\Plugin\Context\InstallContext;
use Shopware\Core\Framework\Plugin\Context\UninstallContext;
use Shopware\Core\Framework\Plugin\Context\UpdateContext;
use Symfony\Component\DependencyInjection\ContainerInterface;

class AlphaFoundation extends Plugin
{

    public function deactivate(DeactivateContext $deactivateContext): void
    {
        $this->getCustomFieldInstaller()->deactivate($deactivateContext);
    }

    public function uninstall(UninstallContext $uninstallContext): void
    {
        if (!$this->container instanceof ContainerInterface) {
            // symfony 6.4: variable has been set to ContainerInterface|null.
            throw new \RuntimeException('container instance missing.');
        }

        $this->getCustomFieldInstaller()->uninstall($uninstallContext);

        if ($uninstallContext->keepUserData()) {
            return;
        }

        /** @var Connection $connection */
        $connection = $this->container->get(Connection::class);
        try {
            $connection->executeStatement('ALTER TABLE `marketing_banner` DROP COLUMN `banner_condition`');
            $connection->executeStatement('ALTER TABLE `marketing_banner` DROP COLUMN `marketing_banner`');
            $connection->executeStatement('DROP TABLE `marketing_banner_property`');
        } catch (\Exception) {

        }
    }

    private function getCustomFieldInstaller(): CustomFieldInstaller
    {
        if (!$this->container instanceof ContainerInterface) {
            // symfony 6.4: variable has been set to ContainerInterface|null.
            throw new \RuntimeException('container instance missing.');
        }

        /** @var EntityRepository $customFieldSetRepository */
        $customFieldSetRepository = $this->container->get('custom_field_set.repository');
        /** @var EntityRepository $customFieldRepository */
        $customFieldRepository = $this->container->get('custom_field.repository');

        return new CustomFieldInstaller($customFieldSetRepository, $customFieldRepository);
    }
}
