<?php

declare(strict_types=1);

namespace AlphaFoundation\Controller;

use AlphaFoundation\Core\Content\Flow\Event\OrderUpdateEvent;
use Shopware\Core\Checkout\Order\OrderCollection;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

#[Route(defaults: ['_routeScope' => ['api']])]
class OrderController extends AbstractController
{
    /**
     * @param EntityRepository<OrderCollection> $orderRepository
     */
    public function __construct(
        private readonly EntityRepository $orderRepository,
        private readonly EventDispatcher $eventDispatcher,
    ) {
    }

    #[Route(path: '/api/_action/alpha-foundation/order/update', name: 'api.action.alpha-foundation.order.update', defaults: ['_acl' => ['admin']], methods: ['PUT'])]
    public function updatedOrderEvent(string $orderId, Request $request, Context $context): JsonResponse
    {
        $order = $this->orderRepository->search(new Criteria([$orderId]), $context)->first();

        if (!$order) {
            throw new NotFoundHttpException();
        }

        $this->eventDispatcher->dispatch(
            new OrderUpdateEvent($order, $context),
            OrderUpdateEvent::EVENT_NAME
        );

        return new JsonResponse(['success' => true]);
    }
}
