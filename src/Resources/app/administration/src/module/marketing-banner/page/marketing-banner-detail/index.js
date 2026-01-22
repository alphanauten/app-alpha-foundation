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
            categoriesCollection: null,
            propertyGroupOptionCollection: null
        };
    },

    computed: {
        ...mapPropertyErrors('banner', [
            'name',
            'bannerType',
            'bannerCondition',
            'propertyGroupOptions'
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

        bannerConditionSelect() {
            return [{
                label: this.$tc("marketing-banner.detail.bannerConditionNo"),
                value: 'no_restriction'
            }, {
                label: this.$tc("marketing-banner.detail.bannerConditionWith"),
                value: 'only_with'
            }, {
                label: this.$tc("marketing-banner.detail.bannerConditionExclude"),
                value: 'exclude'
            }];
        },

        bannerRepository() {
            return this.repositoryFactory.create('marketing_banner');
        },

        categoryRepository() {
            return this.repositoryFactory.create('category');
        },

        propertyGroupOptionRepository() {
            return this.repositoryFactory.create('property_group_option');
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
        },

        propertyGroupOptionCriteria() {
            const criteria = new Criteria();
            criteria.addSorting(Criteria.sort('name', 'ASC'));
            return criteria;
        }
    },

    created() {
        this.createdComponent();
    },

    methods: {
        async createdComponent() {
            this.isLoading = true;

            this.categoriesCollection = new EntityCollection('/category', 'category', Shopware.Context.api);
            this.propertyGroupOptionCollection = new EntityCollection('/property-group-option', 'property_group_option', Shopware.Context.api);

            await this.getBanner();
            await this.loadCategories();
            await this.loadPropertyGroupOptions();

            this.isLoading = false;
        },

        async getBanner() {
            const criteria = new Criteria();
            criteria.setIds([this.$route.params.id]);
            criteria.addAssociation('translations')
            this.element = await this.bannerRepository.search(criteria, Context.api);
            this.element = this.element.first();
            if (this.element.type) {
                this.cmsDataResolverService.resolve({ sections: [{ blocks: [{ slots: [this.element] }] }] }).then(() => {
                    this.initElementConfig(this.element.type);
                    this.initElementData(this.element.type);
                }).catch((exception) => {
                    this.createNotificationError({
                        title: exception.message,
                        message: exception.response,
                    });
                });
            }
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

        async loadPropertyGroupOptions() {
            const criteria = new Criteria(1, 100);
            const propertyGroupOptions = this.element.propertyGroupOptions ? this.element.propertyGroupOptions : [];

            if (propertyGroupOptions.length < 1) {
                return;
            }

            criteria.setIds(propertyGroupOptions);

            return await this.propertyGroupOptionRepository
                .search(criteria, Shopware.Context.api)
                .then((response) => {
                    this.propertyGroupOptionCollection = response;
                });
        },

        onSelectionAdd(category) {
            if (!this.element.categories) {
                this.element.categories = [];
            }

            this.element.categories.push(category.id);
        },

        onSelectionRemove(category) {
            if (!this.element.categories) {
                this.element.categories = [];
            }

            const index = this.element.categories.indexOf(category.id);

            if (index !== -1) {
                this.element.categories.splice(index, 1);
            }
        },

        onPropertyGroupOptionAdd(propertyGroupOption) {
            if (!this.element.propertyGroupOptions) {
                this.element.propertyGroupOptions = [];
            }

            this.element.propertyGroupOptions.push(propertyGroupOption.id);
        },

        onPropertyGroupOptionRemove(propertyGroupOption) {
            if (!this.element.propertyGroupOptions) {
                this.element.propertyGroupOptions = [];
            }

            const index = this.element.propertyGroupOptions.indexOf(propertyGroupOption.id);

            if (index !== -1) {
                this.element.propertyGroupOptions.splice(index, 1);
            }
        },

        onCancel() {
            this.$router.push({ name: 'marketing.banner.index' });
        },

        onSave() {
            this.isSaveSuccessful = false;
            this.isLoading = true;

            this.bannerRepository
                .save(this.element, Context.api)
                .then(() => {
                    this.createNotificationSuccess({
                        title: this.$tc('global.default.success'),
                        message: ''
                    });
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
            this.getBanner();
        },
    }
});
