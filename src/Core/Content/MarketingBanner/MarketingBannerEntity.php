<?php declare(strict_types=1);

namespace AlphaFoundation\Core\Content\MarketingBanner;

use Shopware\Core\Content\Media\MediaEntity;
use Shopware\Core\Content\Rule\RuleCollection;
use Shopware\Core\Content\Cms\Aggregate\CmsSlot\CmsSlotEntity;

class MarketingBannerEntity extends CmsSlotEntity
{
    protected $slot = 'content';

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
     * @var array<string>
     */
    protected array $categories = [];

    /**
     * @var string
     */
    protected string $text = "";

    /**
     * @var string|null
     */
    protected ?string $mediaId = null;

    /**
     * @var MediaEntity|null
     */
    protected ?MediaEntity $media = null;

    /**
     * @var string
     */
    protected string $backgroundColor;

    /**
     * @var string
     */
    protected string $textColor;

    /**
     * @var string
     */
    protected string $border;

    /**
     * @var string
     */
    protected string $css = "";

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

    public function getText(): string
    {
        return $this->text;
    }

    public function setText(string $text): void
    {
        $this->text = $text;
    }

    public function getCss(): string
    {
        return $this->css;
    }

    public function setCss(string $css): void
    {
        $this->css = $css;
    }

    public function getMediaId(): string
    {
        return $this->mediaId;
    }

    public function setMediaId(string $mediaId): void
    {
        $this->mediaId = $mediaId;
    }

    public function getMedia(): ?MediaEntity
    {
        return $this->media;
    }

    public function setMedia(?MediaEntity $media): void
    {
        $this->media = $media;
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

    public function getCategories(): string|array
    {
        return $this->categories;
    }

    public function setCategories(array|string $categories): void
    {
        $this->categories = $categories;
    }
}
