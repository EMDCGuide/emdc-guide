<?php
namespace com\cminds\registration\helper;

class CSVHelper {
	
	static function downloadCSV(array $data, $filename) {
		// output headers so that the file is downloaded rather than displayed
		header('Content-Type: text/csv; charset=utf-8');
		header('Content-Disposition: attachment; filename='. $filename .'.csv');
		$output = fopen('php://output', 'w');
		foreach ($data as $row) {
			fputcsv($output, $row);
		}
		fclose($output);
		exit;
	}
	
}