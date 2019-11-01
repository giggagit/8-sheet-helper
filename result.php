<?php

$text = trim($_POST["detail"]);
$rawData = explode("\n", $text);
$rawData = array_filter($rawData, "trim"); // remove any extra \r characters left behind

foreach($rawData as $key => $str)
{
    $rawData[$key] = explode("	", $str);
}

###############################################
function sortClosure($a, $b)
{
    return strcmp($a["closureNo"], $b["closureNo"]);
}

function slice($value,$long)
{
    for ($i=0; $i < count($value); $i++) {
        $sli[$i] = array_slice($value[$i], 0, $long);
    }
    return($sli);
}

function sptModel($spt,$sptNum)
{
    if ($sptNum == "1") {
        $arrNum = "12";
    } elseif ($sptNum == "2") {
        $arrNum = "15";
    }

    if (substr($spt[$arrNum], -3) == "_14") {
        $model = array("model" => "SPLT_1_4", "rate" => "1:4");
    } elseif (substr($spt[$arrNum], -3) == "_18") {
        $model = array("model" => "SPLT_1_8", "rate" => "1:8");
    } elseif (substr($spt[$arrNum], -3) == "116") {
        $model = array("model" => "SPLT_1_16", "rate" => "1:16");
    }
    return($model);
}

function sptAlias($site,$sptRaw,$sptType,$sptLV)
{
    //$spt = str_replace("SP020","S2",str_replace("_","",$sptRaw[$i]["15"]);
    if ($sptType == "SPLT_1_4") {
        $model = "(4)";
    } elseif ($sptType == "SPLT_1_16") {
        $model = "(16)";
    } else {
        $model = "(8)";
    }
    if ($sptLV == "1") {
        $spt = substr(str_replace("SP01","S11",str_replace("_","",$sptRaw)),"0","6").$model;
    } else {
        $spt = substr(str_replace("SP020","S2",str_replace("_","",$sptRaw)),"0","6").$model;
    }
    return($spt);
}

function splitter($sptRaw)
{
    for ($i=0; $i < count($sptRaw); $i++) {
        if (!empty($sptRaw[$i]["12"]) && !empty($sptRaw[$i]["15"])) {
            $spt1Model = sptModel($sptRaw[$i],"1");
            $spt2Model = sptModel($sptRaw[$i],"2");
            $spt1Raw[$i] = array(
                "owner" => "AWN",
                "closureNo" => $sptRaw[$i]["12"],
                "closureName" => $sptRaw[$i]["12"],
                "closureModel" => $spt1Model["model"],
                "state" => "In Service","running" => "Normal",
                "splitterRate" => $spt1Model["rate"],
                "siteNo" => $_POST["locationID"]."_".$sptRaw[$i]["2"],
                "address" => $sptRaw[$i]["0"],
                "longitude" => $sptRaw[$i]["13"],
                "latitude" => $sptRaw[$i]["14"],
                "VLG" => $sptRaw[$i]["2"],
                "OLT" => $sptRaw[$i]["10"],
                "Alias" => $sptRaw[$i]["10"]."_".sptAlias($sptRaw[$i]["10"],
                $sptRaw[$i]["12"],
                $spt1Model["model"],
                "1"));
            $spt2Raw[$i] = array(
                "owner" => "AWN",
                "closureNo" => $sptRaw[$i]["15"],
                "closureName" => $sptRaw[$i]["15"],
                "closureModel" => $spt2Model["model"],
                "state" => "In Service",
                "running" => "Normal",
                "splitterRate" => $spt2Model["rate"],
                "siteNo" => $_POST["locationID"]."_".$sptRaw[$i]["2"],
                "address" => $sptRaw[$i]["0"],
                "longitude" => $sptRaw[$i]["16"],
                "latitude" => $sptRaw[$i]["17"],
                "VLG" => $sptRaw[$i]["2"],
                "OLT" => $sptRaw[$i]["10"],
                "Alias" => $sptRaw[$i]["10"]."_".sptAlias($sptRaw[$i]["10"],
                $sptRaw[$i]["15"],
                $spt2Model["model"],
                "2"));
        } elseif (!empty($sptRaw[$i]["12"]) && empty($sptRaw[$i]["15"])) {
            $spt1Model = sptModel($sptRaw[$i],"1");
            $spt1Raw[$i] = array(
                "owner" => "AWN",
                "closureNo" => $sptRaw[$i]["12"],
                "closureName" => $sptRaw[$i]["12"],
                "closureModel" => $spt1Model["model"],
                "state" => "In Service",
                "running" => "Normal",
                "splitterRate" => $spt1Model["rate"],
                "siteNo" => $_POST["locationID"]."_".$sptRaw[$i]["2"],
                "address" => $sptRaw[$i]["0"],
                "longitude" => $sptRaw[$i]["13"],
                "latitude" => $sptRaw[$i]["14"],
                "VLG" => $sptRaw[$i]["2"],
                "OLT" => $sptRaw[$i]["10"],
                "Alias" => $sptRaw[$i]["10"]."_".sptAlias($sptRaw[$i]["10"],
                $sptRaw[$i]["12"],
                $spt1Model["model"],
                "1"));
        } else {
            $spt2Model = sptModel($sptRaw[$i],"2");
            $spt2Raw[$i] = array(
                "owner" => "AWN",
                "closureNo" => $sptRaw[$i]["15"],
                "closureName" => $sptRaw[$i]["15"],
                "closureModel" => $spt2Model["model"],
                "state" => "In Service",
                "running" => "Normal",
                "splitterRate" => $spt2Model["rate"],
                "siteNo" => $_POST["locationID"]."_".$sptRaw[$i]["2"],
                "address" => $sptRaw[$i]["0"],
                "longitude" => $sptRaw[$i]["16"],
                "latitude" => $sptRaw[$i]["17"],
                "VLG" => $sptRaw[$i]["2"],
                "OLT" => $sptRaw[$i]["10"],
                "Alias" => $sptRaw[$i]["10"]."_".sptAlias($sptRaw[$i]["10"],
                $sptRaw[$i]["15"],
                $spt2Model["model"],
                "2"));
        }
    }

    $splitterRaw = array("Splitter1" => $spt1Raw,"Splitter2" => $spt2Raw);
    usort($splitterRaw["Splitter1"], "sortClosure");
    usort($splitterRaw["Splitter2"], "sortClosure");
    return($splitterRaw);
}

function spaceSheet($site)
{
    $site = array_map("unserialize", array_unique(array_map("serialize", slice($site,"10"))));
    $vlg = array_values($site);
    for ($i=0; $i < count($vlg); $i++) {
        $village[$i] = array(
            "AWN",
            $vlg[$i]["2"],
            $vlg[$i]["1"],
            $vlg[$i]["0"],
            "SPT",
            $_POST["locationID"],
            $vlg[$i]["2"],
            $vlg[$i]["1"],
            $vlg[$i]["0"],
            "BKK",
            $vlg[$i]["0"],
            $vlg[$i]["3"],
            $vlg[$i]["4"],
            $vlg[$i]["5"],
            $vlg[$i]["8"],
            $vlg[$i]["9"]);
        $village[$i] = implode("	", $village[$i]);
    }

    return($village);
}

function closureSheet($closureRaw)
{
    $spt1Raw = $closureRaw["Splitter1"];
    $spt2Raw = $closureRaw["Splitter2"];
    foreach($spt1Raw as $spt1) {
        $sp1 = substr($spt1["closureNo"], 0,3);
        $splitter[] = array($spt1["owner"],$spt1["closureNo"],$spt1["closureName"],$spt1["closureModel"],$spt1["state"],$spt1["running"],$spt1["splitterRate"],$spt1["siteNo"],$spt1["address"],$spt1["longitude"],$spt1["latitude"],$spt1["Alias"]);
        foreach($spt2Raw as $spt2) {
            $sp2 = substr($spt2["closureNo"], 0,3);
            if ($sp1 === $sp2) {
                $splitter[] = array($spt2["owner"],$spt2["closureNo"],$spt2["closureName"],$spt2["closureModel"],$spt2["state"],$spt2["running"],$spt2["splitterRate"],$spt2["siteNo"],$spt2["address"],$spt2["longitude"],$spt2["latitude"],$spt2["Alias"]);
            }
        }
    }
    return($splitter);
}

/*function terminationSheet($termRaw)
{
    $sptODF = $termRaw["Splitter1"];
    $sptClosure = $termRaw["Splitter2"];
    $a = 0;
    if ($_POST["type"] == "huawei") {
        $x = 0;
        $y = 16;
    }else {
        $x = 1;
        $y = 17;
    }
        for ($i=0; $i < $y; $i++) {
            if (empty($sptODF[$i]["closureNo"])) {
                $sptODF[$i]["closureNo"] = "P".$i;
            }
            if (substr($sptODF[$i]["closureNo"], 1, 2) < 8) {
            }
            while ($x <= substr($sptODF[$i]["closureNo"], 1, 2)) {
                if (substr($sptODF[$i]["closureNo"], 1, 2) == $x) {
                    if (isset($sptODF[$i]["owner"])) {
                        $ODFTemp["ODF"][$x] = array("owner" => "AWN","deviceTypeA" => "ODF", "siteA" => $_POST["locationID"]."_".$sptODF[$i]["OLT"],"deviceNoA" =>"ODF_".$sptODF[$i]["OLT"]."_01","portNoA" => "1/".$x,"siteB" => $_POST["locationID"]."_".$sptODF[$i]["VLG"],"deviceTypeA" => "Closure","deviceNoZ" => $sptODF[$i]["closureNo"], "portNoZ" => "0/1");
                    } else {
                        $ODFTemp["ODF"][$x] = array("owner" => "AWN","deviceTypeA" => "ODF", "siteA" => "Not used","deviceNoA" =>"Not used","portNoA" => "1/".$x,"siteB" => "Not used","deviceTypeA" => "Closure","deviceNoZ" => "Not used", "portNoZ" => "Not used");
                    }
                } else {
                    $ODFTemp["ODF"][$x] = array("owner" => "AWN","deviceTypeA" => "ODF", "siteA" => "Not used","deviceNoA" =>"Not used","portNoA" => "1/".$x,"siteB" => "Not used","deviceTypeA" => "Closure","deviceNoZ" => "Not used", "portNoZ" => "Not used");
                }
                $x++;
            }
        }
    foreach($sptClosure as $spt2) {
        $sp2 = substr($spt2["closureNo"], 0,3);
        if ($portNo <= 8) {
            # code...
            $ODFTemp["ODF"][$x] = array("owner" => "AWN","deviceTypeA" => "Closure", "siteA" => $_POST["locationID"]."_".$sptClosure[$i]["VLG"],"deviceNoA" =>$sptClosure["closureNo"],"portNoA" => "1/".$portNo,"siteB" => $_POST["locationID"]."_".$sptClosure[$i]["VLG"],"deviceTypeA" => "Closure","deviceNoZ" => "Not used", "portNoZ" => "Not used");

            //$portNo++;
        }
        while (substr($sptODF[$a]["closureNo"], 1, 2) <= "8") {
            $ODFTemp["Closure"][$a] = array("owner" => "AWN","deviceTypeA" => "Closure", "siteA" => $_POST["locationID"]."_".$sptClosure[$a]["VLG"],"deviceNoA" =>$sptClosure[$a]["closureNo"],"portNoA" => "1/".$a+"1","siteB" => $_POST["locationID"]."_".$sptClosure[$i]["VLG"],"deviceTypeA" => "Closure","deviceNoZ" => "Not used", "portNoZ" => "Not used");
            $a++;
        }

    }

    return($ODFTemp);
}*/
//$terminationSpace = (terminationSheet(splitter($rawData)));

#########################
$vlgspace = spaceSheet($rawData); //print space sheet
$sp = closureSheet(splitter($rawData)); //print closure sheet
#########################

/*for ($i=0; $i < count($spt1); $i++) {
    $spt1 = array_values($spt1);
    $spt1[$i] = implode("	", $spt1[$i]);
}
for ($i=0; $i < count($spt2); $i++) {
    $spt2 = array_values($spt2);
    $spt2[$i] = implode("	", $spt2[$i]);
}*/
//$comspt1


//usort($a["Splitter1"], "sortClosure");

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    <pre>
        <textarea name="detail" rows="10" cols="150"><?php foreach (spaceSheet($rawData) as $spaceSheet) {
            echo $spaceSheet."\n";
        } ?></textarea>
        <textarea name="detail" rows="10" cols="150"><?php foreach (closureSheet(splitter($rawData)) as $spaceSplitter) {
            $ndata = implode("	", $spaceSplitter);
            $cdata = trim(preg_replace('/\s\s+/', '	', $ndata));
            echo $cdata."\n";
        } ?></textarea>
        <textarea name="detail" rows="10" cols="150"><?php ?></textarea>
    </pre>

    <a href="./" class="button">New OLT</a>
</body>
</html>
