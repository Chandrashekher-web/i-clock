<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <div class="page-title-right">
                <a href="<?php echo base_url('admin/employee/list_employee') ?>" class="btn btn-sm btn-primary">
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
                if (!empty($validation_errors)) {
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
                $attributes = array('id' => 'employee-form', 'class' => 'form-horizontal form-signin', 'novalidate' => 'novalidate');
                echo form_open_multipart($form_action, $attributes);
                ?>   
                <input type="hidden" name="employee_id" id="employee_id" value="<?php echo empty($employee_id) ? NULL : $employee_id; ?>">

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
                            'autofocus' => 'autofocus',
                        );
                        echo form_input($data);
                        ?>
                    </div>
                </div>

                <div class = "form-group row">
                    <label class = "col-md-3 col-form-label" for = "card">Card</label>
                    <div class = "col-md-9">
                        <?php
                        $data = array(
                            'name' => 'card',
                            'id' => 'card',
                            'value' => set_value('card', empty($card) ? NULL : $card),
                            'class' => 'form-control',
                            'placeholder' => 'Enter Card'
                        );
                        echo form_input($data);
                        ?>
                    </div>
                </div>

                <div class="form-group row">                        
                    <label class="col-md-3 col-form-label" for="pin">Pin </label>
                    <div class="col-md-9">
                        <?php
                        $data = array(
                            'name' => 'pin',
                            'id' => 'pin',
                            'value' => set_value('pin', empty($pin) ? NULL : $pin),
                            'class' => 'form-control',
                            'placeholder' => 'Enter Pin No.',
                        );
                        echo form_input($data);
                        ?>
                    </div>
                </div>

                <div class="form-group row">                        
                    <label class="col-md-3 col-form-label" for="administrator">Administrator<font color="red">*</font></label>
                    <div class="col-md-9">
                        <?php
                        echo form_dropdown('administrator', empty($arr_isadmin) ? Null : ($arr_isadmin), empty($administrator) ? NULL : $administrator, 'class="form-control"');
                        ?>
                    </div>
                </div>

                <?php
                if (!empty($user_access['access_user'] == 'Yes')) {
                    ?>

                    <div class="form-group row">                        
                        <label class="col-md-3 col-form-label" for="category">Category</label>
                        <div class="col-md-9">
                            <?php
                            echo form_dropdown('category_id', empty($arr_category) ? Null : ($arr_category), set_value('category_id', empty($category_id) ? NULL : $category_id), 'class="form-control" id="category"');
                            ?>
                        </div>
                    </div>

                    <div class="form-group row">                        
                        <label class="col-md-3 col-form-label" for="sub_category">Sub Category</label>
                        <div class="col-md-9">
                            <?php
                            echo form_dropdown('sub_category_id', empty($arr_sub_category) ? array() : $arr_sub_category, empty($sub_category_id) ? NULL : $sub_category_id, 'id="sub_category" class="form-control input-block-level"');
                            ?>
                        </div>
                    </div>

                    <div class = "form-group row">
                        <label class = "col-md-3 col-form-label" for = "address">Address</label>
                        <div class = "col-md-9">
                            <?php
                            $data = array(
                                'name' => 'address',
                                'id' => 'address',
                                'value' => set_value('address', empty($address) ? NULL : $address),
                                'class' => 'form-control',
                                'placeholder' => 'Enter Address',
                                'col' => 3,
                            );
                            echo form_textarea($data);
                            ?>
                        </div>
                    </div> 
                    <div class = "form-group row">
                        <label class = "col-md-3 col-form-label" for = "property_number">Property Number</label>
                        <div class = "col-md-9">
                            <?php
                            $data = array(
                                'name' => 'property_number',
                                'id' => 'property_number',
                                'value' => set_value('property_number', empty($property_number) ? NULL : $property_number),
                                'class' => 'form-control',
                                'placeholder' => 'Enter Property Number',
                                'col' => 3,
                            );
                            echo form_input($data);
                            ?>
                        </div>
                    </div> 

                    <div class = "form-group row">
                        <label class = "col-md-3 col-form-label" for = "phone">Phone No.</label>
                        <div class = "col-md-9">
                            <?php
                            $data = array(
                                'name' => 'phone',
                                'id' => 'phone',
                                'value' => set_value('phone', empty($phone) ? NULL : $phone),
                                'class' => 'form-control',
                                'placeholder' => 'Enter Phone No.'
                            );
                            echo form_input($data);
                            ?>
                        </div>
                    </div> 

                    <div class = "form-group row">
                        <label class = "col-md-3 col-form-label" for = "vehicle_reg_no">Vehicle Registration No.</label>
                        <div class = "col-md-9">
                            <?php
                            $data = array(
                                'name' => 'vehicle_reg_no',
                                'id' => 'vehicle_reg_no',
                                'value' => set_value('vehicle_reg_no', empty($vehicle_reg_no) ? NULL : $vehicle_reg_no),
                                'class' => 'form-control',
                                'placeholder' => 'Enter Vehicle Registration No.'
                            );
                            echo form_input($data);
                            ?>
                        </div>
                    </div>

                    <div class = "form-group row">
                        <label class = "col-md-3 col-form-label" for = "id_no">ID No.</label>
                        <div class = "col-md-9">
                            <?php
                            $data = array(
                                'name' => 'id_no',
                                'id' => 'id_no',
                                'value' => set_value('id_no', empty($id_no) ? NULL : $id_no),
                                'class' => 'form-control',
                                'placeholder' => 'Enter ID No.'
                            );
                            echo form_input($data);
                            ?>
                        </div>
                    </div>

                    <div class = "form-group row">
                        <label class = "col-md-3 col-form-label" for = "permanent_user">Permanent User</label>
                        <div class = "col-md-9">
                            <?php
                            if (@$permanent_user == "Yes") {
                                $data = array(
                                    'name' => 'permanent_user',
                                    'id' => 'permanent_user',
                                    'value' => 'Yes',
                                    'style' => 'margin:10px',
                                    'checked' => true
                                );

                                echo form_checkbox($data);
                            } else {
                                $data = array(
                                    'name' => 'permanent_user',
                                    'id' => 'permanent_user',
                                    'value' => 'Yes',
                                    'style' => 'margin:10px'
                                );

                                echo form_checkbox($data);
                            }
                            ?>
                        </div>
                    </div>

                    <div class = "form-group row">
                        <label class = "col-md-3 col-form-label" for = "start_date">From Date</label>
                        <div class = "col-md-3">
                            <input type="text" name="start_date" <?php
                            if (@$permanent_user == "Yes") {
                                echo "disabled";
                            }
                            ?> id="start_date"  value="<?php echo date("d/m/Y", strtotime(set_value('start_date', empty($start_date) ? date("Y-m-d") : $start_date))); ?>" class="form-control  mb-1 dd" placeholder="From Date" required>
                        </div>
                        <label class = "col-md-3 col-form-label" for = "end_date">To Date</label>
                        <div class = "col-md-3">
                            <input type="text" value="<?php echo date("d/m/Y", strtotime(set_value('end_date', empty($end_date) ? date("Y-m-d") : $end_date))); ?>" name="end_date"  <?php
                            if (@$permanent_user == "Yes") {
                                echo "disabled";
                            }
                            ?>    
                                   id="end_date"  class="form-control  mb-1 dd" placeholder="To Date" required>
                        </div>
                    </div>

                    <div class="form-group row">                        
                        <label class="col-md-3 col-form-label" for="username">Username</label>
                        <div class="col-md-9">
                            <?php
                            $data = array(
                                'name' => 'username',
                                'id' => 'username',
                                'value' => set_value('username', empty($username) ? NULL : $username),
                                'class' => 'form-control',
                                'placeholder' => 'Enter Username',
                                'autocomplete' => "off",
                            );
                            echo form_input($data);
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
                                'class' => 'form-control password',
                                'placeholder' => 'Enter Password',
                                'autocomplete' => "new-password",
                            );
                            echo form_input($data);
                            ?>

                        </div>
                    </div>

                    <div class = "form-group row">
                        <label class = "col-md-3 col-form-label" for = "access_code_generator">Access Code Generator</label>
                        <div class = "col-md-9">
                            <?php
                            if (@$access_code_generator == "Enable") {
                                $data = array(
                                    'name' => 'access_code_generator',
                                    'id' => 'access_code_generator',
                                    'value' => 'Enable',
                                    'style' => 'margin:10px',
                                    'checked' => true
                                );

                                echo form_checkbox($data);
                            } else {
                                $data = array(
                                    'name' => 'access_code_generator',
                                    'id' => 'access_code_generator',
                                    'value' => 'Enable',
                                    'style' => 'margin:10px'
                                );
                                echo form_checkbox($data);
                            }
                            ?>
                        </div>
                    </div>
                    <div class="form-group row">                        
                        <label class="col-md-3 col-form-label" for="access_group">Access Group</label>
                        <div class="col-md-9">
                            <?php
                            echo form_dropdown('access_group', empty($arr_access_groups) ? Null : ($arr_access_groups), set_value('access_group', empty($access_group) ? NULL : $access_group), 'class="form-control" id="category"');
                            ?>
                        </div>
                    </div>

                    <?php
                }
                ?>

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
        $('#employee-form').attr('autocomplete', 'off');
        $("#employee-form").validate({
            rules: {
                name: {"required": true},
                pin: {"required": true}, //, digits: true, maxlength: 9

            },
            messages: {
                name: {"required": "Enter Employee Name"},
                pin: {"required": "Enter Employee Pin code", digits: "Pin should be in digits", maxlength: "Pin should not be greater than 9 digits"},

            }
        });
        $(document).on('change', '#category', function () {
            var category_id = $(this).val();
            $.ajax({
                type: "POST",
                url: base_url + 'admin/sub_category/get_sub_category_by_category_id',
                data: 'category_id=' + category_id,
                datatype: "html",
                success: function (output) {
                    console.log(output);
                    $('#sub_category').html(output);
                }
            });
        });


        $('.dd').datepicker({
            todayHighlight: true,
            autoclose: true,
            format: 'dd/mm/yyyy',
        });


        $("#end_date").change(function () {

            var startDate = document.getElementById("start_date").value;
            var endDate = document.getElementById("end_date").value;

            var checkSTARTdate = moment(startDate, "DD/MM/YYYY").format("MM/DD/YYYY");
            var checkENDdate = moment(endDate, "DD/MM/YYYY").format("MM/DD/YYYY");

            if ((Date.parse(checkENDdate) < Date.parse(checkSTARTdate))) {
                alert("End date should be greater than Start date");
                document.getElementById("end_date").value = "";
            }


        });



        $('#permanent_user').click(function () {
            if (!$(this).is(':checked')) {
                $('.dd').prop('disabled', false);
            } else
            {
                $('.dd').prop('disabled', true);
            }
        })

    });
</script>
