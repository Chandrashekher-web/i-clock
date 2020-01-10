<!-- start page title -->

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.7.14/css/bootstrap-datetimepicker.min.css">
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <div class="page-title-right">
                <a href="<?php echo base_url('admin/sub_category/list_sub_category') ?>" class="btn btn-sm btn-primary">
                    <i class="fa fa-list"></i>&nbsp;Back
                </a>
            </div>
            <h4 class="page-title"><strong><?php echo empty($form_caption) ? "" : $form_caption; ?></strong></h4>
        </div>
    </div>
</div>     
<!-- end page title --> 


<div class="row" style="margin-top: 50px;">
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
                    $attributes = array('id' => 'category-form', 'class' => 'form-horizontal');
                    echo form_open_multipart($form_action, $attributes);
                ?>   
                <input type="hidden" name="sub_category_id" id="sub_category_id" value="<?php echo empty($sub_category_id) ? NULL : $sub_category_id; ?>">

                <div class="form-group row">                        
                    <label class="col-md-3 col-form-label" for="category_id">Category</label>
                    <div class="col-md-9">
                        <?php
                            echo form_dropdown('category_id', empty($arr_category) ? Null : ($arr_category), set_value('category_id', empty($category_id) ? NULL : $category_id), 'class="form-control"');
                        ?>
                    </div>
                </div>

                <div class="form-group row">                        
                    <label class="col-md-3 col-form-label" for="sub_category_name">Sub Category<font color="red">*</font></label>
                    <div class="col-md-9">
                        <?php
                            $data = array(
                                'name' => 'sub_category_name',
                                'id' => 'sub_category_name',
                                'value' => set_value('sub_category_name', empty($sub_category_name) ? NULL : $sub_category_name),
                                'class' => 'form-control',
                                'placeholder' => 'Enter Sub Category',
                                ''
                            );
                            echo form_input($data);
                        ?>
                    </div>
                </div>

                <div class="form-group row">                        
                    <label class="col-md-3 col-form-label" for="sub_category_name">Timed Access<font color="red">*</font></label>
                    <div class="col-md-9">
                        <div class="custom-control custom-radio pull-left">

                            <input type="radio"  name="timed_access" <?php
                                if (!empty($timed_access) && $timed_access == 'times duration access')
                                {
                                    echo 'checked="checked"';
                                }
                            ?>  value="times duration access" id="times_duration_access" class="custom-control-input timed_access">
                            <label class="custom-control-label" for="times_duration_access">Timed Duration Access</label>
                        </div>
                        <div class="custom-control custom-radio pull-left" style="margin-left: 10px;">
                            <input type="radio"  name="timed_access" <?php
                                if (!empty($timed_access) && $timed_access == 'normal timed access')
                                {
                                    echo 'checked="checked"';
                                }
                            ?>  value="normal timed access" id="normal_timed_access" class="custom-control-input timed_access">
                            <label class="custom-control-label" for="normal_timed_access">Normal Timed Access</label>
                        </div>
                    </div>
                </div>






                <div class="form-group row" id="times_duration_access_div">                        
                    <label class="col-md-3 col-form-label" for="sub_category_name">Timed Duration Access</label>
                    <div class="col-md-3">
                        <?php
                            $data = array(
                                'name' => 'time_duration_access_start_date',
                                'id' => 'time_duration_access_start_date',
                                'value' => set_value('time_duration_access_start_date', empty($time_duration_access_start_date) ? NULL : $time_duration_access_start_date),
                                'class' => 'form-control mydate',
                                'placeholder' => 'Enter Start Date',
                                ''
                            );
                            echo form_input($data);
                        ?>
                    </div>
                    <div class="col-md-3">
                        <?php
                            $data = array(
                                'name' => 'time_duration_access_end_date',
                                'id' => 'time_duration_access_end_date',
                                'value' => set_value('time_duration_access_end_date', empty($time_duration_access_end_date) ? NULL : $time_duration_access_end_date),
                                'class' => 'form-control mydate',
                                'placeholder' => 'Enter End Date',
                                ''
                            );
                            echo form_input($data);
                        ?>
                    </div>
                    <div class="col-md-3">
                        <?php
                            $data = array(
                                'name' => 'time_duration_access_end_time',
                                'id' => 'time_duration_access_end_time',
                                'value' => set_value('time_duration_access_end_time', empty($time_duration_access_end_time) ? NULL : $time_duration_access_end_time),
                                'class' => 'form-control timepicker',
                                'placeholder' => 'Enter End Time',
                                ''
                            );
                            echo form_input($data);
                        ?>
                    </div>
                </div>

                <div class="form-group row" id="normal_timed_access_div" style="display: none" >                        
                    <label class="col-md-3 col-form-label" for="sub_category_name">Normal Timed Access</label>
                    <div class="col-md-3">
                        <?php
                            $data = array(
                                'name' => 'normal_timed_access_start_date',
                                'id' => 'normal_timed_access_start_date',
                                'value' => set_value('normal_timed_access_start_date', empty($normal_timed_access_start_date) ? NULL : $normal_timed_access_start_date),
                                'class' => 'form-control mydate',
                                'placeholder' => 'Enter Start Date',
                                ''
                            );
                            echo form_input($data);
                        ?>
                    </div>
                    <div class="col-md-3">
                        <?php
                            $data = array(
                                'name' => 'normal_timed_access_hours_duration',
                                'id' => 'normal_timed_access_hours_duration',
                                'value' => set_value('normal_timed_access_hours_duration', empty($normal_timed_access_hours_duration) ? NULL : $normal_timed_access_hours_duration),
                                'class' => 'form-control',
                                'placeholder' => 'Enter Hours Duration',
                                'maxlength' => '2',
                                'oninput' => "this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');",
                            );
                            echo form_input($data);
                        ?>
                    </div>
                    <div class="col-md-3">
                        <?php
                            $data = array(
                                'name' => 'normal_timed_access_min_duration',
                                'id' => 'normal_timed_access_min_duration',
                                'value' => set_value('normal_timed_access_min_duration', empty($normal_timed_access_min_duration) ? NULL : $normal_timed_access_min_duration),
                                'class' => 'form-control',
                                'placeholder' => 'Enter Min. Duration',
                                'maxlength' => '2',
                                'oninput' => "this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');",
                            );
                            echo form_input($data);
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


<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.7.14/js/bootstrap-datetimepicker.min.js"></script>
<script type="text/javascript">
    Jquery = $.noConflict();
</script>
<script>
    $(document).ready(function () {
        Jquery('.timepicker').datetimepicker({
            format: 'LT'
        });
        Jquery('.mydate').datetimepicker({
            format: 'DD/MM/YYYY',
        });

        $('.timed_access').on('click', function () {            
            if ($(this).val() == 'times duration access')
            {
                $('#times_duration_access_div').show();
                $('#normal_timed_access_div').hide();
            } else if ($(this).val() == 'normal timed access')
            {
                $('#times_duration_access_div').hide();
                $('#normal_timed_access_div').show();
            } else
            {
                $('#times_duration_access_div').show();
                $('#normal_timed_access_div').hide();
            }
        });

        $("#category_id").focus();

        $("#category-form").validate({
            rules: {
                sub_category_name: {"required": true},
            },
            messages: {
                sub_category_name: {"required": "Enter Sub Category."},
            }
        });
    });
</script>
