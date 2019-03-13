<?php
function my_merge_image($first_img_path, $second_img_path)
{
  if (exif_imagetype($first_img_path) != IMAGETYPE_PNG && exif_imagetype($second_img_path) != IMAGETYPE_PNG)
  {
    echo "Vos fichier ne sont pas sous format PNG";
  }else
  {
    $imagesx = imagesx($first_img_path) + imagesx($second_img_path);
    $imagesy = imagesy($first_img_path) + imagesy($second_img_path);
    header('Content-Type: image/png');
    $img = imagecreatefrompng("images.png");
    imagepng($img);
  }
}
my_merge_image('image1.png', 'image2.png');
