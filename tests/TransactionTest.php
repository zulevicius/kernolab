<?php

    use PHPUnit\Framework\TestCase;

    final class TransactionTest extends TestCase
    {
        protected static function getMethod($name)
        {
            $class = new ReflectionClass('Transaction');
            $method = $class->getMethod($name);
            $method->setAccessible(true);
            return $method;
        }

        public function testApplyProvider()
        {
            $method = self::getMethod('applyProvider');
            $obj = new Transaction();
            $details = 'details details details';
            $provider = $method->invokeArgs($obj, array('eur', &$details));
            $this->assertEquals($provider, 'megacash');
            $this->assertEquals($details, 'details details deta');
        }

        public function testGenerateConfirmationCode()
        {
            $method = self::getMethod('generateConfirmationCode');
            $obj = new Transaction();
            $confirmationCode = $method->invokeArgs($obj, array());
            $this->assertEquals($confirmationCode, '111');
        }
    }