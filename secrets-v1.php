<?php
// Keep your Stripe API key protected by including it as an environment variable
// or in a private script that does not publicly expose the source code.

// This is your test secret API key.
require_once realpath(__DIR__ . "/vendor/autoload.php");
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

$stripeSecretKey = $_ENV['stripeSecretKey_live'];

?>