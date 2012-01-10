<?php
    $phar = new Phar(__DIR__ . '/../Yak.phar');
    $phar->buildFromDirectory(__DIR__ . '/../lib');

    $runFile = implode(PHP_EOL, array_slice(file(__DIR__ . '/run.php'), 2));
    $runFile = str_replace("__DIR__", "'phar://' . __FILE__ ", $runFile);
    $runFile = str_replace('/../lib', '', $runFile);

    $stub = "#!/usr/bin/env php" . PHP_EOL;
    $stub .= "<?php" . PHP_EOL;
    $stub .= "Phar::mapPhar();" . PHP_EOL;
    $stub .= $runFile . PHP_EOL;
    $stub .= "__HALT_COMPILER();" . PHP_EOL;
    $phar->setStub($stub);

?>
