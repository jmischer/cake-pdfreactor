<?php
namespace JMischer\CakePDFreactor\Pdf\Engine;

use CakePdf\Pdf\CakePdf;
use CakePdf\Pdf\Engine\AbstractPdfEngine;
use Cake\Core\App;
use Exception;
use com\realobjects\pdfreactor\webservice\client\PDFreactor;

/**
 *
 * @author jmischer
 *        
 */
class PDFreactorEngine extends AbstractPdfEngine
{

    /**
     * Generates Pdf from html.
     *
     * @return string raw pdf data
     */
    public function output(): string
    {
        $client = $this->getConfig('client', []);
        if ($client instanceof PDFreactor) {
            $pdf_reactor = $client;
        } else {
            $pdf_reactor = $this->createInstance($client);
        }

        $config = $this->createConfig($this->_Pdf);

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
    protected function createInstance(array $client): PDFreactor
    {
        $pdf_reactor = new PDFreactor($client['serviceUrl'] ?? null);
        if (isset($client['apiKey'])) {
            $pdf_reactor->apiKey = $client['apiKey'];
        }

        return $pdf_reactor;
    }

    /**
     * Create the pdf reactor configuration for rendering.
     *
     * @param array $options
     * @param CakePdf $cakepdf
     */
    protected function createConfig(CakePdf $cakepdf)
    {
        $config = $this->getConfig('config', []);
        $config += $this->getConfig('options', []);

        $config['document'] = $cakepdf->html() ?: '<html />';

        return $config;
    }

    /**
     *
     * @param PDFreactor $pdfReactor
     * @param \CakePdf\Pdf\CakePdf $cakepdf
     * @throws Exception
     * @return string
     */
    protected function _output(PDFreactor $pdfReactor, $config)
    {
        $config += [
            'async' => false
        ];

        $async = $config['async'];
        unset($config['async']);

        try {
            if ($async) {
                $id = $pdfReactor->convertAsync($config);
                $result = null;
                while ($result === null) {
                    $progress = $pdfReactor->getProgress($id);
                    $this->debug(sprintf('Progress: %s', $progress->progress));

                    if ($progress->finished) {
                        $result = $pdfReactor->getDocument($id);
                    } else {
                        sleep(1);
                    }
                }
            } else {
                $result = $pdfReactor->convert($config);
            }
            return base64_decode($result->document);
        } catch (Exception $ex) {
            throw new Exception(__d('cake_pdf', 'PDFreactor: {0}', $ex->getMessage()), $ex->getCode(), $ex);
        }
    }

    /**
     * @param mixed $msg
     */
    protected function debug(mixed $msg)
    {
        if (!is_string($msg)) {
            $msg = print_r($msg, true);
        }
        \Cake\Log\Log::debug($msg);
    }
}

