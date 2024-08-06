import template from './marketing-banner-list.html.twig';
import './marketing-banner-list.scss';

const { Component } = Shopware;
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
        }
    },

    created() {
        this.createdComponent();
    },

    methods: {
        createdComponent() {
            this.repository = this.repositoryFactory.create('alpha_marketing_banner');

            this.repository.search(new Criteria(), Shopware.Context.api).then((result) => {
                this.banners = result;
            });
        },

        getColumns() {
            return [{
                property: 'active',
                label: this.$tc('marketing-banner.list.columnActive'),
                inlineEdit: 'boolean',
                allowResize: true
            }, {
                property: 'name',
                label: this.$tc('marketing-banner.list.columnName'),
                routerLink: 'marketing.banner.detail',
                inlineEdit: 'string',
                allowResize: true,
                primary: true
            }, {
                property: 'description',
                label: this.$tc('marketing-banner.list.columnDescription'),
                inlineEdit: 'string',
                allowResize: true,
            }, {
                property: 'validFrom',
                label: this.$tc('marketing-banner.list.validFrom'),
                inlineEdit: 'date',
                allowResize: true,
            }, {
                property: 'validUntil',
                label: this.$tc('marketing-banner.list.validUntil'),
                inlineEdit: 'date',
                allowResize: true,
            }];
        }
    }
});
