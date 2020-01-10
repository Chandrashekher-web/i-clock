<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <div class="page-title-right">
<!--                <a href="<?php echo base_url('admin/employee/list_employee') ?>" class="btn btn-sm btn-primary">
                    <i class="fa fa-list"></i>&nbsp;Back
                </a>-->
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
                    $is_super_admin = is_super_admin();
                ?>   
                <input type="hidden" name="employee_id" id="employee_id" value="<?php echo empty($employee_id) ? NULL : $employee_id; ?>">

                <?php
                if ($is_super_admin)
                {
                ?>
                <div class="form-group row">                        
                    <label class="col-md-3 col-form-label" for="name">Site Name</label>
                    <div class="col-md-9">
                        <?php
                            echo form_dropdown('arr_sites', empty($arr_sites) ? NULL : ($arr_sites), empty($site_id) ? NULL : $site_id, 'class="form-control input-block-level" autofocus="autofocus" id="site_id"');
                        ?>
                    </div>   
                </div>
                <?php }else{ ?>
                <div class="form-group row">                        
                    <label class="col-md-3 col-form-label" for="name">Site Name</label>
                    <div class="col-md-9">
                        <?php
                            echo form_dropdown('arr_sites', empty($arr_sites) ? NULL : ($arr_sites), empty($site_id) ? NULL : $site_id, 'class="form-control input-block-level" autofocus="autofocus" id="site_id" disabled');
                        ?>
                    </div>   
                </div>
                <?php } ?>
               
                
                <div class="form-group row" id="reader_dropdown" style="display: none;">                        
                    <label class="col-md-3 col-form-label" for="name">Reader</label>
                    <div class="col-md-9">
                        <?php
                            echo form_dropdown('arr_reader', empty($arr_reader) ? NULL : ($arr_reader), empty($reader_id) ? NULL : $reader_id, 'class="form-control  input-block-level" autofocus="autofocus" id="reader_id"');
                        ?>
                    </div>   
                </div>
		<?php
                if ($key != 'remote_reader_unlock') {
                    ?>
                 <div class="form-group row">                        
                    <label class="col-md-3 col-form-label" for="name">Show</label>
                    <div class="col-md-9">
                        <?php
                            echo form_dropdown('employee_filter', empty($employee_filter) ? NULL : ($employee_filter), empty($site_id) ? NULL : $site_id, 'class="form-control input-block-level" autofocus="autofocus" id="employee_filter"');
                        ?>
                    </div>   
                </div>
		<?php } ?>
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
        var key = '<?php echo $key; ?>';
        if(key == 'per_reader')
        {
            get_readers();
        }
        else
        {
            get_employee_details();
        }
        if(key == 'per_reader')
        {
            $('#reader_dropdown').show();
        }
        if(key == 'offline_readers')
        {
            get_employee_details();
        }
        $('#site_id').on('change', function () {
//            alert(key);
            if(key == 'per_reader')
            {
                get_readers();
            }
            else
            {
                get_employee_details();
            }

        });
        
        $('#reader_id').on('change', function () {
            get_employee_details();
        });
        
         $('#employee_filter').on('change', function () {
                get_employee_details();
        });

    });

    function get_employee_details()
    {
        var site_id = $('#site_id').val();
        var reader_id = $('#reader_id').val();
        var filter_type = $('#employee_filter').val();
        var key = '<?php echo $key; ?>';
        $.ajax({
            type: "POST",
            dataType: "html",
            data: {site_id: site_id, key: key, reader_id : reader_id, employee_filter: filter_type},
            url: "<?php echo base_url(); ?>admin/employee/get_employee_details",
            success: function (output) {
                $('#item-container').show();
                $('#netprofit').html(output);
            }
        });
    }
    
    function get_readers()
    {
        var site_id = $('#site_id').val();  
        if (site_id != "")
        {
//          blockUI();
            $.ajax({
                type: "POST",
                dataType: "html",
                url: "<?php echo base_url(); ?>admin/employee/get_readers",
                data: {site_id: site_id},
            }).done(function (data) {
//                alert(data);return false;
                if(data == 'no data')
                {
                    var reader_html = "<option value=''>-- Select Reader --</option>";
                    $('#reader_id').html(reader_html);

                }
                else
                {
                    $("#reader_id").html(data);
                }
                }).always(function () {
                    $.unblockUI();
            });
        }
    }
</script>





