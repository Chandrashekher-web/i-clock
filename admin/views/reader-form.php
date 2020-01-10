<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <div class="page-title-right">
                <a href="<?php echo base_url('admin/reader/list_reader') ?>" class="btn btn-sm btn-primary">
                    <i class="fa fa-list"></i>&nbsp;Back
                </a>
            </div>
            <h4 class="page-title"><strong><?php echo empty($form_caption) ? "" : $form_caption; ?></strong></h4>
        </div>
    </div>
</div>     
<!-- end page title --> 
<?php $is_super_admin = is_super_admin(); ?>

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
                <div class="alert alert-success" style="display: none;" id="sync-success">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    Data synced successfully!
                </div>

                <div class="alert alert-success" style="display: none;" id="cmd-success">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    Command sent successfully!
                </div>

                <?php
                    $attributes = array('id' => 'reader-form', 'class' => 'form-horizontal');
                    echo form_open_multipart($form_action, $attributes);
                ?>   
                <input type="hidden" name="reader_id" id="reader_id" value="<?php echo empty($reader_id) ? NULL : $reader_id; ?>">


                <div class="form-group row" <?php echo empty($reader_id) || $is_super_admin ? NULL : "hidden"; ?> >                        
                    <label class="col-md-3 col-form-label" for="sn">Serial No.<font color="red">*</font></label>
                    <div class="col-md-9">
                        <?php
                            $data = array(
                                'name' => 'sn',
                                'id' => 'sn',
                                'value' => set_value('sn', empty($sn) ? NULL : $sn),
                                'class' => 'form-control',
                                'placeholder' => 'Enter Serial No.',
                                'autofocus' => 'autofocus',
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
                                'placeholder' => 'Enter Name',
                            );
                            echo form_input($data);
                        ?>
                    </div>
                </div>

                <div class="form-group row">                        
                    <label class="col-md-3 col-form-label" for="department_id">Department</label>
                    <div class="col-md-9">
                        <?php
                            echo form_dropdown('department_id', empty($arr_department) ? Null : ($arr_department), set_value('department_id',empty($department_id) ? NULL : $department_id), 'class="form-control"');
                        ?>
                    </div>
                </div>
                <?php
                    if ($is_super_admin)
                    {
                        ?>
                        <div class="form-group row">                        
                            <label class="col-md-3 col-form-label" for="profile_id">Command Capacity Profile</label>
                            <div class="col-md-9">
                                <?php
                                echo form_dropdown('profile_id', empty($arr_profile) ? Null : ($arr_profile), set_value('profile_id', empty($profile_id) ? NULL : $profile_id), 'class="form-control"');
                                ?>
                            </div>
                        </div>

                        <div class="form-group row">                        
                            <label class="col-md-3 col-form-label" for="transmission_interval">Transmission Interval<font color="red">*</font></label>
                            <div class="col-md-9">
                                <?php
                                $data = array(
                                    'type' => 'number',
                                    'name' => 'transmission_interval',
                                    'id' => 'transmission_interval',
                                    'value' => set_value('transmission_interval', empty($transmission_interval) ? NULL : $transmission_interval),
                                    'class' => 'form-control',
                                    'min' => 1,
                                    'max' => 720,
                                );
                                echo form_input($data);
                                ?>
                            </div>
                        </div>

                        <div class="form-group row">                        
                            <label class="col-md-3 col-form-label" for="delay">Delay<font color="red">*</font></label>
                            <div class="col-md-9">
                                <?php
                                $data = array(
                                    'type' => 'number',
                                    'name' => 'delay',
                                    'id' => 'delay',
                                    'value' => set_value('delay', empty($delay) ? NULL : $delay),
                                    'class' => 'form-control',
                                    'min' => 1,
                                    'max' => 300,
                                );
                                echo form_input($data);
                                ?>
                            </div>
                        </div>

                        <div class="form-group row">                        
                            <label class="col-md-3 col-form-label" for="password_exempted">Password Exempted<font color="red">*</font></label>
                            <div class="col-md-9">
                                <?php
                                echo form_dropdown('password_exempted', empty($arr_password_exempted) ? Null : ($arr_password_exempted),set_value('password_exempted', empty($password_exempted) ? NULL : $password_exempted), 'class="form-control"');
                                ?>
                            </div>
                        </div>                       

                        <div class="form-group row">                        
                            <label class="col-md-3 col-form-label" for="fpsource">FP Source<font color="red">*</font></label>
                            <div class="col-md-9">
                                <?php
                                echo form_dropdown('fpsource', empty($arr_fpsource) ? Null : ($arr_fpsource), set_value('fpsource',empty($fpsource) ? NULL : $fpsource), 'class="form-control"');
                                ?>
                            </div>
                        </div>
                
                        <div class="form-group row">                        
                            <label class="col-md-3 col-form-label" for="sync_att">Sync Attendance<font color="red">*</font></label>
                            <div class="col-md-9">
                                <?php
                                echo form_dropdown('sync_att', empty($arr_sync_att) ? Null : ($arr_sync_att), set_value('sync_att',empty($sync_att) ? NULL : $sync_att), 'class="form-control"');
                                ?>
                            </div>
                        </div>

                        <?php
                        if (!empty($reader_id))
                        {
                            ?>
                            <div class="form-group row">
                                <div class="col-md-4">
                                    <h5>Data Synchronisation</h5>
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="col-12"><input type="checkbox" name="sync_user" id="sync_user" class="sync_user"> <label for="sync_user">Sync User Info </label></div>
                                            <div class="col-12"><input type="checkbox" name="sync_finger_print" id="sync_finger_print"  class="sync_finger_print"> <label for="sync_finger_print">Sync Finger Print Info</label></div>
                                            <div class="col-12"><input type="checkbox" name="sync_facial" id="sync_facial"  class="sync_facial"> <label for="sync_facial"> Sync facial Info </label></div>
                                            <div class="col-12"><input type="checkbox" name="sync_records" id="sync_records"  class="sync_records"> <label for="sync_records"> Sync T/A Records </label></div>
                                            <div class="col-12"><input type="checkbox" name="update_user" id="update_user"  class="update_user"> <label for="update_user"> Update User Info </label></div>
                                            <button type="button" id="sync_button"  id="button" class="btn btn-primary mr-2"> Go</button> 
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-8">
                                    <h5>Reader Control</h5>

                                    <input type="radio" name="reader_command" value="reboot" id="reboot"><label for="reboot">&nbsp;&nbsp;Reboot</label><br />
                                    <input type="radio" name="reader_command" value="clear_data" id="clear_data"><label for="clear_data">&nbsp;&nbsp;Clear All Data</label><br />
                                    <div class="form-group row">
                                        <div class="col-md-3">
                                            <input type="radio" name="reader_command" value="change_ip" id="change_ip"><label for="change_ip">&nbsp;&nbsp;Change Server IP</label> 
                                        </div>
                                        <div class="col-md-5">
                                            <input type="text" class="form-control" style="width: 53%;height: calc(2.25rem + -6px);" id="ipv4" name="ipv4" placeholder="xxx.xxx.xxx.xxx"/>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-md-3">
                                            <input type="radio" name="reader_command" value="change_password" id="change_password"><label for="change_password">&nbsp;&nbsp;Change Password</label>
                                        </div>
                                        <div class="col-md-5">
                                            <input type="text" class="form-control" style="width: 53%;height: calc(2.25rem + -6px);" id="pswd" name="pswd"/>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-md-3">
                                            <input type="radio" name="reader_command" value="add_work_code" id="add_work_code"><label for="add_work_code">&nbsp;&nbsp;Add Work Code</label>
                                        </div>
                                        <div class="col-md-5">
                                            <input type="text" class="form-control" style="width: 55px;height: calc(2.25rem + -6px);display: inline;" id="add_work_code_id" name="add_work_code_id"/> <input type="text" class="form-control" style="display: inline;width: 53%;height: calc(2.25rem + -6px);" id="work_code_name" name="work_code_name"/>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-md-3">
                                            <input type="radio" name="reader_command" value="delete_work_code" id="delete_work_code"><label for="delete_work_code">&nbsp;&nbsp;Delete Work Code</label>
                                        </div>
                                        <div class="col-md-5">
                                            <input type="text" class="form-control" style="width: 55px;height: calc(2.25rem + -6px);" id="delete_work_code_id" name="delete_work_code_id"/>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-md-3">
                                            <input type="radio" name="reader_command" value="send_command" id="send_command"><label for="send_command">&nbsp;&nbsp;Send Command</label>
                                        </div>
                                        <div class="col-md-5">
                                            <input type="text" class="form-control" style="width: 53%;height: calc(2.25rem + -6px);" id="cmd" name="cmd"/>
                                        </div>
                                    </div>
                                    <button type="button" id="cmd_button"  id="button" class="btn btn-primary mr-2"> Go</button>
                                </div>
                            </div>

                            <?php
                        }
                        ?>
                    <?php } ?>
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
        //input mask bundle ip address
        var ipv4_address = $('#ipv4');
        ipv4_address.inputmask({
            alias: "ip",
            greedy: false //The initial mask shown will be "" instead of "-____".
        });


        $("#reader-form").validate({
            rules: {
                sn: {"required": true},
                name: {"required": true},
                transmission_interval: {"required": true},
                delay: {"required": true},
            },
            messages: {
                sn: {"required": "Enter Reader Serial No."},
                name: {"required": "Enter Reader Name"},
                transmission_interval: {"required": "Enter Transmission Interval Time"},
                delay: {"required": "Enter Delay Time"},
            }
        });

        $(document).on('click', '#cmd_button', function (e) {
            var reader_id = $('#reader_id').val();
            var reader_command = $("input:radio[name=reader_command]:checked").val();
            var ipAddress = $('#ipv4').val();
            var password = $('#pswd').val();
            var add_work_code = $('#add_work_code_id').val();
            var delete_work_code = $('#delete_work_code_id').val();
            var cmd = $('#cmd').val();
            var work_code_name = $('#work_code_name').val();
            $.ajax({
                type: "POST",
                dataType: "json",
                data: {reader_id: reader_id, reader_command: reader_command, ipAddress: ipAddress, password: password, add_work_code: add_work_code, delete_work_code: delete_work_code, cmd: cmd, work_code_name: work_code_name},
                url: "<?php echo base_url(); ?>admin/reader/reader_control",
                success: function (output) {
                    $('#cmd-success').show();
                    //                    location.reload();
                }
            });

        });

        $(document).on('click', '#sync_button', function (e) {
            var reader_id = $('#reader_id').val();
            var sync_user = $("input:checkbox[name=sync_user]:checked").val();
            var sync_finger_print = $("input:checkbox[name=sync_finger_print]:checked").val();
            var sync_facial = $("input:checkbox[name=sync_facial]:checked").val();
            var sync_records = $("input:checkbox[name=sync_records]:checked").val();
            var update_user = $("input:checkbox[name=update_user]:checked").val();
            $.ajax({
                type: "POST",
                dataType: "json",
                data: {reader_id: reader_id, sync_user: sync_user, sync_finger_print: sync_finger_print, sync_facial: sync_facial, sync_records: sync_records, update_user: update_user},
                url: "<?php echo base_url(); ?>admin/reader/reader_data_sync",
                success: function (output) {
                    $('#sync-success').show();
                    //                    location.reload();
                }
            });

        });

    });
</script>
