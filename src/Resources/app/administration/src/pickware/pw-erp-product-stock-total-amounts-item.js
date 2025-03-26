/*
 * @package inventory
 */

const { Context } = Shopware;
import template from './pw-erp-product-stock-total-amounts-item.html.twig';

// eslint-disable-next-line sw-deprecation-rules/private-feature-declarations
Shopware.Component.override('pw-erp-stock-per-warehouse-grid', {
    template,



    methods: {

        getColumns() {
            const c = this.$super('getColumns')
            console.log(c)

            return c.map((column) => {
                return {
                    ...column,
                    allowResize: true,
                }
            });
        }
    }
});