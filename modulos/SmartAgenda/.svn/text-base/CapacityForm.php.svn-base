<?php
/**
 * @file CapacityForm.php
 * @author marcio.ferreira
 * @version 22/10/2015 10:43:56
 * @since 22/10/2015 10:43:56
 * @package SASCAR CapacityForm.php 
 */
include_once ("../../lib/config.php");

//classe métodos usados em comum
require_once (_MODULEDIR_ . 'SmartAgenda/Action/Capacity.php');

$capacity = new Capacity();

if(!empty($_POST['date'])){
	
	$data = explode(',', $_POST['date']);
	
	foreach ($data AS $dt){
		$capacity->setDate($dt);
		//obrigatórios pelos menos um deve ser enviado
		//$capacity->setDate('2015-10-01');
		//$capacity->setDate('2015-08-22');
	}
}

//opcional
$capacity->setLocation($_POST['Location']);

//opcional
$capacity->setCalculateDuration($_POST['CalculateDuration']);
//opcional
$capacity->setCalculateTraveltTime($_POST['CalculateTraveltTime']);

//obrigatório se não passar o location
$capacity->setDontAaggregateResults($_POST['DontAaggregateResults']);
$capacity->setDetermineLocationByWorkZone($_POST['DetermineLocationByWorkZone']);


if(!empty($_POST['TimeSlot'])){

	$dados = explode(',', $_POST['TimeSlot']);

	foreach ($dados AS $dt){
		$capacity->setTimeSlot($dt);
		//opcional
		//$capacity->setTimeSlot('10-12');
		//$capacity->setTimeSlot('8-10');
	}
}


if(!empty($_POST['WorkSkill'])){

	$dados = explode(',', $_POST['WorkSkill']);

	foreach ($dados AS $dt){
		$capacity->setWorkSkill($dt);
		//opcional
		//$capacity->setWorkSkill('CASCO MOVEL');
		//$capacity->setWorkSkill('CASCO FIXO');
	}
}


//opcional
// $capacity->setActivityField('worktype_label', 'ASSISTENCIA');
// $capacity->setActivityField('XA_WO_TYPE', 'AS');
// $capacity->setActivityField('XA_WO_GROUP', 'CSC');
// $capacity->setActivityField('XA_WO_REASON', '90');
$capacity->setWorkTypeLabel($_POST['worktype_label']);
$capacity->setXA_WO_TYPE($_POST['XA_WO_TYPE']);
$capacity->setXA_WO_GROUP($_POST['XA_WO_GROUP']);
$capacity->setXA_WO_REASON($_POST['XA_WO_REASON']);


//obrigtórios
// $capacity->setActivityField('XA_COUNTRY_CODE', 'BR');
// $capacity->setActivityField('XA_STATE_CODE', 'PR');
// $capacity->setActivityField('XA_CITY_CODE', '00005735');
// $capacity->setActivityField('XA_NEIGHBORHOOD_CODE', '00000000');

$capacity->setXA_COUNTRY_CODE($_POST['XA_COUNTRY_CODE']);
$capacity->setXA_STATE_CODE($_POST['XA_STATE_CODE']);
$capacity->setXA_CITY_CODE($_POST['XA_CITY_CODE']);
$capacity->setXA_NEIGHBORHOOD_CODE($_POST['XA_NEIGHBORHOOD_CODE']);


if(count($_POST) > 0){
	$dadosCapacity = $capacity->getCapacity();
}



?>


<html>
	<body>
	
	<form name='dadosCapacity' action="" method="post">
		<br/>
		Date
		<input type="text" value="<?= (!empty($_POST['date'])) ? $_POST['date'] : ''; ?>" id="date" name="date" size="50"  />
		<br/>
		Location
		<input type="text" value="<?= (!empty($_POST['Location'])) ? $_POST['Location'] : ''; ?>" id="Location" name="Location" size="30"  />
		<br/>
		CalculateDuration
		<input type="text" value="<?= (!empty($_POST['CalculateDuration'])) ? $_POST['CalculateDuration'] : ''; ?>" id="CalculateDuration" name="CalculateDuration" size="3"  />
		<br/>
		CalculateTraveltTime
		<input type="text" value="<?= (!empty($_POST['CalculateTraveltTime'])) ? $_POST['CalculateTraveltTime'] : ''; ?>" id="CalculateTraveltTime" name="CalculateTraveltTime" size="3"  />
		<br/>
		DontAaggregateResults
		<input type="text" value="<?= (!empty($_POST['DontAaggregateResults'])) ? $_POST['DontAaggregateResults'] : ''; ?>" id="DontAaggregateResults" name="DontAaggregateResults" size="3"  />
		<br/>
		DetermineLocationByWorkZone
		<input type="text" value="<?= (!empty($_POST['DetermineLocationByWorkZone'])) ? $_POST['DetermineLocationByWorkZone'] : ''; ?>" id="DetermineLocationByWorkZone" name="DetermineLocationByWorkZone" size="3"  />
		<br/>
		TimeSlot
		<input type="text" value="<?= (!empty($_POST['TimeSlot'])) ? $_POST['TimeSlot'] : ''; ?>" id="TimeSlot" name="TimeSlot" size="50"  />
		<br/>
		WorkSkill
		<input type="text" value="<?= (!empty($_POST['WorkSkill'])) ? $_POST['WorkSkill'] : ''; ?>" id="WorkSkill" name="WorkSkill" size="50"  />
		<br/>
		<br/>
		
		worktype_label
		<input type="text" value="<?= (!empty($_POST['worktype_label'])) ? $_POST['worktype_label'] : ''; ?>" id="worktype_label" name="worktype_label" size="50"  />
		<br/>
		XA_WO_TYPE
		<input type="text" value="<?= (!empty($_POST['XA_WO_TYPE'])) ? $_POST['XA_WO_TYPE'] : ''; ?>" id="XA_WO_TYPE" name="XA_WO_TYPE" size="10"  />
		<br/>
		XA_WO_GROUP
		<input type="text" value="<?= (!empty($_POST['XA_WO_GROUP'])) ? $_POST['XA_WO_GROUP'] : ''; ?>" id="XA_WO_GROUP" name="XA_WO_GROUP" size="10"  />
		<br/>
		XA_WO_REASON
		<input type="text" value="<?= (!empty($_POST['XA_WO_REASON'])) ? $_POST['XA_WO_REASON'] : ''; ?>" id="XA_WO_REASON" name="XA_WO_REASON" size="10"  />
		<br/>
		<br/>
		### Obrigatórios ###
		<br/>
		<br/>
		XA_COUNTRY_CODE
		<input type="text" value="<?= (!empty($_POST['XA_COUNTRY_CODE'])) ? $_POST['XA_COUNTRY_CODE'] : ''; ?>" id="XA_COUNTRY_CODE" name="XA_COUNTRY_CODE" size="10"  />
		<br/>
		XA_STATE_CODE
		<input type="text" value="<?= (!empty($_POST['XA_STATE_CODE'])) ? $_POST['XA_STATE_CODE'] : ''; ?>" id="XA_STATE_CODE" name="XA_STATE_CODE" size="10"  />
		<br/>
		XA_CITY_CODE
		<input type="text" value="<?= (!empty($_POST['XA_CITY_CODE'])) ? $_POST['XA_CITY_CODE'] : ''; ?>" id="XA_CITY_CODE" name="XA_CITY_CODE" size="10"  />
		<br/>
		XA_NEIGHBORHOOD_CODE
		<input type="text" value="<?= (!empty($_POST['XA_NEIGHBORHOOD_CODE'])) ? $_POST['XA_NEIGHBORHOOD_CODE'] : ''; ?>" id="XA_NEIGHBORHOOD_CODE" name="XA_NEIGHBORHOOD_CODE" size="10"  />
		<br/>
		<br/>
		<br/>
		
		<input type="submit" id="enviar" value="Enviar" />

	</form>
	</body>
</html>


<?php 

echo '<pre>';
print_r($dadosCapacity);  

?>