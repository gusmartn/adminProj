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

    $marcs = $request->info->macrs;
    $straight = $request->info->straightLine;

    $periods = $request->info->periods;
    $principal = $request->info->principal;
    $tax = $request->info->taxRate/100;
    $startingYear = $request->info->startingYear;

    $depCat3 = array(0.3333,0.4445,0.1481,0.0741);
    $depCat5 = array(0.2,0.32,0.192,0.1152,0.1152,0.0576);
    $depCat7 = array(0.1429,0.2449,0.1749,0.1249,0.0893,0.0892,0.0893,0.0446);
    $depCat10 = array(0.1,0.18,0.1440,0.1152,0.0922,0.0737,0.0655,0.0655,0.0656,0.0655);


    $result = [];
    $anualDep = [];
    $accDep = [];
    $valueInLedger = [];
    $taxPerYear = [];
    $depRate = [];

    for($i = 0; $i < $periods; $i++)
    {
        $result[$i] = 0;
        $anualDep[$i] = 0;
        $accDep[$i] = 0;
        $valueInLedger[$i] = 0;
        $taxPerYear[$i] = 0;
        $depRate[$i] = 0;
    }



    if($straight == true)
    {
        $salValue = $request->info->salvageValue;
        $periodSV = $request->info->salvageValuePeriod;

        for($i = 0; $i < $periods; $i++)
        {

            $depRate[$i] = 1 / $periods;

            $dep = 1 / $periods;
            if($i == $periodSV)
            {
                $anualDep[$i] = ($principal - $salValue) / $periods;
            }
            else
            {
                $anualDep[$i] = $principal / $periods;
            }

        }
    }
    if($marcs == true)
    {
        $depreciationCat = $request->info->depreciationCategory;

        if($depreciationCat = 3)
        {
            for($i = 0; $i < $periods; $i++)
            {
                $depRate[$i] = $depCat5[$i];
                $anualDep[$i] = $depRate[$i] * $principal;
                //echo $years[$i];
            }
        }

        if($depreciationCat = 5)
        {
            for($i = 0; $i < $periods; $i++)
            {
                $depRate[$i] = $depCat5[$i];
                $anualDep[$i] = $depRate[$i] * $principal;
                //echo $years[$i];
            }
        }

        if($depreciationCat = 7)
        {
            for($i = 0; $i < $periods; $i++)
            {
                $depRate[$i] = $depCat5[$i];
                $anualDep[$i] = $depRate[$i] * $principal;
                //echo $years[$i];
            }
        }

        if($depreciationCat = 10)
        {
            for($i = 0; $i < $periods; $i++)
            {
                $depRate[$i] = $depCat5[$i];
                $anualDep[$i] = $depRate[$i] * $principal;
                //echo $years[$i];
            }
        }
    }

    for($i = 0; $i < $periods; $i++)
    {
        $years[$i] = $startingYear + $i;
        if($i == 0)
        {
            $accDep[$i] = $anualDep[$i];
        }
        else
        {
            $accDep[$i] = $accDep[$i-1] + $anualDep[$i];
        }
        $valueInLedger[$i] = $principal - $accDep[$i];
        $taxPerYear[$i] = $valueInLedger[$i] * $tax;
        //echo $anualDep[$i];
    }

    $result = [
        "anualDep" => $anualDep,
        "accDep" => $accDep,
        "valueInLedger" => $valueInLedger,
        "taxPerYear" => $taxPerYear,
        "depRate" => $depRate



    ];

    echo json_encode($result);
}
else {
    echo "Not called properly with username parameter!";
}
?>
