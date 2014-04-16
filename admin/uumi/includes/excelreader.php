<?php
function readExcelFile($file)
{
	require_once 'classes/PHPExcel.php';
        
	try
	{
		/**  Identify the type of $inputFileName  **/
		$inputFileType = PHPExcel_IOFactory::identify($file);
		/**  Create a new Reader of the type that has been identified  **/
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		$objReader->setReadDataOnly(true);
		/**  Load $inputFileName to a PHPExcel Object  **/
		$objPHPExcel = $objReader->load($file);
	}
	catch(Exception $e)
	{
		return $e;
	}
	
	$excelCourseName = ($objPHPExcel->getActiveSheet()->getCell('D3'));
	$excelCourseSize = ($objPHPExcel->getActiveSheet()->getCell('P4'));
	$excelCourseNumber = ($objPHPExcel->getActiveSheet()->getCell('D4'));
	$excelCourseCapacity = ($objPHPExcel->getActiveSheet()->getCell('P3'));
		
	$worksheet = $objPHPExcel->getActiveSheet();
	
	$row = 8;
	$lastRow = $row + ($excelCourseSize->getvalue() - 1); // Minus 1 as we start on the first row
	$users = array();

	// while($row <= $lastRow) OLD LINE, TEST BUT DID NOT SPOT WHEN TOO LOW COUNT WAS GIVEN
	// Until the username column gives way to the total and the surname is not numeric. Surname numeric check prevents malformed excel sheets
	while($worksheet->getCellByColumnAndRow('1', $row)->getvalue() != $excelCourseSize->getvalue() && !is_numeric($worksheet->getCellByColumnAndRow('3', $row)->getvalue()))
	{
		$i = $row - 8;
		
		// Check we have not read the totalling row
		if($worksheet->getCellByColumnAndRow('1', $row)->getvalue() != $excelCourseSize->getvalue() && !is_null($worksheet->getCellByColumnAndRow('1', $row)->getvalue()))
		{
			$users[$i]['serviceno'] = trim($worksheet->getCellByColumnAndRow('1', $row)->getvalue());
			$users[$i]['forename'] = trim(ucwords(strtolower($worksheet->getCellByColumnAndRow('4', $row)->getvalue())));
			$users[$i]['surname'] = trim(ucwords(strtolower($worksheet->getCellByColumnAndRow('3', $row)->getvalue())));
			$users[$i]['rank'] = trim($worksheet->getCellByColumnAndRow('7', $row)->getvalue());
		}
		
		$row++;
	}
	
	$data['users'] = $users;
	$data['coursename'] = $excelCourseName->getvalue();
	$data['coursesize'] = $excelCourseSize->getvalue();
	$data['coursecapacity'] = $excelCourseCapacity->getvalue();
	
	$courseNumberArray = explode('/', $excelCourseNumber->getvalue());
	
	$data['coursecode'] = $courseNumberArray[1];	
	$data['coursegroup'] = $courseNumberArray[2] . substr('' . $courseNumberArray[3] . '', -2);
	
	return $data;
}
?>