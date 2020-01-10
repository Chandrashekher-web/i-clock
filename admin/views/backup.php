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
                    $attributes = array('id' => 'backup-form', 'class' => 'form-horizontal');
                    echo form_open($form_action, $attributes);
                ?>   
                <div class="form-group row">
                    <div class="col-md-9 offset-sm-3">
                        <input type="hidden" id="mode" name="mode" value=""/>
                        <button type="button" name="backup" value="backup"  id="backup" class="backupoption btn btn-primary mr-2"> <i class="fa fa-save" aria-hidden="true"></i> Download Backup</button>
                        <button type="button" name="restore" value="restore"  id="restore" class="backupoption btn btn-primary mr-2"> <i class="fa fa-save" aria-hidden="true"></i> Restore Backup</button>
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
         
         
        $('.backupoption').on('click', function (e) {
            mode = ($(this).val());
            $('#mode').val(mode);
            $('#backup-form').submit();
        })
        
    });
</script>
