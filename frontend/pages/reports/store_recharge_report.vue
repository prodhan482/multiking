<template>
  <div>
    <div class="card" style="margin-top: 20px;">
      <div class="card-header bg-white header-elements-inline">
        <div class="card-title">Recent Recharge Activity &nbsp;&nbsp;&nbsp;&nbsp;<span class="text-success" style="font-weight: bold; font-size: 15px;">Current Balance (&#8364; {{current_balance}})</span> <span v-if="(loan_balance > 0)" class="text-danger" style="font-weight: bold; font-size: 15px;">Loan Amount (&#8364; {{loan_balance}})</span></div>
        <div class="header-elements">
          <div class="list-icons">

          </div>
        </div>
      </div>
      <div class="card-body" :style="{'padding':'0'}">
        <table class="table table-xs datatable-basic dataTable no-footer datatable-scroll-y" style="width:100%">
          <thead>
          <tr>
            <th>Sl</th>
            <th>Created At</th>
            <th>Phone Number</th>
            <th>MFS</th>
            <th>Note</th>
            <th>Vendor Note</th>
            <th>Amount</th>
            <th>Balance</th>
            <th>Status</th>
            <th>Last Updated On</th>
          </tr>
          </thead>
        </table>
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
        limit:200
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
          "columnDefs": [
            {
              'targets': 8,'searchable': false, 'orderable': false, 'width':'10%',
              'render': function (data, type, full, meta)
              {
                var info = $('<div/>').text(data).html();

                if(info === "Pending") return '<span class="badge badge-warning badge-pill" style="font-size: 14px;">'+info+'</span>';
                if(info === "Approved") return '<span class="badge badge-success badge-pill" style="font-size: 14px;">'+info+'</span>';
                if(info === "Progressing") return '<span class="badge badge-info badge-pill" style="font-size: 14px;">'+info+'</span>';
                if(info === "Rejected") return '<span class="badge badge-danger badge-pill" style="font-size: 14px;">'+info+'</span>';

                return '';
              }
            },
          ],
          createdRow: function (row, data, index) {
            if (data[8] == "Balance Refill" || data[8] == "Refund") {
              $(row).addClass("table-success");
            }
          },
          "processing": true,
          "serverSide": true,
          "pageLength": 500,
          "language": {
            "emptyTable": "No Adjustment History Data Found.",
          },
          "ajax": {
            "url": this.$axios.defaults.baseURL+'/api/recharge/activity',
            "type": "POST",
            'beforeSend': function (request) {
              request.setRequestHeader("Authorization", theInstance.$auth.getToken('local'));
            },
            "data": function ( d )
            {
              d.limit = theInstance.tableFilter.limit
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
