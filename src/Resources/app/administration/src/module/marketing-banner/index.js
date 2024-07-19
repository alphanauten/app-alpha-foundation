const { Component, Module } = Shopware;

Component.register('alphanauten-marketing-banner-list', () => import('./page/marketing-banner-list'));

Module.register('alphanauten-marketing-banner', {
    type: 'plugin',
    name: 'MarketingBanner',
    title: 'marketing-banner.general.mainMenuItemGeneral',
    description: 'marketing-banner.general.descriptionTextModule',

    routes: {
        list: {
            component: 'alphanauten-marketing-banner-list',
            path: 'list'
        },
        detail: {
            component: 'alphanauten-marketing-banner-detail',
            path: 'detail/:id',
            props: {
                default: (route) => ({ marketingBannerId: route.params.id }),
            },
            meta: {
                parentPath: 'alphanauten.marketing.banner.list'
            }
        },
        create: {
            component: 'alphanauten-marketing-banner-detail',
            path: 'create',
            meta: {
                parentPath: 'alphanauten.marketing.banner.list'
            }
        },
    },

    navigation: [{
        label: 'marketing-banner.general.mainMenuItemGeneral',
        parent: 'sw-marketing',
        path: 'alphanauten.marketing.banner.list',
        position: 100
    }]
});
