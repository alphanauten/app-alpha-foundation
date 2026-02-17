<?php declare(strict_types=1);

namespace AlphaFoundation\Core\Content\Property\Extension;

use Shopware\Core\Content\Category\CategoryDefinition;
use Shopware\Core\Content\Product\Aggregate\ProductProperty\ProductPropertyDefinition;
use Shopware\Core\Content\Property\PropertyGroupDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityExtension;
use Shopware\Core\Framework\DataAbstractionLayer\Field\OneToOneAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;

class PropertyReviewPoolMergeExtension extends EntityExtension
{
    public function extendFields(FieldCollection $collection): void
    {
        $collection->add(
        new OneToOneAssociationField('reviewPoolMergeExtension','id','property_group_id',PropertyReviewPoolMergeExtensionDefinition::class,true)
        );
    }

    public function getDefinitionClass(): string
    {
        return PropertyGroupDefinition::class;
    }

    public function getEntityName(): string
    {
        return PropertyGroupDefinition::ENTITY_NAME;
    }
}