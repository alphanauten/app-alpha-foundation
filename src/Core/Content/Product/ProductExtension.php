<?php declare(strict_types=1);

namespace AlphaFoundation\Core\Content\Product;

use AlphaFoundation\Core\Content\Product\ListingSetExtension\ListingSetExtensionDefinition;
use Shopware\Core\Content\Product\ProductDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityExtension;
use Shopware\Core\Framework\DataAbstractionLayer\Field\OneToOneAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;

class ProductExtension extends EntityExtension
{
    public function extendFields(FieldCollection $collection): void
    {
        $collection->add(
            new OneToOneAssociationField(
                'listingFeatureSet',
                'id',
                'product_id',
                ListingSetExtensionDefinition::class
            )
        );
    }

    public function getDefinitionClass(): string
    {
        return ProductDefinition::class;
    }
    public function getEntityName(): string
    {
        return ProductDefinition::ENTITY_NAME;
    }
}
