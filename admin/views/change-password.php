<section class="card">
    <div class="card-header">
        <span class="cat__core__title">
            <strong><?php echo empty($form_caption) ? "" : $form_caption; ?></strong>            
        </span>
    </div>
    <div class="card-block">
        <div class="row">
            <div class="col-md-12">
                <?php
                    $validation_errors=validation_errors();
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
                <div class="mb-5">
                    <?php
                        $attributes=array('id' => 'change-password-form', 'class' => 'form-horizontal');
                        echo form_open($form_action, $attributes);
                    ?>   
                    <input type="hidden" name="promocode_id" id="promocode_id" value="<?php echo empty($promocode_id) ? NULL : $promocode_id; ?>">
                    <div class="row">
                        <div class="col-md-6 offset-3"> 
                            <div class="form-group row">                        
                                <label class="col-md-3 col-form-label" for="password">Current Password</label>
                                <div class="col-md-7">
                                    <?php
                                        $data=array(
                                            'name'  => 'password',
                                            'id'    => 'password',
                                            'class' => 'form-control',
                                        );
                                        echo form_password($data);
                                    ?>
                                </div>
                            </div> 
                            <div class="form-group row">                        
                                <label class="col-md-3 col-form-label" for="new_password">New Password</label>
                                <div class="col-md-7">
                                    <?php
                                        $data=array(
                                            'name'  => 'new_password',
                                            'id'    => 'new_password',
                                            'class' => 'form-control',
                                        );
                                        echo form_password($data);
                                    ?>
                                </div>
                            </div> 
                            <div class="form-group row">                        
                                <label class="col-md-3 col-form-label" for="confirm_password">Confirm Password</label>
                                <div class="col-md-7">
                                    <?php
                                        $data=array(
                                            'name'  => 'confirm_password',
                                            'id'    => 'confirm_password',
                                            'class' => 'form-control',
                                        );
                                        echo form_password($data);
                                    ?>
                                </div>
                            </div> 
                            <div class="form-actions">
                                <div class="row">                            
                                    <div class="col-md-12">
                                        <div class="form-group row">
                                            <div class="col-md-7 offset-sm-3">
                                                <button type="submit" name="save" class="btn btn-primary mr-2"> <i class="fa fa-save" aria-hidden="true"></i> Save</button>                                                
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>                            
                        </div>
                    </div>
                    <?php echo form_close(); ?>
                </div>
            </div>
        </div>
    </div>
</section>
<script>
    $(document).ready(function ()
    {
        $("#change-password-form").validate({
            rules: {
                new_password: {"required": true},
                password: {"required": true},
                confirm_password: {equalTo: "#new_password"}
            },
            messages: {
                new_password: {"required": "Enter New Password"},
                password: {"required": "Enter Password"},
                confirm_password: {equalTo: "Password Does Not Match"}
            }
        });
    });
</script>