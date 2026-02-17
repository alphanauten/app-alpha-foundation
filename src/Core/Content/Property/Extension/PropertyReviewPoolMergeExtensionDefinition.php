<?php declare(strict_types=1);

namespace AlphaFoundation\Core\Content\Property\Extension;

use Shopware\Core\Content\Property\PropertyGroupDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\BoolField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\FkField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\OneToOneAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;

class PropertyReviewPoolMergeExtensionDefinition extends EntityDefinition
{
    public const ENTITY_NAME = "alpha_property_review_pool_merge_extension";

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getEntityClass(): string
    {
        return PropertyReviewPoolMergeExtensionEntity::class;
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new IdField('id', 'id'))->addFlags(new Required(), new PrimaryKey()),
            new FkField('property_group_id', 'propertyId', PropertyGroupDefinition::class),
            (new BoolField('merge_review_pools', 'mergeReviewPools')),
            new OneToOneAssociationField('propertyGroup', 'property_group_id', 'id', PropertyGroupDefinition::class, false)
        ]);
    }

    public function getCollectionClass(): string
    {
        return PropertyReviewPoolMergeExtensionEntity::class;
    }
}