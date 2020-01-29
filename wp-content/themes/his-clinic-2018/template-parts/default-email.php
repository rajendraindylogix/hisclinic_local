<?php 
    global $part_args;

    $heading = (!empty($part_args['heading'])) ? $part_args['heading'] : 'Hello';
    $intro = (!empty($part_args['intro'])) ? $part_args['intro'] : '';
    $content = (!empty($part_args['content'])) ? $part_args['content'] : '';
?>

<!DOCTYPE html>
    <html>
    <head>
        <style>
            table {
                font-family: arial, sans-serif;
                border-collapse: collapse;
                width: 100%;
            }
            
            td, th {
                border: 1px solid #dddddd;
                text-align: left;
                padding: 8px;
            }
            
            tr:nth-child(even) {
                background-color: #dddddd;
            }
        </style>
    </head>
    <body>
        <img src="https://www.hisclinic.com/wp-content/themes/his-clinic-2018/assets/img/logo.png" alt="Logo">

        <h3><?php echo $heading ?>,</h3>
        <p><?php echo $intro ?></p>

        <?php echo $content ?>
    </body>
</html>