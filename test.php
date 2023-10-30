<?php
include('vendor/bootstrap.php');
$c = get_declared_classes();
foreach ($c as $cname) {
    echo $cname . "\r\n";
}
