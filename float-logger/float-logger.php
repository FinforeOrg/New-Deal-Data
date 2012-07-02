<?php
/********
php wrapper for show_in_float_logger
*********/
if (!function_exists('float_log')) {
  function float_log($obj) {  
    $data = json_encode(print_r($obj,true));
    ?>
    <script type="text/javascript">
      show_in_float_logger(<?php echo $data;?>);
    </script>
    <?php
  }
}
?>