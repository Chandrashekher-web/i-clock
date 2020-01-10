<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <div class="page-title-right">
                <a href="<?php echo base_url('admin/reader_access_groups/list_reader_access_groups') ?>" class="btn btn-sm btn-primary">
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
                    $attributes = array('id' => 'reader-access-groups-form', 'class' => 'form-horizontal');
                    echo form_open_multipart($form_action, $attributes);
                ?>   
                <input type="hidden" name="reader_access_groups_id" id="reader_access_groups_id" value="<?php echo empty($reader_access_groups_id) ? NULL : $reader_access_groups_id; ?>">
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
                    <label class="col-md-3 col-form-label" for="description">Description<font color="red">*</font></label>
                    <div class="col-md-9">
                        <?php
                            $data = array(
                                'name' => 'description',
                                'id' => 'description',
                                'value' => set_value('description', empty($description) ? NULL : $description),
                                'class' => 'form-control',
                                'placeholder' => 'Enter Description',
                                'cols' => '40',
                                'rows' => '3'
                            );
                            echo form_textarea($data);
                        ?>
                    </div>
                </div>
                <div class="form-group row">                        
                    <label class="col-md-3 col-form-label" for="exit_reader">Time Zone<font color="red">*</font></label>
                    <div class="col-md-9">
                        <?php
                            echo form_multiselect('time_zone[]', empty($time_zone_data) ? Null : ($time_zone_data), empty($time_zone) ? NULL : $time_zone, 'class="time_zone  form-control  input-block-level" id="time_zone" data-rule-required="true" multiple="multiple"');
                        ?>
                    </div>
                </div>
                <div class="form-group row">                        
                    <label class="col-md-3 col-form-label" for="group_verify_type">Group Verify Type<font color="red">*</font></label>
                    <div class="col-md-9">
                        <?php
                            echo form_dropdown('group_verify_type', empty($arr_group_verify_type) ? Null : ($arr_group_verify_type), empty($group_verify_type) ? NULL : $group_verify_type, 'class="form-control  input-block-level" ');
                        ?>
                    </div>
                </div>
                <?php
                    if (!empty($reader_data))
                    {
                        foreach ($reader_data as $key => $value)
                        {
                            ?>
                            <input type="hidden" name="default_reader[]" id="default_reader" class="default_reader" value="<?php echo $key; ?>">
                            <?php
                        }
                    }
                ?>
                <div class="form-group row">                        
                    <label class="col-md-3 col-form-label" for="in_reader">In Reader<font color="red">*</font></label>
                    <div class="col-md-9">
                        <?php
                            echo form_multiselect('in_reader[]', empty($reader_data) ? Null : ($reader_data), empty($in_reader) ? NULL : $in_reader, 'class="reader form-control  input-block-level " id="in_reader" data-rule-required="true" multiple="multiple"');
                        ?>
                    </div>
                </div>

                <div class="form-group row">                        
                    <label class="col-md-3 col-form-label" for="out_reader">Out Reader<font color="red">*</font></label>
                    <div class="col-md-9">
                        <?php
                            echo form_multiselect('out_reader[]', empty($reader_data) ? Null : ($reader_data), empty($out_reader) ? NULL : $out_reader, 'class="reader form-control  input-block-level " id="out_reader" data-rule-required="true" multiple="multiple"');
                        ?>
                    </div>
                </div>

                <div class="form-group row">                        
                    <label class="col-md-3 col-form-label" for="exit_reader">Exit Reader<font color="red">*</font></label>
                    <div class="col-md-9">
                        <?php
                            echo form_multiselect('exit_reader[]', empty($reader_data) ? Null : ($reader_data), empty($exit_reader) ? NULL : $exit_reader, 'class="reader form-control  input-block-level" id="exit_reader" data-rule-required="true" multiple="multiple"');
                        ?>
                    </div>
                </div>
                <div class="form-group row">                        
                    <label class="col-md-3 col-form-label" for="out_reader">Antipass<font color="red">*</font></label>
                    <div class="col-md-9 custom-control custom-checkbox">

                        <?php
                            if (isset($is_antipass) && $is_antipass == 'Yes')
                            {
                                $checked = 'checked';
                            }
                            else
                            {
                                $checked = false;
                            }
                            $data = array(
                                'name' => 'is_antipass',
                                'id' => 'is_antipass',
                                'value' => 'Yes',
                                'class' => 'custom-control-input',
                                'checked' => set_value('is_antipass', $checked),
                            );
                            echo form_checkbox($data);
                        ?>
                        <label class="custom-control-label" for="is_antipass">Check this for Anti pass</label>

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
        $('#in_reader').select2({
            placeholder: "Select a In Reader",
        });
        $('#out_reader').select2({
            placeholder: "Select a Out Reader",
        });
        $('#exit_reader').select2({
            placeholder: "Select a Exit Reader",
        });
        $('#time_zone').select2({
            maximumSelectionLength: 3,
            placeholder: "Select a Time Zone",

        });

        $("#reader-access-groups-form").validate({
            ignore: [],
            rules: {
                code_id: {"required": true},
                description: {"required": true},
                group_verify_type: {"required": true},
//                time_zone: {"required": true, minlength: 2},
//                in_reader: {"required": true},
//                out_reader: {"required": true},
//                exit_reader: {"required": true},
            },
            messages: {
                code_id: {"required": "Enter Code Id"},
                description: {"required": "Enter Description"},
                group_verify_type: {"required": "Select A visitors Verify Type"},
//                time_zone: {"required": "Please select chapter", minlength:("You need to use at least {0} characters for your name.")},
//                in_reader: {"required": "Select A visitors Verify Type"},
//                out_reader: {"required": "Select A visitors Verify Type"},
//                exit_reader: {"required": "Select A visitors Verify Type"},
            }
        });
    });

//    $(".reader").change(function () {
//        var selected_reader_arr = [];
//        var default_reader_arr = $("input[name='default_reader[]']").map(function () {
//            return $(this).val();
//        }).get();
//
//        $("#in_reader option:selected").each(function () {
//            var value = $(this).val();
//            if ($.trim(value)) {
//                selected_reader_arr.push(value.trim());
//            }
//        });
//
//        $("#out_reader option:selected").each(function () {
//            var value = $(this).val();
//            if ($.trim(value)) {
//                selected_reader_arr.push(value.trim());
//            }
//        });
//        $("#exit_reader option:selected").each(function () {
//            var value = $(this).val();
//            if ($.trim(value)) {
//                selected_reader_arr.push(value.trim());
//            }
//        });
//
//        $.each(selected_reader_arr, function (index, item) {
//            if (default_reader_arr.indexOf(item) > -1) {
//                $('.reader option[value="' + item + '"]').prop('disabled', 'disabled');
//            } else {
//                $('.reader option[value="' + item + '"]').removeAttr();
//            }
//        });
//    }).change();

</script>