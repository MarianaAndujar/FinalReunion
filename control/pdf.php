<?php
	
	//session_start();
	require('../static/pdf/fpdf.php');
	include ("../model/MMeeting.class.php");
	
	
	var_dump($_GET);
	$meeting = new MMeeting();
	$subject 	= addslashes($_GET[0]);
	$name 		= addslashes($_SESSION['NOM']);
	$surname 	= addslashes($_SESSION['PRENOM']);
	$show = $meeting->getMeetingToShow($subject, $name, $surname);
	$max = $meeting->getMeetingMaxParticipation($show[0]);
	
	// mode paysage normal de base
	$pdf=new FPDF();
	$pdf->Open();
	
	$pdf->AliasNbPages();
	$pdf->AddPage();
	$pdf->SetFont('Arial','B',16);
	
	$pdf->Cell(40,10,'Titre !'.$show[0]);
    $pdf->Cell(0,10,'Impression de la ligne numéro ',0,1);
	
	$pdf->Output();
	
?>