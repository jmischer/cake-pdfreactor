<?php
namespace JMischer\CakePDFreactor\Pdf\Engine;

use CakePdf\Pdf\CakePdf;
use CakePdf\Pdf\Engine\AbstractPdfEngine;
use Cake\Core\App;
use Exception;

/**
 *
 * @author jmischer
 *        
 */
class PDFreactorEngine extends AbstractPdfEngine
{

    /**
     * The default PDFreactor webservice client class name
     *
     * @var string
     */
    const DEFAULT_WEBSERVICE_CLIENT_CLASS_NAME = '\com\realobjects\pdfreactor\webservice\client\PDFreactor';

    /**
     *
     * @param CakePdf $Pdf
     */
    public function __construct(CakePdf $Pdf)
    {
        parent::__construct($Pdf);
    }

    /**
     * Generates Pdf from html.
     *
     * @return string raw pdf data
     */
    public function output(): string
    {
        // Get client config
        $client = $this->getConfig('client', []);

        // Create pdf reactor instance
        $pdf_reactor = $this->createInstance($client);

        // Get engine options
        $options = $this->getConfig('options', []);

        // Create pdf reactor render configuration
        $config = $this->createConfig($options, $this->_Pdf);

        // Return output
        return $this->_output($pdf_reactor, $config);
    }

    /**
     * Creates the pdf reactor instance.
     *
     * @param mixed $client
     *            The client configuration, class name or instance
     * @throws Exception
     * @return object
     */
    protected function createInstance($client)
    {
        // Get client instance from client config
        if (! is_object($client)) {
            // Initialize service url
            $service_url = null;
            $api_key = null;

            // Check client config is array
            if (is_array($client)) {
                $client += [
                    'className' => self::DEFAULT_WEBSERVICE_CLIENT_CLASS_NAME
                ];
                if (isset($client['serviceUrl'])) {
                    $service_url = $client['serviceUrl'];
                }
                if (isset($client['apiKey'])) {
                    $api_key = $client['apiKey'];
                }
                $client = $client['className'];
            }

            // Get class and create instance
            $client_class_name = App::className($client);
            if (! class_exists($client_class_name)) {
                throw new Exception(__d('cake_pdf', 'PDFreactor: Client "{0}" not found', $client));
            }
            $client = new $client_class_name($service_url);

            // Set api key
            if (isset($api_key)) {
                $client->apiKey = $api_key;
            }
        }

        // Check client methode "convertAsBinary" exists
        if (! method_exists($client, 'convertAsBinary')) {
            throw new Exception(__d('cake_pdf', 'PDFreactor: Missing method "convertAsBinary" for client "{0}"', get_class($client)));
        }

        // Retur instance
        return $client;
    }

    /**
     * Create the pdf reactor configuration for rendering.
     *
     * @param array $options
     * @param CakePdf $cakepdf
     */
    protected function createConfig(array $options, CakePdf $cakepdf)
    {
        // Set config
        $config = $options;

        // Set document to render
        $config['document'] = $cakepdf->html() ?: '<html />';

        // Return config
        return $config;
    }

    /**
     *
     * @param object $pdfReactor
     * @param \CakePdf\Pdf\CakePdf $cakepdf
     * @throws Exception
     * @return string
     */
    protected function _output($pdfReactor, $config)
    {
        try {
            // Convert as binary and return result
            return $pdfReactor->convertAsBinary($config);
        } catch (Exception $ex) {
            throw new Exception(__d('cake_pdf', 'PDFreactor: {0}', $ex->getMessage()), $ex->getCode(), $ex);
        }
    }
}

