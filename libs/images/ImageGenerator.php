<?php

class ImageGenerator {

    private static $exts = array("jpg" => "", "gif" => "", "png" => "");

    /**
     * Verify if the extension is supported by this class
     *
     * @param string $ext extension
     * @return boolean true if the extension is supported
     */
    public static function is_supported_img($ext) 
    {
        if (array_key_exists($ext, self::$exts) == true)
        {
            return true;
        }
        return false;
    }

    /**
     * Generates a thumbnail of an image
     *
     * @param string $img_ref url path to the image to resize
     * @param string $ext extension of the image.
     * @param string $filename path with filename of the image thumbnail to generate
     * @example string $filename "/images/name_of_thumbnail.jpg"
     * @param int $tn_width width of the thumbnail
     * @param int $tn_height height of the thumbnail
     *
     * @return boolean true if successful
     */
    public static function GenerateThumbnail($img_ref, $ext, $filename, $tn_width, $tn_height) 
    {
        $ext = strtolower($ext);
        $img_path = $filename;

        $flag = false;
        //MAKE NEW IMAGE OUT OF EXTENSION
        if ($ext == "gif") {
            $src = imagecreatefromgif($img_ref);
            //header("Content-Type: image/gif");
            $flag = true;
        }
        if ($ext == "jpg") {
            $src = imagecreatefromjpeg($img_ref);
            if (headers_sent() == false)
            {
            //header("Content-Type: image/jpeg");
                $flag = true;
            }
        }
        if ($ext == "png") 
        {
            $src = imagecreatefrompng($img_ref);
            //header("Content-Type: image/png");
            $flag = true;
        }

        if ($flag == false)
        {
            return $flag;
        }
        if ($src == false)
        {
            return false;
        }
        $size = getimageSize($img_ref);
        $width = $size[0];
        $height = $size[1];
        $new_height = round($height * ($tn_width / $width));
        $dst = imagecreatetruecolor($tn_width, $new_height);
        $res = imagecopyresized($dst, $src, 0, 0, 0, 0, $tn_width, $new_height, $width, $height);

        if ($res == false)
        { 
            return $res;
        }
        switch ($ext) {
            case "png":
                $res = imagepng($dst, $img_path, 0);
                break;
            case "jpg":
                $res = imagejpeg($dst, $img_path, 98);
                break;
            case "gif":
                $res = imagegif($dst, $img_path);
                break;
            default:
                return false;
        }
        imagedestroy($src);
        imagedestroy($dst);
        header_remove('Content-Type');
        return $res;
    }

}

?>
