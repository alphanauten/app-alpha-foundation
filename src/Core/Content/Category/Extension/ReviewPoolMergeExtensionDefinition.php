<?php declare(strict_types=1);

namespace AlphaFoundation\Core\Content\Category\Extension;

use Shopware\Core\Content\Category\CategoryDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\BoolField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\FkField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\OneToOneAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ReferenceVersionField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;

class ReviewPoolMergeExtensionDefinition extends EntityDefinition
{
    public const ENTITY_NAME = "alpha_review_pool_merge_extension";

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getEntityClass(): string
    {
        return ReviewPoolMergeExtensionEntity::class;
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new IdField('id', 'id'))->addFlags(new Required(), new PrimaryKey()),
            new FkField('category_id', 'categoryId', CategoryDefinition::class),
            (new BoolField('merge_review_pools', 'mergeReviewPools')),
            new ReferenceVersionField(CategoryDefinition::class, 'category_version_id'),
            new OneToOneAssociationField('category', 'category_id', 'id', CategoryDefinition::class, false)
        ]);
    }

    public function getCollectionClass(): string
    {
        return ReviewPoolMergeExtensionEntity::class;
    }
}