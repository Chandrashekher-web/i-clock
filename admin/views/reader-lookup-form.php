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
                    $attributes = array('id' => 'reader-lookup-form', 'class' => 'form-horizontal');
                    echo form_open_multipart($form_action, $attributes);
                ?>   

                <div class="form-group row">                        
                    <label class="col-md-3 col-form-label" for="name">Name</label>
                    <div class="col-md-9">
                        <?php
                            $data = array(
                                'name' => 'name',
                                'id' => 'name',
                                'value' => set_value('name', empty($name) ? NULL : $name),
                                'class' => 'form-control',
                                'placeholder' => 'Enter Name',
                                'autofocus' => 'autofocus',
                            );
                            echo form_input($data);
                        ?>
                    </div>
                </div>
                
                <div class="form-group row">                        
                    <label class="col-md-3 col-form-label" for="sn">SN</label>
                    <div class="col-md-9">
                        <?php
                            $data = array(
                                'name' => 'sn',
                                'id' => 'sn',
                                'value' => set_value('sn', empty($sn) ? NULL : $sn),
                                'class' => 'form-control',
                                'placeholder' => 'Enter SN',
                                'autofocus' => 'autofocus',
                            );
                            echo form_input($data);
                        ?>
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-md-9 offset-sm-3">
                        <button type="button" name="save"  id="button" class="btn btn-primary mr-2 show-details"> Show Result</button>
                    </div>
                </div>
                
                <div class="col-md-12"  id="item-container" style="display:none;">
                        <div class="form-group row"> 
                            <div class="col-md-12" id="netprofit">

                            </div>
                        </div>
                    </div>
                <?php echo form_close(); ?>
            </div> <!-- end card-body -->
        </div> <!-- end card-->
    </div><!-- end col -->
</div>
<!-- end row -->
<script>
    $(document).ready(function ()
    {        
        $(".show-details").click(function () {
//        $('.show-details').on('click', function () {
            var name = $('#name').val();
            var sn = $('#sn').val();
            
            if(name != '' || sn != '')
            {
                $.ajax({
                    type: "POST",
                    dataType: "html",
                    data: {name: name, sn: sn},
                    url: "<?php echo base_url(); ?>admin/reader/get_reader_search_details",
                    success: function (output) {
                        $('#item-container').show();
                        $('#netprofit').html(output);
                    }
                });
            }
            else
            {
                alert('Enter atleast one of the field!');
            }

        });

    });
</script>





