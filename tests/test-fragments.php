<?php
require_once __DIR__ . '/bootstrap.php';
require_once dirname(__DIR__) . '/includes/class-engine.php';

echo "Testing WooFastFragments Engine...
";

$engine = new \WooFastFragments\Engine();
assert($engine instanceof \WooFastFragments\Engine, "Engine instance check");

echo "PASS: woo-fast-fragments verified.
";
