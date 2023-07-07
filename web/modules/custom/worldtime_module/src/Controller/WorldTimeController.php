<?php

namespace Drupal\worldtime_module\Controller;

use Drupal\Core\Controller\ControllerBase;
use GuzzleHttp\Client;
use Symfony\Component\DependencyInjection\ContainerInterface;

class WorldTimeController extends ControllerBase {

  private $client;

  public function __construct(Client $client) {
    $this->client = $client;
  }

  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('http_client')
    );
  }

  public function content() {
    $cities = ['America/Costa_Rica', 'America/New_York', 'Europe/Belgrade'];
    $times = [];

    foreach ($cities as $city) {
      $response = $this->client->request('GET', "http://worldtimeapi.org/api/timezone/$city");
      $content = $response->getBody()->getContents();
      $data = json_decode($content, TRUE);
      $times[] = [
        'city' => $city,
        'time' => $data['datetime'],
      ];
    }

    return [
      '#theme' => 'worldtime_table',
      '#times' => $times,
    ];
  }
}
