<?php
function dotp($array_input, $array_warning){
	$sum = 0;
	$mul = array();
	$mul["input"] = 0;
	$mul["sent"] = 0;
	//echo json_encode($array_input);
	foreach($array_input as $key=>$tfidf){
		$sum += $tfidf*$array_warning[$key];
		$mul["input"] += pow($tfidf, 2);
		$mul["sent"] += pow($array_warning[$key], 2);
	}
	return $sum/sqrt($mul["input"])*sqrt($mul["sent"]);
}

$servename = "localhost";
$username = "root";
$password = "";
$dbname = "AndroidGuideAPI";
$bridge = mysqli_connect("$servename", "$username","$password", "$dbname");
$userSelect = $_POST["userSelect"];
$entity_parent = $_POST["entity_parent"];
$inputWords = $_POST["inputWords"];
$inputWords = explode(" ", $inputWords); // lower bound
$inputWords = array_map('lcfirst', $inputWords);
if(!$bridge){
    echo "not connect with database";
    die();
}
$input_vector = array();
$count_input = array_count_values($inputWords);
$Enid = array(); // user selected entity id
$Enid_EnName = array();
$EnName = array_keys($userSelect);
foreach ($userSelect as $api => $parent) {
	$Enid = array_merge($Enid,$entity_parent[$api][$parent]);
	foreach ($entity_parent[$api][$parent] as $index => $enid) {
		$Enid_EnName[$enid] = $api;
	}
	$input_vector[$api] = $count_input[$api];
}
$EnName_Warningid = array();
$Warningid = array(); // related warrning ids
$sql2 = "SELECT EntitiesIndex, WarningIndex FROM recommandwarningx WHERE EntitiesIndex IN ('". implode("','", $Enid)."')";
$text2 = mysqli_query($bridge, $sql2);
if(mysqli_num_rows($text2)>0){
    while($row = mysqli_fetch_assoc($text2)) {
        if(!in_array($row["WarningIndex"], $Warningid)){
        	array_push($Warningid, $row["WarningIndex"]);
        }
        $en = $Enid_EnName[$row["EntitiesIndex"]];
        if(!array_key_exists($en, $EnName_Warningid)){
        	$EnName_Warningid[$en] = array();
        }
        array_push($EnName_Warningid[$en], $row["WarningIndex"]); 
    }
}
// calculate idf
//$total_document_num = 267891;
$idf = array();
foreach($EnName_Warningid as $En => $WarningList){
	$idf[$En] = log10(267891/(1+count($WarningList)));
	$input_vector[$En] = $input_vector[$En]*$idf[$En];
}

$warning_url = array();
$warning_rank = array();
$sql3 = "SELECT WarningText, WarningURL FROM warning WHERE id IN ('". implode("','", $Warningid)."')";
$text3 = mysqli_query($bridge, $sql3);
if(mysqli_num_rows($text3)>0){
	while($row = mysqli_fetch_assoc($text3)) {
		$sent = substr(rtrim($row["WarningText"]), 0, -1);
		if(!array_key_exists($sent, $warning_url)){
			$vector = array();
			$warning_url[$sent] = $row["WarningURL"];
			foreach($EnName as $En){
				$tf = substr_count(strtoupper($sent), strtoupper($En));
				$vector[$En] = 0;
				if($tf > 0){
					$idf1 = $idf[$En];
					$vector[$En] = $tf*$idf1;
				}
			}
			$warning_rank[$sent] = dotp($input_vector, $vector);
		}
	}
}

$result = array();
arsort($warning_rank);
$output = array_slice($warning_rank, 0, 10);
foreach($output as $select_sent=>$score){
    $result[$select_sent] = $warning_url[$select_sent];
}
if ($result==null){
    echo "invalid";
}
else{
    echo json_encode($result);
}

$bridge->close();
?>