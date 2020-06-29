<?php

namespace Drupal\aa_whatsapp\whatsapp;

use Drupal\Core\Site\Settings;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\StringTranslation\TranslationInterface;
use Psr\Log\LoggerInterface;
use GuzzleHttp\Client;

/**
 * WhatsApp class to handle with Maytapi.
 */
class WhatsApp {

  use StringTranslationTrait;

  /**
   * A logger instance.
   *
   * @var \Psr\Log\LoggerInterface
   */
  protected $logger;

  /**
   * Hello sign connectivity credentials.
   *
   * @var array
   */
  protected $credentials;

  /**
   * Guzzle http client.
   *
   * @var \GuzzleHttp\Client
   */
  protected $client;

  /**
   * Maytapi base URI.
   *
   * @see http://maytapi.com
   */
  public const MAYTAPI_BASE_URI = 'https://api.maytapi.com/api/';

  /**
   * Establishes the connection to WhatsApp.
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
    $this->credentials = $this->getCredentials();
    $this->client = $this->getClient();
  }

  public function getClient() {
    return new Client([
      // Base URI is used with relative requests
      'base_uri' => self::MAYTAPI_BASE_URI . $this->credentials['product_id'] . '/' . $this->getMyPhoneId() . '/',
      // You can set any number of default request options.
      'timeout'  => 9.0,
    ]);
  }

  /**
   * Get my current phone id.
   *
   * @return string
   *   Phone id.
   */
  public function getMyPhoneId() : string {
    // My phone number UNIQUE KEY CODE that I can get it in.
    // We can get it by executing.
    // So we need to create a http guzzle request.
    // curl -X GET "https://api.maytapi.com/api/102f9c87-3061-49ab-a3ca-488ce4f9fab1/listPhones" -H "accept: application/json" -H "x-maytapi-key: 6f161c44-ca72-49b1-9f8d-9eed6271ea19"
    return $this->credentials['phone_id'];
  }

  /**
   * Get WhatsApp credentials.
   *
   * @return array
   *   Array of credentials.
   *   [
   *    'api_key' => '',
   *    'client_id' => '',
   *   ]
   *
   * @throws \Exception
   */
  public function getCredentials(): array {
    $settings = Settings::get('aa_whatsapp');
    if (empty($settings['whatsapp']['credentials']['api_key'])) {
      throw new \RuntimeException($this->t('Could not connect to WhatsAp because no API key has been set.'));
    }
    if (empty($settings['whatsapp']['credentials']['product_id'])) {
      throw new \RuntimeException($this->t('WhatsApp Product ID must be set in order to use service.'));
    }
    return $settings['whatsapp']['credentials'];
  }

  /**
   * Send message to WhatsApp
   *
   * @param string $to_number
   *   The number to send a message.
   * @param string $message
   *   The body message to send.
   *
   * @return bool
   *   TRUE if message was sent else FALSE.
   */
  public function sendMessage(string $to_number, string $message) :? bool {
    $response = $this->client->post('sendMessage', [
      'headers' => [
        'Content-Type' => 'application/json',
        'x-maytapi-key' => $this->credentials['api_key'],
      ],
      'json' => [
        'to_number' => $to_number,
        'type' => 'text',
        'message' => '🤖:' . $message,
      ],
    ]);
    $data = json_decode($response->getBody());
    return !empty($data->success);
  }
}
