<?php

namespace App\Mail\Transport;

use Illuminate\Http\Client\Factory as HttpFactory;
use Symfony\Component\Mailer\Exception\TransportException;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\AbstractTransport;
use Throwable;

class GmailApiTransport extends AbstractTransport
{
    public function __construct(
        private readonly HttpFactory $http,
        private readonly string $clientId,
        private readonly string $clientSecret,
        private readonly string $refreshToken,
        private readonly int $timeout = 20,
    ) {
        parent::__construct();
    }

    protected function doSend(SentMessage $message): void
    {
        $this->validateConfiguration();

        try {
            $tokenResponse = $this->http
                ->asForm()
                ->acceptJson()
                ->timeout($this->timeout)
                ->retry(2, 250, throw: false)
                ->post('https://oauth2.googleapis.com/token', [
                    'client_id' => $this->clientId,
                    'client_secret' => $this->clientSecret,
                    'refresh_token' => $this->refreshToken,
                    'grant_type' => 'refresh_token',
                ]);

            if (! $tokenResponse->successful()) {
                $reason = $tokenResponse->json('error_description')
                    ?? $tokenResponse->json('error')
                    ?? 'Google menolak permintaan access token.';

                throw new TransportException(
                    sprintf('Gagal memperoleh access token Gmail API: %s', $reason)
                );
            }

            $accessToken = (string) $tokenResponse->json('access_token');

            if ($accessToken === '') {
                throw new TransportException('Respons OAuth Google tidak memuat access token.');
            }

            $rawMessage = rtrim(
                strtr(base64_encode($message->toString()), '+/', '-_'),
                '='
            );

            $sendResponse = $this->http
                ->withToken($accessToken)
                ->acceptJson()
                ->asJson()
                ->timeout($this->timeout)
                ->retry(2, 250, throw: false)
                ->post('https://gmail.googleapis.com/gmail/v1/users/me/messages/send', [
                    'raw' => $rawMessage,
                ]);

            if (! $sendResponse->successful()) {
                $reason = $sendResponse->json('error.message')
                    ?? $sendResponse->body()
                    ?: 'Google menolak pengiriman email.';

                throw new TransportException(
                    sprintf('Pengiriman melalui Gmail API gagal: %s', $reason)
                );
            }

            $gmailMessageId = (string) $sendResponse->json('id');

            if ($gmailMessageId !== '') {
                $message->setMessageId($gmailMessageId);
            }
        } catch (TransportException $exception) {
            throw $exception;
        } catch (Throwable $exception) {
            throw new TransportException(
                sprintf('Koneksi ke Gmail API gagal: %s', $exception->getMessage()),
                is_int($exception->getCode()) ? $exception->getCode() : 0,
                $exception
            );
        }
    }

    private function validateConfiguration(): void
    {
        $missing = [];

        if (trim($this->clientId) === '') {
            $missing[] = 'GMAIL_CLIENT_ID';
        }

        if (trim($this->clientSecret) === '') {
            $missing[] = 'GMAIL_CLIENT_SECRET';
        }

        if (trim($this->refreshToken) === '') {
            $missing[] = 'GMAIL_REFRESH_TOKEN';
        }

        if ($missing !== []) {
            throw new TransportException(
                'Konfigurasi Gmail API belum lengkap: '.implode(', ', $missing)
            );
        }
    }

    public function __toString(): string
    {
        return 'gmail-api';
    }
}
