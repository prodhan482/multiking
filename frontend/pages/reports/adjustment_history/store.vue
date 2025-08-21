<template>
  <div class="card" style="margin-top: 20px">
    <div class="card-header bg-white header-elements-inline">
      <h6 class="card-title">Adjustment History</h6>
      <div class="header-elements">
        <div class="list-icons">
          <a href="#" data-toggle="modal" data-target="#modal_filter" class="list-icons-item"><i class="icon-filter3"></i> Filter</a>
        </div>
      </div>
    </div>
    <div class="card-body" :style="{'padding':'0'}">
      <table class="table datatable-basic dataTable no-footer datatable-scroll-y" style="width:100%">
        <thead>
        <tr>
          <th>Sl</th>
          <th>Date</th>
          <th>Reseller Name</th>
          <th>Amount</th>
        </tr>
        </thead>
        <tbody></tbody>
      </table>
    </div>


    <div id="modal_filter" class="modal fade" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title"><i class="icon-user-plus mr-2"></i> Filter</h5>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
          <div class="modal-body">
            <div class="form-group row">
              <label class="col-form-label col-sm-3">Reseller</label>
              <div class="col-sm-9">
                <select class="form-control" v-model="tableFilter.store_id">
                  <option value="">Select A Reseller</option>
                  <option v-for="option in storeList" v-bind:value="option.id">
                    {{ option.name }}
                  </option>
                </select>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button class="btn btn-link" data-dismiss="modal"><i class="icon-cross2 font-size-base mr-1"></i> Close</button>
            <button class="btn bg-primary" v-on:click="loadPageData()"><i class="icon-checkmark3 font-size-base mr-1"></i> Search</button>
          </div>
        </div>
      </div>
    </div>

  </div>
</template>

<script>

const numeral = !process.client ? null : require('numeral');
var theInstance= {};

export default {
  name: "store",
  middleware: ['auth', 'permission_check'],
  components: {},
  data() {
    return {
      formErrorMessage:'',
      page_message:'',
      masterTable:{},
      windowHeight: 0,
      windowWidth: 0,
      scrollPosition:0,
      formToDoUpdate:false,
      tableFilter:{
        store_id:"",
        storeListLoaded:0
      },
      storeList:{},
      page: 1
    }
  },
  mounted() {
    this.windowHeight = window.innerHeight
    this.windowWidth = window.innerWidth
    window.addEventListener('resize', () => {
      this.windowHeight = window.innerHeight
      this.windowWidth = window.innerWidth
    })
    theInstance = this;
    this.masterTable = $('.dataTable').DataTable({
        scrollX: true,
        scrollY: (this.windowHeight - 260)+'px',
        scrollCollapse: true,
        "searching": false,
        "info": false,
        "paging": false,
        "ordering": false,
        "preDrawCallback": function( settings ) {
          theInstance.scrollPosition = $(".dataTables_scrollBody").scrollTop();
        },
        "drawCallback": function( settings ) {
          //var api = this.api();
          $(".dataTables_scrollBody").scrollTop(theInstance.scrollPosition);
          //theInstance.dTableMount();
          theInstance.page_message = ''
        },
        "columnDefs": [
          {
            'targets': 0,'searchable': false, 'orderable': false, 'width':'5%',
          },
          {
            'targets': 1,'searchable': false, 'orderable': false, 'width':'10%',
          },
          {
            'targets': 3,'searchable': false, 'orderable': false, 'width':'10%',
            'render': function (data, type, full, meta)
            {
              var info = $('<div/>').text(data).html();

              if(parseFloat(info) < 0)
              {
                return '<span style="color:red">('+numeral(parseFloat(info)).format('0,0.00')+')</span>'
              }
              else
              {
                return '<span>'+numeral(parseFloat(info)).format('0,0.00')+'</span>'
              }
            }
          },
        ],
        "processing": true,
        "serverSide": true,
        "pageLength": 500,
        "language": {
          "emptyTable": "No Adjustment History Data Found.",
        },
        "ajax": {
          "url": this.$axios.defaults.baseURL+'/api/report/adjustment_history/store',
          "type": "POST",
          'beforeSend': function (request) {
            request.setRequestHeader("Authorization", theInstance.$auth.getToken('local'));
          },
          "data": function ( d )
          {
            d.store_id = theInstance.tableFilter.store_id
            d.storeListLoaded = theInstance.tableFilter.storeListLoaded
          },
          complete: function(data)
          {
            if(theInstance.tableFilter.storeListLoaded === 0)
            {
              theInstance.storeList = data.responseJSON.storeList
            }
            theInstance.tableFilter.storeListLoaded = 1
            //theInstance.mfs_list = data.responseJSON.mfs_list
            //console.log(data.responseJSON);
          },
          error: function (xhr, error, thrown)
          {
            console.log("Error");
          }
        },
      }
    );
  },
  methods: {
    loadPageData(){
      $('#modal_filter').modal('hide');
      this.masterTable.ajax.reload();
    },
  }
}
</script>
