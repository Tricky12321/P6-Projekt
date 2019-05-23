#!/usr/bin/php
<?php
$linterRegex = array(
    "/(po-group|pogroup|product owner)/i" => "PO group",
    "/(scrum\sGroup|scrum-group)/i" => "process group",
    "/(backend-group)/i" => "backend group",
    "/(frontend-group)/i" => "frontend group",
    "/(choose\scitizen\sscreen)/i" => "citizen selection screen",
    "/(offline-mode)/i" => "offline mode",
    "/(user\sstory|userstory)/i" => "issue",
    "/(flutter)/" => "Flutter",
    "/(user\sinterface)/" => "UI",
    "/(re-design|re\sdesign)/" => "redesign",
    "/(client\sapi|client-api)/" => "client API",
    "/(api)/" => "API",
    "/(Costumers|costomer)/" => "customer",
    "/(Stakeholder)/" => "customer",
    "/(github|Github|git\hub)/" => "GitHub",
    "/(webapi|web-api)/" => "web-API",
    "/(multi-platform|multi\splatform)/" => "multiplatform",
    "/(multiproject|multi\sproject)/" => "multi-project",
    "/(bloc)/" => "BLoC",
    "/(\\cite\{)/" => "\\citep",
    "/(giraf|Giraf)/" => "GIRAF",
    "/([^(\sf\.eks|fx|\seg|\sex|etc)]\.\s+[a-z])/" => "Capital letter after period"


);


$path = getcwd() . "/";
if ($argc == "2") {
    $path = $argv[1];
}
function getDirContents($dir, &$results = array())
{
    if (is_file($dir)) {
        return $dir;
    }
    $files = scandir($dir);

    foreach ($files as $key => $value) {
        $path = realpath($dir . DIRECTORY_SEPARATOR . $value);
        if (!is_dir($path)) {
            if (stripos($path, ".tex") !== false) {
                $results[] = $path;
            }
        } else if ($value != "." && $value != "..") {
            getDirContents($path, $results);
            //$results[] = $path;
        }
    }

    return $results;
}


function CheckAllFiles($files, $linter)
{
    $output = array();
    if (is_array($files)) {
        foreach ($files as $file) {
            CheckFile($file, $linter, $output);
        }
    } else {
        CheckFile($files, $linter, $output);
    }
    return array_unique($output);
}

function CheckFile($file, $linter, &$output = array())
{
    foreach ($linter as $regex => $replacement) {

        $fh = fopen($file, 'rb');
        $line_nr = 1;
        while ($line = fgets($fh)) {
            if (preg_match($regex, $line, $matches, PREG_OFFSET_CAPTURE)) {
                foreach ($matches as $match) {
                    $val ="$file, Line $line_nr:$match[1], '$match[0]' should be '$replacement'";
                    if (!array_key_exists($val, $output)) {
                        $output[] = $val;
                    }
                }
            }
            $line_nr++;
        }
    }
}

$contents = getDirContents($path);
$output = CheckAllFiles($contents, $linterRegex);
foreach ($output as $error) {
    echo "$error\n";
}

echo count($output) . " errors found!";
