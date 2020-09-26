<?php
namespace Drupal\clima\Services;

use Drupal\file\Entity\File;

class Util {
 
 /**
   * Salvar fichero de forma permanente en la base de datos.
   *
   * @param string $fid
   *   Identificador del fichero.
   */
  public function salvaPermanenteFichero($fid, $modulo) {
    if (is_array($fid)) {
      $fid = array_shift($fid);
    }

    $file = File::load($fid);
    $file->setPermanent();
    $file->save();

    // Add usage file.
    \Drupal::service('file.usage')->add($file, $modulo, $modulo, 1);
  }

  /**
   * Obtener url imagen.
   *
   * @param string $fid
   *   Identificador del fichero.
   */
  public function urlImagen($fid) {    
    $file = File::load(reset($fid));
      return file_create_url($file->getFileUri());
  }

  /**
   * Obtener nombre dias siguiente al actual.
   *
   * @param string $cant
   *   Cantidad de dias.
   */
  public function nombreDias($cant) { 
    $dias = [];     

    for ($i=1; $i <= $cant; $i++) {       
      $fecha = new \DateTime();         
      $fecha->modify("+$i days");   
      array_push($dias, ['nombre' => $fecha->format('l'), 'clave' => "dia$i"]);
    }

    return $dias;
  }

  /**
   * Obtener nombre y la fecha dia actual.
   */
  public function nombreFechaDiaActual() { 
    
    $fecha = new \DateTime();  
    return [
        'nombre' => $fecha->format('l'),
        'fecha' => $fecha->format('d M'),
        'clave' => 'diaactual',
    ];
  }


}
