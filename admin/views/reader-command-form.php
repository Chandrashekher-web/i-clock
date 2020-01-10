<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <div class="page-title-right">
                <a href="<?php echo base_url('admin/command_library/list_command_library') ?>" class="btn btn-sm btn-primary">
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
                <input type="hidden" name="command_id" id="command_id" value="<?php echo empty($command_id) ? NULL : $command_id; ?>">





                <div class="form-group row">                        
                    <label class="col-md-3 col-form-label" for="command">Command<font color="red">*</font></label>
                    <div class="col-md-9">
                        <?php
                            $data = array(
                                'name' => 'command',
                                'id' => 'command',
                                'value' => set_value('name', empty($command) ? NULL : $command),
                                'class' => 'form-control',
                                'placeholder' => 'Enter Command',
                                ''
                            );
                            echo form_input($data);
                        ?>
                    </div>
                </div>

                <div class="form-group row">                        
                    <label class="col-md-3 col-form-label" for="command_description">Command Description<font color="red">*</font></label>
                    <div class="col-md-9">
                        <?php
                            $data = array(
                                'name' => 'command_description',
                                'id' => 'command_description',
                                'value' => set_value('name', empty($command_description) ? NULL : $command_description),
                                'class' => 'form-control',
                                'placeholder' => 'Enter Command Description',
                            );
                            echo form_textarea($data);
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
                command: {"required": true},
                command_description: {"required": true},
            },
            messages: {
                command: {"required": "Enter Reader Command."},
                command_description: {"required": "Enter Reader command Description"},
            }
        });
    });
</script>
