const { Component } = Shopware;


Component.extend('marketing-banner-create', 'marketing-banner-detail', {

   methods: {
       getBanner() {
           this.element = this.repository.create(Shopware.Context.api);
       },

       onSave() {
           this.isLoading = true;
           this.repository.save(this.element, Shopware.Context.api).then(() => {
               this.$router.push({ name: 'marketing.banner.detail', params: { id: this.element.id }});
           }).catch((exception) => {
               this.createNotificationError({
                   title: this.$tc('marketing-banner.detail.errorTitle'),
                   message: exception
               });
           }).finally(() => {
               this.isLoading = false;
           });
       }
   }
});
