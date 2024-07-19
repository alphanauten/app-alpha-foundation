import template from './marketing-banner-list.html.twig';
import './marketing-banner-list.scss';

const {Criteria} = Shopware.Data;

export default {
    template,

    inject: [
        'repositoryFactory'
    ],

    data() {
        return {
            entities: null,
            total: 0,
        };
    },

    computed: {
        columns() {
            return [
                {
                    property: 'name',
                    dataIndex: 'name',
                    label: this.$tc('product-guide.list.columns.name'),
                    routerLink: 'alphanauten.marketing.banner.detail',
                    allowResize: true,
                    primary: true
                }
            ];
        },

        entityRepository() {
            return this.repositoryFactory.create('marketing_banner');
        }
    },

    created() {
        this.createdComponent();
    },

    methods: {
        createdComponent() {
            this.loadTypes();
        },

        loadTypes() {
            this.entityRepository
                .search(new Criteria(), Shopware.Context.api)
                .then((result) => {
                    this.total = result.total;
                    this.entities = result;
                });
        },
    }
}
