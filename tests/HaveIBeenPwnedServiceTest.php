<?php

declare(strict_types=1);

namespace Oldas\PwnedPasswords\Tests;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\TransferException;
use Oldas\PwnedPasswords\Exception\CompromisedPasswordException;
use Oldas\PwnedPasswords\HaveIBeenPwnedService;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class HaveIBeenPwnedServiceTest extends TestCase
{
    public function testServiceReportsBadPassword(): void
    {
        $haveIBeenPwnedService = new HaveIBeenPwnedService(
            new Client(),
            5,
            $this->createMock(LoggerInterface::class),
        );

        self::assertTrue($haveIBeenPwnedService->isPwned('password'));
        self::expectException(CompromisedPasswordException::class);
        $haveIBeenPwnedService->validatePassword('password');
    }

    public function testServiceReportsGoodPassword(): void
    {
        $haveIBeenPwnedService = new HaveIBeenPwnedService(
            new Client(),
            5,
            $this->createMock(LoggerInterface::class),
        );

        // The random password should be not leaked
        self::assertFalse($haveIBeenPwnedService->isPwned(random_bytes(20)));
    }

    public function testTimeoutMocked(): void
    {
        $client = $this->createMock(Client::class);
        $client->expects(self::once())
            ->method('request')
            ->willThrowException(new TransferException('timeout'));
        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects(self::once())
            ->method('error');

        $haveIBeenPwnedService = new HaveIBeenPwnedService(
            $client,
            5,
            $logger,
        );

        self::assertNull($haveIBeenPwnedService->isPwned('password'));
    }

    public function testTimeoutReal(): void
    {
        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects(self::once())
            ->method('error');

        $haveIBeenPwnedService = new HaveIBeenPwnedService(
            new Client(),
            0.01,
            $logger,
        );

        self::assertNull($haveIBeenPwnedService->isPwned('password'));
    }
}
