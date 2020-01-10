<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <h4 class="page-title">Change Reader Password</h4>
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
                    if (!empty($message))
                    {
                        ?>
                        <div class="alert alert-success">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                            <?php echo $message; ?>
                        </div>
                        <?php
                    }
                ?>

                <?php
                    $attributes = array('id' => 'employee-form', 'class' => 'form-horizontal');
                    echo form_open_multipart($form_action, $attributes);
                ?>   
                <input type="hidden" name="employee_id" id="employee_id" value="<?php echo empty($employee_id) ? NULL : $employee_id; ?>">

                <div class="form-group row">                        
                    <label class="col-md-3 col-form-label" for="password">Password</label>
                    <div class="col-md-9">
                        <?php
                            $data = array(
                                'name' => 'password',
                                'id' => 'password',
                                'type' => 'password',
//                                'value' => set_value('password', NULL),
                                'class' => 'form-control',
                                'placeholder' => 'Enter password',
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

<?php
//    $output = '';
//    $output .= form_open_multipart('import/save');
//    $output .= '<div class="row">';
//    $output .= '<div class="col-lg-12 col-sm-12"><div class="form-group">';
//    $output .= form_label('Choose File', 'image');
//    $data = array(
//        'name' => 'userfile',
//        'id' => 'userfile',
//        'class' => 'form-control filestyle',
//        'value' => '',
//        'data-icon' => 'false'
//    );
//    $output .= form_upload($data);
//    $output .= '</div> <span style="color:red;">*Please choose an Excel file(.xls or .xlxs) as Input</span></div>';
//    $output .= '<div class="col-lg-12 col-sm-12"><div class="form-group text-right">';
//    $data = array(
//        'name' => 'importfile',
//        'id' => 'importfile-id',
//        'class' => 'btn btn-primary',
//        'value' => 'Import',
//    );
//    $output .= form_submit($data, 'Import Data');
//    $output .= '</div>
//                        </div></div>';
//    $output .= form_close();
//    echo $output;
?>