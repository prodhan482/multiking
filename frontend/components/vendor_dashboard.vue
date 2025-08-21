<template>
  <div style="margin-top: 20px;">
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-header bg-white header-elements-inline">
            <div class="card-title" style="font-weight: bold; font-size: 15px;">Recent Recharge Activities</div>
            <div class="header-elements">
              <div class="list-icons">

              </div>
            </div>
          </div>
          <div class="card-body" :style="{'padding':'0'}">
            <table class="table table-xs datatable-border dataTable no-footer datatable-scroll-y table-border-solid" style="width:100%">
              <thead>
              <tr>
                <th>Sl</th>
                <th>MFS</th>
                <th>Phone Number</th>
                <th>Amount</th>
                <th>Balance</th>
                <th>Action</th>
                <th>Status</th>
                <th>Created At</th>
                <th>Last Updated On</th>
                <th>Note</th>
              </tr>
              </thead>
              <tbody>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
const numeral = !process.client ? null : require('numeral');
var theInstance = {};

export default {
  name: "vendor_dashboard",
  middleware: ['auth', 'permission_check'],
  components: {},
  data() {
    return {
      formErrorMessage:'',
      page_message:'',
      masterTable:{},
      logo:'',
      storeName:'',
      storeOwnerName:'',
      storePhoneNumber:'',
      storeAddress:'',
      windowHeight: 0,
      windowWidth: 0,
      scrollPosition:0,
      formToDoUpdate:false,
      tableFilter:{},
      current_balance:0,
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
      var uinfo = JSON.parse(localStorage.userInfo);

      this.logo = uinfo.logo;
      this.selectedStoreId = uinfo.store_vendor_id;
      this.storeName = uinfo.storeName;
      this.storeOwnerName = uinfo.storeOwnerName;
      this.storePhoneNumber = uinfo.storePhoneNumber;
      this.storeAddress = uinfo.storeAddress;
    }

    setTimeout(function () {
      theInstance.fixTheTable();
    }, 500);
  },
  methods: {
    fixTheTable()
    {
      this.masterTable = $('.dataTable').DataTable({
          scrollX: true,
          scrollY: (this.windowHeight - 300)+'px',//(this.windowHeight - 500)+'px',
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
            theInstance.dTableMount();
            theInstance.page_message = ''
          },
          "columnDefs": [
            {
              'targets': 6,'searchable': false, 'orderable': false, 'width':'10%',
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
            {
              'targets': 5,'searchable': false, 'orderable': false, 'width':'10%',
              'render': function (data, type, full, meta)
              {

                var info = $('<div/>').text(data).html();

                return '<div class="btn-group-sm">'+
                  (info.split('|')[1] === "pending"?'<button type="button" class="btn btn-warning lockRechargeBtt" data-id="'+info.split('|')[0]+'">Lock</button>':'')+
                  (info.split('|')[1] === "progressing"?'<button type="button" class="btn btn-success approveBtt" data-id="'+info.split('|')[0]+'">Approve</button>':'')+
                  (info.split('|')[1] === "progressing"?'<button type="button" class="btn btn-danger rejectBtt" data-id="'+info.split('|')[0]+'">Reject</button>':'')+
                  '</div>';
              }
            },
          ],
          createdRow: function (row, data, index) {
            if (data[1] == "Balance Refill") {
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
              d = theInstance.tableFilter
            },
            complete: function(data)
            {
              theInstance.mfs_list = data.responseJSON.mfs_list
              theInstance.current_balance = data.responseJSON.current_balance
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
    lock:function(recharge_id)
    {
      if(confirm("Are you sure?"))
      {
        this.$axios.post("/api/recharge/lock/"+recharge_id, this.createNewRecharge,{
          headers: {
            Authorization: this.$auth.getToken('local')
          }
        })
          .then(response => {
            location.reload();
            //this.loadPageData();
          })
          .catch(error => {
            if (error.response) {
              switch (error.response.status)
              {
                case 401:
                  this.makeForceLogout()
                  break;
                case 403:
                  alert(error.response.data.message)
                  break;
              }
            }
          })
      }
    },
    approveReject:function(recharge_id, recharge_status)
    {
      var note = prompt("Please enter your note (if any)", "");

      if(confirm("Are you sure?"))
      {
        this.$axios.post("/api/recharge/approve_reject/"+recharge_id, {'recharge_status':recharge_status, 'note':note},{
          headers: {
            Authorization: this.$auth.getToken('local')
          }
        })
          .then(response => {
            this.loadPageData();
          })
          .catch(error => {
            if (error.response) {
              switch (error.response.status)
              {
                case 401:
                  this.makeForceLogout()
                  break;
                case 403:
                  alert(error.response.data.message)
                  break;
              }
            }
          })
      }
    },
    dTableMount() {
      $(".lockRechargeBtt").click(function(e){
        theInstance.lock($(this).data("id"));
      });

      $(".approveBtt").click(function(e){
        theInstance.approveReject($(this).data("id"), 'approved');
      });

      $(".rejectBtt").click(function(e){
        theInstance.approveReject($(this).data("id"), 'rejected');
      });
    },
    async makeForceLogout() {
      if (confirm("Your Session have been Expired. You have to re-login to continue. Press ok to logout")) {
        try {
          let response = await this.$auth.logout()
          console.log(response.data)
        } catch (err) {
          console.log(err)
        }
        window.location.href = "login"
      }
    },
    loadPageData()
    {
      this.masterTable.ajax.reload();
    },
  }
}
</script>

<style scoped>

</style>
