<?php

namespace Framework\Diagnostics;

use Nette,
	Model;


class RestPanel extends Panel
{
	const ICO = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAa1JREFUeNpsU7uqAkEMzSyj4tvOR6etvWBvYW9r7R/YyRYWgp1/YSH2gh/hbwgW4vtRmDsn3gzrsoHDzmxyTjKZDDEzxeGMs9ksVyoVrlar3Gw2ebVacVKs+SeIGWPYWkvpdJoymQw5kR+MRiM6Ho80Ho+N56hAEAQMoiJKLBQK8s3lctTtdulyudBsNjNeAGRkVCLWCFYoOSp4OBxouVwaCxU4o1kXiwW9Xi8aDAaSxX05KnC73ejz+Xwrdw5xFotFAciPx8OTYev12iCrAke43+/U6XTYgqTK8/mcns+nIG75fN73AhVcr1epwqoqHCgb2YG4lctl3weQUQXipAIVwA84kwSilYKscRbK6sC5ADjjptlLpZIcARABdF8r0ObAGbXT6cToAWLdEAkRcdgH2+3WdxhEiADD4VAmzK15s9lIEyeTiT8m+rXf779zkEql/PXACXXcRL/fZ92fz2ff4Pf7Te6N/I7ydDpliOx2O98g7TYEtGwIY+14MieBnjMMQ4NKer2ev2etSIH/jUbDk8WSnqibcW6329xqtbher7O7Ka7VaonP+U+AAQBUkGR+cVVtcAAAAABJRU5ErkJggg==';

	
	/** @var array */
	private static $calls = array();
	
		
	/**
	 * @return string
	 */
	public function getTab()
	{
		return '<span title="REST"><img src="' . self::ICO . '" alt="icon" />' . count(self::$calls) . '</span>';
	}
	
	
	/**
	 * @return \Nette\Templating\ITemplate 
	 */
	public function getPanel()
	{
		$template = $this->createTemplate()
				->setFile(__DIR__ . '/templates/restPanel.latte');
		$template->calls = self::$calls;
		return $template;
	}
	
	
	public static function log($request, $result = NULL)
	{
		self::$calls[$request] = $result;
	}
}
