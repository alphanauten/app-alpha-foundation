const {  Module } = Shopware;

import './page/marketing-banner-list';
import './page/marketing-banner-detail';
import './page/marketing-banner-create';

import './component/alpha-rule-select';
import './views/sw-settings-rule-detail-assignments';

import deDE from './snippet/de-DE.json';
import enGB from './snippet/en-GB.json';

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
