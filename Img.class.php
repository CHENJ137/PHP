 <?php

/**
* 缩略图
*/    
interface iImage {
    /**
    * 创建缩略图
    * @param string ori 原始图片路径,以web根目录为起点,/upload/xxxx,而不是D:/www
    * @param int width 缩略后的宽
    * @param int height 缩略后的高
    * @return string 缩略图的路径 以web根目录/ 为起点
    */
    static function thumb($ori , $width=200 , $height=200);
    /**
    * 添加水印
    * @param string ori 原始图片路径,以web根目录为起点,/upload/xxxx,而不是D:/www
    * @param string $water 水印图片
    * @return string 加水印的图片路径
    */
    static function water($ori , $water);
    /**
    * @return string 错误信息
    */
    static function getError();
}

class Img implements iImage {
    public static function thumb($ori , $width=200 , $height=200) {
        $absori = ROOT.$ori;
        $randString = (new Upload() )->randStr();
        // 图片保存路径
        $path = dirname($ori).'/'.$randString.'.png';
        list($ow,$oh,$type) = getimagesize($absori);
        // 支持的原图片格式
        $map = array(
            1=>'imagecreatefromgif',
            2=>'imagecreatefromjpeg',
            3=>'imagecreatefrompng',
            6=>'imagecreatefromwbmp',
            15=>'imagecreatefromwbmp'
            );
        $func = $map[$type];
        $bim = $func($absori);  //创建大画布

        // 创建小画布
        $sim = imagecreatetruecolor($width, $height);
        $white = imagecolorallocate($sim, 255, 255, 255);
        imagefill($sim, 0, 0, $white);

        //生成缩略图
        $rata = min($width/$ow,$height/$oh);
        $rw = $ow * $rata;
        $rh = $oh * $rata;
        imagecopyresampled($sim, $bim, ($width - $rw) / 2, ($height - $rh) / 2, 0, 0, $rw, $rh, $ow, $oh);


        // 保存缩略图
        imagepng($sim , './'.$path);

        // 销毁画布
        imagedestroy($bim);
        imagedestroy($sim);

        // 返回保存路径
        // $path = substr($path, 1);
        return $path;
    }


    /**
    * 添加水印( notice：当前版本只支持png格式的图片)
    * @param string ori 原始图片路径,以web根目录为起点,/upload/xxxx,而不是D:/www
    * @param string $water 水印图片
    * @return string 加水印的图片路径
    */
     public static function water($ori , $water){
        $randString = (new iUpload())->randStr();
        $path = dirname($ori).'/'.$randString.'.jpeg';
        // 创建画布
        $oim = imagecreatefrompng('./'.$ori);
        $wim = imagecreatefrompng('./'.$water);
        //取得原图片的宽高
        list($oimw,$oimh) = getimagesize('./'.$ori);
        list($wimw,$wimh) = getimagesize('./'.$water);

        //添加水印
        imagecopymerge($oim, $wim, $oimw-$wimw, $oimh-$wimh, 0, 0, $wimw, $wimh, 40);

        //保存图片
        imagepng($oim,'./'.$path);
        return $path;

        //销毁画布
        imagedestroy($oim);
        imagedestroy($wim);
    }
    /**
    * @return string 错误信息
    */
    public static function getError() {
        return self::$error;
    } 

}