<?php

namespace Drupal\clima\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a 'Clima' Block.
 *
 * @Block(
 *   id = "clima_block",
 *   admin_label = @Translation("Clima block"),
 * )
 */
class ClimaBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {

      $form['config']['texto_informativo'] = [
        '#title' => 'Texto ayuda buscador',
        '#type' => 'textfield',
        '#default_value' => $this->configuration['config']['texto_informativo'] ?? 'Introducir latitud, longitud....'
      ];
      $form['config']['btn_texto'] = [         
        '#title' => 'Texto botón buscador', 
        '#type' => 'textfield',
        '#default_value' => $this->configuration['config']['btn_texto'] ?? 'Buscar'
      ];
      $form['config']['unidad_medida_viento'] = [
        '#title' => 'Unidad de medida del viento', 
        '#type' => 'textfield',
        '#default_value' => $this->configuration['config']['unidad_medida_viento'] ?? 'm/s'
      ];
      $form['config']['lat'] = [
        '#title' => 'Latitud inicial', 
        '#type' => 'textfield',
        '#default_value' => $this->configuration['config']['lat'] ?? '33.441792'
      ];
      $form['config']['lon'] = [
        '#title' => 'Longitud', 
        '#type' => 'textfield',
        '#default_value' => $this->configuration['config']['lon'] ?? '-94.037689'
      ];
      $form['config']['imagen_humedad'] = [
        '#type' => 'managed_file',
        '#title' => t('Imagen de la humedad'),
        '#default_value' => $this->configuration['config']['imagen_humedad'] ?? '',
        '#description' => t('El icono debe medir entre 20x20 pixeles y 50x50 pixeles, de extension png jpg jpeg'),
        '#upload_location' => 'public://',
        '#upload_validators' => [
          'file_validate_image_resolution' => ['50x50'],
          'file_validate_extensions' => ['png jpg jpeg'],
        ],
      ];
      $form['config']['imagen_viento'] = [
        '#type' => 'managed_file',
        '#title' => t('Imagen del viento'),
        '#default_value' => $this->configuration['config']['imagen_viento'] ?? '',
        '#description' => t('El icono debe medir entre 20x20 pixeles y 50x50 pixeles, de extension png jpg jpeg'),
        '#upload_location' => 'public://',
        '#upload_validators' => [
          'file_validate_image_resolution' => ['50x50'],
          'file_validate_extensions' => ['png jpg jpeg'],
        ],
      ];
      return $form;
  }

   /**
   * {@inheritdoc}
   */ 
  public function blockSubmit($form, FormStateInterface $form_state) {
    $this->configuration['config'] = $form_state->getValue('config');
    $servicio_util = \Drupal::service('clima.util');
    $servicio_util->salvaPermanenteFichero($this->configuration['config']['imagen_humedad'], 'clima');
    $servicio_util->salvaPermanenteFichero($this->configuration['config']['imagen_viento'], 'clima');
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $servicio_util = \Drupal::service('clima.util');
    $imagen_humedad = $servicio_util->urlImagen($this->configuration['config']['imagen_humedad']);
    $imagen_viento = $servicio_util ->urlImagen($this->configuration['config']['imagen_viento']);
    $dias = $servicio_util->nombreDias(6);
    $dia_actual = $servicio_util->nombreFechaDiaActual();
    
    $build = [
      '#theme' => 'clima',
      '#attached' => [
        'library' => [
          'clima/clima-recursos',
        ],
        'drupalSettings' => [
            'url' => '/api/v1/clima',
            'lat' => $this->configuration['config']['lat'],
            'lon' => $this->configuration['config']['lon'],
        ]
      ],
      '#plugin_id' => $this->getPluginId(),
      '#texto_informativo' => $this->configuration['config']['texto_informativo'],
      '#btn_texto' => $this->configuration['config']['btn_texto'],
      '#unidad_medida_viento' => $this->configuration['config']['unidad_medida_viento'],
      '#imagen_viento' => $imagen_viento,
      '#imagen_humedad' => $imagen_humedad,
      '#dias' => $dias,
      '#dia_actual' => $dia_actual,
    ];
   return $build;
  }

}
