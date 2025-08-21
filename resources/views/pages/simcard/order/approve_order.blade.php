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
                        <h6 class="card-title">Approve Order (<?php echo $orderInfo->name; ?> <?php echo $orderInfo->quantity; ?> Pcs from <?php echo $orderInfo->store_name; ?> @ <?php echo date("F jS, Y", strtotime($orderInfo->created_at)); ?>)</h6>
                        <div class="header-elements">
                            <div class="list-icons  btn-group-sm">
                                <button type="button" class="btn btn-primary btn-sm" v-on:click="save()">Save</button>
                                <button type="button" class="btn btn-success btn-sm" v-on:click="approve()">Approve</button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body" :style="{'padding':'0'}">
                        <form method="post" id="approveOrderSubmission">
                            <table class="table dataTable no-footer">
                            <thead>
                                <tr>
                                    <th>Sl</th>
                                    <th>SIM ICCID</th>
                                    <th>SIM Mobile NUMBER</th>
                                    <th>Price</th>
                                </tr>
                            </thead>
                                <tbody>
                                <?php
                                $i = 1;
                                $l = 0;

                                if(!empty($order_appointed_sim_card) && count($order_appointed_sim_card) > 0)
                                {
                                foreach($order_appointed_sim_card as $row) {
                                ?>
                                <tr class="sim_info_row">
                                    <td><?php echo $l+1; ?></td>
                                    <td><input type="text" class="form-control sim_info_iccid" name="sim_info_iccid[]" tabindex="<?php echo $i; ?>" value="<?php echo $row->sim_card_iccid; ?>" ></td>
                                    <td><input type="text" class="form-control sim_info_number" name="sim_info_number[]" tabindex="<?php echo $i; ?>" value="<?php echo $row->sim_card_mobile_number; ?>" ></td>
                                    <td><input type="hidden" class="form-control sim_info_price" name="sim_info_price[]" value="<?php echo $row->cost; ?>"><input type="hidden" class="form-control sim_info_edit_update" name="sim_info_edit_update[]" value="u_<?php echo $row->id; ?>"><?php echo $row->cost; ?></td>
                                </tr>
                                <?php
                                $l = $l + 1;
                                $i = $i + 1;
                                }
                                } else {
                                if(!empty($orderInfo->saved_simcard_numbers))
                                {
                                foreach(json_decode($orderInfo->saved_simcard_numbers) as $_row) {
                                $row = explode("|", urldecode($_row));
                                ?>
                                <tr class="sim_info_row">
                                    <td><?php echo $l+1; ?></td>
                                    <td><input type="text" class="form-control sim_info_iccid" name="sim_info_iccid[]" tabindex="<?php echo $i; ?>" value="<?php echo $row[0]; ?>" ></td>
                                    <td><input type="text" class="form-control sim_info_number" name="sim_info_number[]" tabindex="<?php echo $i; ?>"  value="<?php echo $row[1]; ?>" ></td>
                                    <td><input type="hidden" class="form-control sim_info_price" name="sim_info_price[]" value="<?php echo $row[2]; ?>"><input type="hidden" class="form-control sim_info_edit_update" name="sim_info_edit_update[]" value="<?php echo $row[3]; ?>"><?php echo $row[2]; ?></td>
                                </tr>
                                <?php
                                $l = $l + 1;
                                $i = $i + 1;
                                }
                                }
                                }
                                ?>

                                <?php
                                if($userInfo->user_type == "super_admin" || $userInfo->user_type == "manager"):
                                    for ($x = $l; $x < intval($orderInfo->quantity) ; $x++) {
                                ?>
                                        <tr class="sim_info_row">
                                            <td><?php echo $x+1; ?></td>
                                            <td><input type="text" class="form-control sim_info_iccid" name="sim_info_iccid[]" tabindex="<?php echo $i; ?>" ></td>
                                            <?php $i = $i + 1; ?>
                                            <td><input type="text" class="form-control sim_info_number" name="sim_info_number[]" tabindex="<?php echo $i; ?>" ></td>
                                            <td><input type="hidden" class="form-control sim_info_price" name="sim_info_price[]" value="<?php echo $orderInfo->price; ?>"><input type="hidden" class="form-control sim_info_edit_update" name="sim_info_edit_update[]" value="i"><?php echo $orderInfo->price; ?></td>
                                        </tr>
                                <?php
                                        $i = $i + 1;
                                    }
                                endif;
                                ?>
                                </tbody>
                        </table>
                        </form>
                    </div>
                </div>
                <br><br>
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
                    masterTable:{},
                    windowHeight: 0,
                    windowWidth: 0,
                    scrollPosition:0,
                    defaultOrderId:'',
                    formToDoUpdate:false,
                    page: 1,
                    orderStatus:{'':'All','approved':'Approved','pending':'Pending','rejected':'Rejected'},
                    tableFilter:{
                        store_id:"",
                        status:"",
                    },
                    newOrder:{
                        store_id:'',
                        product_id:'',
                        quantity:'',
                    }
                }
            },
            mounted() {
                theInstance = this;

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
                save(){
                    var SIM_info = []

                    $( ".sim_info_row" ).each(function( index ) {

                        if($( this ).find('.sim_info_iccid').val().trim().length !=0 && $( this ).find('.sim_info_number').val().trim().length !=0)
                        {
                            SIM_info.push(encodeURIComponent($( this ).find('.sim_info_iccid').val().trim()+"|"+$( this ).find('.sim_info_number').val().trim()+"|"+$( this ).find('.sim_info_price').val().trim()+"|"+$( this ).find('.sim_info_edit_update').val().trim()));
                        }
                    });

                    let formData = new FormData();
                    formData.append("saved_sim_info", JSON.stringify(SIM_info))

                    /*Object.keys(this.newOrder).forEach(key => {
                        formData.append(key, this.newOrder[key])
                    });*/

                    axios.post("<?php echo env('APP_URL', ''); ?>/api/simcard/order/update/<?php echo $orderInfo->id; ?>", formData,
                        {
                            headers: {
                                Authorization: '<?php echo session('AuthorizationToken'); ?>'
                            },
                        }).then(response => {
                        alert("Order Saved Successfully.")
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
                                $('#modal_create_new_order').modal('show');
                                this.page_message = ''
                            }
                        });
                },
                approve(){
                    var SIM_info = []

                    $( ".sim_info_row" ).each(function( index ) {

                        if($( this ).find('.sim_info_iccid').val().trim().length !=0 && $( this ).find('.sim_info_number').val().trim().length !=0)
                        {
                            SIM_info.push(encodeURIComponent($( this ).find('.sim_info_iccid').val().trim()+"|"+$( this ).find('.sim_info_number').val().trim()+"|"+$( this ).find('.sim_info_price').val().trim()+"|"+$( this ).find('.sim_info_edit_update').val().trim()));
                        }
                    });

                    if(confirm("Are you sure?"))
                    {
                        let formData = new FormData();
                        formData.append("sim_info", JSON.stringify(SIM_info))

                        axios.post("<?php echo env('APP_URL', ''); ?>/api/simcard/order/update/<?php echo $orderInfo->id; ?>", formData,
                            {
                                headers: {
                                    Authorization: '<?php echo session('AuthorizationToken'); ?>'
                                },
                            }).then(response => {
                            alert("Order Saved Successfully.")
                            location.href = '/simcard/orders';
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
@include('inc.footer', ['load_datatable_scripts' => false])
