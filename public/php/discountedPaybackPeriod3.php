<?php

/**
 * Set the headers to prevent erros
 */
if (isset($_SERVER['HTTP_ORIGIN'])) {
    header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Max-Age: 86400');
}


if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {

    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS");

    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
        header("Access-Control-Allow-Headers:        {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

    exit(0);
}
/**
 * Retrieve the post json object
 */



$postdata = file_get_contents("php://input");
if (isset($postdata)) {
    /**
     * Convert the $postdata to object
     */
    $request = json_decode($postdata);

    // get values
    $tasaInteres =$request->info->tasaInteres/100;
    $periods = $request->info->periodos;
    $principal = $request->info->principal;

    /**
     * Do something
     */
    $answer ='{';
    $paid = false;

//    $return = array($periods[0]->inflow - $periods[0]->outflow);
    $return = array( $periods[0]->inflow-$principal);
    //array_push($result,);
    for($i =1; $i < count($periods); $i++)
    {

        //structure of calculation
        //echo $return[$i-1]." tasa interes: ".$tasaInteres."   in:".$periods[$i]->inflow."   out:".$periods[$i]->outflow."<br>";
        //  $cumulative cash flow + (previous cash * ROR + (period cash flow))
        $result = $return[$i-1]+($return[$i-1] *$tasaInteres + ($periods[$i]->inflow - $periods[$i]->outflow )) ;
        array_push($return, $result);
        if($result > 0 && $paid ==false)
        {
            $paid =   true;
            $answer .='"paybackPeriod":'.($i+1).',';
        }
    }

    if($paid != true)
    {
        $answer .='"paybackPeriod": "null",';
    }
    $answer .= '"periods":[';

    for($i =0 ; $i <count($periods); $i++)
    {
        $paid = true;
        $answer .= '{"value":'.$return[$i].'},';
    }

    $answer = rtrim($answer, ",");
    $answer.="]}";


    
    /**
     *   This is what the html will receive
     */
    //echo json_encode($some_var);
    echo $answer;
}
else {
    echo "Not called properly with username parameter!";
}
?>
