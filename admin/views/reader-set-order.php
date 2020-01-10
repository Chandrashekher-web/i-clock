<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <h3 class="box-title "></h3>

            <div class="page-title-right" style="margin-right:  10px;">

                <?php echo anchor("admin/reader/list_reader", "Back", ' class="btn btn-primary pull-right"'); ?>
                <button type="button" name="update"  id="update" class="btn btn-primary pull-right mr-2"> <i class="fa fa-save" aria-hidden="true"></i> Update </button>

            </div>

            <h4 class="page-title"><?php echo $this->lang->line('setorder'); ?></h4>
        </div>
    </div>
</div>     
<!-- end page title --> 

<div class="row">
    <div class="col-6">
        <div class="card">
            <div class="card-body">                
                <p id="update_status" class="alert"></p>
                <ul id="sortable" class="list-group">
                    <?php
                        if (!empty($results))
                        {
                            foreach ($results as $id => $value)
                            {
                                ?>
                                <li id="orderid_<?php echo $id; ?>" class="ui-state-default" draggable="true">
                                    <span class="icon fa fa-arrows"></span>
                                    <?php echo $value; ?> 
                                </li>
                                <?php
                            }
                        }
                    ?>
                </ul>	
            </div> <!-- end card body-->
        </div> <!-- end card -->
    </div><!-- end col-->
</div>
<!-- end row-->


<script type="text/javascript">
    $(document).ready(function () {
        $("#sortable").sortable({
            update: function () {
                update_readerorder();
            }
        });

        $('#update').on('click', function (e) {
            update_readerorder();
        });
    });

    function update_readerorder()
    {
        var order = $('#sortable').sortable('toArray');
        $("#sortable_msg").hide();
        $.ajax({
            type: "POST",
            async: true,
            url: base_url + "<?php echo "admin/reader/setordersave/" ?>",
            data: {"item": order},
            beforeSend: function () {
                $("#update_status").html("Processing...").addClass("alert-success").removeClass("hidden");
            },
            dataType: 'json'
        }).done(function (data) {
            if (data.status == "failure")
            {
                $("#update_status").html("Error occured").addClass("alert-danger").removeClass("hidden alert-success");
            } else
            {
                $("#update_status").html("Order updated successfully.").addClass("alert-success").removeClass("hidden alert-danger");
            }
        });

    }
</script>