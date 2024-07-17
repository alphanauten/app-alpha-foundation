<?php declare(strict_types=1);

namespace Alpha\Foundation\Core\Content\Product;

use Shopware\Core\Content\Product\Aggregate\ProductFeatureSet\ProductFeatureSetDefinition;
use Shopware\Core\Content\Product\Aggregate\ProductFeatureSet\ProductFeatureSetEntity;
use Shopware\Core\Content\Product\SalesChannel\SalesChannelProductEntity;
use Shopware\Core\Content\Property\Aggregate\PropertyGroupOption\PropertyGroupOptionEntity;
use Shopware\Core\Defaults;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsAnyFilter;
use Shopware\Core\Framework\Log\Package;
use Shopware\Core\Framework\Struct\ArrayStruct;
use Shopware\Core\Framework\Struct\Struct;
use Shopware\Core\System\CustomField\CustomFieldEntity;
use Shopware\Core\System\Locale\LanguageLocaleCodeProvider;
use Shopware\Core\System\SalesChannel\SalesChannelContext;

#[Package('inventory')]
class ProductFeatureBuilder
{
    /**
     * @internal
     */
    public function __construct(
        private readonly EntityRepository           $customFieldRepository,
        private readonly LanguageLocaleCodeProvider $languageLocaleProvider
    )
    {
    }

    public function prepare(iterable $products, ProductFeatureSetEntity $featureSet, SalesChannelContext $context): void
    {
        $this->loadCustomFields($products, $featureSet, $context);
    }

    public function add(iterable $products, ProductFeatureSetEntity $listingFeatureSet): void
    {
        foreach ($products as $product) {
            if (!($product instanceof SalesChannelProductEntity)) {
                continue;
            }

            $product->addExtension('features', $this->buildFeatures($product, $listingFeatureSet));
        }
    }

    private function buildFeatures(SalesChannelProductEntity $product, ProductFeatureSetEntity $featureSet): ArrayStruct
    {
        $features = [];

        $customFields = $featureSet->getExtension('customFields');

        if ($featureSet === null) {
            return new ArrayStruct();
        }

        $sorted = $featureSet->getFeatures();

        if (empty($sorted)) {
            return new ArrayStruct();
        }

        usort($sorted, static fn(array $a, array $b) => $a['position'] <=> $b['position']);

        foreach ($sorted as $feature) {
            if ($feature['type'] === ProductFeatureSetDefinition::TYPE_PRODUCT_ATTRIBUTE) {
                $features[] = $this->getAttribute($feature['name'], $product);

                continue;
            }

            if ($feature['type'] === ProductFeatureSetDefinition::TYPE_PRODUCT_PROPERTY) {
                $features[] = $this->getProperty($feature['id'], $product);

                continue;
            }

            if ($feature['type'] === ProductFeatureSetDefinition::TYPE_PRODUCT_CUSTOM_FIELD) {
                $features[] = $this->getCustomField($feature['name'], $customFields, $product);

                continue;
            }

            if ($feature['type'] === ProductFeatureSetDefinition::TYPE_PRODUCT_REFERENCE_PRICE) {
                $features[] = $this->getReferencePrice($product);
            }
        }

        return new ArrayStruct(array_filter($features));
    }

    private function loadCustomFields(iterable $products, ProductFeatureSetEntity $featureSet, SalesChannelContext $context): void
    {
        $required = [];

        $customFieldsSet = new ArrayStruct();

        /** @var SalesChannelProductEntity $product */
        foreach ($products as $product) {
            if ($product === null || $product->getCustomFields() === null) {
                continue;
            }

            $names = array_keys($product->getCustomFields());

            foreach ($names as $name) {
                if (!$this->isRequiredCustomField($name, $product, $featureSet)) {
                    continue;
                }

                $key = 'custom-field-' . $name;

                if ($customFieldsSet->has($key)) {
                    // Custom field already loaded
                    continue;
                }

                $required[] = $name;
            }
        }

        if (empty($required)) {
            return;
        }

        $criteria = (new Criteria())->addFilter(new EqualsAnyFilter('name', $required));

        $customFields = $this->customFieldRepository->search($criteria, $context->getContext())->getEntities();

        foreach ($customFields as $field) {
            $key = 'custom-field-' . $field->getName();
            $customFieldsSet->set($key, $field);
        }

        $featureSet->addExtension('customFields', $customFieldsSet);
    }

    /**
     * Checks wether a custom field name is part of the provided product's feature set
     */
    private function isRequiredCustomField(string $name, SalesChannelProductEntity $product, ProductFeatureSetEntity $featureSet): bool
    {
        foreach ($featureSet->getFeatures() as $feature) {
            if ($feature['type'] !== ProductFeatureSetDefinition::TYPE_PRODUCT_CUSTOM_FIELD) {
                continue;
            }

            if ($feature['name'] === $name && \array_key_exists($name, $product->getTranslation('customFields'))) {
                return true;
            }
        }

        return false;
    }

    private function getAttribute(string $name, SalesChannelProductEntity $product): array
    {
        $translated = $product->getTranslated();
        $value = $product->get($name);

        if (\array_key_exists($name, $translated)) {
            $value = $translated[$name];
        }

        if ($value instanceof \DateTimeInterface) {
            $value = $value->format(Defaults::STORAGE_DATE_TIME_FORMAT);
        }

        return [
            'label' => $name,
            'value' => $value,
            'type' => ProductFeatureSetDefinition::TYPE_PRODUCT_ATTRIBUTE,
        ];
    }

    private function getProperty(string $id, SalesChannelProductEntity $product): ?array
    {
        if ($product->getProperties() === null) {
            return null;
        }

        $group = $product->getProperties()->getGroups()->get($id);

        if ($group === null) {
            return null;
        }

        $properties = $product->getProperties()->fmap(
            static function (PropertyGroupOptionEntity $property) use ($id) {
                if ($property->getGroupId() !== $id) {
                    return null;
                }

                return [
                    'id' => $property->getId(),
                    'name' => $property->getTranslation('name'),
                    'mediaId' => $property->getMediaId(),
                    'colorHexCode' => $property->getColorHexCode(),
                ];
            }
        );

        if (empty($properties)) {
            return null;
        }

        $label = $group->getTranslation('name');

        if (empty($label)) {
            return null;
        }

        return [
            'label' => $label,
            'value' => $properties,
            'type' => ProductFeatureSetDefinition::TYPE_PRODUCT_PROPERTY,
        ];
    }

    private function getCustomField(string $name, Struct|null $data, SalesChannelProductEntity $product): ?array
    {
        $fieldKey = sprintf('custom-field-%s', $name);
        $translation = $product->getTranslation('customFields');

        if ($data === null || $translation === null) {
            return null;
        }

        $stringTemplate = "%s%s";
        $unit = '';

        switch ($name) {
            case "min_max_casting_weight":
                return $this->getCastingWeightCustomFields($name, $data, $product);
                break;
            case "rod_length":
                $unit = 'cm';
                break;
        }

        if (!$data->has($fieldKey) || !\array_key_exists($name, $translation)) {
            return null;
        }

        $customField = $data->get($fieldKey);
        $label = $this->getCustomFieldLabel($customField);

        if (empty($label)) {
            return null;
        }

        return [
            'label' => $label,
            'value' => [
                'id' => $customField->getId(),
                'type' => $customField->getType(),
                'content' => sprintf($stringTemplate, $translation[$name], substr($translation[$name], -strlen($unit), strlen($unit)) === $unit ? '' : $unit),
            ],
            'type' => ProductFeatureSetDefinition::TYPE_PRODUCT_CUSTOM_FIELD,
        ];
    }

    private function getCastingWeightCustomFields(string $name, Struct|null $data, SalesChannelProductEntity $product) {
        $fieldKey = sprintf('custom-field-%s', $name);
        $translation = $product->getTranslation('customFields');

        if ( !\array_key_exists('rod_min_casting_weight', $translation) && !\array_key_exists('rod_max_casting_weight', $translation) ) {
            return null;
        }
        $label = 'Wurfgewicht';

        return [
            'label' => $label,
            'value' => [
//                'id' => $customField->getId(),
                'type' => 'text',
                'content' => $translation['rod_min_casting_weight'] . "-" . $translation['rod_max_casting_weight'] . 'g',
            ],
            'type' => ProductFeatureSetDefinition::TYPE_PRODUCT_CUSTOM_FIELD,
        ];
    }

    private function getReferencePrice(SalesChannelProductEntity $product): ?array
    {
        if ($product->getPrice() === null) {
            return null;
        }

        $referencePrice = $product->getPrice()->getReferencePrice();
        $unit = $product->getUnit();

        if ($referencePrice === null || $unit === null) {
            return null;
        }

        return [
            'label' => ProductFeatureSetDefinition::TYPE_PRODUCT_REFERENCE_PRICE,
            'value' => [
                'price' => $referencePrice->getPrice(),
                'purchaseUnit' => $referencePrice->getPurchaseUnit(),
                'referenceUnit' => $referencePrice->getReferenceUnit(),
                'unitName' => $unit->getTranslation('name'),
            ],
            'type' => ProductFeatureSetDefinition::TYPE_PRODUCT_REFERENCE_PRICE,
        ];
    }

    /**
     * Since it's not intended to display custom field labels outside of the admin at the moment,
     * their labels are indexed by the locale code of the system language (fixed value, not translated).
     *
     * @see https://issues.shopware.com/issues/NEXT-9321
     */
    private function getCustomFieldLabel(CustomFieldEntity $customField): ?string
    {
        if ($customField->getConfig() === null || !\array_key_exists('label', $customField->getConfig())) {
            return null;
        }

        $labels = $customField->getConfig()['label'];
        $localeCode = $this->languageLocaleProvider->getLocaleForLanguageId(Defaults::LANGUAGE_SYSTEM);

        return $labels[$localeCode] ?? null;
    }

    private function getDataKey(string $id): string
    {
        return 'product-' . $id;
    }
}
