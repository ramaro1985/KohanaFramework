<?php

include_once('config.php');

$cli = new Doctrine_Cli ( $config );
$cli->run ( $_SERVER ['argv'] );

