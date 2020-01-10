<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
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
                    if (!empty($message))
                    {
                        ?>
                        <div class="alert alert-success">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                            <?php echo $message; ?>
                        </div>
                        <?php
                    }

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

                    $attributes = array('id' => 'config-form', 'class' => 'form-horizontal');
                    echo form_open_multipart($form_action, $attributes);
                ?>   
                <input type="hidden" name="config_id" id="config_id" value="<?php echo empty($config_id) ? NULL : $config_id; ?>">
                <div class="form-group row">                        
                    <label class="col-md-3 col-form-label" for="timed_access_duration_time">Timed Access Duration (in Hours.)<font color="red">*</font></label>
                    <div class="col-md-9">
                        <?php
//                            $data = array(
//                                'name' => 'timed_access_duration_time',
//                                'id' => 'timed_access_duration_time',
//                                'value' => set_value('timed_access_duration_time', empty($timed_access_duration_time) ? NULL : $timed_access_duration_time),
//                                'class' => 'form-control',
//                                'placeholder' => 'Timed Access Duration (in Hours.)',
//                                'autofocus' => 'autofocus'
//                            );
//                            echo form_input($data);
                            
                              echo form_dropdown('timed_access_duration_time', empty($timed_access_duration_time_array) ? Null : ($timed_access_duration_time_array), set_value('timed_access_duration_time', empty($timed_access_duration_time) ? NULL : $timed_access_duration_time), 'class="form-control"');
                      
                        ?>
                    </div>
                </div>

                <div class="form-group row">                        
                    <label class="col-md-3 col-form-label" for="one_time_access_duration_time">One Time Access Duration (in Hours.)<font color="red">*</font></label>
                    <div class="col-md-9">
                        <?php
                            $data = array(
                                'name' => 'one_time_access_duration_time',
                                'id' => 'one_time_access_duration_time',
                                'value' => set_value('one_time_access_duration_time', empty($one_time_access_duration_time) ? NULL : $one_time_access_duration_time),
                                'class' => 'form-control',
                                'placeholder' => 'One Time Access Duration (in Hours.)',
                                'autofocus' => 'autofocus'
                            );
                            echo form_input($data);
                        
                        ?>
                    </div>
                </div>


                 <?php
                $check_admin_type = check_admin_type();
                if ($check_admin_type == 'Super Admin' || $check_admin_type == 'Site Admin')
                {
                    ?>
                <div class="form-group row">
                    <div class="col-md-9 offset-sm-3">
                        <button type="submit" name="save"  id="button" class="btn btn-primary mr-2"> <i class="fa fa-save" aria-hidden="true"></i> Save</button>
                    </div>
                </div>
                <?php } echo form_close(); ?>
            </div> <!-- end card-body -->
        </div> <!-- end card-->
    </div><!-- end col -->
</div>


<script>
    $(document).ready(function () {
        $("#config-form").validate({
            rules: {
                timed_access_duration_time: {"required": true},
                one_time_access_duration_time: {"required": true},

            },
            messages: {
                timed_access_duration_time: {"required": "Timed Access Duration (in Hours.)"},
                one_time_access_duration_time: {"required": "One Time Access Duration (in Hours.)"},

            }
        });
    });
</script>