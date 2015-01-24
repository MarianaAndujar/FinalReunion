<?php

session_start();
    
require_once(dirname(__FILE__) . '/config.inc.php');
require_once(MODEL_DIR . '/MMeeting.class.php');
require_once(MODEL_DIR . '/MMembers.class.php');

if(isset($_SESSION['USER_ID']))
    $uid = $_SESSION['USER_ID'];
else
    die("403");

if(isset($_GET['id'])){
    $meeting_id = intval($_GET['id']);
}else
    die("404");

if(isset($_GET['type']))
    $type = $_GET['type'];
else
    die("404");

$meeting = MMeeting::getMeetingById($meeting_id);
$participants = MMeeting::getMeetingParticipants($meeting_id);
$dates = MMeeting::getMeetingDatesById($meeting_id);
$max_participation = MMeeting::getMeetingMaxParticipation($meeting_id);
$participation_count = sizeof($participants['uids'])+sizeof($participants['unames']);

if(!$meeting)
    die("403");
if(!($meeting['ID_USER'] == $_SESSION['USER_ID']))
    die("403");

date_default_timezone_set('Europe/London');

require_once LIBS_DIR . '/PHPExcel.php';


// Create new PHPExcel object
$objPHPExcel = new PHPExcel();

// Set document properties
$objPHPExcel->getProperties()->setCreator(MMembers::getUsernameById($uid))
                             ->setTitle($meeting['SUBJECT'])
                             ->setSubject($meeting['SUBJECT'])
                             ->setDescription("")
                             ->setKeywords("pdf php")
                             ->setCategory("Report");

$objPHPExcel->setActiveSheetIndex(0);

//Initial data
$objPHPExcel->getActiveSheet()->mergeCells('A1:A3')
                              ->setCellValue('A4', strval($participation_count) . ' participants');

//Generating years
$current_col = 1;
foreach($dates as $year){
    $colspan = 0;
    foreach($year['months'] as $month)
        foreach($month['days'] as $day)
            $colspan += sizeof($day['hours']);
    
    $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($current_col, 1, strval($year['year']));
    $objPHPExcel->getActiveSheet()->mergeCellsByColumnAndRow($current_col, 1, $current_col + $colspan - 1, 1);
    
    $current_col += $colspan;
}


//Generating months
$current_col = 1;
foreach($dates as $year)
    foreach($year['months'] as $month){
        $colspan = 0;
        foreach($month['days'] as $day)
            $colspan += sizeof($day['hours']);
    
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($current_col, 2, strval($month['month']));
        $objPHPExcel->getActiveSheet()->mergeCellsByColumnAndRow($current_col, 2, $current_col + $colspan - 1, 2);
        
        $current_col += $colspan;
    }

//Generating days
$current_col = 1;
foreach($dates as $year)
    foreach($year['months'] as $month)
        foreach($month['days'] as $day){
    
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($current_col, 3, strval($day['day']));
            $objPHPExcel->getActiveSheet()->mergeCellsByColumnAndRow($current_col, 3, $current_col + sizeof($day['hours']) - 1, 3);
            
            $current_col += sizeof($day['hours']);
        }


//Generating hours
$current_col = 1;
foreach($dates as $year)
    foreach($year['months'] as $month)
        foreach($month['days'] as $day)
            foreach($day['hours'] as $hour){
                $label = strval($hour['hour']['BHOUR'] . ":00 - " 
                    . (intval($hour['hour']['BHOUR']) + intval($meeting['DURATION']))
                    . ":00");
                    
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($current_col, 4, $label);
                
                $current_col++;
            }


//Generating participation
$current_col = 1;
$row_num = 5;
if(isset($participants['uids'])) 
    foreach ($participants['uids'] as $participant){
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, $row_num, MMembers::getUsernameById($participant['id_user']));
        
        foreach($dates as $year)
            foreach($year['months'] as $month)
                foreach($month['days'] as $day)
                    foreach($day['hours'] as $hour){
                        $availability = array_filter($hour['availabilities'][0], 
                            function($v) use($participant, $hour){
                                return $v['ID_USER'] == $participant['id_user'] && $v['ID_HOURS'] == $hour['hour']['ID_HOURS'];
                            });
                        
                        $label = sizeof($availability) > 0 ? "OK" : "NOPE"; 
                        
                        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($current_col, $row_num, $label);
                        $current_col++;
                    }
        $row_num++;
    }

$current_col = 1;
if(isset($participants['unames'])) 
    foreach ($participants['unames'] as $participant){
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, $row_num, $participant['owner']);
        
        foreach($dates as $year)
            foreach($year['months'] as $month)
                foreach($month['days'] as $day)
                    foreach($day['hours'] as $hour){
                        $availability = array_filter($hour['availabilities'][0], 
                            function($v) use($participant, $hour){
                                return $v['OWNER'] == $participant['owner'] && $v['ID_HOURS'] == $hour['hour']['ID_HOURS'];
                            });
                        
                        $label = sizeof($availability) > 0 ? "OK" : "NOPE"; 
                        
                        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($current_col, $row_num, $label);
                        $current_col++;
                    }
        $row_num++;
    }

//TODO: display best choice


// Rename worksheet
$objPHPExcel->getActiveSheet()->setTitle('Meeting report');
$objPHPExcel->getActiveSheet()->setShowGridLines(true);

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);


if($type == "pdf"){
    $rendererName = PHPExcel_Settings::PDF_RENDERER_MPDF;
    $rendererLibrary = 'mpdf60';
    $rendererLibraryPath = LIBS_DIR . $rendererLibrary;
    
    if (!PHPExcel_Settings::setPdfRenderer(
            $rendererName,
            $rendererLibraryPath
        )) {
        die('500');
    }
        
    // Redirect output to a client’s web browser (PDF)
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment;filename="01simple.pdf"');
    header('Cache-Control: max-age=0');
    
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'PDF');
    $objWriter->save('php://output');
}else{
    
    // Redirect output to a client’s web browser (Excel5)
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="01simple.xls"');
    header('Cache-Control: max-age=0');
    // If you're serving to IE 9, then the following may be needed
    header('Cache-Control: max-age=1');
    
    // If you're serving to IE over SSL, then the following may be needed
    header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
    header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
    header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
    header ('Pragma: public'); // HTTP/1.0
    
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
    $objWriter->save('php://output');
}
exit;
?>