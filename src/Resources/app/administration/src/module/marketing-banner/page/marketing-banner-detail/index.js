import template from './marketing-banner-detail.html.twig';
import './marketing-banner-detail.scss';

const { Component, Mixin, Context } = Shopware;
const { mapPropertyErrors } = Shopware.Component.getComponentHelper();
const { Criteria, EntityCollection } = Shopware.Data;


Component.register('marketing-banner-detail', {
    template,

    inject: [
        'repositoryFactory',
        'cmsDataResolverService'
    ],

    mixins: [
        Mixin.getByName('notification'),
        Mixin.getByName('cms-element'),
    ],

    metaInfo() {
        return {
            title: this.$createTitle()
        };
    },

    data() {
        return {
            repository: null,
            element: null,
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

            if (this.element.type) {
                this.cmsDataResolverService.resolve({sections: [{blocks: [{slots: [this.element]}]}]}).then(() => {
                    this.initElementConfig(this.element.type);
                    this.initElementData(this.element.type);
                }).catch((exception) => {
                    this.createNotificationError({
                        title: exception.message,
                        message: exception.response,
                    });
                });
            }
            this.isLoading = false;
        },

        async getBanner() {
            this.element = await this.repository.get(this.$route.params.id, Context.api);
        },

        async loadCategories() {
            const criteria = new Criteria(1, 100);
            const categories = this.element.categories ? this.element.categories : [];

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
            if (!this.element.categories) {
                this.$set(this.element, 'categories', []);
            }

            this.element.categories.push(category.id);
        },

        onSelectionRemove(category) {
            if (!this.element.categories) {
                this.$set(this.element, 'categories', []);
            }

            const index = this.element.categories.indexOf(category.id);

            if (index !== -1) {
                this.element.categories.splice(index, 1);
            }
        },

        onCancel() {
            this.$router.push({ name: 'marketing.banner.index' });
        },

        onSave() {
            this.isSaveSuccessful = false;
            this.isLoading = true;

            this.repository
                .save(this.element, Context.api)
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
        },
        onChangeLanguage() {
            this.getItem();
        },
    }
});
