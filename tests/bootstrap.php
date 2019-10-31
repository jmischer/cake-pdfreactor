<?php
/**
 * Test suite bootstrap for PdfReactor.
 *
 * This function is used to find the location of CakePHP whether CakePHP
 * has been installed as a dependency of the plugin, or the plugin is itself
 * installed as a dependency of an application.
 */
$findRoot = function ($root) {
    do {
        $lastRoot = $root;
        $root = dirname($root);
        if (is_dir($root . '/vendor/cakephp/cakephp')) {
            return $root;
        }
    } while ($root !== $lastRoot);

    throw new Exception("Cannot find the root of the application, unable to run tests");
};
$root = $findRoot(__FILE__);
unset($findRoot);
chdir($root);
require_once $root . '/vendor/autoload.php';
require_once $root . '/vendor/cakephp/cakephp/tests/bootstrap.php';


$pdf_reactor_client_class = __DIR__ . '/PDFreactor/PDFreactor.class.php';
if (file_exists($pdf_reactor_client_class)) {
	require_once $pdf_reactor_client_class;
}
