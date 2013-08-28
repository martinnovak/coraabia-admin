<?php

namespace Framework\Gallery;

use Gallery;


class ArtAccessor implements Gallery\ImageAccessors\IImageAccessor
{
	public static function getSrc(\Gallery\Gallery $control, $data)
	{
		return $data->art_path;
	}
	
	public static function getHref(\Gallery\Gallery $control, $data)
	{
		return $control->getPresenter()->link('Image:updateArt', array('id' => $data->art_id));
	}
}
