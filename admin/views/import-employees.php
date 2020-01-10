<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <h4 class="page-title">Import Employees</h4>
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
                 
                <div class="form-group row">                        
                    <label class="col-md-3 col-form-label" for="import_employee">Upload csv</label>
                        <div class="col-md-9">
                            <?php
                                $data = array(
                                    'name' => 'import_employee',
                                    'id' => 'import_employee',
                                    'class' => 'form-control',
//                                    'accept' => 'application/pdf',
                                );
                                echo form_upload($data);
                            ?>
                            <br>
                            <span style="color:red;">*Please choose a csv file(.csv) as Input</span>

                        </div>
                </div>
 <?php
                $check_admin_type = check_admin_type();
                if ($check_admin_type == 'Super Admin' || $check_admin_type == 'Site Admin')
                {
                    ?>
                <div class="form-group row">
                    <div class="col-md-9 offset-sm-3">
                        <button type="submit" name="save"  id="button" class="btn btn-primary mr-2"> <i class="fa fa-save" aria-hidden="true"></i> Import</button>
                    </div>
                </div>
                <?php } echo form_close(); ?>
            </div> <!-- end card-body -->
        </div> <!-- end card-->
    </div><!-- end col -->
</div>
<!-- end row -->


<script>
    $(document).ready(function () {
        
        $("#employee-form").validate({
            rules: {
                import_employee: {"required": true},
              
            },
            messages: {
                import_employee: {"required": "Select a csv File for Implort Employe(s)."},
              
            }
        });
    });
</script>
