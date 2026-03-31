<?php
/**
 * Force l'activation des Application Passwords
 * même si le site utilise Basic Auth (.htpasswd staging)
 *
 * Placez ce fichier dans : wp-content/mu-plugins/
 */
add_filter( 'wp_is_application_passwords_available', '__return_true' );
