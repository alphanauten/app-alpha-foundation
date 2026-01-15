<?php declare(strict_types=1);

namespace AlphaFoundation\Core\Content\Product\ListingSetExtension;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @extends EntityCollection<ListingSetExtensionEntity>
 */
class ListingSetExtensionCollection extends EntityCollection
{
    protected function getExpectedClass(): string
    {
        return ListingSetExtensionEntity::class;
    }
}