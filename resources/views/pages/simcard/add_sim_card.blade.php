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
                        <h6 class="card-title">Add New Sim Card</h6>
                        <div class="header-elements">
                            <div class="list-icons  btn-group-sm">
                                <button type="button" class="btn btn-primary btn-sm" v-on:click="save()">Save</button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body" :style="{'padding':'0'}">

                        <div class="input-group">
                            <span class="input-group-text" id="basic-addon-search1">
                                Product
                            </span>
                            <select class="form-control order-type" v-model="newSimCard.product_id">
                                <option value="">Select A Product</option>
                                <?php foreach($productList as $row): ?>
                                <option value="<?php echo $row->id; ?>"><?php echo $row->name; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <br>
                        <div class="input-group">
                            <span class="input-group-text" id="basic-addon-search1">
                                Lot Name
                            </span>
                            <input type="text" class="form-control" placeholder="Lot Name" v-model="newSimCard.lot_name">
                        </div>
                        <br>

                        <table class="table">
                            <thead>
                                <tr>
                                    <th>SIM ICCID</th>
                                    <th>SIM Mobile NUMBER</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php
                            $i = 1;
                            foreach(range(1, 101) as $row){
                            ?>
                            <tr class="sim_info_row">
                                <td><input type="text" class="form-control sim_info_iccid" name="sim_info_iccid[]" tabindex="<?php echo $i; ?>" ></td>
                                <?php $i = $i + 1; ?>
                                <td><input type="text" class="form-control sim_info_number" name="sim_info_number[]" tabindex="<?php echo $i; ?>" ></td>
                                <?php $i = $i + 1; ?>
                            </tr>
                            <?php
                            }
                            ?>
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
    var theInstance = {};
    $(function(){
        var app = new Vue({
            el: '#app',
            data() {
                return {
                    formErrorMessage:'',
                    page_message:'',
                    enteredUserName:'',
                    enteredUserPassword:'',
                    masterTable:{},
                    windowHeight: 0,
                    windowWidth: 0,
                    scrollPosition:0,
                    newSimCard:{
                        product_id:"",
                        lot_name:""
                    }
                }
            },
            mounted() {
                theInstance = this;
                $('.table').DataTable( {
                    scrollY:        '50vh',
                    scrollCollapse: true,
                    paging:         false,
                    "searching": false,
                    "info": false,
                } );
                $('input').bind('keypress', function(eInner) {
                    if (eInner.keyCode == 13) //if its a enter key
                    {
                        var tabindex = $(this).attr('tabindex');
                        tabindex++; //increment tabindex
                        $('[tabindex=' + tabindex + ']').focus();
                        return false;
                    }
                });
            },
            methods: {
                save()
                {
                    var SIM_info = []

                    $( ".sim_info_row" ).each(function( index ) {

                        if($( this ).find('.sim_info_iccid').val().trim().length !=0 && $( this ).find('.sim_info_number').val().trim().length !=0)
                        {
                            SIM_info.push(encodeURIComponent($( this ).find('.sim_info_iccid').val().trim()+"|"+$( this ).find('.sim_info_number').val().trim()));
                        }
                    });

                    if(theInstance.newSimCard.product_id.length == 0) return alert("Select A Product");
                    if(theInstance.newSimCard.lot_name.length == 0) return alert("Put A Lot Name");
                    if(SIM_info.length < 1)
                    {
                        return alert("Add minimum 1 Sim Card")
                    }

                    let formData = new FormData();
                    formData.append("saved_sim_info", JSON.stringify(SIM_info))
                    formData.append("product_id", theInstance.newSimCard.product_id)
                    formData.append("lot_name", theInstance.newSimCard.lot_name)

                    axios.post("<?php echo env('APP_URL', ''); ?>/api/simcard/add", formData,
                        {
                            headers: {
                                Authorization: '<?php echo session('AuthorizationToken'); ?>'
                            },
                        }).then(response => {
                        alert("Sim Card Added Successfully.")
                        window.location.href = "/simcard/all";
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
                                        alert(error.response.data.message.join(","))
                                        break;
                                }
                            }
                        });
                },
                dTableMount()
                {
                    $(".updateMfs").click(function(e){
                        theInstance.setDefaultMfsId($(this).data("id"));
                    });
                },
                loadPageData(){
                    this.masterTable.ajax.reload();
                },
                resetFilter(){
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
@include('inc.footer', ['load_datatable_scripts' => true])
