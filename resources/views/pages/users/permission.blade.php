@include('inc.header')
@include('inc.menu')
<div class="app-content content ">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper">
        <div class="content-header row">
        </div>

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
                <div class="card">
                    <div class="card-header bg-white header-elements-inline">
                        <h6 class="card-title">User Permission (<?php echo $selected_user_dta->username; ?>)</h6>
                        <div class="header-elements">
                            <button type="button" class="btn btn-primary" onclick="updatePermission()">Update</button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <?php

                            $permission_query_ires_result = DB::select(DB::raw("SELECT GROUP_CONCAT(aauth_perm_to_user.perm_id) as permission_list FROM aauth_perm_to_user WHERE aauth_perm_to_user.user_id = '".$q_user_id."'"));

                            $perm_array = explode("," , $permission_query_ires_result[0]->permission_list);

                            $permission_query_ires_result = DB::select(DB::raw("SELECT aauth_perms_group.perm_group_id AS group_id, aauth_perms.id AS perm_id, aauth_perms.definition, aauth_perms.`name`, aauth_perms_group.group_defination FROM aauth_perms_group LEFT JOIN aauth_perms ON aauth_perms_group.prem_id = aauth_perms.id"));

                            foreach($permission_query_ires_result as $row)
                            {
                                $permissions[$row->group_id]["group_name"] = $row->group_defination;
                                $permissions[$row->group_id]["perm_list"][] = $row;
                            }

                            $r = 0;

                            foreach($permissions as $perm)
                            {
                            ?>
                            <div class="col-xs-12 col-md-4">
                                <input type="checkbox" class="mother_<?php echo preg_replace('/\s+/', '_', $perm["group_name"]); ?>" onclick="toggleCheckUncheck('<?php echo preg_replace('/\s+/', '_', $perm["group_name"]); ?>', 'mother_<?php echo preg_replace('/\s+/', '_', $perm["group_name"]); ?>')"> <span style="font-size: 18px; font-weight: bold"><?php echo $perm["group_name"]; ?></span><br>
                                <?php
                                foreach($perm["perm_list"] as $p)
                                {
                                ?>
                                <input type="checkbox" name="user_permission[]" <?php echo (in_array($p->perm_id, $perm_array)?"checked":"") ?> class="<?php echo preg_replace('/\s+/', '_', $perm["group_name"]); ?>" value="<?php echo $p->perm_id; ?>">  <?php echo $p->definition; ?><br>
                                <?php
                                }
                                ?>
                            </div>
                            <?php
                            $r = $r +1;
                            if($r == 3)
                            {
                            ?>
                        </div><br><br><div class="row">
                            <?php
                            $r = 0;
                            }
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>
<script>
    function toggleCheckUncheck(__class_name, __parent_class_name)
    {
        //var checkBoxes = $("."+__class_name);
        //checkBoxes.prop("checked", !checkBoxes.prop("checked"));
        var checkBoxes = $("."+__class_name);
        checkBoxes.prop("checked", $("."+__parent_class_name).prop("checked"));
    }

    function updatePermission()
    {
        if(confirm("Are You Sure ?"))
        {
            var newPermissionList = []

            $('input[type=checkbox]').each(function () {
                if(this.checked && $(this).val().length > 0)
                {
                    newPermissionList.push($(this).val())
                }
            });

            jQuery.ajax({
                type: "POST",
                beforeSend: function(request) {
                    request.setRequestHeader("Authorization", '<?php echo session('AuthorizationToken'); ?>');
                },
                url: "<?php echo env('APP_URL', ''); ?>/api/user/<?php echo $selected_user_dta->id; ?>/permissions",
                dataType: 'json',
                data: JSON.stringify({
                    permission_ids: newPermissionList.join("|")
                }),
                statusCode: {
                    200: function() {
                        location.reload()
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

    $(function()
    {
        <?php
            foreach($permissions as $perm)
            {
            ?>
        if ($('.<?php echo preg_replace('/\s+/', '_', $perm["group_name"]); ?>:checked').length == $('.<?php echo preg_replace('/\s+/', '_', $perm["group_name"]); ?>').length)
        {
            $(".mother_<?php echo preg_replace('/\s+/', '_', $perm["group_name"]); ?>").prop("checked", true);
        }
        <?php
        }
        ?>
    })
</script>
@include('inc.footer')
