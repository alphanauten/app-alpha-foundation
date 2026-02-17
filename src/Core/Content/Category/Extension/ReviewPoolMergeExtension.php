<?php declare(strict_types=1);

namespace AlphaFoundation\Core\Content\Category\Extension;

use Shopware\Core\Content\Category\CategoryDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityExtension;
use Shopware\Core\Framework\DataAbstractionLayer\Field\OneToOneAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;

class ReviewPoolMergeExtension extends EntityExtension
{
    public function extendFields(FieldCollection $collection): void
    {
        $collection->add(
        new OneToOneAssociationField('reviewPoolMergeExtension','id','category_id',ReviewPoolMergeExtensionDefinition::class,true)
        );
    }

    public function getDefinitionClass(): string
    {
        return CategoryDefinition::class;
    }

    public function getEntityName(): string
    {
        return CategoryDefinition::ENTITY_NAME;
    }
}