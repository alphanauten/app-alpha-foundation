import template from './sw-order-line-items-grid.html.twig'

const { Component, Service, Utils } = Shopware;

Component.override( 'sw-order-line-items-grid', {
  template,
  computed: {
    getLineItemColumns() {
      const columnDefinitions = this.$super('getLineItemColumns')

      columnDefinitions.map(columnDefinition => {
        if (columnDefinition.property === 'payload.productNumber') {
          columnDefinition.visible = true;
        }
        return columnDefinition;
      });

      const pickwareColumns = [
        {
          property: 'availableStock',
          dataIndex: 'availableStock',
          label: 'sw-order.detailBase.columnAvailableStock',
          allowResize: false,
          align: 'right',
          inlineEdit: false,
          width: '90px',
        },
        {
          property: 'externallySent',
          dataIndex: 'externallySent',
          label: 'sw-order.detailBase.columnExternallySent',
          allowResize: false,
          align: 'right',
          inlineEdit: false,
          width: '90px',
        }
      ]

      return [
          ...pickwareColumns,
          ...columnDefinitions
      ];
    },
  },
  methods: {
    getProductAvailableStock(item) {
      if ( ! this.isProductItem(item) )  {
        return '-'
      }

      return item.product.availableStock;
    },
    getProductPhysicalStock(item) {
      if ( ! this.isProductItem(item) || ! item.product.extensions?.pickwareErpPickwareProduct)  {
        return '-'
      }

      const pickwareProduct = item.product.extensions.pickwareErpPickwareProduct;

      if ( pickwareProduct.isStockManagementDisabled ) {
        return '-'
      }

      return pickwareProduct.physicalStock;
    },

    // "shipped" is stock flow into the order
    getProductExternallySent(item) {
      const pickwareLineItem = item.extensions?.pickwareErpPickwareOrderLineItem;

      if ( ! pickwareLineItem ) {
        return '0'
      }

      return pickwareLineItem.externallyFulfilledQuantity;
    },
  },
} );
