<?php

    use PHPUnit\Framework\TestCase;

    final class TransactionsAPITest extends TestCase
    {
        /**
         * @runInSeparateProcess
         */
        public function testPost_inittransaction()
        {
            $data = [
                'details' => 'd',
                'receiver_account' => 'ra',
                'receiver_name' => 'rn',
                'amount' => 1,
                'currency' => 'c'
            ];
            ob_start();
            $t = new TransactionsAPI();
            $trueValue = new Response(500, ['message' => 'Not enough values']);
            ob_end_clean();
            $this->assertEquals($t->post_inittransaction($data), $trueValue);
        }

        /**
         * @runInSeparateProcess
         */
        public function testPut_submittransaction()
        {
            $data = [];
            ob_start();
            $t = new TransactionsAPI();
            $trueValue = new Response(500, ['message' => 'Confirmation code not defined']);
            ob_end_clean();
            $this->assertEquals($t->put_submittransaction($data), $trueValue);
        }

        /**
         * @runInSeparateProcess
         */
        public function testGet_getusertransactions()
        {
            $data = [];
            ob_start();
            $t = new TransactionsAPI();
            $trueValue = new Response(500, ['message' => 'User not defined']);
            ob_end_clean();
            $this->assertEquals($t->get_getusertransactions($data), $trueValue);
        }

        /**
         * @runInSeparateProcess
         */
        public function testGet_gettransaction()
        {
            $data = [];
            ob_start();
            $t = new TransactionsAPI();
            $trueValue = new Response(500, ['message' => 'Transaction ID not defined']);
            ob_end_clean();
            $this->assertEquals($t->get_gettransaction($data), $trueValue);
        }
    }