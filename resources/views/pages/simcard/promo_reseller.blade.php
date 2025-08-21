@include('inc.header')
@include('inc.menu')
<div class="app-content content ">
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
        <?php
        if(count($sc_simcard_offer) == 0)
        {
        ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <div class="alert-body">No Sim Card Offer have been updated or created by Parent reseller of Sub-Reseller (<?php echo $storeDetails->store_name; ?>). Please configure this first.</div>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <?php
        }
        ?>
        <div class="content-body">
            <section class="app-user-list">
                <!-- list section start -->
                <div class="card">
                    <div class="card-header" style="display: block">
                        <div class="row">
                            <div class="col-6">
                                <h6 class="card-title">Configure Reseller (<?php echo $storeDetails->store_name; ?>) Sim Card Offers</h6>
                            </div>
                            <div class="col-3">
                                <select class="form-control" onchange="theInstance.updateTableView(this.value)">
                                    <option value="">All Product</option>
                                    <?php foreach($productList as $row): ?>
                                    <option value="<?php echo $row->id; ?>">Product: <?php echo $row->name; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-3">
                                <div class="header-elements">
                                    <div class="list-icons btn-group-sm">
                                        <button type="button" class="btn btn-success btn-sm" onclick="theInstance.saveConfiguration()">Save</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body" :style="{'padding':'0'}">
                        <table class="table datatable-basic dataTable no-footer datatable-scroll-y">
                            <thead>
                                <tr>
                                    <th>Sl</th>
                                    <th>Product Name</th>
                                    <th>Offer Name</th>
                                    <th>Reseller(<?php echo $storeDetails->store_name; ?>) Bonus</th>
                                    <th>Reseller(<?php echo $storeDetails->store_name; ?>) MNP Bonus</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    $iii = 1;
                                ?>
                                <?php foreach($sc_simcard_offer as $row2): ?>
                                <tr class="product-row product-id-<?php echo $row2->product_id; ?>">
                                    <td><?php echo $iii++; ?></td>
                                    <td><?php echo $row2->inv_products_name; ?></td>
                                    <td><?php echo $row2->title; ?></td>
                                    <td>
                                        <?php
                                        $reseller_offer = json_decode($row2->reseller_offer);
                                        //print_r($reseller_offer);
                                        $ipfld = $row2->reseller_price;
                                        foreach($reseller_offer as $row)
                                        {

                                                $i = explode("|", $row);
                                                if($i[0] == $storeDetails->store_id)
                                                {
                                                    $ipfld = $i[1];
                                                    //print_r($i);
                                                }
                                        }
                                        ?>
                                        <input type="text" class="form-control pickMe" placeholder="Put your bonus" data-offer_id="<?php echo $row2->id; ?>" value="<?php echo $ipfld; ?>">
                                    </td>
                                    <td>
                                        <?php
                                        $mnp_bonus = 0;
                                        foreach($reseller_offer as $row)
                                        {
                                            $i = explode("|", $row);
                                            if(count($i) > 2 && $i[0] == $storeDetails->store_id)
                                            {
                                                $mnp_bonus = $i[2];
                                            }
                                        }
                                        ?>
                                        <input type="text" class="form-control pickMeMnp" placeholder="Put your bonus" data-offer_id_for_mnp="<?php echo $row2->id; ?>" value="<?php echo $mnp_bonus; ?>">
                                    </td>
                                </tr>
                                <?php endforeach; ?>
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
    var theInstance= {
        storeSelectedBaseCurrency:'',
        parent_store_id:'0',
        masterTable:'',
        product_id:'',
        waitingDialogInShow:false,
        showWaitingDialog:function ()
        {
            if(!theInstance.waitingDialogInShow)
            {
                waitingDialog.show();
                waitingDialog.animate("Loading. Please Wait.");
                theInstance.waitingDialogInShow = true;
            }

        },
        hideWaitingDialog:function ()
        {
            setTimeout(function () {
                waitingDialog.hide();
            }, 1000);
            theInstance.waitingDialogInShow = false;
        },
        updateTableView(product_id){
            $(".product-row").show()
            if(product_id.length > 2) $(".product-row:not(.product-id-"+product_id+")").hide();
        },
        saveConfiguration(){
            if(confirm("Are you sure?"))
            {
                var offerDetails = {}

                $('.pickMe').each(function(){
                    offerDetails[$(this).data('offer_id')] = $(this).val()
                    //console.log($(this).data('offer_id')+"----"+$(this).val());
                });

                //console.log(offerDetails);

                $('.pickMeMnp').each(function(){
                    if(offerDetails[$(this).data('offer_id_for_mnp')])
                    {
                        offerDetails[$(this).data('offer_id_for_mnp')] = (offerDetails[$(this).data('offer_id_for_mnp')] + "|" + $(this).val());
                    }
                });

                //console.log(offerDetails);

                theInstance.showWaitingDialog();
                jQuery.ajax({
                    type: "POST",
                    beforeSend: function(request) {
                        request.setRequestHeader("Authorization", '<?php echo session('AuthorizationToken'); ?>');
                    },
                    url: "<?php echo env('APP_URL', ''); ?>/api/simcard/promo/config_reseller_bonus/<?php echo $storeDetails->store_id; ?>",
                    dataType: 'json',
                    data: JSON.stringify({
                        "offerDetails":offerDetails,
                    }),
                    statusCode: {
                        200: function() {
                            //location.reload()
                            theInstance.hideWaitingDialog();
                        },
                        406: function() {
                            location.reload()
                        },
                        401: function() {
                            location.reload()
                        }
                    }
                });
            }
        },
    };
</script>
@include('inc.footer', ['load_datatable_scripts' => true])
