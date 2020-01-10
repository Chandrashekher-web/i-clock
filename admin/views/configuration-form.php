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
                    <label class="col-md-3 col-form-label" for="auto_refresh_time">Auto Refresh Time (in sec.)<font color="red">*</font></label>
                    <div class="col-md-9">
                        <?php
                            $data = array(
                                'name' => 'auto_refresh_time',
                                'id' => 'auto_refresh_time',
                                'value' => set_value('auto_refresh_time', empty($auto_refresh_time) ? NULL : $auto_refresh_time),
                                'class' => 'form-control',
                                'placeholder' => 'Auto Refresh Time',
                                'autofocus' => 'autofocus'
                            );
                            echo form_input($data);
                        ?>
                    </div>
                </div>

                <div class="form-group row">                        
                    <label class="col-md-3 col-form-label" for="offline_timeout">Reader Offline Timeout (in min.)<font color="red">*</font></label>
                    <div class="col-md-9">
                        <?php
                            $data = array(
                                'name' => 'offline_timeout',
                                'id' => 'offline_timeout',
                                'value' => set_value('offline_timeout', empty($offline_timeout) ? NULL : $offline_timeout),
                                'class' => 'form-control',
                                'placeholder' => 'Reader Offline Timeout',
                                'autofocus' => 'autofocus'
                            );
                            echo form_input($data);
                        ?>
                    </div>
                </div>

                <div class="form-group row">                        
                    <label class="col-md-3 col-form-label" for="command_max_capacity">Max Capacity of Commands (in KB)<font color="red">*</font></label>
                    <div class="col-md-9">
                        <?php
                            $data = array(
                                'name' => 'command_max_capacity',
                                'id' => 'command_max_capacity',
                                'value' => set_value('command_max_capacity', empty($command_max_capacity) ? NULL : $command_max_capacity),
                                'class' => 'form-control',
                                'placeholder' => 'Max Capacity of Command send to Reader in KB',
                                'autofocus' => 'autofocus'
                            );
                            echo form_input($data);
                        ?>
                    </div>
                </div>
                <div class="form-group row">                        
                    <label class="col-md-3 col-form-label" for="command_max_number">Max Number of Commands<font color="red">*</font></label>
                    <div class="col-md-9">
                        <?php
                            $data = array(
                                'name' => 'command_max_number',
                                'id' => 'command_max_number',
                                'value' => set_value('command_max_number', empty($command_max_number) ? NULL : $command_max_number),
                                'class' => 'form-control',
                                'placeholder' => 'Max Number of Command send to Reader',
                                'autofocus' => 'autofocus'
                            );
                            echo form_input($data);
                        ?>
                    </div>
                </div>
                <div class="form-group row">                        
                    <label class="col-md-3 col-form-label" for="sms_provider">SMS Provider<font color="red">*</font></label>
                    <div class="col-md-9">
                        <?php
                            echo form_dropdown('sms_provider', empty($sms_provider) ? NULL : $sms_provider, empty($sms_provider_id) ? NULL : $sms_provider_id, 'id="sms_provider"  class="form-control mb-1 select2"');
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


<script>
    $(document).ready(function () {
        $("#config-form").validate({
            rules: {
                auto_refresh_time: {"required": true},
                offline_timeout: {"required": true},
                command_max_capacity: {"required": true},
                command_max_number: {"required": true},
            },
            messages: {
                auto_refresh_time: {"required": "Enter Auto Refresh Time (in sec.)"},
                offline_timeout: {"required": "Enter Reader Offline Timeout (in min.)"},
                command_max_capacity: {"required": "Max Capacity of Commands(in KB)"},
                command_max_number: {"required": "Enter Max Number of Commands"},
            }
        });
    });
</script>