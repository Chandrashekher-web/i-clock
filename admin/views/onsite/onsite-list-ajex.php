<?php
    $check_admin_type = check_admin_type();
    if (!empty($clocking_data))
    {
        foreach ($clocking_data as $employee)
        {
            $href = '';
            if ($check_admin_type == 'Super Admin' || $check_admin_type == 'Site Admin')
            {
                $href = 'target="new" href=' . base_url() . 'admin/employee/add_employee/' . $employee["employee_id"];
            }
            ?>
            <tr id="<?php echo $employee['attendance_id']; ?>">
                <td style="text-align: center"><a <?php echo $href?> ><?php echo $employee['pin']; ?></a></td>
                <td style="text-align: center"><?php echo $employee['emp_name']; ?></td>
                <td style="text-align: center"><?php echo $employee['dt']; ?></td>
                <td style="text-align: center"><?php echo $employee["t"]; ?></td>
            <!--                <td style="text-align: center"><?php echo $employee["direction"]; ?></td>-->
                <td style="text-align: center"><?php echo $employee["sn"]; ?></td>
                <td style="text-align: center"><?php echo $employee["reader"]; ?></td>
                <!--<td style="text-align: center"><button  data-clocking="<?php echo $employee['attendance_id']; ?>"  type="button" class="btn btn-success btn-rounded remove_clocking" >Remove</button></td>-->
            </tr>
            <?php
        }
    }
?>
                    