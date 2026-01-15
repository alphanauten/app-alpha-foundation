<?php declare(strict_types=1);

namespace AlphaFoundation\Core\Content\Product;

use AlphaFoundation\Core\Content\Product\ListingSetExtension\ListingSetExtensionEntity;
use Shopware\Core\Content\Product\Aggregate\ProductFeatureSet\ProductFeatureSetDefinition;
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

    private function prepare(iterable $products, SalesChannelContext $context): ArrayStruct
    {
        return $this->loadCustomFields($products, $context);
    }

    public function add(iterable $products, SalesChannelContext $context): void
    {
        $customFields = $this->prepare($products, $context);
        foreach ($products as $product) {
            if (!($product instanceof SalesChannelProductEntity)) {
                continue;
            }

            $product->addExtension('listingFeatures', $this->buildFeatures($product, $customFields));
        }
    }

    private function buildFeatures(SalesChannelProductEntity $product, ArrayStruct $customFields): ArrayStruct
    {
        /**
         * @var ListingSetExtensionEntity $listingSetExtension
         */
        $listingSetExtension = $product->getExtension('listingFeatureSet');
        if(is_null($listingSetExtension)){
            return new ArrayStruct();
        }
        $sortedFeatures = $listingSetExtension->getListingFeatureSet()->getFeatures();
        if ($sortedFeatures === null) {
            return new ArrayStruct();
        }

        $features = [];
        foreach ($sortedFeatures as $feature) {
            if ($feature['type'] === ProductFeatureSetDefinition::TYPE_PRODUCT_ATTRIBUTE) {
                $features[] = $this->getAttribute($feature['name'], $product);

                continue;
            }

            if ($feature['type'] === ProductFeatureSetDefinition::TYPE_PRODUCT_PROPERTY) {
                $features[] = $this->getProperty($feature['id'], $product);

                continue;
            }

            if ($feature['type'] === ProductFeatureSetDefinition::TYPE_PRODUCT_CUSTOM_FIELD) {
                $features[] = $this->getCustomField($feature['name'],$customFields, $product);

                continue;
            }
        }

        return new ArrayStruct(array_filter($features));
    }

    private function loadCustomFields(iterable $products, SalesChannelContext $context): ArrayStruct
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
                if (!$this->isRequiredCustomField($name, $product)) {
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
            return new ArrayStruct();
        }

        $criteria = (new Criteria())->addFilter(new EqualsAnyFilter('name', $required));

        $customFields = $this->customFieldRepository->search($criteria, $context->getContext())->getEntities();

        foreach ($customFields as $field) {
            $key = 'custom-field-' . $field->getName();
            $customFieldsSet->set($key, $field);
        }

        return $customFieldsSet;
    }

    /**
     * Checks wether a custom field name is part of the provided product's feature set
     */
    private function isRequiredCustomField(string $name, SalesChannelProductEntity $product): bool
    {
        /**
         * @var ListingSetExtensionEntity $listingSetExtension
         */
        $listingSetExtension = $product->getExtension('listingFeatureSet');
        if(is_null($listingSetExtension)){
            return false;
        }
        $sortedFeatures = $listingSetExtension->getListingFeatureSet()->getFeatures();

        foreach ($sortedFeatures as $feature) {
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
        $fieldKey = \sprintf('custom-field-%s', $name);
        $translation = $product->getTranslation('customFields');

        if ($translation === null || !\array_key_exists($name, $translation)) {
            return null;
        }

        if (!$data->has($fieldKey)) {
            return null;
        }

        $customField = $data->get($fieldKey);
        $label = $this->getCustomFieldLabel($customField);
        if (!\is_string($label)) {
            return null;
        }

        return [
            'label' => $label,
            'value' => [
                'id' => $customField->getId(),
                'type' => $customField->getType(),
                'content' => $translation[$name],
            ],
            'type' => ProductFeatureSetDefinition::TYPE_PRODUCT_CUSTOM_FIELD,
        ];
    }

    private function getCastingWeightCustomFields(string $name, Struct|null $data, SalesChannelProductEntity $product)
    {
        $fieldKey = sprintf('custom-field-%s', $name);
        $translation = $product->getTranslation('customFields');

        if (!\array_key_exists('rod_min_casting_weight', $translation) && !\array_key_exists('rod_max_casting_weight', $translation)) {
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
