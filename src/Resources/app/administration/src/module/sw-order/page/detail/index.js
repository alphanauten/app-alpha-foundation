/**
 * @package checkout
 */

const { Mixin } = Shopware;
const { Criteria } = Shopware.Data;

// eslint-disable-next-line sw-deprecation-rules/private-feature-declarations
Shopware.Component.override('sw-order-detail', {
    computed: {
        orderCriteria() {
            const orderCriteria = this.$super('orderCriteria');

            orderCriteria.addAssociation('lineItems.product.pickwareErpPickwareProduct');

            return orderCriteria
        }
    },
});


