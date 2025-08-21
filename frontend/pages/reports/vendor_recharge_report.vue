<template>
  <div>
    <div class="card" style="margin-top: 20px;">
      <div class="card-header bg-white header-elements-inline">
        <div class="card-title">Recent Recharge Activity &nbsp;&nbsp;&nbsp;&nbsp;<span class="text-success" style="font-weight: bold; font-size: 15px;">Current Balance (&#2547; {{current_balance}})</span> <span v-if="(loan_balance > 0)" class="text-danger" style="font-weight: bold; font-size: 15px;">Loan Amount (&#8364; {{loan_balance}})</span></div>
        <div class="header-elements">
          <div class="list-icons">
            <a href="#" data-toggle="modal" data-target="#modal_filter" class="list-icons-item"><i class="icon-filter3"></i> Filter</a>
          </div>
        </div>
      </div>
      <div class="card-body" :style="{'padding':'0'}">
        <table class="table table-xs datatable-basic dataTable no-footer datatable-scroll-y" style="width:100%">
          <thead>
          <tr>
            <th>Sl</th>
            <th>Processed On</th>
            <th>MFS</th>
            <th>Phone Number</th>
            <th>Amount</th>
            <th>Note</th>
          </tr>
          </thead>
        </table>
      </div>
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
              <label class="col-form-label col-sm-3">Phone Number</label>
              <div class="col-sm-9">
                <input type="number" placeholder="Enter Phone Number" class="form-control" v-model="tableFilter.phone_number">
              </div>
            </div>
            <div class="form-group row">
              <label class="col-form-label col-sm-3">From</label>
              <div class="col-sm-9">
                <input type="text" placeholder="Click to open date picker" class="form-control daterange-time" v-model="tableFilter.date_from">
              </div>
            </div>
            <div class="form-group row">
              <label class="col-form-label col-sm-3">To</label>
              <div class="col-sm-9">
                <input type="text" placeholder="Click to open date picker" class="form-control daterange-time" v-model="tableFilter.date_to">
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
  name: "store_recharge_report",
  middleware: ['auth', 'permission_check'],
  components: {},
  data() {
    return {
      formErrorMessage:'',
      page_message:'',
      masterTable:{},
      mfs_list:[],

      storeCommissionList:[],
      commissionList:[],
      conversion_rate:0,

      storeCommissionListById:{},
      commissionListById:{},

      logo:'',
      selectedStoreId:'',
      storeName:'',
      storeOwnerName:'1',
      storePhoneNumber:'2',
      storeAddress:'3',
      windowHeight: 0,
      windowWidth: 0,
      scrollPosition:0,
      formToDoUpdate:false,
      tableFilter:{
        limit:200,
        date_from:"",
        date_to:"",
        phone_number:"",
      },
      current_balance:0,
      loan_balance:0,
      createNewRecharge:{
        note:'',
        mobile_number:'',
        mfs_id:'',
        mfs_type:'',
        recharge_amount:0.0,
        user_id:''
      },
      newPhoneNumber:{
        name:'',
        phone_number:''
      },
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

    $('.daterange-time').daterangepicker({
      //timePicker: true,
      //timePicker24Hour: true,
      startDate: moment().subtract(1, "days").format('YYYY-MM-DD'),
      "singleDatePicker": true,
      opens: 'left',
      applyClass: 'bg-slate-600',
      cancelClass: 'btn-default',
      locale: {
        format: 'YYYY-MM-DD'
      }
    });

    $('.daterange-time').on('apply.daterangepicker', function(ev, picker) {
      //console.log(picker.startDate.format('YYYY-MM-DD'));
      //console.log(picker.endDate.format('YYYY-MM-DD'));
      $(this)[0].dispatchEvent(new Event('input', { 'bubbles': true }))
      //theInstance.tableFilter.date_from = picker.startDate.format('YYYY-MM-DD')
    });

    if((typeof localStorage !== 'undefined'))
    {
      this.masterTable = $('.dataTable').DataTable({
          scrollX: true,
          scrollY: (this.windowHeight - 200)+'px',//(this.windowHeight - 500)+'px',
          scrollCollapse: true,
          "searching": false,
          "info": false,
          "paging": false,
          "ordering": false,
          "preDrawCallback": function(settings)
          {
            theInstance.scrollPosition = $(".dataTables_scrollBody").scrollTop();
          },
          "drawCallback": function(settings)
          {
            //var api = this.api();
            $(".dataTables_scrollBody").scrollTop(theInstance.scrollPosition);
            //theInstance.dTableMount();
            theInstance.page_message = ''
          },
          "columnDefs": [],
          createdRow: function (row, data, index) {

          },
          "processing": true,
          "serverSide": true,
          "pageLength": 500,
          "language": {
            "emptyTable": "No Adjustment History Data Found.",
          },
          "ajax": {
            "url": this.$axios.defaults.baseURL+'/api/recharge/activity?report=1',
            "type": "POST",
            'beforeSend': function (request) {
              request.setRequestHeader("Authorization", theInstance.$auth.getToken('local'));
            },
            "data": function ( d )
            {
              d.limit = theInstance.tableFilter.limit
              d.date_from = theInstance.tableFilter.date_from
              d.date_to = theInstance.tableFilter.date_to
              d.phone_number = theInstance.tableFilter.phone_number
            },
            complete: function(data)
            {
              theInstance.mfs_list = data.responseJSON.mfs_list
              theInstance.current_balance = data.responseJSON.current_balance
              theInstance.loan_balance = data.responseJSON.loan_balance
              theInstance.storeCommissionList = data.responseJSON.store_commission_percent
              theInstance.commissionList = data.responseJSON.commission_percent
              theInstance.conversion_rate = data.responseJSON.conversion_rate

              theInstance.storeCommissionListById = {};
              theInstance.commissionListById = {};

              for (var x in theInstance.storeCommissionList) {
                theInstance.storeCommissionListById[theInstance.storeCommissionList[x].id] = theInstance.storeCommissionList[x].value;
              }

              for (var x in theInstance.commissionList) {
                theInstance.commissionListById[theInstance.commissionList[x].id] = theInstance.commissionList[x].value;
              }

              //console.log(data.responseJSON);
            },
            error: function (xhr, error, thrown)
            {
              console.log("Error");
            }
          },
        }
      );
    }
  },
  methods:{
    loadPageData()
    {
      this.masterTable.ajax.reload();
      $('#modal_filter').modal('hide');
    },
    makeForceLogout()
    {
      if(confirm("Your Session have been Expired. You have to re-login to continue. Press ok to logout")){
        this.userLogout()
      }
    },
  }
}
</script>

<style scoped>

</style>
