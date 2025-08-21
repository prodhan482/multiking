import Vue from 'vue'

const HotTable = !process.client ? null : require('@handsontable/vue').HotTable
export default HotTable

import VueMobileDetection from 'vue-mobile-detection'
Vue.use(VueMobileDetection)

import { VuejsDatatableFactory } from 'vuejs-datatable';
Vue.use( VuejsDatatableFactory );

VuejsDatatableFactory.useDefaultType( false )
  .registerTableType( 'datatable', tableType => tableType.mergeSettings( {
    table: {
      class: 'table datatable-scroll-y dataTable no-footer',
    },
    pager: {
      classes: {
        pager:    'dataTables_paginate paging_simple_numbers',
        selected: 'current',
      },
      icons: {
        next:     '<i class="fas fa-chevron-right" title="Next page"></i>',
        previous: '<i class="fas fa-chevron-left" title="Previous page"></i>',
      },
    },
  }));
