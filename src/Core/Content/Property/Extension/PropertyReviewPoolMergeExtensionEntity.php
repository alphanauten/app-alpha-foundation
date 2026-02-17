<?php declare(strict_types=1);

namespace AlphaFoundation\Core\Content\Property\Extension;

use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityIdTrait;

class PropertyReviewPoolMergeExtensionEntity extends Entity
{
    use EntityIdTrait;
    protected ?bool $mergeReviewPools;

    public function setMergeReviewPools(bool $mergeReviewPools): void
    {
        $this->mergeReviewPools = $mergeReviewPools;
    }

    public function getMergeReviewPools(): bool
    {
        return $this->mergeReviewPools;
    }
}