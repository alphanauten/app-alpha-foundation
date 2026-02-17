/**
 * @package checkout
 */

const {Criteria} = Shopware.Data;

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
