Shopware.Component.extend('alpha-cms-slot', 'sw-cms-slot', {
    computed: {
        //removed all filters because marketing-banner-detail is not a cmsPage
        cmsElements() {
            const elements = Object.entries(this.cmsService.getCmsElementRegistry())

            return Object.fromEntries(elements);
        },
    },
});
