<?php

declare(strict_types=1);

namespace AlphaFoundation\Core\Content\Property\Extension;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @method void                       add(PropertyReviewPoolMergeExtensionEntity $entity)
 * @method void                       set(string $key, PropertyReviewPoolMergeExtensionEntity $entity)
 * @method PropertyReviewPoolMergeExtensionEntity[]    getIterator()
 * @method PropertyReviewPoolMergeExtensionEntity[]    getElements()
 * @method PropertyReviewPoolMergeExtensionEntity|null get(string $key)
 * @method PropertyReviewPoolMergeExtensionEntity|null first()
 * @method PropertyReviewPoolMergeExtensionEntity|null last()
 */
class ReviewPoolMergeExtensionCollection extends EntityCollection
{
    protected function getExpectedClass(): string
    {
        return PropertyReviewPoolMergeExtensionEntity::class;
    }
}
   