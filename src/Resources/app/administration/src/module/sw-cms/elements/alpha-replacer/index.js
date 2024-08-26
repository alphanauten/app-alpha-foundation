const Application = Shopware.Application;
import './component';

Application.getContainer('service').cmsService.registerCmsElement({
    plugin: 'AlphaFoundation',
    icon: 'regular-repeat',
    name: 'alpha-replacer',
    label: 'sw-cms.elements.alpha-replacer.title',
    component: 'sw-cms-el-alpha-replacer',
    previewComponent: true,
    defaultConfig: null
});
