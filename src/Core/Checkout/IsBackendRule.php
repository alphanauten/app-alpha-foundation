<?php declare(strict_types=1);

namespace AlphaFoundation\Core\Checkout;

use Shopware\Core\Content\Product\Cart\ProductCartProcessor;
use Shopware\Core\Framework\Log\Package;
use Shopware\Core\Framework\Rule\Rule;
use Shopware\Core\Framework\Rule\RuleConfig;
use Shopware\Core\Framework\Rule\RuleConstraints;
use Shopware\Core\Framework\Rule\RuleScope;

#[Package('services-settings')]
class IsBackendRule extends Rule
{
    final public const RULE_NAME = 'isAdmin';

    /**
     * @var bool
     *
     * @deprecated tag:v6.7.0 - Will be natively typed
     */
    protected $isAdmin;

    /**
     * @internal
     */
    public function __construct(bool $isAdmin = true)
    {
        parent::__construct();
        $this->isAdmin = $isAdmin;
    }

    public function match(RuleScope $scope): bool
    {
        return $scope->getSalesChannelContext()->hasPermission(ProductCartProcessor::ALLOW_PRODUCT_PRICE_OVERWRITES);
    }

    public function getConstraints(): array
    {
        return [
            'isAdmin' => RuleConstraints::bool(true),
        ];
    }

    public function getConfig(): RuleConfig
    {
        return (new RuleConfig())
            ->booleanField('isAdmin');
    }
}
