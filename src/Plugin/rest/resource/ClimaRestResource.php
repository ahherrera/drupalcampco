<?php

namespace Drupal\clima\Plugin\rest\resource;

use Drupal\rest\Plugin\ResourceBase;
use Drupal\rest\ResourceResponse;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Psr\Log\LoggerInterface;

/**
 * Provides a resource to get view modes by entity and bundle.
 *
 * @RestResource(
 *   id = "clima_rest_resource",
 *   label = @Translation("Parametros del clima"),
 *   uri_paths = {
 *     "canonical" = "/api/v1/clima"
 *   }
 * )
 */
class ClimaRestResource extends ResourceBase {



  /**
   * Constructs a Drupal\rest\Plugin\ResourceBase object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param array $serializer_formats
   *   The available serialization formats.
   * @param \Psr\Log\LoggerInterface $logger
   *   A logger instance.
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    array $serializer_formats,
    LoggerInterface $logger) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $serializer_formats, $logger);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->getParameter('serializer.formats'),
      $container->get('logger.factory')->get('clima')
    );
  }

  /**
   * Responds to GET requests.
   *
   * Returns a list of bundles for specified entity.
   *
   * @throws \Symfony\Component\HttpKernel\Exception\HttpException
   *   Throws exception expected.
   */
  public function get() {
    $lat = \Drupal::request()->get('lat');
    $lon = \Drupal::request()->get('lon');

    \Drupal::service('page_cache_kill_switch')->trigger();

    $data =  \Drupal::service('clima.api')->obtenerDatosClima($lat, $lon);

    $dia_actual = [
      'temp' => intval($data[0]->temp->day),
      'humedad' => intval($data[0]->humidity),
      'viento' => intval($data[0]->wind_speed),
      'imagen' => 'http://openweathermap.org/img/w/' . $data[0]->weather[0]->icon . '.png',
      'clave' => 'diaactual',
    ];

    $semana = [];
    foreach ($data as $clave => $valor) {
       if ($clave > 0) {
         $temp_dia = [
           'temp' => intval($valor->temp->day),
           'min' => intval($valor->temp->min),
           'imagen' => 'http://openweathermap.org/img/w/' . $valor->weather[0]->icon . '.png',
           'clave' => "dia$clave",
         ];
           array_push($semana,$temp_dia);
       }
    }
    return new ResourceResponse(['actual' => $dia_actual, 'semana' => $semana]);
  }

}
