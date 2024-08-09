<?php declare(strict_types=1);

namespace AlphaFoundation\Core\Content\MarketingBanner;

use Shopware\Core\Content\Category\CategoryCollection;
use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityIdTrait;
use Shopware\Core\Content\Rule\RuleCollection;

class MarketingBannerEntity extends Entity
{
    use EntityIdTrait;

    /**
     * @var string
     */
    protected string $name;

    /**
     * @var string|null
     */
    protected ?string $description = null;

    /**
     * @var bool
     */
    protected bool $active;

    /**
     * @var string
     */
    protected string $bannerType;

    /**
     * @var array|bool|float|int|string|null
     */
    protected ?CategoryCollection $categories = null;

    /**
     * @var RuleCollection|null
     */
    protected ?RuleCollection $rules = null;

    /**
     * @var \DateTimeInterface|null
     */
    protected $validFrom;

    /**
     * @var \DateTimeInterface|null
     */
    protected $validUntil;

    /**
     * MarketingBannerEntity constructor.
     */
    public function __construct() {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): void
    {
        $this->active = $active;
    }

    public function getBannerType(): string
    {
        return $this->bannerType;
    }

    public function setBannerType(string $bannerType): void
    {
        $this->bannerType = $bannerType;
    }

    public function setRules(?RuleCollection $rules): void
    {
        $this->rules = $rules;
    }

    public function getRules(): ?RuleCollection {
        return $this->rules;
    }

    public function getValidFrom()
    {
        return $this->validFrom;
    }

    public function setValidFrom(\DateTimeInterface $validFrom)
    {
        $this->validFrom = $validFrom;
    }

    public function getValidUntil()
    {
        return $this->validUntil;
    }

    public function setValidUntil(\DateTimeInterface $validUntil)
    {
        $this->validUntil = $validUntil;
    }

    public function getCategories(): float|array|bool|int|string|null
    {
        return $this->categories;
    }

    public function setCategories(float|array|bool|int|string|null $categories): void
    {
        $this->categories = $categories;
    }
}
