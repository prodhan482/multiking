
<div id="changePassword" class="modal fade" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Password</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="form-group row">
                    <label class="col-form-label col-sm-3">New Password</label>
                    <div class="col-sm-9">
                        <input type="text" placeholder="Enter New Password" class="form-control" id="centralNewPassword">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-warning" data-dismiss="modal"> Cancel</button>
                <button class="btn btn-danger" onclick="changeThePassword_fgdjkl()"> Update</button>
            </div>
        </div>
    </div>
</div>

<div id="changePin" class="modal fade" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Transaction Pin</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="form-group row">
                    <label class="col-form-label col-sm-3">New Pin</label>
                    <div class="col-sm-9">
                        <input type="number" placeholder="Enter New Transaction Pin" class="form-control" id="centralNewTransactionPin">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-warning" data-dismiss="modal"> Cancel</button>
                <button class="btn btn-danger" onclick="changeThePin_fgdjkl()"> Update</button>
            </div>
        </div>
    </div>
</div>

<div class="sidenav-overlay"></div>
<div class="drag-target"></div>

<!-- BEGIN: Footer-->
<footer class="footer footer-static footer-light">
    <p class="clearfix mb-0" style="display: none">
        <span class="float-md-left d-block d-md-inline-block mt-25">COPYRIGHT &copy; 2021<a class="ml-25" href="https://1.envato.market/pixinvent_portfolio" target="_blank">Pixinvent</a><span class="d-none d-sm-inline-block">, All rights Reserved</span></span>
        <span class="float-md-right d-none d-md-block">Hand-crafted & Made with<i data-feather="heart"></i></span>
    </p>
</footer>

{{--<button class="btn btn-primary btn-icon scroll-top" type="button"><i data-feather="arrow-up"></i></button>--}}

<!-- END: Footer-->

<!-- BEGIN: Page Vendor JS-->
<script src="/assets/vendors/js/ui/jquery.sticky.js"></script>
<script src="/assets/vendors/js/charts/apexcharts.min.js"></script>
<script src="/assets/vendors/js/extensions/toastr.min.js"></script>
<script src="/assets/vendors/js/extensions/moment.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/numeral.js/2.0.6/numeral.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/bootbox.js/5.5.2/bootbox.min.js"></script>
<!-- END: Page Vendor JS-->

<!-- BEGIN: Theme JS-->
<script src="/assets/js/core/app-menu.js"></script>
<script src="/assets/js/core/app.js"></script>
<!-- END: Theme JS-->

<?php if(!empty($load_dashboard_scripts)): ?>
<!-- BEGIN: Page JS-->
<script src="/assets/js/scripts/pages/dashboard-analytics.js"></script>
<script src="/assets/js/scripts/pages/app-invoice-list.js"></script>
<!-- END: Page JS-->
<?php endif; ?>

<?php if(!empty($load_datatable_scripts)): ?>
<script src="/assets/vendors/js/tables/datatable/jquery.dataTables.min.js"></script>
<script src="/assets/vendors/js/tables/datatable/datatables.bootstrap4.min.js"></script>
<script src="/assets/vendors/js/tables/datatable/dataTables.responsive.min.js"></script>
<script src="/assets/vendors/js/tables/datatable/responsive.bootstrap4.js"></script>
<script src="/assets/vendors/js/tables/datatable/datatables.checkboxes.min.js"></script>
<script src="/assets/vendors/js/tables/datatable/datatables.buttons.min.js"></script>
<script src="/assets/vendors/js/tables/datatable/jszip.min.js"></script>
<script src="/assets/vendors/js/tables/datatable/pdfmake.min.js"></script>
<script src="/assets/vendors/js/tables/datatable/vfs_fonts.js"></script>
<script src="/assets/vendors/js/tables/datatable/buttons.html5.min.js"></script>
<script src="/assets/vendors/js/tables/datatable/buttons.print.min.js"></script>
<script src="/assets/vendors/js/tables/datatable/dataTables.rowGroup.min.js"></script>
<script src="/assets/vendors/js/tables/datatable/dataTables.fixedHeader.min.js"></script>
<script src="/assets/vendors/js/tables/datatable/dataTables.fixedColumns.min.js"></script>
<?php endif; ?>

<?php if(!empty($load_pick_a_date_scripts)): ?>
<script src="/assets/vendors/js/pickers/pickadate/picker.js"></script>
<script src="/assets/vendors/js/pickers/pickadate/picker.date.js"></script>
<script src="/assets/vendors/js/pickers/pickadate/picker.time.js"></script>
<script src="/assets/vendors/js/pickers/pickadate/legacy.js"></script>
<?php endif; ?>

<script>
    $(window).on('load', function() {
        if (feather) {
            feather.replace({
                width: 14,
                height: 14
            });
        }
    })

    function changeThePassword_fgdjkl()
    {
        if(jQuery("#centralNewPassword").val().length > 2)
        {
            jQuery.ajax({
                type: "PATCH",
                beforeSend: function(request) {
                    request.setRequestHeader("Authorization", '<?php echo session('AuthorizationToken'); ?>');
                },
                url: "<?php echo env('APP_URL', ''); ?>/api/me/update",
                dataType: 'json',
                data: JSON.stringify({"new_password":jQuery("#centralNewPassword").val()}),
                statusCode: {
                    200: function() {
                        alert("Password Changed Successfully. Please Re-Login")
                        window.location.href = "/logout";
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
    }
    function changeThePin_fgdjkl()
    {
        if(jQuery("#centralNewTransactionPin").val().length > 3)
        {
            jQuery.ajax({
                type: "PATCH",
                beforeSend: function(request) {
                    request.setRequestHeader("Authorization", '<?php echo session('AuthorizationToken'); ?>');
                },
                url: "<?php echo env('APP_URL', ''); ?>/api/me/res/update",
                dataType: 'json',
                data: JSON.stringify({"transaction_pin":jQuery("#centralNewTransactionPin").val()}),
                statusCode: {
                    200: function() {
                        alert("Pin Changed Successfully");
                        location.reload()
                    },
                    406: function() {
                        //location.reload()
                        alert("Invalid PIN. Pin must be number and 4 digit only.")
                    },
                    401: function() {
                        //location.reload()
                    }
                }
            });
        } else {
            alert("Invalid PIN. Pin must be number and 4 digit only.")
        }
    }
</script>
</body>
<!-- END: Body-->

</html>
