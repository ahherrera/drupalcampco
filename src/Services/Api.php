<?php
namespace Drupal\clima\Services;

class Api {

  public function obtenerDatosClima($lat, $lon) {

    $client = \Drupal::httpClient();
    $url = "https://api.openweathermap.org/data/2.5/onecall?lat=$lat&lon=$lon&exclude=hourly,minutely,current&appid=cd83edb463505e78d712089387c676b9&units=metric";
    try {
      $response = $client->get($url);
      $data = json_decode($response->getBody());
      return $data->daily;
    }
    catch (RequestException $e) {
      watchdog_exception('clima', $e);
      return [];
    }
  }

}
