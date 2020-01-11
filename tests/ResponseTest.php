<?php

    use PHPUnit\Framework\TestCase;

    final class ResponseTest extends TestCase
    {
        public function testApplyProvider()
        {
            $status = 200;
            $data = ['a' => 1.12, 'b' => 'x'];
            $r = new Response($status, $data);
            $this->assertEquals($r->getHttpStatusCode(), $status);
            $this->assertEquals($r->getJsonData(), json_encode($data));
        }
    }