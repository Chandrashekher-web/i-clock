<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <div class="page-title-right">
                <a href="<?php echo base_url('admin/site/add_site') ?>" class="btn btn-sm btn-primary">
                    <i class="fa fa-list"></i>&nbsp;Add Site
                </a>
            </div>
            <h4 class="page-title">Sites</h4>
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


<script>
    
    $(document).ready(function() {  
        $(document).on("click", ".delete-site", function(event) {
            event.preventDefault();
            var url =  $(this).attr("url");
            
            bootbox.confirm("<b>Are you sure you want to DELETE this site?<b>", function(result){ 
                if(result)
                {
                    bootbox.confirm("<b>This site and all associated data will be permanently deleted from the server?<b>", function(result2){ 
                        if(result2)
                        {
                            window.location.href = url;
                        }
                    });
                }
            });
            
        });
    });
    
</script>
