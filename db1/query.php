<?php
$servename = "localhost";
$username = "root";
$password = "";
$dbname = "AndroidGuideAPI";
#$userInput = $_POST["userInput"];
$userInput = !empty($_POST['userInput']) ? $_POST['userInput'] : "";
$bridge = mysqli_connect("$servename", "$username","$password", "$dbname");

// check connection
if (!$bridge){
    echo "not connect with database";
    die();
}
// delete !?.
$symbol = array("!", ";", ".", "?");
if(in_array(substr($userInput, -1), $symbol)){
    $userInput = substr($userInput, 0, -1);
}

// define stopwords
$stopwords = array("What", "How", "When", "I", "a", "about", "above", "above", "across", "after", "afterwards", "again", "against", "all", "almost", "alone", "along", "already", "also","although","always","am","among", "amongst", "amoungst", "amount",  "an", "and", "another", "any","anyhow","anyone","anything","anyway", "anywhere", "are", "around", "as",  "at", "back","be","became", "because","become","becomes", "becoming", "been", "before", "beforehand", "behind", "being", "below", "beside", "besides", "between", "beyond", "bill", "both", "bottom","but", "by", "call", "can", "cannot", "cant", "co", "con", "could", "couldnt", "cry", "de", "describe", "detail", "do", "done", "down", "due", "during", "each", "eg", "eight", "either", "eleven","else", "elsewhere", "empty", "enough", "etc", "even", "ever", "every", "everyone", "everything", "everywhere", "except", "few", "fifteen", "fify", "fill", "find", "fire", "first", "five", "for", "former", "formerly", "forty", "found", "four", "from", "front", "full", "further", "get", "give", "go", "had", "has", "hasnt", "have", "he", "hence", "her", "here", "hereafter", "hereby", "herein", "hereupon", "hers", "herself", "him", "himself", "his", "how", "however", "hundred", "ie", "if", "inc", "indeed", "interest", "into", "is", "it", "its", "itself", "keep", "last", "latter", "latterly", "least", "less", "ltd", "made", "many", "may", "me", "meanwhile", "might", "mill", "mine", "more", "moreover", "most", "mostly", "move", "much", "must", "my", "myself", "name", "namely", "neither", "never", "nevertheless", "next", "nine", "no", "nobody", "none", "noone", "nor", "not", "nothing", "now", "nowhere", "of", "off", "often", "on", "once", "one", "only", "onto", "or", "other", "others", "otherwise", "our", "ours", "ourselves", "out", "over", "own","part", "per", "perhaps", "please", "put", "rather", "re", "same", "see", "seem", "seemed", "seeming", "seems", "serious", "several", "she", "should", "show", "side", "since", "sincere", "six", "sixty", "so", "some", "somehow", "someone", "something", "sometime", "sometimes", "somewhere", "still", "such", "system", "take", "ten", "than", "that", "the", "their", "them", "themselves", "then", "thence", "there", "thereafter", "thereby", "therefore", "therein", "thereupon", "these", "they", "thickv", "thin", "third", "this", "those", "though", "three", "throughout", "thru", "thus", "to", "together", "too", "top", "toward", "towards", "twelve", "twenty", "two", "un", "under", "until", "up", "upon", "us", "very", "via", "was", "we", "well", "were", "what", "whatever", "when", "whence", "whenever", "where", "whereafter", "whereas", "whereby", "wherein", "whereupon", "wherever", "whether", "which", "while", "whither", "who", "whoever", "whole", "whom", "whose", "why", "Why", "will", "with", "without", "would", "yet", "you", "your", "yours", "yourself", "yourselves", "the","data","Android", "in","inside","through", "within","method","Method","class","Class","cause");

// parent words
//$parentwords = array("in","inside","through", "within");
// filter entities and find their parents
$originInput = $userInput;
$userInput = preg_replace('/\b('.implode('|',$stopwords).')\b/','',$userInput);
$input_words = array();
$Enid = array();
$input_words = explode(" ", $userInput);
$unique_input_words = array_unique($input_words); // potential entities
$Entity_parent = array(); //{Entity1:{Parent1: [1,2], Parent2: [3]}, Entity2:{Parent: [4]},...}

$sql1 = "SELECT id, EntityName, EntityParent FROM Entities WHERE EntityName IN ('". implode("','", $unique_input_words)."')";
$text1 = mysqli_query($bridge, $sql1);
if(mysqli_num_rows($text1)>0){
    while($row = mysqli_fetch_assoc($text1)) {
        if($row["EntityParent"]){
            $pieces = explode(".", $row["EntityParent"]);
            $parent = end($pieces);
            if (!array_key_exists(lcfirst($row["EntityName"]), $Entity_parent)){
                $Entity_parent[lcfirst($row["EntityName"])] = array();
            }if (!array_key_exists($parent, $Entity_parent[lcfirst($row["EntityName"])])){
                $Entity_parent[lcfirst($row["EntityName"])][$parent] = array();
            }if (!in_array($row["id"], $Entity_parent[lcfirst($row["EntityName"])][$parent])){
                array_push($Entity_parent[lcfirst($row["EntityName"])][$parent], $row["id"]);
            }
        }
    }
}
$select = array();
foreach($Entity_parent as $entity=>$parent){
    //if(count($Entity_parent[$entity])>1){
        if(!array_key_exists($entity, $select)){
            ksort($Entity_parent[$entity]);
            $select[$entity] = (array_keys($Entity_parent[$entity]));    
        } 
    //}
}
$msg=array();
$msg["select"]  = $select;
$msg["en_pa"]  = $Entity_parent;
$msg["inputWords"] = $originInput;
if(!empty($select)){
    //echo json_encode($select);  
    echo json_encode($msg);  
} else{
    echo "invalid";
} 

$bridge->close();
?>