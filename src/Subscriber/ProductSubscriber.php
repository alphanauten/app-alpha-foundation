<?php

declare(strict_types=1);

namespace AlphaFoundation\Subscriber;

use AlphaFoundation\Core\Content\MarketingBanner\MarketingBannerEntity;
use AlphaFoundation\Core\Content\Product\ProductFeatureBuilder;
use Shopware\Core\Content\Category\Event\NavigationLoadedEvent;
use Shopware\Core\Content\Cms\DataResolver\ResolverContext\ResolverContext;
use Shopware\Core\Content\Product\Events\ProductListingCriteriaEvent;
use Shopware\Core\Content\Product\ProductEvents;
use Shopware\Core\Content\Product\SalesChannel\SalesChannelProductEntity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\System\SalesChannel\Entity\SalesChannelEntityLoadedEvent;
use Shopware\Core\System\SalesChannel\Entity\SalesChannelRepository;
use Shopware\Storefront\Page\Product\ProductPageLoadedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Shopware\Core\Framework\DataAbstractionLayer\DefinitionInstanceRegistry;
use Shopware\Core\Content\Cms\DataResolver\CmsSlotsDataResolver;
use Shopware\Core\Content\Cms\Aggregate\CmsSlot\CmsSlotCollection;


class ProductSubscriber implements EventSubscriberInterface
{
    public const PRODUCT_BANNER_TYPE = 'product';
    public const CATEGORY_BANNER_TYPE = 'category';

    public function __construct(
        private readonly SalesChannelRepository $productRepository,
        private readonly EntityRepository      $featureSetRepository,
        private readonly ProductFeatureBuilder $productFeatureBuilder,
        private readonly EntityRepository $marketingBannerRepository,
        private readonly DefinitionInstanceRegistry $definitionInstanceRegistry,
        private readonly CmsSlotsDataResolver $resolver
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
            ProductPageLoadedEvent::class => 'onProductPageLoaded',
            NavigationLoadedEvent::class => 'onNavigationLoaded'
        ];
    }

    public function onNavigationLoaded(NavigationLoadedEvent $event): void
    {
        $activeCategory = $event->getNavigation()->getActive();
        $salesChannelRulesIds = $event->getSalesChannelContext()->getRuleIds() ?? [];
        $marketingBanners = $this->marketingBannerRepository->search($this->buildProductMarketingBannerCriteria(self::CATEGORY_BANNER_TYPE), $event->getContext())->getEntities();
        /** @var MarketingBannerEntity $marketingBanner */
        foreach ($marketingBanners as $marketingBanner)
        {
            $marketingRules = $marketingBanner->getRules()->getIds() ?? [];

            if (empty($marketingRules))
            {
                continue;
            }
            $isMarketingRulesIncluded = array_diff(array_keys($marketingRules),array_values($salesChannelRulesIds));
            if (!empty($isMarketingRulesIncluded))
            {
                $marketingBanners->remove($marketingBanner->getId());
            }
        }
        $salesChannelContext = $event->getSalesChannelContext();

        if ($marketingBanners->count() === 0) {
            return;
        }

        $cmsSlotCollection = CmsSlotCollection::createFrom($marketingBanners);

        $resolverContext = new ResolverContext($salesChannelContext, new \Symfony\Component\HttpFoundation\Request());

        $this->resolver->resolve($cmsSlotCollection, $resolverContext);


        $activeCategory->assign(['alphaMarketingBanners' => $cmsSlotCollection]);
    }
    public function extendListingCriteria(ProductListingCriteriaEvent $event): void
    {
        $criteria = $event->getCriteria();
        $criteria->addAssociation('properties.group');
        $criteria->addAssociation('marketing_banner');
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

    public function onProductPageLoaded(ProductPageLoadedEvent $event)
    {
        $page = $event->getPage();
        $salesChannelRulesIds = $event->getSalesChannelContext()->getRuleIds() ?? [];
        $marketingBanners = $this->marketingBannerRepository->search($this->buildProductMarketingBannerCriteria(self::PRODUCT_BANNER_TYPE), $event->getContext())->getEntities();
        /** @var MarketingBannerEntity $marketingBanner */
        foreach ($marketingBanners as $marketingBanner)
        {
            $marketingRules = $marketingBanner->getRules()->getIds() ?? [];

            if (empty($marketingRules))
            {
                continue;
            }
            $isMarketingRulesIncluded = array_diff(array_keys($marketingRules),array_values($salesChannelRulesIds));
            if (!empty($isMarketingRulesIncluded))
            {
                $marketingBanners->remove($marketingBanner->getId());
            }
        }

        $salesChannelContext = $event->getSalesChannelContext();

        if ($marketingBanners->count() === 0) {
            return;
        }

        $cmsSlotCollection = CmsSlotCollection::createFrom($marketingBanners);

        $resolverContext = new ResolverContext($salesChannelContext, new \Symfony\Component\HttpFoundation\Request());

        $this->resolver->resolve($cmsSlotCollection, $resolverContext);

        $page->assign(['alphaMarketingBanners'=>$cmsSlotCollection]);
    }

    protected function buildProductMarketingBannerCriteria(string $bannerType): Criteria
    {
        $criteria = new Criteria();
        $criteria->addAssociation('rules');
        $criteria->addFilter(new EqualsFilter('bannerType', $bannerType));
        return $criteria;
    }
}
