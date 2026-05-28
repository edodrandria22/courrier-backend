<?php

namespace App\Service\mercure;

use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;

class MercureService
{
    private HubInterface $hub;

    public function __construct(HubInterface $hub)
    {
        $this->hub = $hub;
    }

    /**
     * Envoie une mise à jour en temps réel via Mercure
     * * @param string $topic Le sujet (ex: 'https://example.com/chat')
     * @param array $data Les données à envoyer (sera converti en JSON)
     */
    public function sendNotification(string $topic, array $data): void
    {
        $update = new Update(
            $topic,
            json_encode($data)
        );

        $this->hub->publish($update);
    }
}