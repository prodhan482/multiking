@include('inc.header', ['load_vuejs' => true, 'load_html2canvas' => true])

<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" data-keyboard="false" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Please enter your transaction pin</h5>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="exampleInputPassword1">Transaction PIN</label>
                    <input type="password" class="form-control" id="exampleInputPassword1" placeholder="Put Transaction PIN">
                </div>
            </div>
            <div class="modal-footer">

                <button type="button" class="btn btn-danger" onclick="logout()">Logout</button>
                <button type="button" class="btn btn-primary" onclick="verify()">Submit</button>
            </div>
        </div>
    </div>
</div>

<script>
    $(function(){

        $('#exampleModal').modal({
            show:true,
            keyboard: false,
            backdrop: 'static'
        });

        /*let transaction_pin = prompt("Please enter your transaction pin", "");

        if (transaction_pin != null) {

        } else {
            location.href = '/logout';
        }*/
    })

    function verify()
    {
        if($('#exampleInputPassword1').val().length === 0)
        {
            return alert("Please enter your transaction pin")
        }

        jQuery.ajax({
            type: "POST",
            beforeSend: function(request) {
                request.setRequestHeader("Authorization", '<?php echo session('AuthorizationToken'); ?>');
            },
            url: "<?php echo env('APP_URL', ''); ?>/api/validate_transaction_pin",
            dataType: 'json',
            data: JSON.stringify({"transaction_pin":$('#exampleInputPassword1').val()}),
            statusCode: {
                200: function() {
                    location.reload()
                },
                422: function() {
                    alert("Invalid Pin")
                    location.reload()
                },
                401: function() {
                    location.reload()
                }
            }
        });
    }

    function logout()
    {
        location.href = '/logout';
    }
</script>
@include('inc.footer', ['load_dashboard_scripts' => true, 'load_datatable_scripts' => true])
