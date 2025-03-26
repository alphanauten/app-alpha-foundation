import template from './sw-order-list.html.twig';

/**
 * @package checkout
 */

const { Mixin } = Shopware;
const { Criteria } = Shopware.Data;

// eslint-disable-next-line sw-deprecation-rules/private-feature-declarations
Shopware.Component.override('sw-order-list', {
    template,

    methods: {
        getOrderColumns() {
            const columns = this.$super('getOrderColumns');

            return [
                ...columns,
                {
                    property: 'phone',
                    dataIndex: 'billingAddress.phoneNumber',
                    label: 'sw-order.list.columnBillingPhone',
                    allowResize: true,
                    visible: false,
                },
            ];
        },
    },
});
