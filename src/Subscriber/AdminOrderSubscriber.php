<?php

declare(strict_types=1);

namespace AlphaFoundation\Subscriber;

use Doctrine\DBAL\Connection;
use Shopware\Core\Checkout\Payment\SalesChannel\AbstractPaymentMethodRoute;
use Shopware\Core\Checkout\Shipping\SalesChannel\AbstractShippingMethodRoute;
use Shopware\Core\Content\Product\Cart\ProductCartProcessor;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\System\SalesChannel\Context\SalesChannelContextPersister;
use Shopware\Core\System\SalesChannel\Event\SalesChannelProcessCriteriaEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class AdminOrderSubscriber implements EventSubscriberInterface
{

    public function __construct(
        private readonly RequestStack $requestStack,
        private readonly AbstractPaymentMethodRoute $paymentMethodRoute,
        private readonly AbstractShippingMethodRoute $shippingMethodRoute,
        private readonly SalesChannelContextPersister $contextPersister,
        private readonly EntityRepository $paymentMethodRepository,
        private readonly EntityRepository $shippingMethodRepository,
        private readonly Connection $connection,
    ) {
    }

    /**
     * @return string[]
     */
    public static function getSubscribedEvents(): array
    {
        // Return the events to listen to as array like this:  <event to listen to> => <method to execute>
        return [
            'sales_channel.product.process.criteria' => 'onSalesChannelProductCriteria',
        ];
    }


    public function onSalesChannelProductCriteria(SalesChannelProcessCriteriaEvent $event): Criteria
    {
        $criteria = $event->getCriteria();

        if ($event->getSalesChannelContext()->hasPermission(ProductCartProcessor::ALLOW_PRODUCT_PRICE_OVERWRITES)) {
            $criteria->resetFilters();
        }

        return $criteria;
    }

}
