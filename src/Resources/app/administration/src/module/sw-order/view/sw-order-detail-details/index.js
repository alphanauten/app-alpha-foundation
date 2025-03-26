import template from './sw-order-detail-details.html.twig';

Shopware.Component.override('sw-order-detail-details', {
    template,

    computed: {
        disableChangePayment() {
            if (this.transaction.stateMachineState.technicalName !== 'open') {
                return true
            }
        },
        disableChangeShipping() {
            if (this.order.deliveries.first().stateMachineState.technicalName !== 'open') {
                return true
            }
        },

        paymentMethodCriteria() {
            const criteria = new Criteria(1, 25);

            criteria.addFilter(Criteria.equals('active', 1));

            return criteria;
        },

        shippingMethodCriteria() {
            const criteria = new Criteria(1, 25);

            criteria.addFilter(Criteria.equals('active', 1));

            return criteria;
        },
    }
});