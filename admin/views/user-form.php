<style>
    .select2-search__field{
        width : 54.75em !important;
    }
</style>

<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <div class="page-title-right">
                <a href="<?php echo base_url('admin/user/list_users') ?>" class="btn btn-sm btn-primary">
                    <i class="fa fa-list"></i>&nbsp;Back
                </a>
            </div>
            <h4 class="page-title"><strong><?php echo empty($form_caption) ? "" : $form_caption; ?></strong></h4>

        </div>
    </div>
</div>     
<!-- end page title --> 


<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <?php
                    $validation_errors = validation_errors();
                    if (!empty($validation_errors))
                    {
                        ?>
                        <div class="col-xs-12 alert alert-danger">
                            <button type="button" class="close" data-dismiss="alert"
                                    aria-hidden="true">
                                &times;
                            </button>
                            <?php echo $validation_errors; ?>
                        </div>
                        <?php
                    }
                ?>

                <?php
                    $attributes = array('id' => 'admin-form', 'class' => 'form-horizontal');
                    echo form_open_multipart($form_action, $attributes);
                ?>   
                <input type="hidden" name="admin_id" id="admin_id" value="<?php echo empty($admin_id) ? NULL : $admin_id; ?>">

                <div class="form-group row">                        
                    <label class="col-md-3 col-form-label" for="admin_name">Name</label>
                    <div class="col-md-9">
                        <?php
                            $data = array(
                                'name' => 'admin_name',
                                'id' => 'admin_name',
                                'value' => set_value('admin_name', empty($admin_name) ? NULL : $admin_name),
                                'class' => 'form-control',
                                'placeholder' => 'Enter Name',
                                'autofocus' => true
                            );
                            echo form_input($data);
                        ?>
                    </div>
                </div>

                <?php $session_site_id = get_session_site_id(); ?>
                <div class="form-group row" <?php echo ((!empty($admin_id) && $session_site_id == $admin_id && $admin_type == 'Super Admin')) ? "hidden" : NULL; ?>>                        
                    <label class="col-md-3 col-form-label" for="admin_type">Admin Type</label>
                    <div class="col-md-9">
                        <?php
                            echo form_dropdown('admin_type', empty($arr_admin_type) ? Null : ($arr_admin_type), empty($admin_type) ? NULL : $admin_type, 'class="form-control input-block-level"');
                        ?>
                    </div>
                </div>

                <div class="form-group row site_dropdown_div" style="display: none">                        
                    <label class="col-md-3 col-form-label" for="sites">Site ID</label>
                    <div class="col-md-9">
                        <?php
                            echo form_dropdown('sites', empty($arr_sites) ? Null : ($arr_sites), empty($sites) ? NULL : $sites, 'class="form-control input-block-level"');
                        ?>

                    </div>
                </div>

                <div class="form-group row site_dropdown_div" style="display: none">                        
                    <label class="col-md-3 col-form-label" for="site_trans">Access to other sites</label>
                    <div class="col-md-9">
                        <?php
                            echo form_multiselect('site_trans[]', empty($arr_site_trans) ? '' : $arr_site_trans, empty($site_id_arr) ? '' : $site_id_arr, 'id="site_trans", class="form-control select2"');
//                            echo form_dropdown('site', empty($arr_sites) ? Null : ($arr_sites), empty($sites) ? NULL : $sites, 'class="form-control input-block-level"');
                        ?>

                    </div>
                </div>

                <div class="form-group row">                        
                    <label class="col-md-3 col-form-label" for="password">Password</label>
                    <div class="col-md-9">
                        <?php
                            $data = array(
                                'name' => 'password',
                                'id' => 'password',
                                'value' => set_value('password', empty($password) ? NULL : $password),
                                'class' => 'form-control',
                                'placeholder' => 'Enter Password',
                                'type' => 'password',
                                'autofocus' => true
                            );
                            echo form_password($data);
                        ?>
                    </div>
                </div>


                <div class="form-group row">                        
                    <label class="col-md-3 col-form-label" for="admin_status">Admin Status</label>
                    <div class="col-md-9">
                        <?php
                            echo form_dropdown('admin_status', empty($arr_admin_status) ? Null : ($arr_admin_status), empty($admin_status) ? NULL : $admin_status, 'class="form-control input-block-level"');
                        ?>
                    </div>
                </div>


                <div class="form-group row">
                    <div class="col-md-9 offset-sm-3">
                        <button type="submit" name="save"  id="button" class="btn btn-primary mr-2"> <i class="fa fa-save" aria-hidden="true"></i> Save</button>
                    </div>
                </div>
                <?php echo form_close(); ?>
            </div> <!-- end card-body -->
        </div> <!-- end card-->
    </div><!-- end col -->
</div>
<!-- end row -->
<script>
    $(document).ready(function () {
        $('.select2').select2();

//        $("#admin-form").validate({
//            rules: {
//                admin_name: {"required": true},
//                admin_type: {"required": true},
//                Password: {"required": true},
//                admin_status: {"required": true}
//            },
//            messages: {
//                admin_name: {"required": "Enter Admin Name"},
//                admin_type: {"required": "Select Admin Type"},
//                Password: {"required": "Enter Password"},
//                admin_status: {"required": "Select Admin Status"}
//            }
//        });


        $("#admin-form").validate({
            rules: {
                admin_name: {"required": true},
                admin_type: {"required": true},
                password: {
//                    required: function (element) {
//                        if ($("#admin_id").val() == '') {
//                            return true;
//                        } else {
//                            return false;
//                        }
//                    },
                    required: true,
                    mypassword: true
                },
                admin_status: {"required": true}
            },
            messages: {
                admin_name: {"required": "Enter Admin Name"},
                admin_type: {"required": "Select Admin Type"},
                password: {
                    required: "Please provide a password",
                    mypassword: "Password  must contain atleast one capital letter and one number"
                },
                admin_status: {"required": "Select Admin Status"}
            }
        });
        $.validator.addMethod('mypassword', function (value, element) {
            return this.optional(element) || (value.match(/[A-Z]/) && value.match(/[0-9]/));
        });
    });

    $(document).ready(function () {
        $('select[name=admin_type]').change(function () {
            if ($(this).val() === 'Site Admin' || $(this).val() === 'Report Admin' || $(this).val() === 'Dashboard Admin')
            {
                $(".site_dropdown_div").show();
            } else {
                $(".site_dropdown_div").hide();
            }
        });
    });

    $(document).ready(function () {
        if ($('select[name=admin_type]').val() === 'Site Admin' || $('select[name=admin_type]').val() === 'Report Admin' || $('select[name=admin_type]').val() === 'Dashboard Admin')
        {
            $(".site_dropdown_div").show();
        } else {
            $(".site_dropdown_div").hide();
        }
    });
</script>
