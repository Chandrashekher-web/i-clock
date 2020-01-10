<style>
    .display-hide {    display: none;}

    table{
        width:100%;
        table-layout: fixed;
    }
    .tbl-header{
        /*background-color: rgba(255,255,255,0.3);*/
        padding-right:6px;
    }
    .tbl-content{
        height:500px;
        overflow-x:auto;
        margin-top: 0px;
    }
    th{
        /*        padding: 20px 15px;
                text-align: left;
                font-weight: 500;
                font-size: 12px;
                
                text-transform: uppercase;*/
        border:  1px solid grey!important;
        padding:5px!important;
        text-align: center;
    }
    td{
        /*        padding: 15px;
                text-align: left;
                vertical-align:middle;*/
        /*        font-weight: 300;
                font-size: 12px;*/

        border:  1px solid grey!important;
        padding:5px!important;
    }


    body{
        /*        background: -webkit-linear-gradient(left, #25c481, #25b7c4);
                background: linear-gradient(to right, #25c481, #25b7c4);*/

    }

    /* for custom scrollbar for webkit browser*/

    ::-webkit-scrollbar {
        width: 6px;
    } 
    ::-webkit-scrollbar-track {
        -webkit-box-shadow: inset 0 0 6px rgba(0,0,0,0.3); 
    } 
    ::-webkit-scrollbar-thumb {
        -webkit-box-shadow: inset 0 0 6px rgba(0,0,0,0.3); 
    }

</style>
<!-- start page title -->
<?php
    $attributes = array('id' => 'employee-form', 'class' => 'form-horizontal');
    echo form_open_multipart($form_action, $attributes);
?>
<div class="row">
    <div class="col-12">
        <div class="page-title-box card-body">
            <h4 class="page-title"><strong><?php echo empty($form_caption) ? "" : $form_caption; ?></strong></h4>
            <?php
                $check_admin_type = check_admin_type();
                if ($check_admin_type == 'Super Admin' || $check_admin_type == 'Site Admin')
                {
                    ?>
                    <button type="submit" name="save"  id="button" class="btn btn-danger mr-2">Delete</button>
                <?php } ?>
            <span class="pull-right"><label class="col-form-label" for="search">Search&nbsp;</label><input type="text" id="search"/></span>
            <!--<button onclick="delete_employess();" type="button" name="delete" id="button" class="btn btn-primary mr-2"> Delete</button>-->
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
                <!--                <div id="status_err" class="alert alert-danger display-hide">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                    <span id="err1">An Error has occured, please try again.</span>
                                </div>
                                <div id="statusd_suc" class="alert alert-success display-hide" >
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                    Successfully Deleted.
                                </div>-->


                <table class='table table-striped table-bordered' id="myTable">
                    <tr>
                        <th style="width:80px"><input name="selectchkbox" id="selectchkbox" type="checkbox" /></th>
                        <th>Pin</th>
                        <th>Name</th>
                        <th>Password</th>
                        <th>RF Card</th>
                    </tr>
                    <?php
                        if (!empty($empdata))
                        {
                            foreach ($empdata as $key => $employee)
                            {
                                ?>
                                <tr>
                                    <td style="width:80px; text-align:center"><input name="employee_id[]" type="checkbox" value="<?php echo trim($employee['employee_id']); ?>" /></td>
                                    <td><?php echo $employee['pin']; ?></td>
                                    <td><?php echo $employee['name']; ?></td>
                                    <td><?php echo $employee['password']; ?></td>
                                    <td><?php echo $employee['card']; ?></td>
                                </tr>
                                <?php
                            }
                        }
                    ?>
                </table>
                <!--                <div class="form-group row">
                                    <div class="col-md-9 offset-sm-3">
                                        <button type="submit" name="save"  id="button" class="btn btn-primary mr-2">Delete</button>
                                    </div>
                                </div>-->


            </div> <!-- end card-body -->
        </div> <!-- end card-->
    </div><!-- end col -->
</div>
<!-- end row -->
<?php echo form_close(); ?>

<!-- DELETE employees MODALE -->
<div class="modal fade" id="confirm_box" tabindex="-1" role="dialog" aria-labelledby="confirm_box" aria-hidden="true">

    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title custom_align" id="Heading" style="color: black;">Delete Employees - are you sure?</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times" aria-hidden="true"></i></button>

            </div>            
            <div class="modal-footer ">            

                <button type="button" id="yes_delete" class="btn btn-primary">Yes</button>
                <button type="button" class="btn" data-dismiss="modal">Cancel</button>
            </div>
        </div>
        <!-- /.modal-content --> 
    </div>
</form>
<!-- /.modal-dialog --> 
</div>
<!-- MODALE -->	

<script>
    $(document).ready(function ()
    {
        $("#selectchkbox").click(function () {
            blockUI();
            $('input:checkbox').not(this).prop('checked', this.checked);
            $.unblockUI();
        });

        //form submit
        $("form").submit(function (event) {
            var favorite = [];
            $.each($("input[name='employee_id[]']:checked"), function () {
                favorite.push($(this).val());
            });
            var selectchk = favorite.join(",");
            if (selectchk == "")
            {
                alert('Please select employee(s)');
                return false;
            }
            return confirm('Delete Employees - are you sure?');

//            $('#confirm_box').modal({backdrop: 'static', keyboard: false}).on('click', '#yes_delete', function () {
//
//                $('#confirm_box').modal('hide');
//                return true;
//            });
//            return false;
        });

        $("#search").on("keyup", function () {
            var value = $(this).val().toLowerCase();
            $("#myTable tr").filter(function () {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });
    });

    function delete_employess()
    {
//        alert('dfk');return false;
        var favorite = [];
        $.each($("input[name='employee_id']:checked"), function () {
            favorite.push($(this).val());
        });
        var selectchk = favorite.join(",");
        if (selectchk == "")
        {
            alert('Please select employee(s)');
            return false;
        }

        $('#confirm_box').modal({backdrop: 'static', keyboard: false}).one('click', '#yes_delete', function () {

            $('#confirm_box').modal('hide');
            $.ajax({
                url: "<?php echo base_url(); ?>admin/employee/delete_bulk_employees",
                type: "POST",
                data: {selectchk: selectchk},
                dataType: "JSON",
                success: function (res) {
//                            alert(res.error);
                    if (res.error) {
                        $('#status_err').show();
                        $('#statusd_suc').hide();
                    } else {

                        $('#status_err').hide();
                        $('#statusd_suc').show();
//                        location.reload();
                        setTimeout(function () {
                            window.location.reload();
                        }, 5000);
                    }
                }
            });
        });
    }
</script>