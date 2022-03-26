<?php

namespace LionOffice;

use LionFiles\FILES;
use Dompdf\Dompdf;

class PDF {

	private static Dompdf $dompdf;

	public function __construct() {

	}

	public static function load(): void {
		self::$dompdf = new Dompdf();
	}

	public static function convertToPdf(string $path, string $url): string {
		FILES::folder($url);
		$file_name = ($url . FILES::getName($path) . ".pdf");
		self::$dompdf->loadHtml(file_get_contents($path));
		self::$dompdf->render();
		file_put_contents($file_name, self::$dompdf->output());

		return $file_name;
	}

}