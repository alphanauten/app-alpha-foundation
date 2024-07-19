<?php

declare(strict_types=1);

namespace AlphaFoundation\Subscriber;

use AlphaFoundation\Core\Content\Product\ProductFeatureBuilder;
use Shopware\Core\Content\Product\Events\ProductIndexerEvent;
use Shopware\Core\Content\Product\Events\ProductListingCriteriaEvent;
use Shopware\Core\Content\Product\ProductEvents;
use Shopware\Core\Content\Product\SalesChannel\SalesChannelProductEntity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\System\SalesChannel\Entity\SalesChannelEntityLoadedEvent;
use Shopware\Core\System\SalesChannel\Entity\SalesChannelRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ProductSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly SalesChannelRepository $productRepository,
        private readonly EntityRepository      $featureSetRepository,
        private readonly ProductFeatureBuilder $productFeatureBuilder
    ) {
    }

    /**
     * @return string[]
     */
    public static function getSubscribedEvents(): array
    {
        // Return the events to listen to as array like this:  <event to listen to> => <method to execute>
        return [
            ProductEvents::PRODUCT_LISTING_CRITERIA => 'extendListingCriteria',
            'sales_channel.'.ProductEvents::PRODUCT_LOADED_EVENT => 'onProductsLoaded',
            ProductIndexerEvent::class => 'onProductIndexerEvent',
        ];
    }

    public function extendListingCriteria(ProductListingCriteriaEvent $event)
    {
        $criteria = $event->getCriteria();

        $criteria->addAssociation('properties.group');
    }

    public function onProductsLoaded(SalesChannelEntityLoadedEvent $event)
    {
        /** @var SalesChannelProductEntity $productEntity */
        foreach ($event->getEntities() as $productEntity) {
            $variantListingConfig = $productEntity->getVariantListingConfig();
            $variantListingConfig?->assign([
                'displayParent' => true,
                'mainVariantId' => null,
            ]);

            if ( $productEntity->getCalculatedCheapestPrice() && $productEntity->getChildCount() > 0 ) {
                $productEntity->setCalculatedPrice(
                    $productEntity->getCalculatedCheapestPrice()
                );
            }
        }

        $this->extendProductsWithFeatures($event);
    }

    public function extendProductsWithFeatures(SalesChannelEntityLoadedEvent $event)
    {
        $criteria = new Criteria(['018caa63e3d5722c9f3bcbe91fb4c1b6']);
        $listingFeatureSet = $this->featureSetRepository->search($criteria, $event->getContext())->first();

        if ( ! $listingFeatureSet ) {
            return;
        }

        $this->productFeatureBuilder->prepare($event->getEntities(), $listingFeatureSet, $event->getSalesChannelContext());
        $this->productFeatureBuilder->add($event->getEntities(), $listingFeatureSet);
    }


    public function onProductIndexerEvent(ProductIndexerEvent $event)
    {

    }
}
