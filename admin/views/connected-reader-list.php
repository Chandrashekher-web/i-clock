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
            <?php
                
         
                
              // $output =  passthru("tail -f /var/log/apache2/access.log 2>&1");
                $output = shell_exec("tail -f /var/log/apache2/access.log 2>&1");
                // $output = exec("tail -f /var/log/apache2/access.log 2>&1");
                echo str_replace(PHP_EOL, '<br />', $output); // line OK
//               $handle = popen("tail -f /var/log/apache2/access.log 2>&1", 'r');
//                while (!feof($handle))
//                {
//                    $buffer = fgets($handle);
//                    echo "$buffer<br/>\n";
//                    ob_flush();
//                    flush();
//                }
//                pclose($handle);
//                
            ?>
        </div> <!-- end card-->
    </div><!-- end col -->
</div>




