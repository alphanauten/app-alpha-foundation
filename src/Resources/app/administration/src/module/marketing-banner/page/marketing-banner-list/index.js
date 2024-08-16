import template from './marketing-banner-list.html.twig';
import './marketing-banner-list.scss';

const { Component, Filter } = Shopware;
const { Criteria } = Shopware.Data;

Component.register('marketing-banner-list', {
    template,

    inject: [
        'repositoryFactory'
    ],

    data() {
        return {
            repository: null,
            banners: null
        };
    },

    metaInfo() {
        return {
            title: this.$createTitle()
        };
    },

    computed: {
        columns() {
            return this.getColumns();
        },
        dateFilter() {
            return Filter.getByName('date');
        },
    },

    created() {
        this.createdComponent();
    },

    methods: {
        createdComponent() {
            this.repository = this.repositoryFactory.create('marketing_banner');

            this.repository.search(new Criteria(), Shopware.Context.api).then((result) => {
                this.banners = result;
            });
        },

        getColumns() {
            return [{
                property: 'active',
                label: this.$tc('marketing-banner.list.columns.active'),
                inlineEdit: 'boolean',
                allowResize: true
            }, {
                property: 'name',
                label: this.$tc('marketing-banner.list.columns.name'),
                routerLink: 'marketing.banner.detail',
                inlineEdit: 'string',
                allowResize: true,
                primary: true
            }, {
                property: 'description',
                label: this.$tc('marketing-banner.list.columns.description'),
                inlineEdit: 'string',
                allowResize: true,
            }, {
                property: 'validFrom',
                label: this.$tc('marketing-banner.list.columns.validFrom'),
                inlineEdit: 'date',
                allowResize: true,
            }, {
                property: 'validUntil',
                label: this.$tc('marketing-banner.list.columns.validUntil'),
                inlineEdit: 'date',
                allowResize: true,
            }];
        }
    }
});
