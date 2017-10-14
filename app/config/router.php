<?php

$router = $di->getRouter();

// Define your routes here
$router->add('/', [
  "controller" => "corporates",
  "action" => "index"
]);

$router->handle();
