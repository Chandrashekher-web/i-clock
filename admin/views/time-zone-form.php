<!-- start page title -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.7.14/css/bootstrap-datetimepicker.min.css">
<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <div class="page-title-right">
                <a href="<?php echo base_url('admin/time_zone') ?>" class="btn btn-sm btn-primary">
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
                 
                    $attributes = array('id' => 'reader-access-groups-form', 'class' => 'form-horizontal');
                    echo form_open_multipart($form_action, $attributes);
                ?>   
                <input type="hidden" name="time_zone_id" id="time_zone_id" value="<?php echo empty($time_zone_id) ? NULL : $time_zone_id; ?>">
                <input type="hidden" name="site_id" id="site_id" value="<?php echo empty($site_id) ? NULL : $site_id; ?>">

                <div class="form-group row">                        
                    <label class="col-md-3 col-form-label" for="code_id">Code ID<font color="red">*</font></label>
                    <div class="col-md-9">
                        <?php
                            $data = array(
                                'name' => 'code_id',
                                'id' => 'code_id',
                                'value' => set_value('code_id', empty($code_id) ? NULL : $code_id),
                                'class' => 'form-control',
                                'placeholder' => 'Enter Code ID',
                                'autofocus' => 'autofocus',
                                'oninput' => "this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');",
                            );
                            echo form_input($data);
                        ?>
                    </div>
                </div>

                <div class="form-group row">                        
                    <label class="col-md-3 col-form-label" for="name">Name<font color="red">*</font></label>
                    <div class="col-md-9">
                        <?php
                            $data = array(
                                'name' => 'name',
                                'id' => 'name',
                                'value' => set_value('name', empty($name) ? NULL : $name),
                                'class' => 'form-control',
                                'placeholder' => 'Enter Zone Name',
                            );
                            echo form_input($data);
                        ?>
                    </div>
                </div>




                <div class="form-group row">                        
                    <label class="col-md-3 col-form-label" for="exit_reader">Weekly Schedule</label>
                    <div class="col-md-9">
                        <div class="row">
                            <div class="col-md-4 col-form-label"><label>Weekday</label></div>
                            <div class="col-md-4 col-form-label"><label>Start</label></div>
                            <div class="col-md-4 col-form-label"><label>End</label></div>
                        </div>



                        <?php
                            foreach ($arr_week_day as $week_day)
                            {
                                ?>
                                <div class="row">

                                    <div class="col-md-4 col-form-label" ><label><?php echo $week_day; ?></label></div>
                                    <div class="col-md-4 col-form-label" >
                                        <?php
                                        $data = array(
                                            'name' => $week_day . '_start_time',
                                            'id' => $week_day . '_start_time',
                                            'class' => 'form-control timepicker',
                                            'value' => set_value($week_day . '_start_time', empty($weeks_arr[$week_day . '_start_time']) ? NULL : $weeks_arr[$week_day . '_start_time']),
                                            'autofocus' => 'autofocus',
                                            'oninput' => "this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');",
                                        );
                                        echo form_input($data);
                                        ?>
                                    </div>
                                    <div class="col-md-4 col-form-label" >
                                        <?php
                                        $data = array(
                                            'name' => $week_day . '_end_time',
                                            'id' => $week_day . '_end_time',
                                            'class' => 'form-control timepicker',
                                            'value' => set_value($week_day . '_end_time', empty($weeks_arr[$week_day . '_end_time']) ? NULL : $weeks_arr[$week_day . '_end_time']),
                                            'autofocus' => 'autofocus',
                                            'oninput' => "this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');",
                                        );
                                        echo form_input($data);
                                        ?>
                                    </div>
                                </div>
                            <?php } ?>
                    </div>
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

        $("#reader-access-groups-form").validate({
            rules: {
                code_id: {"required": true},
                name: {"required": true},
            },
            messages: {
                code_id: {"required": "Enter Code Id"},
                name: {"required": "Enter Time Zone Name"},
            }
        });
    });
</script>