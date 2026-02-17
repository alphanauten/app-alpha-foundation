import template from './sw-order-list.html.twig';

/**
 * @package checkout
 */


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
