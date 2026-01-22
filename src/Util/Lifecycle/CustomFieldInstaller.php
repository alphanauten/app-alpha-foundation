<?php

declare(strict_types=1);

namespace AlphaFoundation\Util\Lifecycle;

use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\Plugin\Context\ActivateContext;
use Shopware\Core\Framework\Plugin\Context\DeactivateContext;
use Shopware\Core\Framework\Plugin\Context\InstallContext;
use Shopware\Core\Framework\Plugin\Context\UninstallContext;
use Shopware\Core\Framework\Plugin\Context\UpdateContext;
use Shopware\Core\Framework\Uuid\Uuid;
use Shopware\Core\System\CustomField\CustomFieldTypes;

class CustomFieldInstaller implements InstallerInterface
{
    final public const FOUNDATION_FIELDSET = 'alpha_foundation';
    final public const LISTING_SHOW_PARENT_ONLY = 'show_parent_products_only';

    private readonly array $customFields;

    private readonly array $customFieldSets;

    public function __construct(
        private readonly EntityRepository $customFieldSetRepository,
        private readonly EntityRepository $customFieldRepository,
    ) {
        $this->customFieldSets = [
            [
                'id' => Uuid::fromStringToHex(self::FOUNDATION_FIELDSET),
                'name' => self::FOUNDATION_FIELDSET,
                'relation' => [
                    'id' => Uuid::fromStringToHex(self::FOUNDATION_FIELDSET.'_relation_category'),
                    'entityName' => 'category',
                ],
                'config' => [
                    'label' => [
                        'en-GB' => 'Foundation',
                        'de-DE' => 'Foundation',
                    ],
                ],
            ],
        ];

        $this->customFields = [
            [
                'id' => Uuid::fromStringToHex(self::LISTING_SHOW_PARENT_ONLY),
                'name' => self::LISTING_SHOW_PARENT_ONLY,
                'type' => CustomFieldTypes::BOOL,
                'customFieldSetId' => Uuid::fromStringToHex(self::FOUNDATION_FIELDSET),
                'config' => [
                    'type' => CustomFieldTypes::SWITCH,
                    'customFieldType' => CustomFieldTypes::SWITCH,
                    'componentName' => 'sw-field',
                    'customFieldPosition' => 1,
                    'label' => [
                        'en-GB' => 'Show parent products only',
                        'de-DE' => 'Nur Vaterartikel anzeigen',
                    ],
                ],
            ],
        ];
    }

    public function install(InstallContext $context): void
    {

    }

    public function update(UpdateContext $context): void
    {

    }

    public function uninstall(UninstallContext $context): void
    {
        foreach ($this->customFieldSets as $customFieldSet) {
            $this->deactivateCustomFieldSet($customFieldSet, $context->getContext());
        }
        foreach ($this->customFields as $customField) {
            $this->deactivateCustomField($customField, $context->getContext());
        }
    }

    public function activate(ActivateContext $context): void
    {

    }

    public function deactivate(DeactivateContext $context): void
    {
        foreach ($this->customFieldSets as $customFieldSet) {
            $this->deactivateCustomFieldSet($customFieldSet, $context->getContext());
        }
        foreach ($this->customFields as $customField) {
            $this->deactivateCustomField($customField, $context->getContext());
        }
    }

    public function cleanup(InstallContext $context): void
    {
        //        $this->removeObsoleteCustomFieldSets($context->getContext());
    }

    private function deactivateCustomField(array $customField, Context $context): void
    {
        $data = [
            'id' => $customField['id'],
            'name' => $customField['name'],
            'type' => $customField['type'],
            'active' => false,
            'customFieldSetId' => $customField['customFieldSetId'],
        ];

        $this->customFieldRepository->upsert([$data], $context);
    }

    private function deactivateCustomFieldSet(array $customFieldSet, Context $context): void
    {
        $data = [
            'id' => $customFieldSet['id'],
            'name' => $customFieldSet['name'],
            'config' => $customFieldSet['config'],
            'active' => false,
            'relations' => [
                [
                    'id' => $customFieldSet['relation']['id'],
                    'entityName' => $customFieldSet['relation']['entityName'],
                ],
            ],
        ];

        $this->customFieldSetRepository->upsert([$data], $context);
    }
}
