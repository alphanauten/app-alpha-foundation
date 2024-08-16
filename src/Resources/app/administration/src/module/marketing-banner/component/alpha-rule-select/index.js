import template from './alpha-rule-select.html.twig';
import './alpha-rule-select.scss';

const { Component } = Shopware;

Component.register('alpha-rule-select', {
    template,

    model: {
        prop: 'collection',
        event: 'collection-added-item'
    },

    inject: ['repositoryFactory'],

    props: {
        collection: {
            type: Array,
            required: false,
            default: null
        },
        ruleScope: {
            type: Array,
            required: false,
            default: null
        }
    },

    data() {
        return {
            showRuleModal: false
        };
    },

    methods: {
        onChange(collection) {
            this.$emit('update:collection', collection);
        },
        onSaveRule(ruleId) {
            const ruleRepository = this.repositoryFactory.create(
                this.collection.entity,
                this.collection.source
            );

            ruleRepository.assign(ruleId, this.collection.context).then(() => {
                ruleRepository.search(this.collection.criteria, this.collection.context).then((searchResult) => {
                    this.$emit('collection-added-item', searchResult);
                    this.$refs.ruleSelect.sendSearchRequest();
                });
            });
        }
    }
});
