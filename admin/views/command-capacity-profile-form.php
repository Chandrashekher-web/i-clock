<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <div class="page-title-right">
                <a href="<?php echo base_url('admin/command_capacity_profile/list_command_capacity_profile') ?>" class="btn btn-sm btn-primary">
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
                    $attributes = array('id' => 'reader-form', 'class' => 'form-horizontal');
                    echo form_open_multipart($form_action, $attributes);
                ?>   
                <input type="hidden" name="profile_id" id="profile_id" value="<?php echo empty($profile_id) ? NULL : $profile_id; ?>">
                <div class="form-group row">                        
                    <label class="col-md-3 col-form-label" for="profile_name">Profile Name<font color="red">*</font></label>
                    <div class="col-md-9">
                        <?php
                            $data = array(
                                'name' => 'profile_name',
                                'id' => 'profile_name',
                                'value' => set_value('name', empty($profile_name) ? NULL : $profile_name),
                                'class' => 'form-control',
                                'placeholder' => 'Enter Profile Name',
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
                                'placeholder' => 'Max Capacity of Command send to Reader in KB'
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
        $( "#command" ).focus();
        $("#reader-form").validate({
            rules: {
                profile_name: {"required": true},
                command_max_capacity: {"required": true},
                command_max_number: {"required": true},
            },
            messages: {
                profile_name: {"required": "Enter Profile Name"},
                command_max_capacity: {"required": "Max Capacity of Commands(in KB)"},
                command_max_number: {"required": "Enter Max Number of Commands"},
            }
        });
    });
</script>
