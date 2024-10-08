<?php declare(strict_types=1);

namespace AlphaFoundation\Core\Content\MarketingBanner;

use AlphaFoundation\Core\Content\MarketingBanner\Aggregate\MarketingBannerRule\MarketingBannerRuleDefinition;
use AlphaFoundation\Core\Content\MarketingBanner\Aggregate\MarketingBannerTranslation\MarketingBannerTranslationDefinition;
use Shopware\Core\Content\Media\MediaDefinition;
use Shopware\Core\Content\Rule\RuleDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\DateField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\FkField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\AllowHtml;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\ApiAware;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\CascadeDelete;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Runtime;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\WriteProtected;
use Shopware\Core\Framework\DataAbstractionLayer\Field\JsonField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ListField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\LongTextField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ManyToManyAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ManyToOneAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\TranslatedField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\TranslationsAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;
use Shopware\Core\Framework\DataAbstractionLayer\Field\BoolField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\StringField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;

class MarketingBannerDefinition extends EntityDefinition
{
    final public const ENTITY_NAME = 'marketing_banner';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getEntityClass(): string
    {
        return MarketingBannerEntity::class;
    }

    public function getCollectionClass(): string
    {
        return MarketingBannerCollection::class;
    }

    public function getDefaults(): array
    {
        return [
            'name' => '',
            'type' => 'alpha-replacer',
            'active' => true,
            'page' => 1,
            'minResults' => 0,
            'autoSize' => false
        ];
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new IdField('id', 'id'))->addFlags(new Required(), new PrimaryKey()),
            (new StringField('name', 'name'))->addFlags(new Required(), new ApiAware()),
            (new StringField('description', 'description')),
            (new BoolField('active', 'active')),
            (new StringField('banner_type', 'bannerType'))->addFlags(new Required(), new ApiAware()),
            (new ListField('categories', 'categories'))->addFlags(new ApiAware()),
            (new StringField('text', 'text'))->addFlags(new ApiAware(), new AllowHtml()),
            (new StringField('background_color', 'backgroundColor'))->addFlags(new ApiAware()),
            (new StringField('text_color', 'textColor'))->addFlags(new ApiAware()),
            (new StringField('border', 'border'))->addFlags(new ApiAware()),
            (new TranslatedField('config'))->addFlags(new ApiAware()),
            (new TranslatedField('customFields'))->addFlags(new ApiAware()),
            (new LongTextField('css', 'css'))->addFlags(new AllowHtml(false)),
            (new StringField('type', 'type'))->addFlags(new ApiAware()),
            (new JsonField('data', 'data'))->addFlags(new ApiAware(), new Runtime(), new WriteProtected()),
            (new DateField('valid_from', 'validFrom')),
            (new DateField('valid_until', 'validUntil')),
            (new FkField('media_id', 'mediaId', MediaDefinition::class))->addFlags(new ApiAware()),
            (new ManyToOneAssociationField('media', 'media_id', MediaDefinition::class, 'id', true))->addFlags(new ApiAware()),
            (new ManyToManyAssociationField('rules', RuleDefinition::class, MarketingBannerRuleDefinition::class, 'banner_id', 'rule_id'))->addFlags(new CascadeDelete()),
            (new TranslationsAssociationField(MarketingBannerTranslationDefinition::class, 'marketing_banner_id'))->addFlags(new ApiAware()),
        ]);
    }
}
