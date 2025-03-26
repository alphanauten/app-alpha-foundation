/**
 * @package checkout
 */

const { Component, State } = Shopware;
const { Criteria } = Shopware.Data;

// eslint-disable-next-line sw-deprecation-rules/private-feature-declarations
Shopware.Component.override('sw-order-create-options', {
    computed: {

        shippingMethodCriteria() {
            const criteria = new Criteria();
            criteria.addFilter(Criteria.equals('active', 1));

            return criteria;
        },

        paymentMethodCriteria() {
            const criteria = new Criteria();
            criteria.addFilter(Criteria.equals('active', 1));

            return criteria;
        },
    },
});
