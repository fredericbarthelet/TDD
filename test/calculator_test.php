<?php

use PHPUnit\Framework\TestCase;
use Prophecy\Argument;

require 'calculator.php';

/**
 * Testing class for calculator
 */
class CalculatorTest extends TestCase
{
	private $calculator;
	private $logger;
	private $webservice;

	public function setup()
	{
		$this->logger = $this->prophesize(ILogger::class);
		$this->webservice = $this->prophesize(IWebService::class);
        $this->calculator = new Calculator($this->logger->reveal(), $this->webservice->reveal());
	}

	/**
	 * @dataProvider dataProvider
	 */
	public function testAddData($addInput, $expectedResult)
	{
		$this->assertSame($expectedResult, $this->calculator->Add($addInput));
	}

	public function dataProvider()
	{
		return [
			['', 0],
			['12', 22],
			['10,12', 22],
			['3,1,5,7,4', 20],
			['2,3,2,3,2', 12],
			['2\n3', 5],
			['2\n3,2,5\n3', 15],
			['//?@\n3?@22?@5?@3', 33],
			['1001,2', 2],
			['//[?][l]\n3?5l21l15?7', 51],
			['//[@#?][lui][-]\n2@#?3-6lui4', 15]
		];
	}

	public function testAddNegative()
	{
		$this->expectException(InvalidArgumentException::class);
		$result = $this->calculator->Add('2\n3,2,5\n-3');
	}

	public function testLogAddConfirmed()
	{
		$result = $this->calculator->Add('2,3,2,3,2');
		$this->logger->write($result)->shouldBeCalled();
	}

	public function testNotifyWebServiceOnLogException()
	{
		$this->logger->write(Argument::cetera())->willThrow(new Exception('message'));
		$result = $this->calculator->Add('2,3,2,3,2');
		$this->webservice->notify('message')->shouldBeCalled();
	}
}
