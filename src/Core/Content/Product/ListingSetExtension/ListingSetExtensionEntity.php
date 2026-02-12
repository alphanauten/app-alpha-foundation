<?php declare(strict_types=1);

namespace AlphaFoundation\Core\Content\Product\ListingSetExtension;

use Shopware\Core\Content\Product\Aggregate\ProductFeatureSet\ProductFeatureSetEntity;
use Shopware\Core\Content\Product\ProductEntity;
use Shopware\Core\Framework\DataAbstractionLayer\Entity;

class ListingSetExtensionEntity extends Entity
{
    protected ProductEntity|null $product = null;
    protected ProductFeatureSetEntity|null $listingFeatureSet;

    public function getProduct(): ProductEntity
    {
        return $this->product;
    }

    public function setProduct(ProductEntity $product): void
    {
        $this->product = $product;
    }

    public function getListingFeatureSet(): ProductFeatureSetEntity|null
    {
        return $this->listingFeatureSet;
    }

    public function setListingFeatureSet(ProductFeatureSetEntity|null $listingFeatureSet): void
    {
        $this->listingFeatureSet = $listingFeatureSet;
    }
}