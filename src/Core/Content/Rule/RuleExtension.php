<?php declare(strict_types=1);

namespace AlphaFoundation\Core\Content\Rule;

use AlphaFoundation\Core\Content\MarketingBanner\Aggregate\MarketingBannerRule\MarketingBannerRuleDefinition;
use AlphaFoundation\Core\Content\MarketingBanner\MarketingBannerDefinition;
use Shopware\Core\Content\Rule\RuleDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityExtension;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Inherited;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ManyToManyAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;

class RuleExtension extends EntityExtension
{
    public function extendFields(FieldCollection $collection): void
    {
        $collection->add(
            (new ManyToManyAssociationField(
                'marketing_banner_rule',
                MarketingBannerDefinition::class,
                MarketingBannerRuleDefinition::class,
                'rule_id',
                'banner_id'
            ))->addFlags(new Inherited())
        );
    }

    public function getDefinitionClass(): string
    {
        return RuleDefinition::class;
    }
}