<?php
/**
 * File containing class for manipulation with images
 *
 * @package CL
 * @author Rene Kliment <rene.kliment@gmail.com>
 * @version 1.0
 * @license http://www.fsf.org/licensing/licenses/agpl-3.0.html GNU Affero General Public License - Version 3, 19 November 2007
 *
 * This software is dual-licensed - it is also available under a commercial license,
 * so there's possibility to use this software or its parts in software which is not license-compatible.
 * For more information about licensing, contact the author.
 */

/**
 * Class for manipulation with images
 * @package CL
 */
class CL_Images
{
    /**
     * @var object the instance of the class (Singleton pattern)
     */
    private static $instance;

    /**
     * Nothing there, there is not need of initialization of anything - yet ...
     */
    function __construct()
    {

    }

    /**
     * Function to get the instance of the class
     * 
     * @return object instance of this class
     */
    public static function getInstance()
    {
        if (self::$instance === NULL) {
            self::$instance = new self;
        }
        
        return self::$instance;
    }

    /**
     * This function resizes given image, if it has bigger dimensions, than we need and saves it (also makes chmod 0777 on the file) ...
     * It uses GD or ImageMagick, setting is available to change in CL's config file.
     *
     * @param string $inputFileName the input file to work with
     * @param string $outputFileName the file to write into the final (resized) image
     * @param integer $maxNewWidth maximal width of image
     * @param integer $maxNewHeight maximal height of image
     * @return bool TRUE, if everything was successful (resize and chmod), else FALSE
     */
    function resize($inputFileName, $outputFileName, $maxNewWidth, $maxNewHeight)
    {
        $imageInfo = getimagesize($inputFileName);
        $fileType = $imageInfo['mime'];
        $extension = strtolower(str_replace('.','',substr($outputFileName,-4)));
        $originalWidth = $imageInfo[0];
        $originalHeight = $imageInfo[1];

        if ($originalWidth > $maxNewWidth  OR $originalHeight > $maxNewHeight) {
            $newWidth = $maxNewWidth;
            $newHeight = $originalHeight / ($originalWidth / $maxNewWidth);

            if ($newHeight > $maxNewHeight) {
                $newHeight = $maxNewHeight;
		$newWidth = $originalWidth / ($originalHeight / $maxNewHeight);
            }
            $newWidth = ceil($newWidth);
            $newHeight = ceil($newHeight);
        } else {
            $newWidth = $originalWidth;
            $newHeight = $originalHeight;
        }

        $ok = FALSE;
        if (CL::getConf('CL_Images/engine') == 'imagick-cli') {
            exec("convert -thumbnail ".$newWidth."x".$newHeight." ".$inputFileName." ".$outputFileName);
            $ok = TRUE;
        } elseif (CL::getConf('CL_Images/engine') == 'imagick-php') {
            $image = new Imagick($inputFileName);
            $image->thumbnailImage($newWidth, $newHeight);
            $ok = (bool)$image->writeImage($outputFileName);
            $image->clear();
            $image->destroy();
        } else {
            $out = imageCreateTrueColor($newWidth, $newHeight);

            switch (strtolower($fileType)) {
            case 'image/jpeg':
                $source = imageCreateFromJpeg($inputFileName);
                break;

            case 'image/png':
                $source = imageCreateFromPng($inputFileName);
                break;

            case 'image/gif':
                $source = imageCreateFromGif($inputFileName);
                break;

            default:
                break;
            }

            imageCopyResized($out, $source,0,0,0,0,$newWidth,$newHeight,$originalWidth,$originalHeight);

            switch (strtolower($extension)) {
            case 'jpg':
            case 'jpeg':
                if (imageJpeg($out, $outputFileName)) {
                    $ok = TRUE;
                }
                break;

            case 'png':
                if (imagePng($out, $outputFileName)) {
                    $ok = TRUE;
                }
                break;

            case 'gif':
                if (imageGif ($out, $outputFileName)) {
                    $ok = TRUE;
                }
                break;

            default:
                break;
            }

            imageDestroy($out);
            imageDestroy($source);
        }

        if ($ok AND chmod($outputFileName, 0777)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

}
?>