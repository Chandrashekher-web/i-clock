<style>
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

</style>
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


        <?php
            $attributes = array('id' => 'employee-form', 'class' => 'form-horizontal');
            echo form_open_multipart($form_action, $attributes);
        ?>   
        <input type="hidden" name="filtermode" value="" id="filtermode"/>
        <input type="hidden" name="mode" value="" id="mode"/>

        <div class="row">
             <?php
                $check_admin_type = check_admin_type();
                if ($check_admin_type == 'Super Admin' || $check_admin_type == 'Site Admin')
                {
                    ?>
            <div class="col-6">
                <div class="form-group row">                        
                    <label class="col-md-3 col-form-label" for="employee_status_update">Apply to selected : </label>
                    <div class="col-md-6">
                        <?php
                            echo form_dropdown('employee_status_update', empty($arr_employee_status_update) ? Null : ($arr_employee_status_update), empty($update_mode) ? NULL : $update_mode, 'class="form-control submit_class"');
                        ?>
                    </div>
                </div>
            </div>
                <?php }?>
            <div class="col-3">
                <div class="form-group row">                        
                    <label class="col-md-3 col-form-label" for="employee_filter">Filter : </label>
                    <div class="col-md-6">
                        <?php
                            echo form_dropdown('employee_filter', empty($arr_employee_filter) ? Null : ($arr_employee_filter), empty($status) ? NULL : $status, 'class="form-control submit_class"');
                        ?>
                    </div>
                </div>
            </div>
            <div class="col-3">
                <label class="col-form-label" for="search">Search&nbsp;</label><input type="text" id="search"/>
            </div>
        </div>   
    </div>
</div>
<div class="clearfix" style="margin: 10px;"></div>

<div class="col-md-12" >
    <table class="table table-hover table-centered mb-0" id="myTable">
        <thead>
            <tr>
                <th width="25px;"><input type="checkbox" id="check_all_commands" class="check_all_commands"/></th>
                <th width="300px;">Name</th>
                <th width="100px;">Pin</th>
                <th width="100px;">RF Card</th>                
                <th width="100px;">FP Count</th>
                <th width="120px;">Status</th>
            </tr>
        </thead>
        <tbody>
            <?php
                $sno = 1;
                if (is_array($arr_employee))
                {
                    foreach ($arr_employee as $key => $employee)
                    {                        
                        ?>
                        <tr>
                            <td width="25px;" style="text-align: center;"><input type="checkbox" name="employee_id[]" value="<?php echo $employee["employee_id"]; ?>"  /></td>
                            <td width="100px;"><?php echo $employee["name"]; ?></td>
                            <td width="100px;"><?php echo $employee["pin"]; ?></td>
                            <td width="100px;"><?php echo $employee["card"]; ?></td>
                            <td width="100px;"><?php echo $employee["fpcount"]; ?></td>
                            <td width="120px;"><?php echo $employee["status"]; ?></td>
                        </tr>
                        <?php
                        $sno++;
                    }
                }
            ?>
        </tbody>
    </table>
</div>



</div>

<?php
    echo form_close();
?>

</div> 
</div>


<script>
    $(document).ready(function () {

        $('.submit_class').on('change', function () {

            if ($(this).val() == 'All')
            {
                $("#filtermode").val('');
            } else if ($(this).val() == 'Active')
            {
                $("#filtermode").val('Active');
            } else if ($(this).val() == 'Inactive')
            {
                $("#filtermode").val('Inactive');
            } else if ($(this).val() == 'update_to_active')
            {
                if ($("input:checkbox").is(':checked'))
                {
                    $('#mode').val('Active');
                } else
                {
                    alert('select a employee for update');
                    return false;
                }
            } else if ($(this).val() == 'update_to_inactive')
            {
                if ($("input:checkbox").is(':checked'))
                {
                    $('#mode').val('Inactive');
                } else
                {
                    alert('select a employee for update');
                    return false;
                }
            }
            $("#employee-form").submit();
        });


        $('.check_all_commands').on('change', function () {
            blockUI();
            $("input:checkbox").prop('checked', $(this).prop("checked"));
            $.unblockUI();
        });
        
        $("#search").on("keyup", function () {
            var value = $(this).val().toLowerCase();
            $("#myTable tr").filter(function () {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });
    });
</script>
