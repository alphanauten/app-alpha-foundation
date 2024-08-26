const { Component, Mixin, Context } = Shopware;
const { Criteria } = Shopware.Data;

Component.override('sw-settings-rule-detail-assignments', {
    computed: {
        associationEntitiesConfig() {
            let config = this.$super('associationEntitiesConfig');
            config.push({
                entityName: 'marketing_banner',
                criteria: () => {
                    const criteria = new Criteria();
                    criteria.setLimit(this.associationLimit);
                    criteria.addFilter(Criteria.equals('rules.id', this.rule.id));

                    return criteria;
                },
                detailRoute: 'marketing.banner.detail',
                gridColumns: [{
                    property: 'name',
                    label: 'Name',
                    rawData: true,
                    sortable: false,
                    routerLink: 'marketing.banner.detail',
                    allowEdit: false,
                }],
            });
            return config;
        }
    }
});
