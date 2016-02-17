<?php

include ("phmagick/phmagick.php");
include ("wideimage/WideImage.php");

class Image {

	protected static $instance = null;

	protected function __construct ()
	{
	}

	public static function getInstance ()
	{
		if (is_null (self::$instance)) {
			self::$instance = new self ();
		}
		return self::$instance;
	}

	public function img ($filename, $directory, $width, $height, $crop = true, $cropos = 'center', $maskimage = null, $trans = false, $name = null, $fillbg = null, $altimg = '') {
		if (Config::get ('image_resize_method') == 'im') {
			return $this->img_im ($filename, $directory, $width, $height, $crop, $cropos, $maskimage, $trans, $name, $fillbg, $altimg);
		} else {
			return $this->img_gd ($filename, $directory, $width, $height, $crop, $cropos, $maskimage, $trans, $name, $fillbg, $altimg);
		}
	}

	public function img_im ($filename, $directory, $width, $height, $crop = true, $cropos = 'center', $maskimage = null, $trans = false, $name = null, $fillbg = null, $altimg = '') {
		$folder = (strpos ($directory, '/') && strpos ($directory, '..') !== 0) ? 'media/uploads/' . $directory : substr ($directory, 1);
		if (strpos ($directory, '..') === 0) $folder = '.' . $folder;

		if (!$filename || !file_exists ($folder . $filename)) {
			if (!empty ($altimg) && file_exists ('media/dsg/' . $altimg)) {
				$filename = 'media/dsg/' . $altimg;
			} else {
				return false;
			}
		} else {
			$filename = $folder . $filename;
		}

		$fileSize = @filesize ($filename);
		if ($fileSize > Config::get ("max_img_size") || $fileSize == 0) return false;

		$size = @getImageSize ($filename);
		$mime = explode ("/", $size['mime']);
		@$ext = $mime[1];
		$w = $size[0];
		$h = $size[1];

		if (($maskimage && $trans) || $ext == 'gif') $ext = 'png';

		$new = md5 ($filename . $fileSize . $size[3] . $width . $height . $crop . $cropos . $maskimage . $trans . $name . $fillbg) . "." . $ext;
		if (!empty ($name))
			$new = Page::urlConvert ($name) . '-' . $new;

		if (!file_exists (Config::get ("img_cache_dir") . $new)) {
			switch (strtolower ($ext)) {
				case 'jpg':
				case 'jpeg':
					$quality = Config::get('image_jpeg_quality') ? Config::get('image_jpeg_quality') : 80;
				break;

				case 'png':
					$quality = Config::get('image_png_quality') ? Config::get('image_png_quality') : 8;
				break;

				default:
					$quality = '';
			}

			$image = new phMagick ($filename);
			if (Config::get ("imagemagick_path"))
				$image->setImageMagickPath (Config::get ("imagemagick_path"));

			$image->setDestination (Config::get ("img_cache_dir") . $new)->setImageQuality ($quality);
			if ($crop) {
				if (($width && $width < $w) || ($height && $height < $h)) {

					$_width = $width;
					$_height = $height;

					if ($w / $h <= ($width / $height)) {
						$_height = ceil ($h / ($w / $width));
					} else {
						$_width = ceil ($_height / $h * $w);
					}
					$image->resize ($_width, $_height);
				}

				switch ($cropos) {

					case 'center':
						$image->crop ($width, $height);
					break;

					case 'top':
						$image->crop ($width, $height, 0, 0, 'None');
					break;
				}
			} else {
				if (!empty ($fillbg)) {
					$image->setDestination (Config::get ("img_cache_dir") . $new)->resizeExactlyNoCrop ($width, $height, $fillbg);
				} else {
					$image->setDestination (Config::get ("img_cache_dir") . $new)->resize ($width, $height);
				}

			}

			if ($maskimage) {
				if ($trans) {
					// 					$image = $image->applyMask ($mask, 0, 0);

				} else {
					$image->watermark ('media/dsg/' . $maskimage, phMagickGravity::NorthWest, 100);
				}
			}
// 			$image->sharpen (1);
		}

		return Config::get ("img_cdn_host") . "/" . Config::get ("img_cache_dir") . $new;
	}


	public function img_gd ($filename, $directory, $width, $height, $crop = true, $cropos = 'center', $maskimage = null, $trans = false, $name = null, $fillbg = null, $altimg = '') {
// 		$folder = strpos ($directory, '/') ? 'media/uploads/' . $directory : substr ($directory, 1);
		$folder = (strpos ($directory, '/') && strpos ($directory, '..') !== 0) ? 'media/uploads/' . $directory : substr ($directory, 1);
		if (strpos ($directory, '..') === 0) $folder = '.' . $folder;

		if (!$filename || !file_exists ($folder . $filename)) {
			if (!empty ($altimg) && file_exists ('media/dsg/' . $altimg)) {
				$filename = 'media/dsg/' . $altimg;
			} else {
				return false;
			}
		} else {
			$filename = $folder . $filename;
		}

		$fileSize = @filesize ($filename);
		if ($fileSize > Config::get ("max_img_size") || $fileSize == 0) return false;

		$size = @getImageSize ($filename);
		$mime = explode ("/", $size['mime']);
		@$ext = $mime[1];

		if ($maskimage && $trans) $ext = 'png';
		$ext = strtolower ($ext);

		$new = md5 ($filename . $fileSize . $size[3] . $width . $height . $crop . $cropos . $maskimage . $trans . $name . $fillbg) . "." . $ext;
		if (!empty ($name))
			$new = Page::urlConvert ($name) . '-' . $new;

		if (!file_exists (Config::get ("img_cache_dir") . $new)) {
			switch ($ext) {
				case 'jpg':
				case 'jpeg':
					$quality = Config::get('image_jpeg_quality') ? Config::get('image_jpeg_quality') : 80;
				break;

				case 'png':
					$quality = Config::get('image_png_quality') ? Config::get('image_png_quality') : 8;
				break;

				default:
					$quality = '';
			}

			$image = WideImage::load($filename);
			if ($crop) {
				switch ($cropos) {
					case 'center':
						$image = $image->resize($width, $height, 'outside', 'down')->crop("50%-" . floor ($width / 2), "50%-" . floor ($height / 2), $width, $height);
					break;

					case 'top':
						$image = $image->resize($width, $height, 'outside', 'down')->crop("50%-" . floor ($width / 2), 0, $width, $height);
					break;
				}
			} else {
				if (!empty ($fillbg)) {
					$image = $image->resize($width, $height, 'inside', 'down');
					$fillbg = ($fillbg != 'none') ? $image->allocateColor (Utils::hex2rgb ($fillbg)) : null;
					$image = $image->resizeCanvas ($width, $height, (($width - $image->getWidth()) / 2), (($height - $image->getHeight()) / 2), $fillbg);
				} else {
					$image = $image->resize($width, $height, 'inside', 'down');
				}
			}

			if ($maskimage) {
				$mask = WideImage::load ('media/dsg/' . $maskimage);

				if ($trans) {
					$image = $image->applyMask ($mask, 0, 0);

				} else {
					$image = $image->merge ($mask, 0, 0);
				}
			}
			if (function_exists ('imageconvolution') && $ext != 'png')
				$image->unsharp (80, 0.5, 3);

			$image->saveToFile(Config::get ("img_cache_dir") . $new, $quality);

			unset ($image);
		}

		return Config::get ("img_cdn_host") . "/" . Config::get ("img_cache_dir") . $new;
	}

}
