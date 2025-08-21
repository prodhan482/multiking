@include('inc.header', ['load_vuejs' => true])
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
                        <h6 class="card-title">Convert Currency Rates <?php if($hideTable){echo '<span style="color: red">(Please Create a Reseller First)</span>';} ?></h6>
                        <div class="header-elements">
                            <div class="list-icons">
                                <button type="button" class="btn btn-primary" v-on:click="saveConversionRates()">Update</button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body" :style="{'padding':'0'}">
                        <table <?php if($hideTable){echo 'style="display: none"';} ?> class="table datatable-basic dataTable no-footer datatable-scroll-y">
                            <thead>
                                <tr>
                                    <th>Sl</th>
                                    <th>From</th>
                                    <th>To</th>
                                    <th>Conversion</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(item, index) in tableData">
                                    <td v-html="(index + 1)"></td>
                                    <td>Euro</td>
                                    <td v-html="(item.name)"></td>
                                    <td>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">1 &euro; = </span>
                                            </div>
                                            <input type="text" placeholder="Put Conversion Rate" class="form-control " v-model="item.conv_amount">
                                            <div class="input-group-append">
                                                <span class="input-group-text" v-html="(''+item.name)"></span>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
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
                    tableData:[
                        {
                            type:'bdt',
                            name:'BDT',
                            conv_amount:102
                        },
                        {
                            type:'usd',
                            name:'USD',
                            conv_amount:1.19
                        },
                        {
                            type:'gbp',
                            name:'GBP',
                            conv_amount:0.86
                        },
                        {
                            type:'cfa_franc',
                            name:'CFA Franc',
                            conv_amount:655.96
                        }
                    ]
                }
            },
            mounted() {
                theInstance = this;
                <?php if(!empty($userInfo->currency_conversions_list)): ?>
                this.tableData = JSON.parse('<?php echo json_encode($userInfo->currency_conversions_list); ?>');
                <?php endif; ?>
            },
            methods: {
                saveConversionRates(){
                    if(confirm("Are You Sure ?")) {
                        axios.post("<?php echo env('APP_URL', ''); ?>/api/stores/save_store_currency", {"<?php echo ($userInfo->user_type == "super_admin"?"default_conv_rate_json":"store_conv_rate_json"); ?>":JSON.stringify(this.tableData)},
                            {
                                headers: {
                                    Authorization: '<?php echo session('AuthorizationToken'); ?>'
                                },
                            }).then(response => {
                                location.reload();
                            })
                            .catch(error => {
                                if (error.response) {
                                    switch (error.response.status)
                                    {
                                        case 401:
                                            this.makeForceLogout()
                                            break;
                                        case 406:
                                            console.log(error.response)
                                            this.formErrorMessage = error.response.data.message.join(",")
                                            break;
                                    }
                                    this.page_message = ''
                                }
                            });

                    }
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
@include('inc.footer', ['load_datatable_scripts' => true])

