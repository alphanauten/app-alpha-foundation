<?php declare(strict_types=1);

namespace AlphaFoundation\Core\Content\MarketingBanner;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @method void               add(MarketingBannerEntity $entity)
 * @method void               set(string $key, MarketingBannerEntity $entity)
 * @method MarketingBannerEntity[]    getIterator()
 * @method MarketingBannerEntity[]    getElements()
 * @method MarketingBannerEntity|null get(string $key)
 * @method MarketingBannerEntity|null first()
 * @method MarketingBannerEntity|null last()
 */
class MarketingBannerCollection extends EntityCollection
{
    protected function getExpectedClass(): string
    {
        return MarketingBannerEntity::class;
    }
}