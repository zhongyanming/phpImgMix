<?php

// 重置图片文件大小
function resize_image($filename, $tmpname, $xmax, $ymax)
{
    $ext = explode(".", $filename);
    $ext = $ext[count($ext)-1];

    if($ext == "jpg" || $ext == "jpeg")
        $im = imagecreatefromjpeg($tmpname);
    elseif($ext == "png")
        $im = imagecreatefrompng($tmpname);
    elseif($ext == "gif")
        $im = imagecreatefromgif($tmpname);

    $x = imagesx($im);
    $y = imagesy($im);

    if($x <= $xmax && $y <= $ymax)
        return $im;

    if($x >= $y) {
        $newx = $xmax;
        $newy = $newx * $y / $x;
    }
    else {
        $newy = $ymax;
        $newx = $x / $y * $newy;
    }

    $im2 = imagecreatetruecolor($newx, $newy);
    imagecopyresized($im2, $im, 0, 0, 0, 0, floor($newx), floor($newy), $x, $y);
    return $im2;
}


/**
 * 图片合并
 * 输入pic_path,bg_path
 * 进行合并
 **/
$pic_path = '';
$bg_path = '';//背景图片

$bg_w = 600; // 背景图片宽度
$bg_h = 600; // 背景图片高度
$background = resize_image($bg_path, $bg_path, $bg_w, $bg_h);
$color = imagecolorallocate($background, 202, 201, 201); // 为真彩色画布创建白色背景，再设置为透明
imagefill($background, 0, 0, $color);
imageColorTransparent($background, $color);
$start_x = 0; // 开始位置X
$start_y = 0; // 开始位置Y
$pic_w = intval($bg_w); // 宽度
$pic_h = intval($bg_h); // 高度
$pathInfo = pathinfo($pic_path);
switch( strtolower($pathInfo['extension']) ) {
    case 'jpg':
    case 'jpeg':
        $imagecreatefromjpeg = 'imagecreatefromjpeg';
        break;
    case 'png':
        $imagecreatefromjpeg = 'imagecreatefrompng';
        break;
    case 'gif':
    default:
        $imagecreatefromjpeg = 'imagecreatefromstring';
        $pic_path = file_get_contents($pic_path);
        break;
}
$resource = $imagecreatefromjpeg($pic_path);
// $start_x,$start_y copy图片在背景中的位置
// 0,0 被copy图片的位置
// $pic_w,$pic_h copy后的高度和宽度
imagecopyresized($background,$resource,$start_x,$start_y,0,0,$pic_w,$pic_h,imagesx($resource),imagesy($resource)); // 最后两个参数为原始图片宽度和高度，倒数两个参数为copy时的图片宽度和高度

header("Content-type: image/jpg");
imagejpeg($background);
imagepng($background, "./result/".time().".png");
?>