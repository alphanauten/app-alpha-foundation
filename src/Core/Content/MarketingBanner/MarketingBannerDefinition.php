<?php declare(strict_types=1);

namespace AlphaFoundation\Core\Content\MarketingBanner;

use AlphaFoundation\Core\Content\MarketingBanner\Aggregate\MarketingBannerCategory\MarketingBannerCategoryDefinition;
use AlphaFoundation\Core\Content\MarketingBanner\Aggregate\MarketingBannerRule\MarketingBannerRuleDefinition;
use Shopware\Core\Content\Category\CategoryDefinition;
use Shopware\Core\Content\Rule\RuleDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ConfigJsonField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\DateField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\ApiAware;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\CascadeDelete;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Inherited;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ListField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ManyToManyAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;
use Shopware\Core\Framework\DataAbstractionLayer\Field\BoolField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\StringField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;

class MarketingBannerDefinition extends EntityDefinition
{
    final public const ENTITY_NAME = 'alpha_marketing_banner';

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

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new IdField('id', 'id'))->addFlags(new Required(), new PrimaryKey()),
            (new StringField('name', 'name'))->addFlags(new Required(), new ApiAware()),
            (new StringField('description', 'description')),
            (new BoolField('active', 'active')),
            (new StringField('banner_type', 'bannerType'))->addFlags(new Required(), new ApiAware()),
            (new ListField('categories', 'categories', IdField::class))
                ->addFlags(new ApiAware(), new Inherited()),
//            (new ConfigJsonField('category_list', 'categoryList'))->addFlags(new Required(), new ApiAware()),
//            (new ManyToManyAssociationField('categories', CategoryDefinition::class, MarketingBannerCategoryDefinition::class, 'banner_id', 'category_id'))->addFlags(new CascadeDelete()),
//            (new ManyToManyAssociationField('rules', RuleDefinition::class, MarketingBannerRuleDefinition::class, 'banner_id', 'rule_id'))->addFlags(new CascadeDelete()),
            (new DateField('valid_from', 'validFrom')),
            (new DateField('valid_until', 'validUntil')),
        ]);
    }
}
