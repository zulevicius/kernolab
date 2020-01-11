<?php

    class TransactionsAPI extends API
    {
        public function post_inittransaction($data): Response
        {
            if (empty($data['user_id']) ||
                empty($data['details']) ||
                empty($data['receiver_account']) ||
                empty($data['receiver_name']) ||
                empty($data['amount']) ||
                empty($data['currency'])
            ) {
                return new Response(self::HTTP_ERROR, ['message' => 'Not enough values']);
            }
            $t = new Transaction();
            $result = $t->create($data['user_id'], $data['details'], $data['receiver_account'],
                $data['receiver_name'], $data['amount'], $data['currency']);
            if (is_array($result)) {
                return new Response(self::HTTP_OK, $result);
            }
            return new Response(self::HTTP_ERROR, ['message' => $result]);
        }

        public function put_submittransaction($data): Response
        {
            if (empty($data['code'])) {
                return new Response(self::HTTP_ERROR, ['message' => 'Confirmation code not defined']);
            }
            $t = new Transaction();
            $rowsAffected = $t->confirm($data['code']);
            if ($rowsAffected === 1) {
                return new Response(self::HTTP_OK, ['message' => 'Transaction confirmed']);
            }
            return new Response(self::HTTP_ERROR, ['message' => 'No transactions to confirm']);
        }

        public function put_submitalltransactions($data): Response
        {
            $t = new Transaction();
            $rowsAffected = $t->confirmAll();
            return new Response(self::HTTP_OK, ['message' => $rowsAffected . ' transactions confirmed']);
        }

        public function get_getusertransactions($data): Response
        {
            if (empty($data['user_id'])) {
                return new Response(self::HTTP_ERROR, ['message' => 'User not defined']);
            }
            $t = new Transaction();
            $transactions = $t->getUserTransactions($data['user_id']);
            return new Response(self::HTTP_OK, $transactions);
        }
    }