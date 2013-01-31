<?php

    define("QUESTIONLIST", "data/QuestionList.xml");
    define("RESULTS", "data/ResultsTemp.xml");
    define("MYDATABASE", "ListaDeQuestoes");


    if(isset($_POST['nodata'])){
        $nodata = $_POST['nodata'];
        questions_selector();
    }
    
    if(isset($_POST['question'])){
        $qid = $_POST['question'];
        question_data($qid);        
        /*
        echo '{
  "cols": [
        {"id":"","label":"Topping","pattern":"","type":"string"},
        {"id":"","label":"Slices","pattern":"","type":"number"}
      ],
  "rows": [
        {"c":[{"v":"Mushrooms","f":null},{"v":3,"f":null}]},
        {"c":[{"v":"Onions","f":null},{"v":1,"f":null}]},
        {"c":[{"v":"Olives","f":null},{"v":1,"f":null}]},
        {"c":[{"v":"Zucchini","f":null},{"v":1,"f":null}]},
        {"c":[{"v":"Pepperoni","f":null},{"v":2,"f":null}]}
      ]
}';*/
    }

    function question_data($qid){
        $mysql = connectDatabase();
        /*
        $data = array();
        $data['title'] = "Titulo";
        $data['data'] = array();
        $data['data']['cols'] = array();
        $data['data']['cols'][0]= array();
        $data['data']['cols'][0]['id']='';
        $data['data']['cols'][0]['label']='Topping';
        $data['data']['cols'][0]['pattern']='';
        $data['data']['cols'][0]['type']='string';
        $data['data']['cols'][1]= array();
        $data['data']['cols'][1]['id']='';
        $data['data']['cols'][1]['label']='Slices';
        $data['data']['cols'][1]['pattern']='';
        $data['data']['cols'][1]['type']='number';
        $data['data']['rows'] = array();
        $data['data']['rows'][0]= array();
        $data['data']['rows'][0]['c']=array();
        $data['data']['rows'][0]['c'][0]=array();
        $data['data']['rows'][0]['c'][0]['v']='Mushrooms';
        $data['data']['rows'][0]['c'][0]['f']=null;
        $data['data']['rows'][0]['c'][1]['v']=3;
        $data['data']['rows'][0]['c'][1]['f']=null;
        $data['data']['rows'][1]= array();
        $data['data']['rows'][1]['c']=array();
        $data['data']['rows'][1]['c'][0]=array();
        $data['data']['rows'][1]['c'][0]['v']='Onions';
        $data['data']['rows'][1]['c'][0]['f']=null;
        $data['data']['rows'][1]['c'][1]['v']=1;
        $data['data']['rows'][1]['c'][1]['f']=null;
        $data['data']['rows'][2]= array();
        $data['data']['rows'][2]['c']=array();
        $data['data']['rows'][2]['c'][0]=array();
        $data['data']['rows'][2]['c'][0]['v']='Olives';
        $data['data']['rows'][2]['c'][0]['f']=null;
        $data['data']['rows'][2]['c'][1]['v']=1;
        $data['data']['rows'][2]['c'][1]['f']=null;
        $data['data']['rows'][3]= array();
        $data['data']['rows'][3]['c']=array();
        $data['data']['rows'][3]['c'][0]=array();
        $data['data']['rows'][3]['c'][0]['v']='Zucchini';
        $data['data']['rows'][3]['c'][0]['f']=null;
        $data['data']['rows'][3]['c'][1]['v']=1;
        $data['data']['rows'][3]['c'][1]['f']=null;
        $data['data']['rows'][4]= array();
        $data['data']['rows'][4]['c']=array();
        $data['data']['rows'][4]['c'][0]=array();
        $data['data']['rows'][4]['c'][0]['v']='Pepperoni';
        $data['data']['rows'][4]['c'][0]['f']=null;
        $data['data']['rows'][4]['c'][1]['v']=7;
        $data['data']['rows'][4]['c'][1]['f']=null;
        */

        $sql = 'SELECT  `titulo` ,  `enunciado` FROM  `questions` WHERE  `qID`='. $qid .';';
        $question = mysql_query_b($sql,$mysql);
        $row = mysql_fetch_array($question);
        if(strlen($row['titulo']) > 0)
        {
            $titulo = $row['titulo'] . ' - ' . $row['enunciado'];
        }
        else
        {
            $titulo = $row['enunciado'];
        }
        
        $sql = 'SELECT `alternativeID`,`texto` FROM `alternativas` WHERE `questionID`='. $qid .';';
        $alternatives = mysql_query_b($sql,$mysql);
        
        $data['options']['title'] = $titulo;
        $data['options']['width'] = 800;
        $data['options']['height'] = 800;
        $data['options']['sliceVisibilityThreshold'] = 0;
        $data['options']['pieResidueSliceLabel'] = 'Outros';
        
        //animações nao funciona em PieChart
        //$data['options']['animation']['duration'] = 500;
        //$data['options']['animation']['easing'] = 'out';
        
        //$data['options']['tooltip']['showColorCode'] = true; //exibe um quadrado com a cor que representa quando foca um pedaço do grafico
        
        $data['data']['cols'][0] = array('label' => 'Alternativa', 'type' => 'string');
        $data['data']['cols'][1] = array('label' => 'Respostas dos alunos', 'type' => 'number');
        
        while($row = mysql_fetch_array($alternatives)){
            $id = $row['alternativeID'];
            $respostas = count_assinaladas($qid, $id);
            $data['data']['rows'][$id]['c'] = array(0 => array('v'=>$row['texto']), 1=>array('v'=>$respostas));
        }
        
        $json = json_encode($data);
        echo $json;
        
        /*
        $data['data']['cols'][0]= array();
        //$data['data']['cols'][0]['id']='';
        $data['data']['cols'][0]['label']='Alternativa';
        //$data['data']['cols'][0]['pattern']='';
        $data['data']['cols'][0]['type']='string';
        $data['data']['cols'][1]= array();
        //$data['data']['cols'][1]['id']='';
        $data['data']['cols'][1]['label']='Respostas dos alunos';
        //$data['data']['cols'][1]['pattern']='';
        $data['data']['cols'][1]['type']='number';

        $data['data']['rows'][0]= array();
        $data['data']['rows'][0]['c']=array();
        //$data['data']['rows'][0]['c'][0]=array();
        $data['data']['rows'][0]['c'][0]['v']='Mushrooms';
        //$data['data']['rows'][0]['c'][0]['f']=null;
        $data['data']['rows'][0]['c'][1]['v']=3;
        //$data['data']['rows'][0]['c'][1]['f']=null;
        //*/
    }

    function count_assinaladas($questionID, $alternativeID){
        $mysql = connectDatabase();
        $respostas = mysql_query_b('SELECT COUNT(*) AS Respostas FROM `respostasAlternativas` WHERE `qID`='. $questionID .' AND `assinalada`='.$alternativeID,$mysql);
        $row = mysql_fetch_array($respostas);
        return $row['Respostas'] + 0;
    }

    function questions_selector(){
            $mysql = connectDatabase();
            
            $questions = mysql_query_b("SELECT  `qID` ,  `titulo` ,  `enunciado` FROM  `questions` WHERE 1",$mysql);
            echo '<select id="select_question" name="question">';
            echo '<option value="0">Todas as questões</option>';
            while($row = mysql_fetch_array($questions)){    
            //echo var_dump($row) . '</br></br>';
                echo '<option value="' . $row['qID'] .'">';
                if(strlen($row['titulo']) > 0){
                    if(strlen($row['titulo']) > 50){
                        $titulo = substr($row['titulo'],0,50) . '...';
                    }
                    else
                    {
                        $titulo = $row['titulo'];
                    }
                    echo $titulo;
                }
                else {
                    if(strlen($row['enunciado']) > 50){
                        $titulo = substr($row['enunciado'],0,50) . '...';
                    }
                    else
                    {
                        $titulo = $row['enunciado'];
                    }
                    echo $titulo;
                }
                echo '</option>';
            }
            echo '</select>';
        
    }

    function connectDatabase(){
            $mysql = mysql_connect("localhost", "root", "einstein");
            mysql_select_db(MYDATABASE, $mysql);
            return $mysql;
    }

    function mysql_query_b($sql,$db){
        $res = mysql_query($sql,$db);
        if($err = mysql_error()){
            echo "<p id=\"error\">" . $err . "<p/>";
        }
        return $res;
    }

?>