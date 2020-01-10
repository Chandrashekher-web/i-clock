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
                    $attributes = array('id' => 'access-groups-form', 'class' => 'form-horizontal');
                    echo form_open_multipart($form_action, $attributes);
                ?>   
                <input type="hidden" name="site_id" id="site_id" value="<?php echo empty($site_id) ? NULL : $site_id; ?>">
                <div class="form-group row">                        
                    <label class="col-md-3 col-form-label" for="reader">Reader<font color="red">*</font></label>
                    <div class="col-md-9">
                        <?php
                            echo form_multiselect('reader[]', empty($reader) ? Null : ($reader), '', 'class="reader form-control  input-block-level " id="reader" multiple="multiple"');
                        ?>
                    </div>
                </div>
               <?php $check_admin_type = check_admin_type();
        if ($check_admin_type == 'Super Admin' || $check_admin_type == 'Site Admin')
        { ?>
                <div class="form-group row">
                    <div class="col-md-9 offset-sm-3">
                        <button type="submit" name="save"  id="button" class="btn btn-primary mr-2"> <i class="fa fa-save" aria-hidden="true"></i> Save</button>
                    </div>
                </div>
        <?php }?>
                <?php echo form_close(); ?>
            </div> <!-- end card-body -->
        </div> <!-- end card-->
    </div><!-- end col -->
</div>
<!-- end row -->


<script>
    $(document).ready(function () {
        $('#reader').select2({
            placeholder: "Select a Reader",
        });
        $("#access-groups-form").validate({
            ignore: [],
            rules: {
                reader: {"required": true},
            },
            messages: {
                reader: {"required": "Select A Reader"},
            }
        });
    });


</script>