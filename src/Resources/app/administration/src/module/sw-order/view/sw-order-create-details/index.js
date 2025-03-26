/**
 * @package checkout
 */

const {Component, Mixin, State} = Shopware;
const {Criteria} = Shopware.Data;

// eslint-disable-next-line sw-deprecation-rules/private-feature-declarations

Shopware.Component.override('sw-order-create-details', {
    computed: {

        shippingMethodCriteria() {
            const criteria = new Criteria(1, 25);

            criteria.addFilter(Criteria.equals('active', 1));

            return criteria;
        },

        paymentMethodCriteria() {
            const criteria = new Criteria(1, 25);

            criteria.addFilter(Criteria.equals('active', 1));

            return criteria;
        },
    },
});
