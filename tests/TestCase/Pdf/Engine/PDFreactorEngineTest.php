<?php

namespace JMischer\CakePDFreactor\Test\TestCase\Pdf\Engine;

use Cake\TestSuite\TestCase;
use CakePdf\Pdf\CakePdf;
use JMischer\CakePDFreactor\Pdf\Engine\PDFreactorEngine;

/**
 *
 * @author jmischer
 *        
 */
class PDFreactorEngineTest extends TestCase {
	/**
	 * 
	 * @var \PHPUnit\Framework\MockObject\MockObject
	 */
	private $pdfReactorClient;
	
	/**
	 * 
	 * {@inheritDoc}
	 * @see \Cake\TestSuite\TestCase::setUp()
	 */
	public function setUp() {
		parent::setUp();
		
		// Create pdf reactor client mock
		$this->pdfReactorClient = $this->getMockBuilder('PDFreactor')
			->setMethods(['convertAsBinary'])
			->getMock();
	}
	
	/**
	 * 
	 * {@inheritDoc}
	 * @see \Cake\TestSuite\TestCase::tearDown()
	 */
	public function tearDown() {
		parent::tearDown();
		unset($this->pdfReactorClient);
	}
	
	/**
	 * 
	 */
	public function testOutputRealClient() {
		if (!class_exists(PDFreactorEngine::DEFAULT_WEBSERVICE_CLIENT_CLASS_NAME)) {
			static::markTestSkipped('Test skipped, PDFreactor client class not loaded.');
			return;
		}
		$cakepdf = new CakePdf([
			'engine' => [
				'className' => 'JMischer/CakePDFreactor.PDFreactor'
			]
		]);
		$cakepdf->html("<foo>bar</foo>");
		$output = $cakepdf->engine()->output();
		$this->assertStringStartsWith('%PDF-1.4', $output);
		$this->assertStringEndsWith("%%EOF\n", $output);
	}
	
	/**
	 * Test output of client gets called.
	 */
	public function testOutput() {
		// Configure mock
		$this->pdfReactorClient->expects($this->once())
			->method('convertAsBinary')
			->will($this->returnCallback(function() {
				return "%PDF-1.4 MOCK ... %%EOF\n";
			}));
		
		$Pdf = new CakePdf([
			'engine' => [
				'className' => 'JMischer/CakePDFreactor.PDFreactor',
				'client' => $this->pdfReactorClient
			]
		]);
		$Pdf->html('<foo>bar</foo>');
		$output = $Pdf->engine()->output();
		$this->assertStringStartsWith('%PDF-1.4 MOCK', $output);
		$this->assertStringEndsWith("%%EOF\n", $output);
	}
	
	/**
	 * Test createInstance gets passed the client config.
	 */
	public function testCreateInstance() {
		// Configure mock
		$this->pdfReactorClient->expects($this->once())
			->method('convertAsBinary')
			->will($this->returnCallback(function() {
				return "%PDF-1.4 MOCK ... %%EOF\n";
			}));
			
		// Mock PdfReactorEngine
		$engineClass = $this->getMockClass(PDFreactorEngine::class, ['createInstance']);
		
		// Initialize client configuration
		$client_config = [
			'className' => '\PDFreactor',
			'serviceUrl' => 'http://localhost:9423/service/rest',
			'apiKey' => '1234567890'
		];
		
		$cakePdf = new CakePdf([
			'engine' => [
				'className' => '\\' . $engineClass,
				'client' => $client_config
			],
		]);
		
		// Get the mocked engine from CakePdf instance
		$mock_engine = $cakePdf->engine();
		$mock_engine->expects($this->once())
			->method('createInstance')
			->will($this->returnCallback(function ($options) use ($client_config) {
				$this->assertEquals($options, $client_config);
				return $this->pdfReactorClient;
			}));
		$mock_engine->output();
	}
	
	/**
	 * Test output of client gets called.
	 */
	public function testException() {
		// Configure mock
		$this->pdfReactorClient->expects($this->once())
			->method('convertAsBinary')
			->will($this->returnCallback(function() {
				throw new \Exception("Foo Bar");
			}));
			
		$Pdf = new CakePdf([
			'engine' => [
				'className' => 'JMischer/CakePDFreactor.PDFreactor',
				'client' => $this->pdfReactorClient
			]
		]);
		
		$this->expectException(\Exception::class);
		$Pdf->engine()->output();
	}
	
	/**
	 * Test Exception client not found
	 */
	public function testExceptionClientNotFound() {
		$Pdf = new CakePdf([
			'engine' => [
				'className' => 'JMischer/CakePDFreactor.PDFreactor',
				'client' => [
					'className' => 'FooBar',
					'serviceUrl' => 'http://localhost'
				]
			]
		]);
		$this->expectException(\Exception::class);
		$Pdf->engine()->output();
	}
	
	/**
	 * Test Exception "missing convertAsBinary"
	 */
	public function testExceptionMissingMethod() {
		$Pdf = new CakePdf([
			'engine' => [
				'className' => 'JMischer/CakePDFreactor.PDFreactor',
				'client' => new \stdClass()
			]
		]);
		$this->expectException(\Exception::class);
		$Pdf->engine()->output();
	}
}

