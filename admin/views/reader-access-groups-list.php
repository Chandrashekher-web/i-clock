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
                        <a href="<?php echo base_url('admin/reader_access_groups/add_reader_access_groups') ?>" class="btn btn-sm btn-primary">
                            <i class="fa fa-list"></i>&nbsp;Add Reader Access Groups
                        </a>
                    </div>
                <?php } ?>
            <h4 class="page-title"><?php echo empty($form_caption) ? "" : $form_caption; ?></h4>
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

<script type="text/javascript">

    $('body').on('change', '.change_status', function () {
        event.preventDefault();
        var id = $(this).attr("data-id");
        $.blockUI();
        $.ajax({
            type: "POST",
            url: base_url + 'admin/reader_access_groups/change_status/' + id,

        }).done(function () {

        }).always(function () {
            $.unblockUI();
        });
    });
</script>