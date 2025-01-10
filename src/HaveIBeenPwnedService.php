<?php

declare(strict_types=1);

namespace Oldas\PwnedPasswords;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Oldas\PwnedPasswords\Exception\CompromisedPasswordException;
use Psr\Log\LoggerInterface;
use SensitiveParameter;

use function hash;
use function strtolower;
use function substr;

class HaveIBeenPwnedService implements HaveIBeenPwnedServiceInterface
{
    private const API_URL = 'https://api.pwnedpasswords.com/range/';

    public function __construct(
        private Client $client,
        private float $timeoutInSeconds,
        private LoggerInterface $logger,
    ) {
    }

    public function isPwned(
        #[SensitiveParameter]
        string $plaintextPassword,
    ): ?bool {
        $hashPassword = hash('sha1', $plaintextPassword);
        $prefix = substr($hashPassword, 0, 5);

        try {
            $hashOccurrences = $this->getHashOccurrences($prefix);
        } catch (GuzzleException $e) {
            $this->logger->error($e->getMessage()); // skip password validation

            return null;
        }
        foreach ($hashOccurrences as $foundHash) {
            if ($foundHash === $hashPassword) {
                return true;
            }
        }

        return false;
    }

    public function validatePassword(
        #[SensitiveParameter]
        string $plaintextPassword,
    ): void {
        $isPwned = $this->isPwned($plaintextPassword);
        if ($isPwned === true) {
            throw CompromisedPasswordException::create();
        }
    }

    /**
     * @return string[]
     *
     * @throws GuzzleException
     */
    private function getHashOccurrences(string $hashPrefix): array
    {
        $response = $this->client->request(
            'GET',
            self::API_URL . $hashPrefix,
            [
                'timeout' => $this->timeoutInSeconds,
            ],
        );
        $contents = (string)$response->getBody();
        $hashes = [];
        foreach (explode("\r\n", $contents) as $line) {
            $result = explode(':', $line);
            $hashes[] = strtolower($hashPrefix . $result[0]);
        }

        return $hashes;
    }
}
