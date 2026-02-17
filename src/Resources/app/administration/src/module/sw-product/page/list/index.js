/*
 * @package inventory
 */

import template from './sw-product-list.html.twig';

Shopware.Component.override('pw-erp-warehouse-grid', {
    template,

    computed: {
      defaultCurrency() {
          return this.getDefaultCurrency()
      }
    },

    methods: {
        getDefaultCurrency() {
            return this.currencies.find((currency) => {
                return currency.isSystemDefault;
            });
        },

        getProductColumns() {
            const columns = this.$super('getProductColumns');
            const defaultCurrency = this.getDefaultCurrency()

            return [
                ...columns,
                {
                    property: 'purchasePrice',
                    label: this.$tc('sw-product.list.purchasePrice'),
                    dataIndex: 'purchasePrice.price',
                    allowResize: true,
                    // currencyId: defaultCurrency?.id ?? Context.app.systemCurrencyId,
                    visible: true,
                },
            ];
        },

        onDuplicate(referenceProduct) {
            return this.$super('onDuplicate', referenceProduct);
        },

        onDuplicateFinish(duplicate) {
            return this.$super('onDuplicateFinish', duplicate);
        },

        debugData(item) {
            console.log(item)
        }
    },
});
