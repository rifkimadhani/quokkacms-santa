<?php

/**
 * Created by PhpStorm.
 * User: Erick
 * Date: 12/15/2017
 * Time: 12:29 PM
 */
class HelperExif
{

	static public function getExtension($file): string {
		$type = exif_imagetype ( $file);
		switch ($type){
			case IMAGETYPE_GIF:
				return "gif";
			case IMAGETYPE_JPEG:
				return "jpeg";
			case IMAGETYPE_PNG:
				return "png";
			case IMAGETYPE_SWF:
				return "swf";
			case IMAGETYPE_PSD:
				return "psd";
			case IMAGETYPE_BMP:
				return "bmp";
			case IMAGETYPE_TIFF_II:
			case IMAGETYPE_TIFF_MM:
				return "tiff";
			case IMAGETYPE_ICO:
				return "ico";
			case IMAGETYPE_WEBP:
				return "webp";
		}
	}


}