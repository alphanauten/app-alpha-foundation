import template from "./sw-property-detail-base.html.twig";

Shopware.Component.override('sw-property-detail-base', {
    template,

    inject: [
        'repositoryFactory',
    ],
    computed: {
        reviewPoolMergeRepository() {
            return this.repositoryFactory.create('alpha_property_review_pool_merge_extension');
        },
    },

    created() {
        this.createdComponent();
    },

    methods: {
        createdComponent() {
            if (!this.propertyGroup.extensions.reviewPoolMergeExtension) {
                this.propertyGroup.extensions['reviewPoolMergeExtension'] = this.reviewPoolMergeRepository.create(Shopware.Context.api);
                this.propertyGroup.extensions['reviewPoolMergeExtension'].propertyGroupId = this.propertyGroup.id;
            }
        }
    }
});