<?php

require_once BASE_DIR . LIBS . IMAGES . IMAGEGENERATOR;

/**
 * Description of uploader
 *
 * @author Gabriel
 */
class FileUploader 
{

    /**
     * Associative array of file extension
     * pointing to size limit(bytes)
     * array("ext"=>5000)
     * @var array
     */
    private $types = array();

    /**
     *
     * @param array Associative array of file extension with size limit(bytes)
     * array("ext"=>5000)
     *
     */
    public function __construct($typesarr) 
    {
        $this->types = $typesarr;
    }

    /**
     * validates if an file is an image supported
     * by the thumbnail generator
     *
     * @param string $path string with the image path
     * @return boolean true if is an image
     */
    public static function is_file_image($path) 
       {
        if (file_exists($path) != true)
        {
            return false;
        }
        $type = explode(".", $path);
        $type_r = $type[count($type) - 1];
        if (ImageGenerator::is_supported_img($type_r) == false)
        {
            return false;
        }
        return true;
    }

    /**
     * Upload the file to the specified directory
     * @param array  $file $_FILE element to upload
     * @param string $dir directory to upload
     *
     * @return int|string new file name, UPLOAD_ERR_INI_SIZE: 1
     * UPLOAD_ERR_FORM_SIZE: 2, UPLOAD_ERR_NO_TMP_DIR: 6
     * UPLOAD_ERR_CANT_WRITE: 7, UPLOAD_ERR_EXTENSION: 8
     * UPLOAD_ERR_PARTIAL: 3 if $_FILE['n']['error']
     * throws an error, -1 if is not a $_FILE element,
     * -2 if the extension is invalid, -3 if file limit
     * size exceeds, -4 if false was returned in php function
     * move_uploaded_file().
     *
     */
    public function uploadfile($file, $dir) {
        if (!isset($file['error']))
        {
            return -1;
        }
        if ($file['error'] > 0)
        {
            return $file['error'];
        }
        $fpath = $dir;
        $img_name = uniqid();
        $type = explode(".", $file['name']);
        $type_r = $type[count($type) - 1];
        $img_name = $img_name . "." . $type_r;
        $fpath.=$img_name;

        if (array_key_exists($type_r, $this->types) == false)
        {
            return -2;
        }
        $size = $file['size'];
        if ($size >= $this->types[$type_r]) 
        {
            return -3;
        }
        if (move_uploaded_file($file['tmp_name'], $fpath)) 
        {
            return $img_name;
        } 
        else 
        {
            return -4;
        }
    }

    /**
     * Upload an image and generates its thumbnail to the
     * specified directory
     *
     * @param array  $file $_FILE element to upload
     * @param string $dir directory to upload
     * @param int file size limit in bytes
     * @param int tn_width thumbnail width in px
     * @param int tn_height thumbnail height in px
     * @param bool generate_thumbs turn thumb generation on or off
     *
     * @return int|string new file name, UPLOAD_ERR_INI_SIZE: 1
     * UPLOAD_ERR_FORM_SIZE: 2, UPLOAD_ERR_NO_TMP_DIR: 6
     * UPLOAD_ERR_CANT_WRITE: 7, UPLOAD_ERR_EXTENSION: 8
     * UPLOAD_ERR_PARTIAL: 3 if $_FILE['n']['error']
     * throws an error, -1 if is not a $_FILE element,
     * -2 if the extension is invalid, -3 if file limit
     * size exceeds, -4 if false was returned in php function
     * move_uploaded_file().
     */
    public static function uploadimage($file, $dir, $size_limit, $tn_width, $tn_height, $generate_thumbs = true) {
        if (!isset($file['error']))
        {
            return -1;
        }
        if ($file['error'] > 0)
        {
            return $file['error'];
        }
        $fpath = $dir;
        $img_name = uniqid();
        $type = explode(".", $file['name']);
        $type_r = $type[count($type) - 1];
        $img_thumb = $img_name . "_thumb" . "." . $type_r;
        $img_name = $img_name . "." . $type_r;
        $fpath.=$img_name;

        if (ImageGenerator::is_supported_img($type_r) == false)
        {
            return -2;
        }
        $size = $file['size'];
        if ($size >= $size_limit)
        {
            return -3;
        }
        if (move_uploaded_file($file['tmp_name'], $fpath)) 
        {
            if ($generate_thumbs)
            {    
                ImageGenerator::GenerateThumbnail($fpath, $type_r, $dir . $img_thumb, $tn_width, $tn_height);
            }
            return array($img_name, $img_thumb);
        }
        else
        {
            return -4;
        }
    }

    /**
     * Gets the file size in bytes of a file type
     *
     * @param string $filetype file type extension
     * @return integer size of the file, -1 if file type is not defined in class
     */
    private function getExtSize($filetype) 
    {
        if (isset($this->types[$filetype]))
        {
            return $this->types[$filetype];
        }
        else
        {
            return -1;
        }
    }

    /**
     * Upload an image and generates its thumbnail to the
     * specified directory
     *
     * @param array  $file $_FILE element to upload
     * @param string $dir directory to upload
     * @param int $tn_width thumbnail width in px
     * @param int $tn_height thumbnail height in px
     * @param boolean  $delete_original true if the original image have to be deleted.
     * this mode is useful for resizing images.
     * @return int|string new file name, UPLOAD_ERR_INI_SIZE: 1
     * UPLOAD_ERR_FORM_SIZE: 2, UPLOAD_ERR_NO_TMP_DIR: 6
     * UPLOAD_ERR_CANT_WRITE: 7, UPLOAD_ERR_EXTENSION: 8
     * UPLOAD_ERR_PARTIAL: 3 if $_FILE['n']['error']
     * throws an error, -1 if is not a $_FILE element,
     * -2 if the extension is invalid, -3 if file limit
     * size exceeds, -4 if false was returned in php function
     * move_uploaded_file().
     */
    public function _uploadimage($file, $dir, $tn_width = 190, $tn_height = 135, $delete_original = false) 
    {
        if (!isset($file['error']))
        {
            return -1;
        }
        if ($file['error'] > 0)
        {
            return $file['error'];
        }
        $fpath = $dir;
        $img_name = uniqid();
        $type = explode(".", $file['name']);
        $type_r = $type[count($type) - 1];
        $img_thumb = $img_name . "_thumb" . "." . $type_r;
        $img_name = $img_name . "." . $type_r;
        $fpath.=$img_name;

        if (ImageGenerator::is_supported_img($type_r) == false)
        {
            return -2;
        }
        $size = $file['size'];
        $s = $this->getExtSize($type_r);

        if ($s == -1)
        {
            return -2;
        }

        if ($size >= $s) 
        {
            return -3;
        }
        if (move_uploaded_file($file['tmp_name'], $fpath)) 
        {
            $generated = ImageGenerator::GenerateThumbnail($fpath, $type_r, $dir . $img_thumb, $tn_width, $tn_height);
            if ($generated == true) 
            {
                if ($delete_original == true) 
                {
                    $this->deletefile($fpath);
                    return $img_thumb;
                }
                else
                {
                    return array($img_name, $img_thumb);
                }
            }
        } 
        else 
        {
            return -4;
        }
    }

    /**
     * deletes a file
     *
     * @param string $filepath path of the file
     * to delete
     * @return boolean true on success
     */
    public function deletefile($filename) 
    {
        $filepath = BASE_DIR . DS . $filename;
        if (is_file($filepath) == true) {
            return unlink($filepath);
        }
        return false;
    }

}

?>
