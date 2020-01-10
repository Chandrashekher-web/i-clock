<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <div class="page-title-right">
<!--                <a href="<?php echo base_url('admin/employee/list_employee') ?>" class="btn btn-sm btn-primary">
                    <i class="fa fa-list"></i>&nbsp;Back
                </a>-->
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
                    $attributes = array('id' => 'employee-form', 'class' => 'form-horizontal');
                    echo form_open_multipart('', $attributes);
                ?>
                <div class="form-group">

                    <div class="form-row align-items-center">


                        <div class="col-2">
                            <?php
                                echo form_dropdown('hours_filter', empty($site_hours_filter) ? NULL : $site_hours_filter, empty($conditions_arr['hours_filter']) ? NULL : $conditions_arr['hours_filter'], 'id="hours_filter"  class="form-control mb-1 select2"');
                            ?>

                        </div>
                        <div class="col-2">
                            <?php
                                echo form_dropdown('employee', empty($employee_arr) ? NULL : $employee_arr, empty($conditions_arr['employee_id']) ? NULL : $conditions_arr['employee_id'], 'id="employee" data-toggle="select2" class="form-control mb-1 select2"');
                            ?>

                        </div>
                        <div class="col-2">
                            <?php
                                echo form_dropdown('reader', empty($reader_arr) ? NULL : $reader_arr, empty($conditions_arr['reader_id']) ? NULL : $conditions_arr['reader_id'], 'id="reader" data-toggle="select2" class="form-control mb-1 select2"');
                            ?>
                        </div>
                        <div class="col-2">
                            <?php
                                echo form_dropdown('department', empty($department_arr) ? NULL : $department_arr, empty($conditions_arr['department_id']) ? NULL : $conditions_arr['department_id'], 'id="department" data-toggle="select2" class="form-control mb-1 select2"');
                            ?>
                        </div>
                        <div class="col-2">
                            <input type="hidden" name="mode" value="filter"/>
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </div>

                </div>
                <table class="table table-bordered mb-0" id="myTable">
                    <tr>                           
                        <th>Pin</th>
                        <th>Visitors Name</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Reader</th>
                        <th>Reader Name</th>     
                        <!--<th>Remove</th>-->     
                    </tr>

                    <tbody id="mytbody">
                        <?php
                            $check_admin_type = check_admin_type();
                            $last_clocking_id = '';
                            if (!empty($clocking_data))
                            {
                                $last_clocking_id = current($clocking_data);
                                $last_clocking_id = $last_clocking_id['attendance_id'];
                                foreach ($clocking_data as $employee)
                                {

                                    $href = '';
                                    if ($check_admin_type == 'Super Admin' || $check_admin_type == 'Site Admin')
                                    {
                                        $href = 'target="new" href=' . base_url() . 'admin/employee/add_employee/' . $employee["employee_id"];
                                    }
                                    ?>
                                    <tr id="<?php echo $employee['attendance_id']; ?>">
                                        <td style="text-align: center"><a <?php echo $href; ?> ><?php echo $employee['pin']; ?></a></td>
                                        <td style="text-align: center"><?php echo $employee['emp_name']; ?></td>
                                        <td style="text-align: center"><?php echo $employee['dt']; ?></td>
                                        <td style="text-align: center"><?php echo $employee["t"]; ?></td>
                                        <td style="text-align: center"><?php echo $employee["sn"]; ?></td>
                                        <td style="text-align: center"><?php echo $employee["reader"]; ?></td>
                                        <!--<td style="text-align: center"><button  data-clocking="<?php echo $employee['attendance_id']; ?>"  type="button" class="btn btn-success btn-rounded remove_clocking" >Remove</button></td>-->
                                    </tr>
                                    <?php
                                }
                            }
                        ?>
                    </tbody>
                    <input type="hidden" id="last_clocking_id" value="<?php echo $last_clocking_id; ?>"/>
                </table>

                <?php echo form_close(); ?>
            </div> <!-- end card-body -->
        </div> <!-- end card-->
    </div><!-- end col -->
</div>
<!-- end row -->
<script>

    $('.date').datepicker({
        todayHighlight: true,
        autoclose: true,
        format: 'dd/mm/yyyy',
    });

    $('.remove_clocking').click(function () {
        var clocking_id = $(this).data('clocking');
        $.blockUI();
        $.ajax({
            type: "POST",
            url: base_url + 'admin/live_clocking/remove_clocking/',
            data: {clocking_id: clocking_id},
        }).done(function () {
            $("#" + clocking_id).remove();
        }).always(function () {
            $.unblockUI();
        });
    });

    setInterval(function () {
        // $.blockUI();
        $.ajax({
            type: "POST",
            url: base_url + 'admin/onsite/get_latest_clocking/',
            data: {hours_filter: $('#hours_filter').val(), employee: $('#employee').val(), reader: $('#reader').val(), department: $('#department').val()},
        }).done(function (data) {
            // $.unblockUI();
            $("#mytbody").html(data);
        }).always(function () {

        });

    }, 1000 * 10 * 1);

</script>

