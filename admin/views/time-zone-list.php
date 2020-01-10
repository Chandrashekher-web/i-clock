<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
             <?php
                $check_admin_type = check_admin_type();
                if ($check_admin_type == 'Super Admin' || $check_admin_type == 'Site Admin')
                {
                    ?>
            <div class="page-title-right">
                <a href="<?php echo base_url('admin/time_zone/add_time_zone') ?>" class="btn btn-sm btn-primary">
                    <i class="fa fa-list"></i>&nbsp;Add Time Zone
                </a>
            </div>
                <?php }?>
            <h4 class="page-title">Time Zone</h4>
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
                ?>
                <?php echo $table; ?>
            </div> <!-- end card body-->
        </div> <!-- end card -->
    </div><!-- end col-->
</div>
<!-- end row-->

