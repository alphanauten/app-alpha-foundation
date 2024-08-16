<?php declare(strict_types=1);

namespace AlphaFoundation\Subscriber;

use AlphaFoundation\Core\Content\Product\ProductFeatureBuilder;
use Shopware\Core\Content\Product\Events\ProductListingCriteriaEvent;
use Shopware\Core\Content\Product\Events\ProductListingResultEvent;
use Shopware\Core\Content\Product\ProductEvents;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ProductFeatureSetLoader implements EventSubscriberInterface
{
    public function __construct(
        private readonly EntityRepository      $featureSetRepository,
        private readonly ProductFeatureBuilder $productFeatureBuilder
    )
    {
    }

    public static function getSubscribedEvents()
    {
        return [
            ProductEvents::PRODUCT_LISTING_CRITERIA => 'extendListingCriteria',
            ProductEvents::PRODUCT_LISTING_RESULT => 'extendResultWithFeatures'
        ];
    }

    public function extendListingCriteria(ProductListingCriteriaEvent $event)
    {
        $criteria = $event->getCriteria();

        $criteria->addAssociation('properties.group');
    }

    public function extendResultWithFeatures(ProductListingResultEvent $event)
    {
        $criteria = new Criteria(['018caa63e3d5722c9f3bcbe91fb4c1b6']);
        $listingFeatureSet = $this->featureSetRepository->search($criteria, $event->getContext())->first();

        $productListing = $event->getResult();
        $this->productFeatureBuilder->prepare($productListing->getEntities(), $listingFeatureSet, $event->getSalesChannelContext());
        $this->productFeatureBuilder->add($productListing->getEntities(), $listingFeatureSet);
    }

}
