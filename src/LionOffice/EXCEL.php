<?php

namespace LionOffice;

use LionFiles\FILES;
use PhpOffice\PhpSpreadsheet\{ Spreadsheet, IOFactory };
use PhpOffice\PhpSpreadsheet\Worksheet\{ Worksheet, Drawing };
use PhpOffice\PhpSpreadsheet\Style\{ Fill, Color };
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Reader\Xls;

class EXCEL {

	private static ?Spreadsheet $spreadsheet = null;
	private static ?Worksheet $worksheet = null;
	private static array $list_push = [];

	public function __construct() {

	}

	public static function new(string $path, string $file_name): string {
		FILES::folder($path);
		$file_name = FILES::rename("{$file_name}.xlsx");
		(new Xlsx(new Spreadsheet()))->save("{$path}{$file_name}");
		return $file_name;
	}

	public static function load(string $path): void {
		self::$spreadsheet = IOFactory::createReader("Xlsx")->load($path);
		self::$worksheet = self::$spreadsheet->getActiveSheet();
	}

	public static function merge(string $columns): void {
		self::$worksheet->mergeCells($columns);
	}

	public static function image(string $column, string $path, ?int $height = null): void {
		$drawing = new Drawing();
		$drawing->setCoordinates($column);
		$drawing->setPath($path);
		if ($height != null) $drawing->setHeight($height);
		$drawing->setWorksheet(self::$worksheet);
	}

	public static function size(string $columns, int $size): void {
		self::$worksheet->getStyle($columns)->getFont()->setSize($size);
	}

	public static function bold(string $column): void {
		self::$worksheet->getStyle($column)->getFont()->setBold(true);
	}

	public static function color(string $column, string $color): void {
		self::$worksheet->getStyle($column)->getFont()->getColor()->setARGB($color);
	}

	public static function background(string $column, string $color, ?string $type_color = null): void {
		if (strtoupper($type_color) === 'FILL_SOLID') {
			$setType = Fill::FILL_SOLID;
		} elseif (strtoupper($type_color) === 'FILL_GRADIENT_LINEAR') {
			$setType = Fill::FILL_GRADIENT_LINEAR;
		} else {
			$setType = Fill::FILL_SOLID;
		}

		self::$worksheet->getStyle($column)->getFill()->setFillType($setType)->getStartColor()->setARGB($color);
	}

	public static function insert(int $number): void {
		self::$worksheet->insertNewRowBefore($number);
	}

	public static function push(string $value, string $char): void {
		self::$list_push[$char] = $value;
	}

	public static function add(string $index = null, string $value = null): void {
		if ($index != null && $value != null) {
			self::$worksheet->setCellValue($index, FILES::replace($value));
		} else {
			foreach (self::$list_push as $key => $cell) {
				self::$worksheet->setCellValue(((string) $key), FILES::replace($cell));
			}
		}
	}

	public static function save(string $path, string $file_name): string {
		$file_name = FILES::rename("{$file_name}.xlsx");
		(new Xlsx(self::$spreadsheet))->save("{$path}{$file_name}");
		return $file_name;
	}

}