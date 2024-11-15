<?php
namespace App\WebhookServer;

use Spatie\WebhookServer\Signer\Signer as DefaultSigner;

class Signer implements DefaultSigner{

    public function signatureHeaderName(): string{
        return "authorization";
    }
    public function calculateSignature(string $webhookUrl,array $payload, string $secret): string{
        return $secret ;
    }
}
