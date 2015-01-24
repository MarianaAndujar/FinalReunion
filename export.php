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
                             ->setDescription($meeting['DESCRIPTION'])
                             ->setKeywords("pdf php")
                             ->setCategory("Report");

$objPHPExcel->getActiveSheet()->getPageSetup()
    ->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);

$objPHPExcel->setActiveSheetIndex(0);

//Initial data
$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, 1, $meeting['SUBJECT']);
$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, 2, $meeting['DESCRIPTION']);

$objPHPExcel->getActiveSheet()->mergeCells('A3:A5')
                              ->setCellValue('A4', strval($participation_count) . ' participants');

//Affichage des années
$current_col = 1;
foreach($dates as $year){
    $colspan = 0;
    foreach($year['months'] as $month)
        foreach($month['days'] as $day)
            $colspan += sizeof($day['hours']);
    
    $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($current_col, 3, strval($year['year']));
    $objPHPExcel->getActiveSheet()->mergeCellsByColumnAndRow($current_col, 3, $current_col + $colspan - 1, 3);
    
    $objPHPExcel->getActiveSheet()
                            ->getStyleByColumnAndRow($current_col, 3)
                            ->applyFromArray(array(
                                'fill' => array(
                                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                                    'color' => array('rgb' => '333333')
                                ),
                                'borders' => array(
                                    'outline' => array(
                                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                                        'color' => array('rgb' => '000000')
                                    )
                                )
                            ))
                            ->getAlignment()
                            ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    
    $current_col += $colspan;
}


//Affichage des mois
$current_col = 1;
foreach($dates as $year)
    foreach($year['months'] as $month){
        $colspan = 0;
        foreach($month['days'] as $day)
            $colspan += sizeof($day['hours']);
    
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($current_col, 4, strval($month['month']));
        $objPHPExcel->getActiveSheet()->mergeCellsByColumnAndRow($current_col, 4, $current_col + $colspan - 1, 4);
        
        $objPHPExcel->getActiveSheet()
                            ->getStyleByColumnAndRow($current_col, 4)
                            ->applyFromArray(array(
                                'fill' => array(
                                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                                    'color' => array('rgb' => '555555')
                                ),
                                'borders' => array(
                                    'outline' => array(
                                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                                        'color' => array('rgb' => '000000')
                                    )
                                )
                            ))
                            ->getAlignment()
                            ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        
        $current_col += $colspan;
    }

//Affichage des jours
$current_col = 1;
foreach($dates as $year)
    foreach($year['months'] as $month)
        foreach($month['days'] as $day){
    
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($current_col, 5, strval($day['day']));
            $objPHPExcel->getActiveSheet()->mergeCellsByColumnAndRow($current_col, 5, $current_col + sizeof($day['hours']) - 1, 5);
            
            $objPHPExcel->getActiveSheet()
                            ->getStyleByColumnAndRow($current_col, 5)
                            ->applyFromArray(array(
                                'fill' => array(
                                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                                    'color' => array('rgb' => 'aaaaaa')
                                ),
                                'borders' => array(
                                    'outline' => array(
                                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                                        'color' => array('rgb' => '000000')
                                    )
                                )
                            ))
                            ->getAlignment()
                            ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            
            $current_col += sizeof($day['hours']);
        }


//Affichage des heures
$current_col = 1;
foreach($dates as $year)
    foreach($year['months'] as $month)
        foreach($month['days'] as $day)
            foreach($day['hours'] as $hour){
                $label = strval($hour['hour']['BHOUR'] . ":00 - " 
                    . (intval($hour['hour']['BHOUR']) + intval($meeting['DURATION']))
                    . ":00");
                    
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($current_col, 6, $label);
                
                $objPHPExcel->getActiveSheet()
                            ->getStyleByColumnAndRow($current_col, 6)
                            ->applyFromArray(array(
                                'fill' => array(
                                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                                    'color' => array('rgb' => 'dddddd')
                                ),
                                'borders' => array(
                                    'outline' => array(
                                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                                        'color' => array('rgb' => '000000')
                                    )
                                )
                            ))
                            ->getAlignment()
                            ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                
                $current_col++;
            }


//Affichage des participations
//Les utilisateurs par UID et username n'ayant pas de doublons, on les affiche
//séparément
$current_col = 1;
$row_num = 7;
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
                        
                        $label = sizeof($availability) > 0 ? "OK" : ""; 
                        
                        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($current_col, $row_num, $label);
                        
                        $objPHPExcel->getActiveSheet()
                            ->getStyleByColumnAndRow($current_col, $row_num)
                            ->applyFromArray(array(
                                'fill' => array(
                                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                                    'color' => array('rgb' => (sizeof($availability) > 0) ? '00FF00' : 'FF0000')
                                ),
                                'borders' => array(
                                    'outline' => array(
                                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                                        'color' => array('rgb' => '000000')
                                    )
                                )
                            ))
                            ->getAlignment()
                            ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                            
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
                        
                        $label = sizeof($availability) > 0 ? "OK" : ""; 
                        
                        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($current_col, $row_num, $label);
                        
                        $objPHPExcel->getActiveSheet()
                            ->getStyleByColumnAndRow($current_col, $row_num)
                            ->applyFromArray(array(
                                'fill' => array(
                                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                                    'color' => array('rgb' => (sizeof($availability) > 0) ? '00FF00' : 'FF0000')
                                ),
                                'borders' => array(
                                    'outline' => array(
                                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                                        'color' => array('rgb' => '000000')
                                    )
                                )
                            ))
                            ->getAlignment()
                            ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    
                        $current_col++;
                    }
        $row_num++;
    }


//Affichage des résultats
$row_num++;
$current_col = 1;
foreach($dates as $year)
    foreach($year['months'] as $month)
        foreach($month['days'] as $day)
            foreach($day['hours'] as $hour){
                $availabilities = $hour['availabilities'][0];
                
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($current_col, $row_num, sizeof($availabilities));
                
                $objPHPExcel->getActiveSheet()
                    ->getStyleByColumnAndRow($current_col, $row_num)
                    ->applyFromArray(array(
                        'fill' => array(
                            'type' => PHPExcel_Style_Fill::FILL_SOLID,
                            'color' => array('rgb' => (sizeof($availabilities) == $max_participation) ? '00FF00' : 'FF0000')
                        ),
                        'borders' => array(
                            'outline' => array(
                                'style' => PHPExcel_Style_Border::BORDER_THIN,
                                'color' => array('rgb' => '000000')
                            )
                        )
                    ))
                    ->getAlignment()
                    ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                
                $current_col++;                
            }

//Renommer la spreadsheet et afficher les lignes pour discerner l'arborescence.
//Les lignes ne s'affichent pas sans cette option sur les PDF.
$objPHPExcel->getActiveSheet()->setTitle('Meeting report');
$objPHPExcel->getActiveSheet()->setShowGridLines(true);


//Envoi du fichier
//Le reste est tiré de la doc PHPExcel
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
    header('Content-Disposition: attachment;filename="report.pdf"');
    header('Cache-Control: max-age=0');
    
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'PDF');
    $objWriter->save('php://output');
}else{
    $objPHPExcel->setActiveSheetIndex(0);
    // Redirect output to a client’s web browser (Excel5)
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="report.xls"');
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