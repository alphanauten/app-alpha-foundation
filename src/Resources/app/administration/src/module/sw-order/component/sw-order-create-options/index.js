/**
 * @package checkout
 */

const { Criteria } = Shopware.Data;

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
