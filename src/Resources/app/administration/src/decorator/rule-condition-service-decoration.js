Shopware.Application.addServiceProviderDecorator('ruleConditionDataProviderService', (ruleConditionService) => {
    ruleConditionService.addCondition('isAdmin', {
        component: 'sw-condition-generic',
        label: 'Is Admin',
        scopes: ['global']
    });

    return ruleConditionService;
});