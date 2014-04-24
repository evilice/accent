<?php
/**
 * Created by JetBrains PhpStorm.
 * User: S.Goncharov
 * Date: 03.03.13
 * Time: 17:38
 * To change this template use File | Settings | File Templates.
 */
class Img {
    private $quality = 80;
    private $source;
    private $image;
    private $info = [];

    public function __construct($image) {

        if(file_exists($image)) {
            $this->source = $image;
            $r = getimagesize($image);
            $i = pathinfo($image);
            list($this->info['width'], $this->info['height']) = $r;
            $this->info['mime'] = $r['mime'];
            $this->info['ext'] = $i['extension'];
            $this->info['name'] = $i['filename'];
            $this->info['dirname'] = $i['dirname'];
            switch(strtolower($this->info['ext'])) {
                case 'gif': { $this->image = imagecreatefromgif($image); break; }
                case 'png': { $this->image = imagecreatefrompng($image); break; }
                case 'jpg': {}
                case 'jpeg': { $this->image = imagecreatefromjpeg($image); break; }
                default: return false;
            }
        } return false;
    }

    public function info() { return $this->info; }

    /**
     * @param $w
     * @param int $h
     * @param bool $crop
     * @return Img
     */
    public function resize($w, $h = 0, $crop = false) {
        $step = 0;
        $ratio = ($w/$this->info['width']);
        if($h > $this->info['height']*$ratio) {
            $ratio = ($h/$this->info['height']);
            $wt = $this->info['width']*$ratio;
            $step = (($wt-$w)/2)/$ratio;

            $newImg = imagecreatetruecolor($w, $h);
            imagecopyresampled($newImg, $this->image, 0, 0, $step, 0, $w, $h, $this->info['width']-$step*2, $this->info['height']);
        } else {
            if($h == 0 && !$crop) $h = $this->info['height']*$ratio;
            else $step = round(($this->info['height']-$h/$ratio)/2);

            $newImg = imagecreatetruecolor($w, $h);
            imagecopyresampled($newImg, $this->image, 0, 0, 0, $step, $w, $h, $this->info['width'], $this->info['height']-$step*2);
        }

        $this->image = $newImg;
        return $this;
    }

    public function save($newName = false) {
        $name = ($newName)?$newName:$this->source;
        switch(strtolower($this->info['ext'])) {
            case 'gif': { imagegif($this->image, $name); break; }
            case 'png': { imagepng($this->image, $name); break; }
            case 'jpg': {}
            case 'jpeg': { imagejpeg($this->image, $name); break; }
        }
        imagedestroy($this->image);
        chmod($newName, 0755);
    }
}
