<?php

namespace AlphaFoundation\Resolver;

use Shopware\Core\Content\Cms\Aggregate\CmsSlot\CmsSlotEntity;
use Shopware\Core\Content\Cms\DataResolver\Element\ElementDataCollection;
use Shopware\Core\Content\Cms\DataResolver\ResolverContext\EntityResolverContext;
use Shopware\Core\Content\Cms\DataResolver\ResolverContext\ResolverContext;
use Shopware\Core\Content\Cms\SalesChannel\Struct\BuyBoxStruct;
use Shopware\Core\Content\Product\Cms\BuyBoxCmsElementResolver;
use Shopware\Core\Content\Product\SalesChannel\Detail\ProductConfiguratorLoader;
use Shopware\Core\Content\Product\SalesChannel\Review\AbstractProductReviewRoute;
use Shopware\Core\Content\Product\SalesChannel\SalesChannelProductEntity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Aggregation\Metric\CountAggregation;
use Shopware\Core\Framework\DataAbstractionLayer\Search\AggregationResult\Metric\CountResult;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\MultiFilter;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Symfony\Component\HttpFoundation\Request;

class CustomBuyBoxCmsElementResolver extends BuyBoxCmsElementResolver
{
    public function __construct(
        private readonly ProductConfiguratorLoader  $configuratorLoader,
        private readonly EntityRepository           $productReviewRepository,
        private readonly AbstractProductReviewRoute $productReviewRoute
    )
    {
        parent::__construct($configuratorLoader, $productReviewRepository);
    }

    public function enrich(CmsSlotEntity $slot, ResolverContext $resolverContext, ElementDataCollection $result): void
    {
        $buyBox = new BuyBoxStruct();
        $slot->setData($buyBox);

        $productConfig = $slot->getFieldConfig()->get('product');
        if ($productConfig === null) {
            return;
        }

        $product = null;

        if ($productConfig->isMapped() && $resolverContext instanceof EntityResolverContext) {
            $product = $this->resolveEntityValue($resolverContext->getEntity(), $productConfig->getStringValue());
        }

        if ($productConfig->isStatic()) {
            $product = $this->getSlotProduct($slot, $result, $productConfig->getStringValue());
        }

        /** @var SalesChannelProductEntity|null $product */
        if ($product !== null) {
            $buyBox->setProduct($product);
            $buyBox->setProductId($product->getId());
            $buyBox->setConfiguratorSettings($this->configuratorLoader->load($product, $resolverContext->getSalesChannelContext()));
            $buyBox->setTotalReviews($this->getReviewsCount($product, $resolverContext->getSalesChannelContext()));
        }
    }

    protected function getReviewsCount(SalesChannelProductEntity $product, SalesChannelContext $context): int
    {
        $result = $this->productReviewRoute->load($product->getId(), new Request(), $context, new Criteria());

        return $result->getResult()->count();
    }

}