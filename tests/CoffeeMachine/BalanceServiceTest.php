<?php declare(strict_types=1);

use CoffeeMachine\Exception\Balance\ChangeError;
use CoffeeMachine\Exception\Balance\IncorrectMoney;
use CoffeeMachine\Exception\Balance\NotEnoughMoney;
use PHPUnit\Framework\TestCase;

class BalanceServiceTest extends TestCase
{
    /**
     * @var \CoffeeMachine\BalanceService
     */
    private $balanceService;

    protected function setUp(): void
    {
        $denominations = [
            10,
            100,
            1000,
        ];
        $this->balanceService = new CoffeeMachine\BalanceService($denominations);
    }

    public function testInitialBalanceIsZero()
    {
        $this->assertEquals(0, $this->balanceService->getBalance());
    }

    public function testMoneyAddedCorrectly()
    {
        $this->balanceService->add(10);
        $this->assertEquals(10, $this->balanceService->getBalance());

        $this->balanceService->add(100);
        $this->assertEquals(110, $this->balanceService->getBalance());

        $this->balanceService->add(1000);
        $this->assertEquals(1110, $this->balanceService->getBalance());
    }

    public function testMoneySubtractedCorrectly()
    {
        $this->balanceService->add(1000);

        $this->balanceService->sub(100);
        $this->assertEquals(900, $this->balanceService->getBalance());

        $this->balanceService->sub(50);
        $this->assertEquals(850, $this->balanceService->getBalance());

        $this->balanceService->sub(1);
        $this->assertEquals(849, $this->balanceService->getBalance());
    }

    public function testThrowExceptionOnNotEnoughMoneyToSubtract()
    {
        $this->expectException(NotEnoughMoney::class);
        $this->balanceService->sub(10);
    }

    public function testThrowExceptionOnIncorrectMoneyAdd()
    {
        $this->expectException(IncorrectMoney::class);
        $this->balanceService->add(37);
    }

    public function testSingleDenominationChange()
    {
        $this->balanceService->add(1000);
        $change = [1000 => 1];
        $this->assertEquals($change, $this->balanceService->change());
        $this->assertEquals(0, $this->balanceService->getBalance());
    }

    public function testZeroBalanceChange()
    {
        $this->assertEquals([], $this->balanceService->change());
        $this->assertEquals(0, $this->balanceService->getBalance());
    }

    public function testSeveralDenominationChange()
    {
        $this->balanceService->add(1000);
        $this->balanceService->add(1000);
        $this->balanceService->sub(40);
        $change = [
            1000 => 1,
            100 => 9,
            10 => 6
        ];
        $this->assertEquals($change, $this->balanceService->change());
        $this->assertEquals(0, $this->balanceService->getBalance());
    }

    public function testThrowExceptionOnChangeError()
    {
        $this->balanceService->add(1000);
        $this->balanceService->sub(1);
        $this->expectException(ChangeError::class);
        try {
            $this->balanceService->change();
        } finally {
            $this->assertEquals(999, $this->balanceService->getBalance());
        }
    }

    public function testKeepChangeOnChangeErrorAndKeepChangeFlagPassed()
    {
        $this->balanceService->add(1000);
        $this->balanceService->sub(1);
        $change = [
            100 => 9,
            10 => 9,
        ];
        $this->assertEquals($change, $this->balanceService->change($keepRestBalance = true));
        $this->assertEquals(9, $this->balanceService->getBalance());
    }
}