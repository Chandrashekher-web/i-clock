<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <?php 
                 $is_super_admin =  is_super_admin();
                if($is_super_admin) {?>
            <div class="page-title-right">
                <a href="<?php echo base_url('admin/command_library/add_command') ?>" class="btn btn-sm btn-primary">
                    <i class="fa fa-list"></i>&nbsp;Add Command
                </a>
            </div>
             <?php }?>           
            <h4 class="page-title">Command List</h4>
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
 