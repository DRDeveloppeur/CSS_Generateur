<?php
function spriter($files = array(), $dest = 'sprite.png', $imgwidth = 12000, $imgheight = 900) {
    // calcule la taille de l'image
    $height = $imgheight;
    $files_tmp = array();
    $width = $imgwidth;

    foreach ($files as $key => $file) {
      list($w[$key], $h[$key], $t) = getimagesize($file);
      $files_tmp[] = array('file' => $file, 'type' => $t);

    }

    // créé l'image vide
    $img = imagecreatetruecolor($width, $height);
    $background = imagecolorallocatealpha($img, 5, 5, 5, 127);
    imagefill($img, 0, 0, $background);
    imagealphablending($img, false);
    imagesavealpha($img, true);

// ajoute les images de la gauche vers la droite
    foreach ($files_tmp as $file) {
      $tmp[] = imagecreatefrompng($file['file']);
    }
      for ($i=0; $i < count($files); $i++) {
        if ($file['type'] == IMAGETYPE_PNG) {
          if ($i == 0){
            echo "OK".PHP_EOL;
            imagecopyresampled($img, $tmp[$i], 0, 0, 0, 0, $w[$i], $h[$i], $w[$i], $h[$i]);
          }
          if ($i > 0)
            echo "OK. ";
            imagecopyresampled($img, $tmp[$i], $w[0]*$i, 0, 0, 0, $w[0], $h[0], $w[$i], $h[$i]);
          } else {
            die('Erreur : type d\'image incorrect');
          }
        }
    // sauvegarder l'image
    if (empty($dest)) {
        header('Content-Type: images/png');
        imagepng($img);
    } else {
        imagepng($img, $dest . '.png');
    }
}

spriter(glob('images/*.png'), 'Spriteimg', 1900, 1000);
?>
