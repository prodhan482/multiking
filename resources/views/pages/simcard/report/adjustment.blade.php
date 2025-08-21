@include('inc.header', ['load_vuejs' => true, 'load_pick_a_date_scripts' => true])
@include('inc.menu')
<div class="app-content content " id="app">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper">
        <div class="content-header row"></div>
        @if ($message = Session::get('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <div class="alert-body">{{ $message }}</div>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif
        @if ($message = Session::get('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <div class="alert-body">{{ $message }}</div>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif
        <div class="content-body">
            <section class="app-user-list">
                <!-- list section start -->
                <div class="card">
                    <div class="card-header bg-white header-elements-inline">
                        <h6 class="card-title">TRANSACTIONS OVERVIEW</h6>
                    </div>
                    <div class="card-body" :style="{'padding':'0'}">
                        <div class="row p-1">
                            <?php if(!empty($userInfo) && !empty($userInfo->permission_lists) &&
                            (in_array("StoreController::list", $userInfo->permission_lists))  ): ?>
                            <div class="col-md-3 col-sm-12">
                                <div class="input-group">
                                    <select class="form-control do-me-select2-store" v-model="tableFilter.store_id">
                                        <option value="<?php //echo $reseller_id; ?>">Select A Reseller</option>
                                        <?php foreach($storeList as $row): ?>
                                        <option value="<?php echo $row->id; ?>">Reseller -> <?php echo $row->name; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <?php endif; ?>
                            <div class="col-md-2 col-sm-12">
                                <input type="text" class="form-control addADateTimePickerHere hasDatepicker daterange-time-from" placeholder="From (Date Picker)" v-model="tableFilter.start_date">
                            </div>

                            <div class="col-md-2 col-sm-12">
                                <input type="text" class="form-control addADateTimePickerHere hasDatepicker daterange-time-to" placeholder="To (Date Picker)" v-model="tableFilter.end_date">
                            </div>
                            <div class="col-md-5 col-sm-12">
                                <button type="button" class="btn btn-primary" v-on:click="loadTableData">Search</button>
                                <button type="button" class="btn btn-success" v-on:click="generateXlsFile">Generate Bill Copy</button>
                                <button type="button" class="btn btn-warning" v-on:click="resetFilter">Clear</button>
                            </div>
                        </div>

                        <table class="table dataTable no-footer">
                            <thead>
                            <tr>
                                <th>Sl</th>
                                <th>Date</th>
                                <?php if(!empty($userInfo) && !empty($userInfo->permission_lists) &&
                                (in_array("StoreController::list", $userInfo->permission_lists))  ): ?>
                                <th>Reseller</th>
                                <?php endif; ?>
                                <th>Description</th>
                                <th>Due</th>
                                <th>Adjustment</th>
                                <th>Balance</th>
                            </tr>
                            </thead>
                            <tbody>
                                <tr v-for="cols in tableData">
                                    <td v-for="cell in cols" v-html="cell"></td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr v-for="cols in footerData">
                                    <th v-for="cell in cols" v-html="cell"></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                </div>
                <!-- list section end -->
            </section>
        </div>
    </div>
</div>
<script>
    $(function(){
        var app = new Vue({
            el: '#app',
            data() {
                return {
                    formErrorMessage:'',
                    page_message:'',
                    enteredUserName:'',
                    enteredUserPassword:'',
                    masterTable:null,
                    windowHeight: 0,
                    windowWidth: 0,
                    scrollPosition:0,
                    defaultOrderId:'',
                    fromPicker:{},
                    toPicker:{},
                    formToDoUpdate:false,
                    page: 1,
                    tableData:[],
                    footerData:[],
                    tableFilter:{
                        generateXLS:false,
                        store_id:"<?php echo $reseller_id; ?>",
                        start_date:"<?php echo date("Y-m-d", time()); ?>",
                        end_date:"<?php echo date("Y-m-d", time()); ?>",
                    }
                }
            },
            mounted() {
                theInstance = this;
                this.loadTableData();

                theInstance.fromPicker = $('.daterange-time-from').pickadate({
                    format: 'yyyy-mm-dd',
                    selectYears: true,
                    selectMonths: true,
                    onClose: function() {
                        theInstance.tableFilter.start_date = theInstance.fromPicker.pickadate('picker').get("value");
                    }
                });

                theInstance.toPicker = $('.daterange-time-to').pickadate({
                    format: 'yyyy-mm-dd',
                    selectYears: true,
                    selectMonths: true,
                    onClose: function() {
                        theInstance.tableFilter.end_date = theInstance.toPicker.pickadate('picker').get("value");
                    }
                });

                $('.do-me-select2-store').select2();
                $(".do-me-select2-store").val(theInstance.tableFilter.store_id).trigger('change');
                $('.do-me-select2-store').on('select2:select', function (e) {
                    theInstance.tableFilter.store_id = $('.do-me-select2-store').select2('data')[0].id
                });
            },
            methods: {
                generateXlsFile()
                {
                    let formData = new FormData();

                    theInstance.tableFilter.generateXLS = true
                    Object.keys(theInstance.tableFilter).forEach(key => {
                        formData.append(key, theInstance.tableFilter[key])
                    });

                    axios.post("<?php echo env('APP_URL', ''); ?>/api/simcard/report/adjustment",  formData,
                        {
                            headers: {
                                Authorization: '<?php echo session('AuthorizationToken'); ?>'
                            },
                        }).then(response => {

                            window.location.href = response.data.xls_file_path;

                        }).catch(error => {
                            if (error.response) {
                                switch (error.response.status)
                                {
                                    case 401:
                                        this.makeForceLogout()
                                        break;
                                    case 406:
                                        console.log(error.response)
                                        alert(error.response.data.message.join(","))
                                        break;
                                }
                                this.page_message = ''
                            }
                        });
                },
                loadTableData()
                {
                    let formData = new FormData();
                    theInstance.tableFilter.generateXLS = false;

                    Object.keys(this.tableFilter).forEach(key => {
                        formData.append(key, this.tableFilter[key])
                    });

                    axios.post("<?php echo env('APP_URL', ''); ?>/api/simcard/report/adjustment", formData,
                        {
                            headers: {
                                Authorization: '<?php echo session('AuthorizationToken'); ?>'
                            },
                        }).then(response => {
                        theInstance.tableData = response.data.tableData
                        theInstance.footerData = response.data.footerData

                        if(theInstance.masterTable) {
                            theInstance.masterTable.destroy();
                        }

                        setTimeout(function () {
                            theInstance.masterTable = $('.table').DataTable( {
                                scrollY:        '50vh',
                                scrollCollapse: true,
                                paging:         false,
                                "ordering": false,
                                "searching": false,
                                "info": false,
                            } );
                        }, 500)

                    }).catch(error => {
                        if (error.response) {
                            switch (error.response.status)
                            {
                                case 401:
                                    this.makeForceLogout()
                                    break;
                                case 406:
                                    console.log(error.response)
                                    alert(error.response.data.message.join(","))
                                    break;
                            }
                            this.page_message = ''
                        }
                    });
                },
                resetFilter(){
                    theInstance.tableFilter.store_id = ""
                    theInstance.tableFilter.product_id = ""
                    theInstance.tableFilter.start_date = "<?php echo date("Y-m-d", time()); ?>"
                    theInstance.tableFilter.end_date = "<?php echo date("Y-m-d", time()); ?>"
                    this.loadPageData();
                },
                makeForceLogout()
                {
                    if(confirm("Your Session have been Expired. You have to re-login to continue. Press ok to logout")){
                        this.userLogout()
                    }
                },
                async userLogout() {
                    try {
                        let response = await this.$auth.logout()
                        console.log(response.data)
                    } catch (err) {
                        console.log(err)
                    }
                },
            }
        })
    });
</script>
<style>
    .Pending > td {
        background-color: #ffffff !important;
        border-bottom: black solid 1px;
    }
    .Approved > td {
        background-color: rgba(75, 255, 0, 0.13) !important;
    }
    .Rejected > td {
        background-color: rgba(255, 9, 0, 0.13) !important;
    }
    .select2-container .select2-selection--single
    {
        height: 34px;
    }
</style>
<script src="/assets/vendors/js/forms/select/select2.full.min.js"></script>
@include('inc.footer', ['load_datatable_scripts' => true, 'load_pick_a_date_scripts' => true])
