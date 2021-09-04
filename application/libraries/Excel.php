<?php 
if (!defined('BASEPATH')) exit('No direct script access allowed');  
 
require_once APPPATH."/third_party/PHPExcel-1.8.0/PHPExcel.php";
require_once 'BASE_Mime.php';
/**
 * PHPExcel 
 *
 * This is a wrapper class/library for PHPExcel
 * @author _BA5E
 * @package    application\libraries\Excel
 * @category   library
 * @link       
 */
class Excel extends PHPExcel 
{
	var $separator  =   ';';    /** separator used to explode each line */
	var $enclosure  =   '"';    /** enclosure used to decorate each field */
	var $max_row_size   =   4096;    /** maximum row size to be used for decoding */
	
	
	const DEBUG_FILENAME = "PHPExcel.log";
	
	private $ci;
	
	public $defaultColumnWidth	= 15;
	public $firstRowIsHeader 	= true;
	public $freezeFirstRow 		= true;
	public $header_bg 			= 'F8F8F8';
	public $bold_header			= true;
	
	public $save_file 			= true;
	public $filename 			= "output";
	public $title				= "";
	public $subject				= "";
	public $desc				= "";
	public $keywords			= "";
	public $category			= "";
	public $creator				= "";
	public $last_modify_by		= "";
	
	
    public function __construct() 
    {
        parent::__construct();
        
        $this->ci =& get_instance();
        
        $this->creator 			= $this->ci->config->item('site_title');
        $this->last_modify_by 	= $this->ci->config->item('site_title');
    }
    
    
    /**
     * Generate XLS File from CSV-Data
     * 
     * @param array $csv_array >> array("sheet1"=>"YOUR;CSV;STRING", "sheet2"=>"YOUR;CSV;STRING")
     */
    public function generateXLSFromCSVArray($csv_array, $sep_field="©¨»·¿×²º¹²×¿·«¨®", $sep_set="®¨»°¿×²º¹²×¿°«¨©")
    {
    	write2Debugfile(self::DEBUG_FILENAME, "generate xls filename[$this->filename] field_seperator[$sep_field] row_delimiter[$sep_set]\n", false);
    	
    	// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
    	$objPHPExcel = new PHPExcel();
		$objPHPExcel->getProperties()
			->setTitle($this->title)
			->setSubject($this->subject)
			->setDescription($this->desc)
			->setKeywords($this->keywords)
			->setCategory($this->category)
			->setCreator($this->creator)
			->setLastModifiedBy($this->last_modify_by)
		;
		
		$sheetIndex = 0;
		$vc 		= 1;
		foreach (array_keys($csv_array) as $id)
		{
			$csv_str 		= $csv_array[$id];
			$myArr 			= explode($sep_set, $csv_str);
			$columnCount	= count(explode($sep_field, $myArr[0]));
			$colIdentifier 	= array();
			$a				= "A";		// column letter
			$c 				= 0;		// column counter
			$rowCount 		= 1;		// row counter
			
			write2Debugfile(self::DEBUG_FILENAME, "create sheet[$id]index[$sheetIndex] columns[$columnCount] rows[".count($myArr)."]", true);
			// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
			// create new sheet
			$objPHPExcel->createSheet($sheetIndex);
			$objPHPExcel->setActiveSheetIndex($sheetIndex);
			$objPHPExcel->getActiveSheet()->setTitle($id);
			$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_PORTRAIT);
			$objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
			$objPHPExcel->getActiveSheet()->getPageSetup()->setHorizontalCentered(true);
			$objPHPExcel->getActiveSheet()->getDefaultStyle()->getFont()->setName('Arial');

			while ($c < $columnCount)
			{
				$colIdentifier[$c] = $a;
				$objPHPExcel->getActiveSheet()->getColumnDimension($a)->setWidth($this->defaultColumnWidth);
				$c++;
				$a++;
			}
			
			write2Debugfile(self::DEBUG_FILENAME, "", true);
			
			// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
			// loop through csv data rows
			foreach ($myArr as $row)
			{
				$dr 		= explode($sep_field, trim($row));
				$index 		= 0;
				
				write2Debugfile(self::DEBUG_FILENAME, "- row[$rowCount] -".print_r($dr, true), true);
				
				foreach ($dr as $entry)
				{
					if ($rowCount == 1 && $this->firstRowIsHeader === true)
					{
						// some custom styling for the header
						$objPHPExcel->getActiveSheet()->getRowDimension($rowCount)->setRowHeight(16);
						$objPHPExcel->getActiveSheet()->getStyle($colIdentifier[$index].$rowCount)->getFont()->setBold($this->bold_header)->setSize(12);
						$objPHPExcel->getActiveSheet()->getStyle($colIdentifier[$index].$rowCount)->getAlignment()->setHorizontal("left")->setWrapText(true);
						$objPHPExcel->getActiveSheet()->getStyle($colIdentifier[$index].$rowCount)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB($this->header_bg);
						$objPHPExcel->getActiveSheet()->getStyle($colIdentifier[$index].$rowCount)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);

						if ($this->freezeFirstRow === true){
							$objPHPExcel->getActiveSheet()->freezePane('A2');
						}
					}
					else{
						$objPHPExcel->getActiveSheet()->getStyle($colIdentifier[$index].$rowCount)->getFont()->setSize(10);
						$objPHPExcel->getActiveSheet()->getStyle($colIdentifier[$index].$rowCount)->getAlignment()->setHorizontal("left");
						$objPHPExcel->getActiveSheet()->getStyle($colIdentifier[$index].$rowCount)->getAlignment()->setWrapText(true);
					}
					
					$type = PHPExcel_Cell_DataType::TYPE_STRING; 
					$objPHPExcel->setActiveSheetIndex($sheetIndex)->getCellByColumnAndRow($index, $rowCount)->setValueExplicit($entry, $type);
					
					write2Debugfile(self::DEBUG_FILENAME, "  - write value[".$entry."] to cell[".$colIdentifier[$index].$rowCount."]", true);
					$index++;
				}
				$rowCount++;
			}
			$sheetIndex++;
		}
		
		$objPHPExcel->setActiveSheetIndex(0); // set 1st sheet as active

		$result = array();
		if ($this->save_file == false)
		{
			// Redirect output to a client’s web browser (Excel5)
			header('Content-Type: application/vnd.ms-excel');
			header('Content-Disposition: attachment;filename="'.$this->filename.'.xls"');
			header('Cache-Control: max-age=0');
			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
			$objWriter->save('php://output');
		}
		else{
			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
			$result = $objWriter->save(safe_utf8_decode(upload_path()."/".$this->filename.".xls") );
			
			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'CSV');
			$result = $objWriter->save(safe_utf8_decode(upload_path()."/".$this->filename.".csv") );
		}
		
		
		
    	write2Debugfile(self::DEBUG_FILENAME, "RESULT:\n".print_r($result, true)."\nFile: ".upload_path()."temp/".$this->filename.".xls");
    }
    
    public function createSomething()
    {
		//activate worksheet number 1
		$this->Excel->setActiveSheetIndex(0);
		//name the worksheet
		$this->Excel->getActiveSheet()->setTitle('test worksheet');
		//set cell A1 content with some text
		$this->Excel->getActiveSheet()->setCellValue('A1', 'This is just some text value');
		//change the font size
		$this->Excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(20);
		//make the font become bold
		$this->Excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
		//merge cell A1 until D1
		$this->Excel->getActiveSheet()->mergeCells('A1:D1');
		//set aligment to center for that merged cell (A1 to D1)
		$this->Excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		 
		$filename='just_some_random_name.xls'; //save our workbook as this file name
		header('Content-Type: application/vnd.ms-excel'); //mime type
		header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
		header('Cache-Control: max-age=0'); //no cache
		            
		//save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
		//if you want to save it as .XLSX Excel 2007 format
		$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');  
		//force user to download the Excel file without writing it to server's HD
		$objWriter->save('php://output');
    }
    
    
    
    
    /**
     * import localization file into the database
     * 
     * @param string $filename	
     * @param array $required_cols >> array("cola", "colb", "etc");
     * @param array $additonal_cols >> array("user_id"=>"maxmueller", "timestamp"=>time(), "etc"=>"etc");
     * @param bool $proceed_to_database >> when false only the queries will be returned in the BASE_Result
     * @return BASE_Result
     */
    public static function import_localization_file($filename, $required_cols, $additonal_cols, $proceed_to_database=true)
    {
    	if (!file_exists($filename)){
    		$result = new BASE_Result(false, lang("error_file_not_found"));
    	}

    	$available_readers = array(
    		"xlsx"=>'Excel2007',
    		"xls"=>'Excel5',
    		"csv"=>"CSV",
    	);
    	
    	$ext = BASE_Mime::getExtensionForFile($filename);
    	
    	if (array_key_exists(strtolower($ext), $available_readers) && BASE_Mime::validate_mimetype($filename))
    	{
    		$result = new BASE_Result(false, lang("error_while_processing_file"));
    		
    		// extension and mimetype ok. 
    		$reader_name = $available_readers[strtolower($ext)]; 
    		$objReader = PHPExcel_IOFactory::createReader($reader_name);
    		
    		if (is_object($objReader))
    		{
    			$ci =& get_instance();
    			
    			$table		= TBL_LOCALES_GENERIC;
    			
    			$objExcel 	= $objReader->load($filename);
    			$worksheet 	= $objExcel->getActiveSheet();
    			$num_rows 	= $worksheet->getHighestRow();
    			$highest_col_char	= $worksheet->getHighestColumn();
    			$highest_col 		= 0;
    			$i = "A";
    			for($i="A"; $i != $highest_col_char; $i++) {
    				$highest_col++;
    			}
    			
    			$messages	= array();
    			$queries	= array();
    			$debug		= "";
    			
    			$max_cols_allowed = count($required_cols);
    			
    			if($highest_col > $max_cols_allowed)
    			{
    				return new BASE_Result(false, "invalid column count ($highest_col) in file (".count($required_cols)." columns exceeded)");
    			}
    			
    			write2Debugfile(self::DEBUG_FILENAME, "- reader '$reader_name' created and file '$filename' loaded.\nrows: ".$worksheet->getHighestRow()." highest column: ".$worksheet->getHighestColumn(), false);
    			
    			$values			= array();
    			$row_index 		= 0;
    			foreach ($worksheet->getRowIterator() as $row)
    			{
    				$debug .= 'Row number: ' . $row->getRowIndex() . "\r\n";

    				$values[$row_index] = array();
    				
    				$cellIterator = $row->getCellIterator();
    				$cellIterator->setIterateOnlyExistingCells(false); // Loop all cells, even if it is not set
    				
    				$cell_counter 	= 0; // count cells per row
    				$row_ok 		= false;
    				foreach ($cellIterator as $cell)
    				{
    					if (!is_null($cell))
    					{
    						$debug .= 'Cell: ' . $cell->getCoordinate() . ' - ' . $cell->getValue() . "\r\n";
    						
    						if ($cell_counter > $max_cols_allowed){
    							$row_ok = false;
    						}
    						else{
    							$row_ok = true;
    							$values[$row_index][  $required_cols[$cell_counter]  ] = $cell->getValue() ;
    						}
    						$cell_counter ++;
    					}
    				}
    				
    				// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
    				// all cells proceeded
    				if ($cell_counter < count($required_cols) || $row_ok == false)
    				{
    					$messages[] = "invalid column count '$cell_counter' in line ".$row->getRowIndex()." (".count($required_cols)." columns expected)";
    				}
    				else 
    				{
    					// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
    					// add or overwrite from additional data array
    					foreach ($additonal_cols as $key => $value){
    						$values[$row_index][$key] = $value;
    					}
    				}
    				
    				$row_index++;
    			} // last row 
    			
    			/*
    			foreach ($values as $row_index => $row_data)
    			{
    				$values_str = "";
    				foreach ($row_data as $value)
    				{
    					$values_str .= $ci->db->escape($value).",";
    				}
    				
    				//$queries[] = $ci->app_model->getInsertString($table, $row_data);
    				
    				
    				$fields 	= "`" . implode("`,`", array_keys($row_data)) . "`";
    				$sql 		= "REPLACE INTO `".$table."` (".$fields.") VALUES(".substr($values_str, 0, -1).")";
    				$queries[] 	= $sql;
    				
    			}
    			*/
    			
    			write2Debugfile(self::DEBUG_FILENAME, "- values ".print_r($values, true));
    			//write2Debugfile(self::DEBUG_FILENAME, "- debug\n".$debug);

    			if (count($messages) == 0)
    			{
    				if ($proceed_to_database)
    				{
    					//$result = $ci->app_model->BASE_Transaction($queries);
    					$ci->db->insert_batch($table, $values);
    					
    					if ($result->error == "")
    					{
    						$result->status 	= E_STATUS_CODE::SUCCESS;
    						$result->extra		= $queries;
    					}
    					
	    					
    				}
    				else{
    					$result = new BASE_Result($queries, "", $queries, E_STATUS_CODE::SUCCESS);
    					$result->messages = $messages;
    				}
    			}
    			else{
    				$msg_string = '<ul><li>'.implode('</li><li>', $messages).'</li></ul>';
    				$result = new BASE_Result(false, lang("error_import_localization_file")."<br>".$msg_string, $messages, E_STATUS_CODE::ERROR);
    			}
    		}
    	}
    	else{
    		$result = new BASE_Result(false, lang("error_unsupported_extension"));
    	}

    	return $result;
    }
    
    /**
     * loads a file (csv, xls, xlsx) and returns a BASE_Result with CSV
     * 
     * @param string $filename
     * @param string $table
     * @return BASE_Result
     */
    public function parse_file($filename)
    {
    	if (!file_exists($filename)){
    		$result = new BASE_Result(false, lang("error_file_not_found"));
    	}
    	
    	$available_readers = array(
    		"xlsx"=>'Excel2007',
    		"xls"=>'Excel5',
    		"csv"=>"CSV",
    	);
    	 
    	$ext = BASE_Mime::getExtensionForFile($filename);
    	 
    	if (array_key_exists(strtolower($ext), $available_readers) && BASE_Mime::validate_mimetype($filename))
    	{
    		$result = new BASE_Result(false, lang("error_while_processing_file"));
    	
    		// extension and mimetype ok.
    		$reader_name = $available_readers[strtolower($ext)];
    	
    		$objReader = PHPExcel_IOFactory::createReader($reader_name);
    	
    		if (is_object($objReader))
    		{
    			$objExcel = $objReader->load($filename);
    			 
    			$objWriter = new PHPExcel_Writer_CSV($objExcel);
    			$objWriter->setDelimiter(';');
    			$objWriter->setEnclosure('"');
    			$objWriter->setLineEnding("\r\n");
    			$objWriter->setUseBOM(true);
    			$objWriter->setSheetIndex(0);
    			
    			ob_start();
    			$objWriter->save('php://output');
    			
    			$csv = ob_get_clean();
    			
    			$result = new BASE_Result($csv, "", array(), E_STATUS_CODE::SUCCESS);
    			
    			write2Debugfile(self::DEBUG_FILENAME, "- reader '$reader_name' created and file '$filename' loaded.\n".$csv);
    		}
    	}
    	else{
    		$result = new BASE_Result(false, lang("error_unsupported_file"));
    	}
    	
    	return $result;
    }
}
