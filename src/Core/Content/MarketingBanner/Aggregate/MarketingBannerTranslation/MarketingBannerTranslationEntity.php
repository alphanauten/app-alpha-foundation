<?php declare(strict_types=1);

namespace AlphaFoundation\Core\Content\MarketingBanner\Aggregate\MarketingBannerTranslation;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCustomFieldsTrait;
use Shopware\Core\Framework\DataAbstractionLayer\TranslationEntity;

class MarketingBannerTranslationEntity extends TranslationEntity
{
    use EntityCustomFieldsTrait;

    protected ?array $config = null;

    public function getConfig(): ?array
    {
        return $this->config;
    }

    public function setConfig(array $config): void
    {
        $this->config = $config;
    }
}
