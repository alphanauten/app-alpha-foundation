const { Component, Module } = Shopware;

import './page/marketing-banner-list';
import './page/marketing-banner-detail';
import './page/marketing-banner-create';
// Component.register('alphanauten-marketing-banner-list', () => import('./page/marketing-banner-list'));
// Component.register('alphanauten-marketing-banner-detail', () => import('./page/marketing-banner-detail'));
// Component.extend(() => import('./page/marketing-banner-create'), 'alphanauten-marketing-banner-detail');

Module.register('marketing-banner', {
    type: 'plugin',
    name: 'marketing-banner',
    title: 'marketing-banner.general.mainMenuItemGeneral',
    description: 'marketing-banner.general.descriptionTextModule',

    routes: {
        index: {
            component: 'marketing-banner-list',
            path: 'index'
        },
        detail: {
            component: 'marketing-banner-detail',
            path: 'detail/:id?',
            meta: {
                parentPath: 'marketing.banner.index'
            }
        },
        create: {
            component: 'marketing-banner-create',
            path: 'create',
            meta: {
                parentPath: 'marketing.banner.index'
            }
        },
    },

    navigation: [{
        label: 'marketing-banner.general.mainMenuItemGeneral',
        parent: 'sw-marketing',
        path: 'marketing.banner.index',
        position: 100
    }]
});
