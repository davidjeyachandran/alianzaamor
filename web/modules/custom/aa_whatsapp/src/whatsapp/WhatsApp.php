<?php

namespace Drupal\aa_whatsapp\whatsapp;

use Drupal\Core\StringTranslation\TranslationInterface;
use HelloSign\Client;
use Psr\Log\LoggerInterface;

/**
 * WhatsApp class to handle with Maytapi.
 */

class WhatsApp {

  /**
   * Establishes the connection to HelloSign.
   *
   * @param \Psr\Log\LoggerInterface $logger
   *   A logger instance.
   * @param \Drupal\Core\StringTranslation\TranslationInterface $string_translation
   *   The string translation service.
   *
   * @throws \Exception
   */
  public function __construct(LoggerInterface $logger, TranslationInterface $string_translation) {
    $this->logger = $logger;
    $this->stringTranslation = $string_translation;

    // $this->credentials = $this->getHelloSignCredentials();

    // $this->client = new Client($this->credentials['api_key']);
  }

  /**
   *
   */
  public function sendMessage(string $to_number, $message) {

  }
}
