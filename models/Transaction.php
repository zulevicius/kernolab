<?php

    class Transaction extends DBEntity
    {
        const STATUS_UNCONFIRMED = 'UNCONFIRMED';
        const STATUS_COMPLETED = 'COMPLETED';
        const PROVIDER_EUR = 'megacash';
        const PROVIDER_DEFAULT = 'supermoney';
        const MAX_TOTAL_CURRENCY_AMOUNT_PER_USER = 1000;
        const MAX_TRANSACTIONS_PER_HOUR = 10;
        const AMOUNT_FOR_FIVE_PERCENT_FEE = 100;

        public function create(
            int $userId,
            string $details,
            string $receiverAccount,
            string $receiverName,
            $amount,
            string $currency
        ) {
            $fee = $this->calculateFee($amount, $userId);
            $allAmountWithFees = $this->getUserCurrencyAmountsSum($userId, $currency) + $amount + $fee;
            if ($allAmountWithFees >= self::MAX_TOTAL_CURRENCY_AMOUNT_PER_USER) {
                return 'Amount of ' . strtoupper($currency) . ' currency allowed to transfer is already reached';
            } elseif ($this->getUserLastHourTransactionsAmount($userId) >= self::MAX_TRANSACTIONS_PER_HOUR) {
                return 'Amount of transactions allowed to make in one hour is exceeded';
            }

            $provider = $this->applyProvider($currency, $details);
            $confirmationCode = $this->generateConfirmationCode();
            $status = self::STATUS_UNCONFIRMED;

            $sql = '
                INSERT INTO transactions (user_id, details, receiver_account, receiver_name, amount, currency, fee, status, confirmation_code, provider)
                     VALUES (:user_id, :details, :receiver_account, :receiver_name, :amount, :currency, :fee, :status, :confirmation_code, :provider)';
            $bindArr = [
                ':user_id' => $userId,
                ':details' => $details,
                ':receiver_account' => $receiverAccount,
                ':receiver_name' => $receiverName,
                ':amount' => $amount,
                ':currency' => $currency,
                ':fee' => $fee,
                ':status' => $status,
                ':confirmation_code' => $confirmationCode,
                ':provider' => $provider
            ];
            $id = $this->dbInsert($sql, $bindArr);
            return $id === '0' ? 'Failed to create transaction' : $this->get($id);
        }

        /**
         * Confirms the oldest unconfirmed transaction (since API does not identify transactions by ID)
         * @param string $code
         * @return int
         */
        public function confirm(string $code): int
        {
            $sql = "
                UPDATE transactions
                   SET status = '" . self::STATUS_COMPLETED . "'
                 WHERE id = (SELECT MIN(id)
                               FROM transactions
                              WHERE status = '" . self::STATUS_UNCONFIRMED . "') AND
                       confirmation_code = :confirmation_code";
            $bindArr = [':confirmation_code' => $code];
            return $this->dbUpdate($sql, $bindArr);
        }

        public function confirmAll(): int
        {
            $sql = "UPDATE transactions SET status = '" . self::STATUS_COMPLETED . "' WHERE status != '" . self::STATUS_COMPLETED . "'";
            return $this->dbUpdate($sql, []);
        }

        public function getUserTransactions(int $userId): array
        {
            $sql = '
                  SELECT id transaction_id, details, receiver_account, receiver_name, amount, currency, fee, status
                    FROM transactions
                   WHERE user_id = :user_id
                ORDER BY date_created DESC';
            $bindArr = [':user_id' => $userId];
            return $this->dbSelect($sql, $bindArr);
        }

        private function applyProvider(string $currency, &$details): string
        {
            if ($currency === 'eur') {
                $provider = self::PROVIDER_EUR;
                $details = substr($details, 0, 20);
            } else {
                $provider = self::PROVIDER_DEFAULT;
                $details .= rand();
            }
            return $provider;
        }

        private function calculateFee($amount, int $userId): float
        {
            $todayTransactionsAmount = $this->getUserTodayTransactionsSum($userId);
            if ($todayTransactionsAmount > self::AMOUNT_FOR_FIVE_PERCENT_FEE) {
                $feeSize = 0.05;
            } else {
                $feeSize = 0.1;
            }
            return round(floatval($amount) * $feeSize, 2);
        }

        private function generateConfirmationCode(): string
        {
            return '111';
        }

        private function getUserCurrencyAmountsSum(int $userId, string $currency): float
        {
            $sql = 'SELECT (IFNULL(SUM(amount), 0) + IFNULL(SUM(fee), 0)) total FROM transactions WHERE user_id = :user_id AND currency = :currency';
            $bindArr = [':user_id' => $userId, ':currency' => $currency];
            $result = $this->dbSelect($sql, $bindArr);
            if ($row = $result[0]) {
                return floatval($row['total']);
            }
            return 0;
        }

        protected function getUserTodayTransactionsSum(int $userId): float
        {
            $sql = 'SELECT IFNULL(SUM(amount), 0) s FROM transactions WHERE user_id = :user_id AND date_created > CURDATE()';
            $bindArr = [':user_id' => $userId];
            $result = $this->dbSelect($sql, $bindArr);
            if ($row = $result[0]) {
                return floatval($row['s']);
            }
            return 0;
        }

        private function getUserLastHourTransactionsAmount(int $userId): int
        {
            $sql = 'SELECT COUNT(*) ct FROM transactions WHERE user_id = :user_id AND date_created > DATE_SUB(NOW(), INTERVAL 1 HOUR)';
            $bindArr = [':user_id' => $userId];
            $result = $this->dbSelect($sql, $bindArr);
            if ($row = $result[0]) {
                return intval($row['ct']);
            }
            return 0;
        }

        private function get($id)
        {
            $sql = '
                  SELECT id transaction_id, details, receiver_account, receiver_name, amount, currency, fee, status
                    FROM transactions
                   WHERE id = :id';
            $bindArr = [':id' => $id];
            $result = $this->dbSelect($sql, $bindArr);
            if ($transaction = $result[0]) {
                return $transaction;
            }
            return 'Transaction not found';
        }
    }