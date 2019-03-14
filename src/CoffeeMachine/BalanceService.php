<?php declare(strict_types=1);

namespace CoffeeMachine;

use CoffeeMachine\Exception\Balance\ChangeError;
use CoffeeMachine\Exception\Balance\IncorrectMoney;
use CoffeeMachine\Exception\Balance\NotEnoughMoney;

class BalanceService
{
    /**
     * @var int
     */
    private $balance = 0;

    /**
     * @var array
     */
    private $correctMoneyDenominations;

    /**
     * BalanceService constructor.
     *
     * @param array $correctMoneyDenominations
     */
    public function __construct(array $correctMoneyDenominations)
    {
        $this->correctMoneyDenominations = $correctMoneyDenominations;
    }

    /**
     * @return int
     */
    public function getBalance(): int
    {
        return $this->balance;
    }

    /**
     * @param int $money
     *
     * @throws IncorrectMoney
     */
    public function add(int $money): void
    {
        $this->ensureMoneyHasCorrectDenomination($money);
        $this->balance += $money;
    }

    /**
     * @param int $money
     *
     * @throws NotEnoughMoney
     */
    public function sub(int $money): void
    {
        $this->ensureEnoughMoneyToSubtract($money);
        $this->balance -= $money;
    }

    /**
     * @param int $money
     *
     * @throws NotEnoughMoney
     */
    private function ensureEnoughMoneyToSubtract(int $money): void
    {
        if ($this->balance < $money) {
            throw new NotEnoughMoney();
        }
    }

    /**
     *
     *
     * @param int $money
     *
     * @throws IncorrectMoney
     */
    private function ensureMoneyHasCorrectDenomination(int $money): void
    {
        if (!in_array($money, $this->correctMoneyDenominations, true)) {
            throw new IncorrectMoney();
        }
    }

    /**
     * Change should be returned in maximum denominations (100 x 1, not 10 x 10)
     *
     * @param bool $keepRestBalance
     *
     * @return array $denomination => $count
     * @throws ChangeError
     */
    public function change(bool $keepRestBalance = false): array
    {
        $change = [];
        $returnDenominations = $this->correctMoneyDenominations;
        rsort($returnDenominations);
        $restBalance = $this->balance;

        foreach ($returnDenominations as $denomination) {
            $denominationsCount = intdiv($restBalance, $denomination);
            if ($denominationsCount) {
                $change[$denomination] = $denominationsCount;
            }

            $restBalance -= $denominationsCount * $denomination;
            if ($restBalance === 0) {
                break;
            }
        }

        if ($restBalance > 0 && !$keepRestBalance) {
            throw new ChangeError();
        }

        $this->balance = $restBalance;

        return $change;
    }
}