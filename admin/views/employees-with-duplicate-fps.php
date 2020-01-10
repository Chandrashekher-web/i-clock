<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <div class="page-title-right">
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
                    $attributes = array('id' => 'employee-form', 'class' => 'form-horizontal');
                    echo form_open_multipart($form_action, $attributes);
                ?>   
                <input type="hidden" name="employee_id" id="employee_id" value="<?php echo empty($employee_id) ? NULL : $employee_id; ?>">


<!--                <div class="form-group row">                        
                    <label class="col-md-3 col-form-label" for="name">Site Name</label>
                    <div class="col-md-9">
                        <?php
                            echo form_dropdown('arr_sites', empty($arr_sites) ? NULL : ($arr_sites), empty($site_id) ? NULL : $site_id, 'class="form-control  input-block-level" autofocus="autofocus" id="site_id"');
                        ?>
                    </div>   
                </div>-->
                
                <div class="col-md-12"  id="item-container" style="display:none;">
                    <div class="form-group row"> 
                        <div class="col-md-12" id="netprofit">

                        </div>
                    </div>
                </div>

                <?php echo form_close(); ?>
            </div> <!-- end card-body -->
        </div> <!-- end card-->
    </div><!-- end col -->
</div>
<!-- end row -->
<script>
    $(document).ready(function ()
    {       
        get_employee_details();
//        $('#site_id').on('change', function () {
//            get_employee_details();
//        });

    });

    function get_employee_details()
    {
        var site_id = $('#site_id').val();
        blockUI();
        $.ajax({
            type: "POST",
            dataType: "html",
            data: {site_id: site_id},
            url: "<?php echo base_url(); ?>admin/employee/get_employee_with_duplicate_fps",
            }).done(function (output) {
                $('#item-container').show();
                $('#netprofit').html(output);

            }).always(function () {
                $.unblockUI();
            });
    }
</script>





