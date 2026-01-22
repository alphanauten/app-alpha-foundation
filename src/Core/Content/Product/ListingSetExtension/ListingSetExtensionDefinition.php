<?php declare(strict_types=1);

namespace AlphaFoundation\Core\Content\Product\ListingSetExtension;

use Shopware\Core\Content\Product\Aggregate\ProductFeatureSet\ProductFeatureSetDefinition;
use Shopware\Core\Content\Product\ProductDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\FkField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\OneToOneAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ReferenceVersionField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;

class ListingSetExtensionDefinition extends EntityDefinition
{

    public const ENTITY_NAME = "alpha_product_extension_listing_feature_set";

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new IdField('id', 'id'))->addFlags(new Required(), new PrimaryKey()),
            new FkField('product_id', 'productId', ProductDefinition::class),
            new ReferenceVersionField(ProductDefinition::class, 'product_version_id'),
            new FkField('feature_set_id', 'featureSetId', ProductDefinition::class),
            new OneToOneAssociationField('product', 'product_id', 'id', ProductDefinition::class, false),
            new OneToOneAssociationField('listingFeatureSet', 'feature_set_id', 'id', ProductFeatureSetDefinition::class)
        ]);
    }

    public function getEntityClass(): string
    {
        return ListingSetExtensionEntity::class;
    }

    public function getCollectionClass(): string
    {
        return ListingSetExtensionCollection::class;
    }
}