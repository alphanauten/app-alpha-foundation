import template from './sw-product-feature-set-form.html.twig';

Shopware.Component.override('sw-product-feature-set-form', {
    template,

    inject: [
        'repositoryFactory'
    ],

    computed: {
        listingFeatureSetRepository() {
            return this.repositoryFactory.create('alpha_product_extension_listing_feature_set');
        },
    },

    created() {
        this.createdComponent();
    },

    methods: {
        createdComponent() {
            if (!this.product.extensions.listingFeatureSet) {
                this.product.extensions['listingFeatureSet'] = this.listingFeatureSetRepository.create(Shopware.Context.api);
                this.product.extensions['listingFeatureSet'].productVersionId = this.product.versionId;
                this.product.extensions['listingFeatureSet'].productId = this.product.id;
            }
        }
    }
});