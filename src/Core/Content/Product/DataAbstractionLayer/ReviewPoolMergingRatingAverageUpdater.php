<?php declare(strict_types=1);

namespace AlphaFoundation\Core\Content\Product\DataAbstractionLayer;

use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\Connection;
use Monolog\Logger;
use mysql_xdevapi\Exception;
use Shopware\Core\Content\Product\DataAbstractionLayer\RatingAverageUpdater;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\Doctrine\RetryableQuery;
use Shopware\Core\Framework\Log\Package;
use Shopware\Core\Framework\Uuid\Uuid;

#[Package('framework')]
class ReviewPoolMergingRatingAverageUpdater extends RatingAverageUpdater
{

    public function __construct(
        private readonly Connection $connection,
        private Logger              $logger
    )
    {
        parent::__construct($this->connection);
    }

    /**
     * @param array<string> $ids
     * @throws \Doctrine\DBAL\Exception
     */
    public function update(array $ids, Context $context): void
    {
        if (empty($ids)) {
            return;
        }

        $versionId = Uuid::fromHexToBytes($context->getVersionId());

        RetryableQuery::retryable($this->connection, function () use ($ids, $versionId): void {
            $this->connection->executeStatement(
                'UPDATE product SET rating_average = NULL WHERE (parent_id IN (:ids) OR id IN (:ids)) AND version_id = :version',
                ['ids' => Uuid::fromHexToBytesList($ids), 'version' => $versionId],
                ['ids' => ArrayParameterType::BINARY]
            );
        });

        //sort out non merge products
        $query = $this->connection->createQueryBuilder();
        $query->select(
            'IFNULL(product.parent_id, product.id) as id',
            'AVG(product_review.points) as `average`'
        );
        $query->from('product');
        $query->leftJoin('product', 'product_category_tree', 'product_category_tree', 'product.id = product_category_tree.product_id');
        $query->leftJoin('product_category_tree', 'category', 'category', 'category.id = product_category_tree.category_id');
        $query->leftJoin('category', 'alpha_review_pool_merge_extension', 'alpha_review_pool_merge_extension', 'category.id = alpha_review_pool_merge_extension.category_id');
        $query->leftJoin('category', 'product_category_tree', 'second_tree', 'category.id = second_tree.category_id');

        $query->leftJoin('second_tree', 'product', 'all_products', 'all_products.id = second_tree.product_id');
        $query->leftJoin('all_products', 'product_review', 'product_review', 'all_products.id = product_review.product_id OR all_products.parent_id = product_review.product_id');

        $query->andWhere('product.id IN (:ids) OR product.parent_id IN (:ids)');
        $query->andWhere('alpha_review_pool_merge_extension.merge_review_pools = TRUE');
        $query->andWhere('product_review.status = 1');

        $query->setParameter('ids', Uuid::fromHexToBytesList($ids), ArrayParameterType::BINARY);
        $query->addGroupBy('IFNULL(product.parent_id, product.id)');
        $this->logger->error('exec query');
        try {
            $categoryAverages = $query->executeQuery()->fetchAllAssociative();
        } catch (\Exception $exception) {
            $this->logger->error($exception->getMessage());
            return;
        }

        $query = $this->connection->createQueryBuilder();
        $query->select(
            'IFNULL(product.parent_id, product.id) as id',
            'AVG(product_review.points) as `average`'
        );
        $query->from('product');
        $query->leftJoin('product', 'product_property', 'product_property', 'product.id = product_property.product_id');
        $query->leftJoin('product_property', 'property_group_option', 'property_group_option', 'property_group_option.id = product_property.property_group_option_id');
        $query->leftJoin('property_group_option', 'alpha_property_review_pool_merge_extension', 'alpha_property_review_pool_merge_extension', 'alpha_property_review_pool_merge_extension.property_group_id = property_group_option.property_group_id');
        $query->leftJoin('property_group_option', 'product_property', 'second_product_property', 'property_group_option.id = second_product_property.property_group_option_id');

        $query->leftJoin('property_group_option', 'product', 'all_products', 'all_products.id = second_product_property.product_id');
        $query->leftJoin('all_products', 'product_review', 'product_review', 'all_products.id = product_review.product_id OR all_products.parent_id = product_review.product_id');

        $query->andWhere('product.id IN (:ids) OR product.parent_id IN (:ids)');
        $query->andWhere('alpha_property_review_pool_merge_extension.merge_review_pools = TRUE');

        $query->setParameter('ids', Uuid::fromHexToBytesList($ids), ArrayParameterType::BINARY);
        $query->addGroupBy('IFNULL(product.parent_id, product.id)');
        $this->logger->error('exec query');
        try {
            $propertyAverages = $query->executeQuery()->fetchAllAssociative();
        } catch (\Exception $exception) {
            $this->logger->error($exception->getMessage());
            return;
        }

        $trueAverage = [];

        foreach ($categoryAverages as $product) {
            $trueAverage[bin2hex($product['id'])] = $product['average'];
        }
        foreach ($propertyAverages as $product) {
            if (!key_exists(bin2hex($product['id']), $trueAverage)) {
                $trueAverage[bin2hex($product['id'])] = $product['average'];
            } else {
                $trueAverage[bin2hex($product['id'])] = ($trueAverage[bin2hex($product['id'])] + $product['average']) / 2;
            }
        }

        $this->logger->error(print_r($trueAverage, true));

        $productsWithoutMerge = array_diff($ids, array_keys($trueAverage));
        $this->logger->error(print_r($productsWithoutMerge, true));
        parent::update($productsWithoutMerge, $context);

        $query = new RetryableQuery(
            $this->connection,
            $this->connection->prepare('UPDATE product SET rating_average = :average WHERE id = :id AND version_id = :version')
        );

        foreach ($trueAverage as $id => $average) {
            $query->execute([
                'average' => $average,
                'id' => hex2bin($id),
                'version' => $versionId,
            ]);
        }
    }
}
