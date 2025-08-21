<template>
  <div class="row match-height">
    <div class="col-lg-6 col-12">
      <div class="card">
        <div class="card-body">
          <div class="form-group row">
            <label class="col-form-label col-sm-3">MFS</label>
            <div class="col-sm-9">
              <select class="form-control select-mfs" data-fouc v-model="request.selected_mfs">
                <option value="">Select a Option</option>
                <option v-for="option in mfs_list" :value="option.mfs_id">{{option.mfs_name}}</option>
              </select>
            </div>
          </div>
          <div class="form-group row" v-if="mfs_list_by_id[request.selected_mfs]">
            <label class="col-form-label col-sm-3">Type</label>
            <div class="col-sm-9">
              <select class="form-control mfs-type" v-model="request.mfs_type">
                <option value="" selected>Select A Type</option>
                <option value="personal" v-if="(mfs_list_by_id[request.selected_mfs].mfs_type !== 'mobile_recharge')">Personal</option>
                <option value="agent" v-if="(mfs_list_by_id[request.selected_mfs].mfs_type !== 'mobile_recharge')">Agent</option>
                <option value="prepaid" v-if="(mfs_list_by_id[request.selected_mfs].mfs_type === 'mobile_recharge')">Prepaid</option>
                <option value="postpaid" v-if="(mfs_list_by_id[request.selected_mfs].mfs_type === 'mobile_recharge')">Postpaid</option>
              </select>
            </div>
          </div>
          <div class="form-group row" v-if="mfs_package_list[request.selected_mfs] && mfs_package_list[request.selected_mfs].length > 0">
            <label class="col-form-label col-sm-3">Package</label>
            <div class="col-sm-9">
              <select class="form-control mfs-package" v-model="request.selected_mfs_package">
                <option value="">Select a Package</option>
                <option v-for="option in mfs_package_list[request.selected_mfs]" :value="option.row_id">{{option.package_name}}</option>
              </select>
            </div>
          </div>
          <div class="form-group row">
            <label class="col-form-label col-sm-3">Mobile Number</label>
            <div class="col-sm-9">
              <div class="input-group">
                <input type="text" class="form-control" placeholder="01XXXXXXXXX" v-model="request.mobile_number">
              </div>
            </div>
          </div>
          <div class="form-group row">
            <label class="col-form-label col-sm-3">Send Amount</label>
            <div class="col-sm-9">
              <div class="input-group">
                <input type="text" class="form-control" placeholder="In Amount" v-model="request.send_money" v-on:keyup="comChargClacu">
                <span class="input-group-append">
                        <span class="input-group-text">BDT</span>
                </span>
              </div>
            </div>
          </div>
          <div class="form-group row" v-if="(parseFloat(request.charge) > 0)">
            <label class="col-form-label col-sm-3">Charge</label>
            <div class="col-sm-9">
              BDT {{ toNum(request.send_money * request.charge) }}/=
            </div>
          </div>
          <div class="form-group row" v-if="(parseFloat(request.commission) > 0)">
            <label class="col-form-label col-sm-3">Commission</label>
            <div class="col-sm-9">
              BDT {{ toNum(request.send_money * request.commission) }}/=
            </div>
          </div>
          <div class="form-group row" v-if="request.send_money > 0">
            <label class="col-form-label col-sm-3">Receive Amount</label>
            <div class="col-sm-9">
              <div class="input-group">
                <input type="text" class="form-control" placeholder="In Amount" v-model="request.receive_money">
                <span class="input-group-append">
                  <span class="input-group-text">BDT</span>
                </span>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-sm-9 offset-sm-3">
              <button type="reset" class="btn btn-primary mr-1 waves-effect waves-float waves-light" v-on:click="sendARequest">Submit</button>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-6 col-12">
      <div class="card">
        <div class="card-header d-flex justify-content-between pb-0">
<!--          <h4 class="card-title">Promorions</h4>-->
        </div>
        <div class="card-body">
          .
        </div>
      </div>
    </div>
    <div class="col-12">
      <div class="card">
        <div class="card-body" style="padding: 0">
          <table class="table datatable-basic dataTable no-footer datatable-scroll-y">
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
            <tbody></tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
const numeral = !process.client ? null : require('numeral');
var theInstance = {};
export default {
  name: "store_dashboard",
  middleware: ['auth', 'permission_check'],
  components: {},
  data() {
    return {
      masterTable:{},
      mfs_list:[],
      mfs_list_by_id:{},
      mfs_package_list:{},
      mfs_package_list_id:{},
      currentPackageList:[],
      tableFilter:{
        limit:5
      },
      request:{
        selected_mfs:"",
        selected_mfs_package:"",
        mfs_type:"",
        send_money:"",
        receive_money:"",
        charge:0,
        commission:0,
        total:"",
        mobile_number:""
      }
    }
  },
  mounted() {

    theInstance = this;
    $('.select').select2();

    $('.mfs-package').select2();
    $('.select-mfs').select2();
    $('.select-mfs').on('select2:select', function (e) {
        if($('.mfs-package').hasClass("select2-hidden-accessible")){ $('.mfs-package').select2('destroy') };
        theInstance.request.selected_mfs = $('.select-mfs').select2('data')[0].id
        //console.log(theInstance.mfs_package_list[theInstance.request.selected_mfs])
        setTimeout(function(){
            $('.mfs-package').select2();
            $('.mfs-package').on('select2:select', function (e) {

                theInstance.request.selected_mfs_package = $('.mfs-package').select2('data')[0].id

                if(theInstance.mfs_package_list_id[$('.mfs-package').select2('data')[0].id])
                {
                  var ll = $('.mfs-package').select2('data')[0].id
                  theInstance.request.charge = parseFloat(theInstance.mfs_package_list_id[ll].charge);
                  if(theInstance.request.charge > 0) theInstance.request.charge = theInstance.request.charge / 100;

                  theInstance.request.commission = parseFloat(theInstance.mfs_package_list_id[ll].discount);
                  if(theInstance.request.commission > 0) theInstance.request.commission = theInstance.request.commission / 100;

                  theInstance.request.send_money = theInstance.mfs_package_list_id[ll].amount;

                  theInstance.comChargClacu();
                }

            });
        }, 300);

        if(theInstance.request.send_money.length < 1) theInstance.request.send_money = 100;

        theInstance.request.charge = parseFloat(theInstance.mfs_list_by_id[theInstance.request.selected_mfs].default_charge);
        if(theInstance.request.charge > 0) theInstance.request.charge = theInstance.request.charge / 100;

        theInstance.request.commission = parseFloat(theInstance.mfs_list_by_id[theInstance.request.selected_mfs].default_commission);
        if(theInstance.request.commission > 0) theInstance.request.commission = theInstance.request.commission / 100;

        theInstance.comChargClacu();
    });

    setTimeout(function () {
      theInstance.doTable();
    }, 500);
  },
  methods: {
    doTable()
    {
      this.masterTable = $('.dataTable').DataTable({
          scrollX: true,
          scrollY: (this.windowHeight - 550)+'px',//(this.windowHeight - 500)+'px',
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

              for(var m in data.responseJSON.mfs_list)
              {
                theInstance.mfs_package_list[data.responseJSON.mfs_list[m].mfs_id] = []
                theInstance.mfs_list_by_id[data.responseJSON.mfs_list[m].mfs_id] = data.responseJSON.mfs_list[m]
              }

              for(var m in data.responseJSON.mfs_package_list)
              {
                var id = data.responseJSON.mfs_package_list[m].mfs_id;
                if (typeof theInstance.mfs_package_list[id] === 'undefined') theInstance.mfs_package_list[id] = []
                if (typeof theInstance.mfs_package_list[id] !== 'undefined') theInstance.mfs_package_list[id].push(data.responseJSON.mfs_package_list[m]);

                theInstance.mfs_package_list_id[data.responseJSON.mfs_package_list[m].row_id] = data.responseJSON.mfs_package_list[m];
              }

              for(var m in data.responseJSON.store_mfs_slab)
              {
                if(parseFloat(data.responseJSON.store_mfs_slab[m].charge) > 0){
                  theInstance.mfs_list_by_id[data.responseJSON.store_mfs_slab[m].id].default_charge = data.responseJSON.store_mfs_slab[m].charge
                }
                if(parseFloat(data.responseJSON.store_mfs_slab[m].commission) > 0) {
                  theInstance.mfs_list_by_id[data.responseJSON.store_mfs_slab[m].id].default_commission = data.responseJSON.store_mfs_slab[m].commission
                }
              }
            },
            error: function (xhr, error, thrown)
            {
              console.log("Error");
            }
          },
        }
      );
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
    comChargClacu() {

      theInstance.request.receive_money = numeral(parseFloat(theInstance.request.send_money) + parseFloat(theInstance.request.send_money * theInstance.request.commission) - parseFloat(theInstance.request.send_money * theInstance.request.charge)).format('0.00');

    },
    comChargClacu2() {
      //theInstance.request.send_money = numeral(parseFloat(theInstance.request.send_money) + parseFloat(theInstance.request.send_money * theInstance.request.commission) - parseFloat(theInstance.request.send_money * theInstance.request.charge)).format('0.00');
    },
    toNum(info)
    {
      return numeral(parseFloat(info)).format('0,0.00');
    },
    loadPageData(){
      this.masterTable.ajax.reload();
    },
    sendARequest()
    {
      if(!theInstance.request.mobile_number.match(/(^(\+01|01))[3-9]{1}(\d){8}$/))
      {
        alert("Invalid Bangladeshi Mobile Number.")
        return;
      }

      if(theInstance.request.mfs_type.length === 0)
      {
        alert("Please select a type")
        return;
      }

      if(confirm("Are you sure?"))
      {
        var transactionPin = prompt("Please enter Transaction Pin", "");

        if (transactionPin != null) {
          theInstance.request.transaction_pin = transactionPin
          theInstance.request.mfs_id = theInstance.request.selected_mfs
          theInstance.request.recharge_amount = theInstance.request.receive_money

          this.$axios.post("/api/recharge/create", theInstance.request,
            {
              headers: {
                Authorization: this.$auth.getToken('local')
              },
            }).then(response => {
            //this.loadPageData();
            location.reload();
          })
            .catch(error => {
              if (error.response) {
                switch (error.response.status) {
                  case 401:
                    this.makeForceLogout()
                    break;
                  default:
                    alert(error.response.data.message.join(","))
                    break;
                }
              }
            });
        }
      }
    }
  }
}
</script>

<style scoped>

</style>
