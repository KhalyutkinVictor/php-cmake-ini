<?php

function printHelp() {
    echo "cmake-ini help:\n"
        . "    --help                 Show this help\n"
        . "    --name=<name>          Required. Specifies the name of the project\n"
        . "    --lang=<lang>          C or CXX. C by default\n"
        . "    --stndrt=<standart>    17 by default. Specifies the standart version of language\n"
        . "    --out=<out path>       Path to output(project) directory\n"
        . "    --exportcc=<yes|no>    If yes, generates a compile_commands.json file\n";
}

function getArgVal($name) {
    global $argv;
    foreach ($argv as $arg) {
        if (strpos($arg, '--' . $name . '=') !== 0) {
            continue;
        }

        $kv = explode('=', $arg, 2);
        return $kv[1];
    }
    return null;
}

function getArgValOrError($name, $error) {
    $val = getArgVal($name);
    if ($val === null) {
        echo "Error\n    " . $error . "\n\n";
        printHelp();
        exit(23);
    }
    return $val;
}

$statusCode = 0;
$output = [];
exec("cmake --version", $output, $statusCode);

if ($statusCode !== 0) {
    echo "Cmake not found in path, or some error happen when execute > cmake --version" . PHP_EOL;
    exit(22);
}

function getCppDefaultEntryContent() {
    return "#include <iostream>\n\n"
        . "int main()\n{\n"
        . "    std::cout << \"Hello, world!!!\\n\";\n"
        . "    return 0;\n"
        . "}\n";
}

function getCDefaultEntryContent() {
    return "#include <stdio.h>\n\n"
        . "int main()\n{\n"
        . "    printf(\"Hello, world!!!\\n\");\n"
        . "    return 0;\n"
        . "}\n";
}

function getContentForLang($lang) {
    $output = 'Hello, world!!!';
    switch ($lang) {
        case 'CXX':
            $output = getCppDefaultEntryContent();
            break;
        
        case 'C':
            $output = getCDefaultEntryContent();
            break;
    }
    return $output;
}

$version = explode(' ', $output[0])[2];
$projectName = getArgValOrError('name', '--name argument is required (--name=myproject)');
$projectLang = getArgVal('lang') ?? 'C';
$projectLangStandart = getArgVal('stndrt') ?? '17';

$projectEntry = 'main.c';
$projectLangStandartProp = 'CMAKE_C_STANDART';
if ($projectLang === 'CXX') {
    $projectLangStandartProp = 'CMAKE_CXX_STANDART';
    $projectEntry = 'main.cpp';
}

$fileContent = "cmake_minimum_required(VERSION {$version})\n"
    . "project({$projectName} {$projectLang})\n\n"
    . "set({$projectLangStandartProp} {$projectLangStandart})\n\n"
    . 'add_executable(${PROJECT_NAME} ' . $projectEntry . ')' . PHP_EOL;

$outDir = getArgVal('out') ?? './';

if (file_exists($outDir . '/CMakeLists.txt')) {
    echo "Error\n    CMakeLists.txt is already exists in output directory\n";
    exit(24);
}

file_put_contents($outDir . '/CMakeLists.txt', $fileContent);

$fileContent = getContentForLang($projectLang);

file_put_contents($outDir . '/' . $projectEntry, $fileContent);

$exportcc = getArgVal('exportcc');
if ($exportcc && (strcasecmp($exportcc, 'yes') === 0 || strcasecmp($exportcc, 'y') === 0)) {
    exec('cmake -DCMAKE_EXPORT_COMPILE_COMMANDS=On ' . $outDir, $output, $statusCode);
    if ($statusCode !== 0) {
        echo "Warning:\n"
            . "    > cmake -DCMAKE_EXPORT_COMPILE_COMMANDS=On {$outDir}\n"
            . "    command execution failed with code {$statusCode}. More details bellow:\n"
            . '    ' . implode("\n    ", $output) . "\n";
    }
}
