const { Component } = Shopware;


Component.extend('marketing-banner-create', 'marketing-banner-detail', {

   methods: {
       getBanner() {
           this.banner = this.repository.create(Shopware.Context.api);
       },

       onSave() {
           this.isLoading = true;
           this.repository.save(this.banner, Shopware.Context.api).then(() => {
               this.$router.push({ name: 'marketing.banner.detail', params: { id: this.banner.id }});
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
