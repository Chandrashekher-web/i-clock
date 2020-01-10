<style>
    .red
    {
        background: red;
        color: white;
    }
    .green
    {
        background: green;
        color: white;
    }
    .yellow
    {
        background: yellow;
        color: black;
    }
</style>

<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box">

            <h4 class="page-title">Site Online / Offline Reader</h4>
        </div>
    </div>
</div>     
<!-- end page title --> 

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <table class="table table-striped table-bordered" id="myTable">
                    <thead>
                        <tr>
                            <th style="width:20%">Site Code</th>
                            <th style="width:50%">Site Name</th>
                            <th style="width:10%">Total Reader</th>
                            <th style="width:10%">Offline Reader</th>
                            <th style="width:10%">Online Reader</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            if (!empty($site_date))
                            {
                                $myclass = '';
                                foreach ($site_date as $key => $employee)
                                {
                                    if ($employee['offline'] == $employee['total_reader'])
                                    {
                                        $myclass = 'red';
                                    }
                                    else if ($employee['online'] == $employee['total_reader'])
                                    {
                                        $myclass = 'green';
                                    }
                                    else
                                    {
                                        $myclass = 'yellow';
                                    }
                                    ?>
                                    <tr >
                                        <td class="<?php echo $myclass; ?>"><?php echo $employee['site_code']; ?></td>
                                        <td class="<?php echo $myclass; ?>"><?php echo $employee['name']; ?></td>
                                        <td class="text-right <?php echo $myclass; ?>"><?php echo $employee['total_reader']; ?></td>
                                        <td class="text-right <?php echo $myclass; ?>"><?php echo $employee['offline']; ?></td>
                                        <td class="text-right <?php echo $myclass; ?>"><?php echo $employee['online']; ?></td>
                                    </tr>
                                    <?php
                                }
                            }
                        ?>
                    </tbody>
                </table>
            </div> <!-- end card body-->
        </div> <!-- end card -->
    </div><!-- end col-->
</div>
<!-- end row-->
