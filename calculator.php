<?php

/**
 * Calculator class
 */
class Calculator 
{
	private $logger;
	private $webService;

	public function __construct(ILogger $logger, IWebService $webService)
	{
		$this->logger = $logger;
		$this->webService = $webService;
	}

	public function Add(string $input)
	{
		$delimiters = ['\n'];

		if (strpos($input, '//') === 0) {
			$customDelimitersString = substr($input, 2, strpos($input, '\n') - 2);
			if (strpos($customDelimitersString, '[') !== false && strpos($customDelimitersString, ']') !== false) {
				$customdelimiters = explode('][', substr($customDelimitersString, 1, strlen($customDelimitersString) - 2));
				$delimiters = array_merge($delimiters, $customdelimiters);
			}
			else {
				$delimiters[] = $customDelimitersString;			
			}
		}
		foreach ($delimiters as $delimiter) {
			$input = str_replace($delimiter, ',', $input);
		}

		$numberList = explode(',', $input);
		$invalidNumbers = [];
		$numbersTooBig = 0;

		foreach ($numberList as $number) {
			if ($number < 0) {
				$invalidNumbers[] = $number;
			}
			if ($number > 1000){
				$numbersTooBig += $number;
			}
		}

		if (count($invalidNumbers) > 0) {
			throw new InvalidArgumentException("Negative numbers not allowed ".implode(' ', $invalidNumbers));
		}

		$expectedResult = array_sum($numberList) - $numbersTooBig;

		try {
			$this->logger->write($expectedResult);
		} catch (\Exception $exception) {
			$this->webService->notify($exception->getMessage());
		}

		return $expectedResult;
	}
}


class ILogger
{
	public function write()
	{
		var_dump('toto');
	}
}

class IWebService
{
	public function notify()
	{
	}
}
