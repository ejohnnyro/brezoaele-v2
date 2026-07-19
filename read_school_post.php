<?php
/**
 * Temp script to read school post details.
 */
require_once( $_SERVER['DOCUMENT_ROOT'] . '/wp-load.php' );

$post = get_post( 2024 );
if ( !$post ) {
    echo "Post 2024 not found!";
    exit;
}

echo "TITLE: " . $post->post_title . "\n";
echo "METADATA:\n";
$meta = get_post_custom( 2024 );
foreach ( $meta as $key => $values ) {
    echo "  $key: " . implode( ', ', $values ) . "\n";
}
echo "CONTENT:\n" . $post->post_content . "\n";
