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

    /**
     * Do something
     */

    // Get Values

    $periodos = $request->info->periodos; // Array containing inFlows, outFlows & cashFlows
    $noPeriodos = count($periodos);
    $principal = $request->info->principal;
    $taxRate = $request->info->taxRate;
    $tasaInteres = $request->info->tasaInteres;

    $npv = -$principal;

    for ($i=1; $i <= $noPeriodos ; $i++) {
        $npv += ($periodos[$i]->inflow * (1-$taxRate)) / ((1+$tasaInteres)**($i));
    }

    /*$some_var = [
        "some_name" => "some_val",
        "some_number"=> 10,
        "some_object"=> [
            "val" => 1
        ]
    ];

    /**
    *   This is what the html will receive
    */
    echo json_encode($npv);

}
else {
    echo "Not called properly with username parameter!";
}
?>
