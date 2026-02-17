<?php declare(strict_types=1);

namespace AlphaFoundation\Core\Content\Product\SalesChannel\Review;

use AlphaFoundation\Service\LeafCategoriesResolver;
use Shopware\Core\Content\Category\CategoryEntity;
use Shopware\Core\Content\Product\ProductEntity;
use Shopware\Core\Content\Product\SalesChannel\Review\AbstractProductReviewRoute;
use Shopware\Core\Content\Product\SalesChannel\Review\ProductReviewRouteResponse;
use Shopware\Core\Content\Property\Aggregate\PropertyGroupOption\PropertyGroupOptionEntity;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsAnyFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\MultiFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Sorting\FieldSorting;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Symfony\Component\HttpFoundation\Request;

class AggregateProductReviewRoute extends AbstractProductReviewRoute
{
    private AbstractProductReviewRoute $productReviewRoute;

    public function __construct(
        AbstractProductReviewRoute        $productReviewRoute,
        private readonly EntityRepository $productRepository,
        private readonly EntityRepository $productReviewRepository,
        private readonly EntityRepository $propertyGroupOptionRepository,
    )
    {
        $this->productReviewRoute = $productReviewRoute;
    }

    public function getDecorated(): AbstractProductReviewRoute
    {
        return $this->productReviewRoute;
    }

    public function load(string $productId, Request $request, SalesChannelContext $context, Criteria $criteria): ProductReviewRouteResponse
    {
        // Produkt mit Kategorien laden
        $productCriteria = (new Criteria([$productId]))
            ->addAssociation('categoriesRo')
            ->addAssociation('properties.group');

        /** @var ProductEntity|null $product */
        $product = $this->productRepository->search($productCriteria, $context->getContext())->first();
        if (!$product) {
            return $this->productReviewRoute->load($productId, $request, $context, $criteria);
        }

        $categoriesToMerge = [];
        $propertiesToMerge = [];
        /**
         * @var CategoryEntity $category
         */
        foreach ($product->getCategoriesRo() as $category) {
            $reviewPoolMergeExtension = $category->getExtension('reviewPoolMergeExtension');
            if (!is_null($reviewPoolMergeExtension) && $reviewPoolMergeExtension->getVars()['mergeReviewPools']) {
                $categoriesToMerge[] = $category->getId();
            }
        }

        foreach ($product->getProperties() as $property) {
            $reviewPoolMergeExtension = $property->getGroup()->getExtension('reviewPoolMergeExtension');
            if (!is_null($reviewPoolMergeExtension) && $reviewPoolMergeExtension->getVars()['mergeReviewPools']) {
                $propertiesToMerge[] = $property->getId();
            }
        }

        if (count($categoriesToMerge) == 0 && count($propertiesToMerge) == 0) {
            return $this->productReviewRoute->load($productId, $request, $context, $criteria);
        }

        $active = new MultiFilter(MultiFilter::CONNECTION_OR, [new EqualsFilter('status', true)]);
        if ($customer = $context->getCustomer()) {
            $active->addQuery(new EqualsFilter('customerId', $customer->getId()));
        }

        $criteria->addFilter($active);
        $criteria->setTitle('product-review-route');
        $productIds = [];
        // Alle Produkt-IDs in diesen Leaf-Kategorien ermitteln
        if (count($categoriesToMerge) > 0) {
            $productIds = array_merge($productIds, $this->getProductIdsInLeafCategories($categoriesToMerge, $context));
        }
        if (count($propertiesToMerge) > 0) {
            $productIds = array_merge($productIds, $this->getProductIdsFromProperties($propertiesToMerge, $context));
        }

        // WICHTIG: Eigene Filter setzen (nicht die ursprünglichen productId-Filter übernehmen)
        $criteria->addFilter(
            new MultiFilter(MultiFilter::CONNECTION_AND, [
                $active,
                new EqualsAnyFilter('productId', $productIds),
            ])
        );


        $result = $this->productReviewRepository->search($criteria, $context->getContext());

        return new ProductReviewRouteResponse($result);
    }

    private function getProductIdsInLeafCategories(array $categoryIds, SalesChannelContext $context): array
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('active', true));
        $criteria->addFilter(new EqualsFilter('visibilities.salesChannelId', $context->getSalesChannelId()));
        $criteria->addFilter(new EqualsAnyFilter('categoriesRo.id', $categoryIds));
        $criteria->setLimit(10000);

        return array_values(array_unique(
            $this->productRepository->searchIds($criteria, $context->getContext())->getIds()
        ));
    }

    private function getProductIdsFromProperties(array $propertyIds, SalesChannelContext $context): array
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsAnyFilter('id', $propertyIds));
        $criteria->addAssociation('productProperties');
        $properties = $this->propertyGroupOptionRepository->search($criteria, $context->getContext())->getElements();

        $productIds = [];
        /**
         * @var PropertyGroupOptionEntity $property
         */
        foreach ($properties as $property) {

            $productIds = array_merge($productIds,$property->getProductProperties()->getIds());
        }

        return array_values(array_unique(
            $productIds
        ));
    }
}
