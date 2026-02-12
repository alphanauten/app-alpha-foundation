import template from './sw-category-detail-base.html.twig';


Shopware.Component.override('sw-category-detail-base', {
    template,

    computed: {
        reviewPoolMergeRepository() {
            return this.repositoryFactory.create('alpha_review_pool_merge_extension');
        },
    },

    created() {
        this.createdComponent();
    },

    methods: {
        createdComponent() {
            if (!this.category.extensions.reviewPoolMergeExtension) {
                this.category.extensions['reviewPoolMergeExtension'] = this.reviewPoolMergeRepository.create(Shopware.Context.api);
                this.category.extensions['reviewPoolMergeExtension'].categoryVersionId = this.category.versionId;
                this.category.extensions['reviewPoolMergeExtension'].categoryId = this.category.id;
            }
        }
    }
});