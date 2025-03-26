
/**
 * @package checkout
 */

const { Service } = Shopware;
const { Criteria } = Shopware.Data;

// eslint-disable-next-line sw-deprecation-rules/private-feature-declarations
Shopware.Component.override('sw-order-product-select', {

    computed: {
        productCriteria() {
            const criteria = new Criteria(1, 25);

            criteria.addAssociation('options.group');

            criteria.addFilter(
                Criteria.multi('OR', [
                    Criteria.equals('childCount', 0),
                    Criteria.equals('childCount', null),
                ]),
            );

            return criteria;
        },
    },
});
