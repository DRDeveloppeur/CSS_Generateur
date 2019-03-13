<?php
//recursive
function scan_dir($dossier){
  global $files;
  if ($ouvrir = opendir($dossier)) {
    while (false !== ($filer = readdir($ouvrir))) {
      if ($filer != "." && $filer != "..") {
        if (is_dir($dossier."/".$filer)) {
          $chemin = $dossier."/".$filer;
          if (!empty($files)) {
            $png = glob($chemin."/*.png");
            $files = array_merge($files, $png);
          }
          scan_dir($chemin);
        }
      }
    }
  }
}

// Spriter

function spriter($files = array(), $dest = 'sprite', $imgwidth = 100, $imgheight = 100, $padding = 0) {
  global $paddings;

  foreach ($files as $key => $file) {  // calcule la taille de l'image
    if (is_array($file)) {
      spriter($file);
    }
    list($w[$key], $h[$key], $t) = getimagesize($file);
    $files_tmp[] = array('file' => $file, 'type' => $t);
  }
  $imgwidth = 100*count($files)+$padding*(count($files)-1);
  $img = imagecreatetruecolor($imgwidth, $imgheight);  // créé l'image vide
  $background = imagecolorallocatealpha($img, 255, 255, 255, 127);
  imagefill($img, 0, 0, $background);
  imagealphablending($img, false);
  imagesavealpha($img, true);
  foreach ($files_tmp as $file) {  // ajoute les images de la gauche vers la droite
    $tmp[] = imagecreatefrompng($file['file']);
  }
  for ($i=0; $i < count($files); $i++) {
    if ($file['type'] == IMAGETYPE_PNG) {
      if ($i == 0){
        imagecopyresampled($img, $tmp[$i], 0, 0, 0, 0, 100, 100, $w[$i], $h[$i]);
      } if ($i > 0)
        $paddings = 100*$i+$padding*$i;
        imagecopyresampled($img, $tmp[$i], $paddings, 0, 0, 0, 100, 100, $w[$i], $h[$i]);
    }
  }
  if (empty($dest)) {  // sauvegarder l'image
      imagepng($img);
  } else {
      imagepng($img, $dest . '.png');
  }
}

// Création du CSS
function css(array $files, $dest = 'style.css', $padding = 0){
    $style = fopen($dest, 'w+');
    fputs($style, '.sprite {'.PHP_EOL.'  background-image: rgba(255, 255, 255, 127);'.PHP_EOL.'  display: inline;'.PHP_EOL.'}'.PHP_EOL.PHP_EOL.PHP_EOL);
    for ($i=0; $i < count($files); $i++) {
      $new_width = 100*$i+$padding*$i;
      $files_1[] = preg_replace('#.+\/#','', $files[$i]);
      $file[] = preg_replace('#\..+#','', $files_1[$i]);
      fputs($style, '.sprite-'.$file[$i].' {'.PHP_EOL.'  width: 100px;'.PHP_EOL.'  height: 100px;'.PHP_EOL.'  background-position: -'.$new_width.'px, -0px;'.PHP_EOL.'}'.PHP_EOL.PHP_EOL);
    }
    fclose($style);
}

// Ligne de commande
$dossier = end($argv);
$files = glob($dossier."/*.png");

if (!is_dir($dossier)) {
  echo "Veuillez indiquer un dossier valid en dernier argument !".PHP_EOL;
  exit;
}
for ($i=1; $i < $argc ; $i++) {
    if ($argv[$i] == "-r" || $argv[$i] == "-recursive") {
      if (is_dir($dossier)) {
      scan_dir($dossier);
    }
  }
}

for ($i=1; $i < $argc ; $i++) {
  if ($argv[$i] == "-p" || $argv[$i] == "-padding") {
    if (!empty($argv[$i+1]) && $argv[$i+1] != $dossier && is_numeric($argv[$i+1])) {
      $padding = $argv[$i+1];
    }
    if (empty($argv[$i+1]) || $argv[$i+1] == $dossier) {
      echo "Vous n'avez pas donner un padding ou il n'est pas valid ! :(".PHP_EOL;
      exit;
    }
  }
}

for ($i=1; $i < $argc ; $i++) {
  if (is_dir($dossier)) {
    if ($argv[$i] == '-i' || $argv[$i] == '-output-image') {
      if (!empty($argv[$i+1]) && $argv[$i+1] != $dossier) {
        spriter($files, $argv[$i+1], 100*count($files), 100, $padding);
        echo "Votre sprite ".$argv[$i+1].".png à partir du dossier ".$dossier." à était crée avec succès.".PHP_EOL;
      }
      if (empty($argv[$i+1]) || $argv[$i+1] == $dossier) {
        echo "Vous n'avez pas donner de nom au sprite ! :(".PHP_EOL;
        exit;
      }
    }
  }
}

if (!in_array("-i", $argv) && !in_array("-output-image", $argv)) {
  spriter($files, $dest = 'sprite', 100*count($files), 100, $padding);
  echo "Votre sprite sprite.png à partir du dossier ".$dossier." à était crée avec succès.".PHP_EOL;
}

for ($i=1; $i < $argc ; $i++) {
  if (is_dir($dossier)) {
    if ($argv[$i] == '-s' || $argv[$i] == '-output-style') {
      if (!empty($argv[$i+1]) && $argv[$i+1] != $dossier) {
        css($files, $argv[$i+1].".css", $padding);
        echo "Votre CSS ".$argv[$i+1].".css à était crée avec succès.".PHP_EOL;
      }
      if (empty($argv[$i+1]) || $argv[$i+1] == $dossier) {
        echo "Vous n'avez pas donner de nom au fichier CSS ! :(".PHP_EOL;
        exit;
      }
    }
  }
}
/***
if (!in_array("-s", $argv) || !in_array("-output-style", $argv) || !in_array("-i", $argv) || !in_array("-output-image", $argv) || !in_array("-r", $argv) || !in_array("-recursive", $argv) || empty($dossier)) {
  echo "Voici les option possible :".PHP_EOL;
}
***/
if (!in_array("-s", $argv) && !in_array("-output-style", $argv)) {
  css($files, "style.css", $padding);
  echo "Votre CSS style.css à était crée avec succès.".PHP_EOL;
}

echo "Merci d'avoir utiliser css_generator ! :)".PHP_EOL.PHP_EOL;
