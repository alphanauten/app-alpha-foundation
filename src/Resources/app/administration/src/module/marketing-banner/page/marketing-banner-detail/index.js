import template from './marketing-banner-detail.html.twig';
import './marketing-banner-detail.scss';

const { Component, Mixin, Context } = Shopware;
const { mapPropertyErrors } = Shopware.Component.getComponentHelper();
const { Criteria, EntityCollection } = Shopware.Data;


Component.register('marketing-banner-detail', {
    template,

    inject: [
        'repositoryFactory'
    ],

    mixins: [
        Mixin.getByName('notification')
    ],

    metaInfo() {
        return {
            title: this.$createTitle()
        };
    },

    data() {
        return {
            repository: null,
            banner: null,
            isLoading: false,
            isSaveSuccessful: false,
            categoriesCollection: null
        };
    },

    computed: {
        ...mapPropertyErrors('banner', [
            'name',
            'bannerType'
        ]),

        bannerTypeSelect() {
            return [{
                label: this.$tc("marketing-banner.detail.bannerTypeCategory"),
                value: 'category'
            }, {
                label: this.$tc("marketing-banner.detail.bannerTypeProduct"),
                value: 'product'
            }];
        },


        bannerRepository() {
            return this.repositoryFactory.create('marketing_banner');
        },

        categoryRepository() {
            return this.repositoryFactory.create('category');
        },

        categoryCriteria() {
            const criteria = new Criteria();
            return criteria;
        },

        ruleFilter() {
            const criteria = new Criteria();
            criteria.setLimit(null);
            return criteria;
        },

        isRuleSelectDisabled() {
            return false;
        }
    },

    created() {
        this.createdComponent();
    },

    methods: {
        async createdComponent() {
            this.isLoading = true;

            this.repository = this.repositoryFactory.create('marketing_banner');
            this.categoriesCollection = new EntityCollection('/category', 'category', Shopware.Context.api);

            await this.getBanner();
            await this.loadCategories();

            this.isLoading = false;
        },

        async getBanner() {
            this.banner = await this.repository.get(this.$route.params.id, Context.api);
        },

        async loadCategories() {
            const criteria = new Criteria(1, 100);
            const categories = this.banner.categories ? this.banner.categories : [];

            if (categories.length < 1) {
                return;
            }

            criteria.setIds(categories);

            return await this.categoryRepository
                .search(criteria, Object.assign({}, Shopware.Context.api, { inheritance: true }))
                .then((result) => {
                    this.categoriesCollection = result;
                });
        },

        onSelectionAdd(category) {
            if (!this.banner.categories) {
                this.$set(this.banner, 'categories', []);
            }

            this.banner.categories.push(category.id);
        },

        onSelectionRemove(category) {
            if (!this.banner.categories) {
                this.$set(this.banner, 'categories', []);
            }

            const index = this.banner.categories.indexOf(category.id);

            if (index !== -1) {
                this.banner.categories.splice(index, 1);
            }
        },

        onCancel() {
            this.$router.push({ name: 'marketing.banner.index' });
        },

        onSave() {
            this.isSaveSuccessful = false;
            this.isLoading = true;

            this.repository
                .save(this.banner, Context.api)
                .then(() => {
                    this.getBanner();
                    this.isSaveSuccessful = true;
                })
                .catch((exception) => {
                    this.createNotificationError({
                        title: this.$tc('marketing-banner.detail.notification.error'),
                        message: this.$tc('marketing-banner.detail.notification.errorMessage'),
                    });
                })
                .finally(() => this.isLoading = false);
        }
    }
});
