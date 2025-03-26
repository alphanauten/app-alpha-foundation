import './sw-order-general-info.scss';
import template from './sw-order-general-info.html.twig';

/**
 * @package checkout
 */

const { Component, Mixin } = Shopware;

const { Criteria, EntityCollection } = Shopware.Data;
/**
 * @private
 */
Shopware.Component.override('sw-order-general-info', {
    template,

    data: {
      repertusDocuments: []
    },

    computed: {
        customerRepository() {
            return this.repositoryFactory.create('customer');
        },

        customerCriteria() {
            const criteria = new Criteria(1, null);

            criteria.addAssociation('repertusAgeVerification');

            return criteria;
        },
    },

    methods: {

        createdComponent() {
           this.$super('createdComponent')

            this.customerRepository
                .get(this.order.orderCustomer.customerId, Shopware.Context.api, this.customerCriteria)
                .then((customer) => {
                    this.repertusDocuments = customer.extensions.repertusAgeVerification;
                });
        },
    }
});
