<?php

declare(strict_types=1);

namespace AlphaFoundation\Core\Content\Category\Extension;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @method void                       add(ReviewPoolMergeExtensionEntity $entity)
 * @method void                       set(string $key, ReviewPoolMergeExtensionEntity $entity)
 * @method ReviewPoolMergeExtensionEntity[]    getIterator()
 * @method ReviewPoolMergeExtensionEntity[]    getElements()
 * @method ReviewPoolMergeExtensionEntity|null get(string $key)
 * @method ReviewPoolMergeExtensionEntity|null first()
 * @method ReviewPoolMergeExtensionEntity|null last()
 */
class ReviewPoolMergeExtensionCollection extends EntityCollection
{
    protected function getExpectedClass(): string
    {
        return ReviewPoolMergeExtensionEntity::class;
    }
}
   