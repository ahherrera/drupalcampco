(function ($, Drupal, drupalSettings) {

  obtenerDatos(drupalSettings.lat, drupalSettings.lon);

}) (jQuery, Drupal, drupalSettings);

function buscarDatosCoordenadas() {
    debugger;
    var coordenadas = jQuery('#coordenadas').val();
    var coord_array = coordenadas.split(',');
    obtenerDatos(coord_array[0], coord_array[1]);
}

function obtenerDatos(lat, lon) {
    jQuery.get(drupalSettings.url, { lat: lat, lon: lon })
        .done(function (data) {
            jQuery('#' + data.actual.clave).text(data.actual.temp);
            jQuery('#viento').text(data.actual.viento);
            jQuery('#humedad').text(data.actual.humedad + '%');
            jQuery('#diactualimagen').attr("src", data.actual.imagen);

            data.semana.forEach(element => {
                jQuery('#' + element.clave).text(element.temp);
                jQuery('#' + element.clave + 'min').text(element.min);
                jQuery('#' + element.clave + 'imagen').attr("src", element.imagen);
            });
        });
}
