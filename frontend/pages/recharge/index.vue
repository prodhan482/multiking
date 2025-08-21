<template>
  <div>
    <div v-if="false" class="card" style="margin-top: 20px">
      <div class="card-header bg-white header-elements-inline">
        <h6 class="card-title">Recharge</h6>
        <div class="header-elements">
          <div class="list-icons">

          </div>
        </div>
      </div>
      <div class="card-body">
        <form>
          <div class="row">
            <div class="col">
              <div class="input-group">
                <span class="input-group-prepend">
									<span class="input-group-text"><i class="icon-mobile"></i>&nbsp;&nbsp;+88</span>
								</span>
                <input type="text" class="form-control" placeholder="Mobile Number" v-model="createNewRecharge.mobile_number">
              </div>
            </div>
            <div class="col">
              <div class="input-group">
                <span class="input-group-prepend">
									<span class="input-group-text"><i class="icon-chip"></i></span>
								</span>
                <select class="form-control" v-model="createNewRecharge.mfs_id">
                  <option value="">Select A MFS</option>
                  <option v-for="option in mfs_list" v-bind:value="option.mfs_id">
                    {{ option.mfs_name }}
                  </option>
                </select>
              </div>
            </div>
            <div class="col">
              <div class="input-group">
                <span class="input-group-prepend">
									<span class="input-group-text"><i class="icon-server"></i></span>
								</span>
                <select class="form-control" v-model="createNewRecharge.mfs_type">
                  <option value="">Select A MFS Type</option>
                  <option value="personal" selected>Personal</option>
                  <option value="agent">Agent</option>
                </select>
              </div>
            </div>
            <div class="col">
              <div class="input-group">
                <textarea rows="1" cols="3" class="form-control" placeholder="Note (If Any)" v-model="createNewRecharge.note"></textarea>
              </div>
            </div>
            <div class="col">
              <div class="input-group">
                <span class="input-group-prepend">
									<span class="input-group-text"><i class="icon-cash"></i></span>
								</span>
                <input type="number" class="form-control" placeholder="Recharge Amount" v-model="createNewRecharge.recharge_amount">
              </div>
            </div>
            <div class="col">
              <button type="button" v-on:click="createRecharge" class="btn btn-primary">Do Recharge <i class="icon-power2 ml-2"></i></button>
            </div>
          </div>
        </form>
      </div>
    </div>
    <div class="card" style="margin-top: 20px">
      <div class="card-header bg-white header-elements-inline">
        <h6 class="card-title">Recent Recharge Activity</h6>
        <div class="header-elements">
          <div class="list-icons">
            <a href="#" data-toggle="modal" data-target="#modal_filter" class="list-icons-item"><i class="icon-filter3"></i> Filter</a>
          </div>
        </div>
      </div>
      <div class="card-body" :style="{'padding':'0'}">
        <table class="table table-xs datatable-basic dataTable no-footer datatable-scroll-y">
          <thead>
          <tr>
            <th>Sl</th>
            <th>Created At</th>
            <th>Phone Number</th>
            <th>MFS</th>
            <th>Note</th>
            <th>Vendor Note</th>
            <th>Store</th>
            <th>Vendor</th>
            <th>Amount</th>
            <th>Status</th>
            <th>Last Updated On</th>
            <th>Action</th>
          </tr>
          </thead>
          <tbody></tbody>
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
            <div class="form-group row">
              <label class="col-form-label col-sm-3">Vendor</label>
              <div class="col-sm-9">
                <select class="form-control" v-model="tableFilter.vendor_id">
                  <option value="">Select A Vendor</option>
                  <option v-for="option in vendorList" v-bind:value="option.id">
                    {{ option.name }}
                  </option>
                </select>
              </div>
            </div>
            <div class="form-group row">
              <label class="col-form-label col-sm-3">Mfs</label>
              <div class="col-sm-9">
                <select class="form-control" v-model="tableFilter.mfs_id">
                  <option value="">Select A MFS</option>
                  <option v-for="option in mfs_list" v-bind:value="option.mfs_id">
                    {{ option.mfs_name }}
                  </option>
                </select>
              </div>
            </div>
            <div class="form-group row">
              <label class="col-form-label col-sm-3">Status</label>
              <div class="col-sm-9">
                <select class="form-control" v-model="tableFilter.recharge_status">
                  <option value="">Select A Status</option>
                  <option value="pending">Pending</option>
                  <option value="approved">Approved</option>
                  <option value="progressing">Progressing</option>
                  <option value="rejected">Rejected</option>
                </select>
              </div>
            </div>
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
    name: "recharge",
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
          date_from:"",
          date_to:"",
          store_id:"",
          vendor_id:"",
          mfs_id:"",
          recharge_status:"",
          phone_number:"",
          vendorListLoaded:0,
          storeListLoaded:0
        },
        mfs_list:[],
        storeList:[],
        vendorList:[],
        createNewRecharge:{
          note:'',
          mobile_number:'',
          mfs_id:'',
          mfs_type:'',
          recharge_amount:0.0,
          user_id:''
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
      this.masterTable = $('.dataTable').DataTable({
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
              'targets': 9,'searchable': false, 'orderable': false, 'width':'10%',
              'render': function (data, type, full, meta)
              {
                var info = $('<div/>').text(data).html();

                if(info === "Pending" || info === "Requested") return '<span class="badge badge-warning badge-pill" style="font-size: 14px;">'+info+'</span>';
                if(info === "Approved") return '<span class="badge badge-success badge-pill" style="font-size: 14px;">'+info+'</span>';
                if(info === "Progressing") return '<span class="badge badge-info badge-pill" style="font-size: 14px;">'+info+'</span>';
                if(info === "Rejected") return '<span class="badge badge-danger badge-pill" style="font-size: 14px;">'+info+'</span>';

                return '';
              }
            },
            {
              'targets': 11,'searchable': false, 'orderable': false, 'width':'10%',
              'render': function (data, type, full, meta)
              {

                var info = $('<div/>').text(data).html();

                return '<div class="btn-group btn-group-sm">'+
                  (info.split('|')[1] === "progressing"?'<button type="button" class="btn btn-success unlockRechargeBtt" data-id="'+info.split('|')[0]+'">UnLock</button>':'')+
                  (info.split('|')[1] === "requested"?'<button type="button" class="btn btn-success approveBtt" data-id="'+info.split('|')[0]+'">Approve</button>':'')+
                  ((info.split('|')[1] !== "approved" && info.split('|')[1] !== "rejected")?'<button type="button" class="btn btn-danger rejectBtt" data-id="'+info.split('|')[0]+'">Reject</button>':'')+
                  '</div>';
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
            "url": this.$axios.defaults.baseURL+'/api/recharge/activity',
            "type": "POST",
            'beforeSend': function (request) {
              request.setRequestHeader("Authorization", theInstance.$auth.getToken('local'));
            },
            "data": function ( d )
            {
              console.log(theInstance.tableFilter)
              d.date_from = theInstance.tableFilter.date_from
              d.date_to = theInstance.tableFilter.date_to
              d.store_id = theInstance.tableFilter.store_id
              d.vendor_id = theInstance.tableFilter.vendor_id
              d.mfs_id = theInstance.tableFilter.mfs_id
              d.recharge_status = theInstance.tableFilter.recharge_status
              d.phone_number = theInstance.tableFilter.phone_number
              d.vendorListLoaded = theInstance.tableFilter.vendorListLoaded
              d.storeListLoaded = theInstance.tableFilter.storeListLoaded
            },
            complete: function(data)
            {
              theInstance.mfs_list = data.responseJSON.mfs_list

              if(theInstance.tableFilter.storeListLoaded === 0)
              {
                theInstance.storeList = data.responseJSON.storeList
              }

              if(theInstance.tableFilter.vendorListLoaded === 0)
              {
                theInstance.vendorList = data.responseJSON.vendorList
              }

              theInstance.tableFilter.vendorListLoaded = 1
              theInstance.tableFilter.storeListLoaded = 1

              theInstance.dTableMount()
              //console.log(data.responseJSON);
            },
            error: function (xhr, error, thrown)
            {
              console.log("Error");
            }
          },
        }
      );

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



      /*$('.daterange-time2').daterangepicker({
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

      $('.daterange-time-start2').on('apply.daterangepicker', function(ev, picker) {
        //console.log(picker.startDate.format('YYYY-MM-DD'));
        //console.log(picker.endDate.format('YYYY-MM-DD'));
        $(this)[0].dispatchEvent(new Event('input', { 'bubbles': true }))
        theInstance.tableFilter.date_to = picker.startDate.format('YYYY-MM-DD')
      });*/
    },
    methods: {
      createRecharge:function()
      {
        if(confirm("Are you sure?"))
        {
          if(!this.createNewRecharge.mobile_number.match(/(^(\+01|01))[3-9]{1}(\d){8}$/))
          {
            alert("Invalid Bangladeshi Mobile Number.")
            return;
          }

          if(parseFloat(this.createNewRecharge.recharge_amount) <= 0)
          {
            alert("Invalid Recharge Amount.")
            return;
          }

          if(this.createNewRecharge.mfs_id === "")
          {
            alert("Please select a MFS")
            return;
          }

          if(this.createNewRecharge.mfs_type === "")
          {
            alert("Please select A MFS Type")
            return;
          }

          this.$axios.post("/api/recharge/create", this.createNewRecharge,{
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
                }
              }
            })
        }
      },
      dTableMount() {
        $(".unlockRechargeBtt").click(function(e){
          theInstance.unlock($(this).data("id"));
        });

        $(".rejectBtt").click(function(e){
          theInstance.approveReject($(this).data("id"), 'rejected');
        });

        $(".approveBtt").click(function(e){
          theInstance.approveReject($(this).data("id"), 'pending');
        });
      },

      approveReject:function(recharge_id, recharge_status)
      {
        if(confirm("Are you sure?"))
        {
          this.$axios.post("/api/recharge/approve_reject/"+recharge_id, {'recharge_status':recharge_status},{
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
      unlock:function(recharge_id)
      {
        if(confirm("Are you sure?"))
        {
          this.$axios.post("/api/recharge/unlock/"+recharge_id, this.createNewRecharge,{
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
      loadPageData()
      {
        $('#modal_filter').modal('hide');
        this.masterTable.ajax.reload();
      },
    }
  }
</script>

<style scoped>

</style>
