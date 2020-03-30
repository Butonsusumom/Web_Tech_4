<?php

include "variable.php";
include "config.php";
    
function LoadData($id) {
    $mySQL = mysqli_connect('localhost', 'root', 'Blumblum1@', 'dataphp');
	if (!$mySQL) {
    echo "Ошибка: Невозможно установить соединение с MySQL." . PHP_EOL;
    echo "Код ошибки errno: " . mysqli_connect_errno() . PHP_EOL;
    echo "Текст ошибки error: " . mysqli_connect_error() . PHP_EOL;
    exit;
}
    $SQL = "SELECT `word` FROM new_table WHERE id = $id";
    $request = mysqli_query($mySQL, $SQL);
    $data = mysqli_fetch_assoc($request);
    
    return $data['word'];
}
    
$template = file_get_contents("main.tpt");
    
$template = preg_replace("/({FILE=\")([a-zA-z.0-9_]*)(\"})/u", "<?php include \"$2\"; ?>", $template);
$template = preg_replace("/({CONFIG=\")([a-zA-z.0-9_]*)(\"})/u", "<?=$$2?>", $template);
$template = preg_replace("/({VAR=\")([a-zA-z.0-9_]*)(\"})/u", "<?=\$VARS['$2']?>", $template);
    
$template = preg_replace("/({IF \")([a-zA-z.0-9_]*)(\")([><=!]*)(\")([a-zA-z.0-9_]*)(\"})/u", 
                        "<?php if($2 $4 $6): ?>", $template);
$template = preg_replace("/({ELSE})/u", "<?php else: ?>", $template);
$template = preg_replace("/({ENDIF})/u", "<?php endif; ?>", $template);
    
$template = preg_replace("/({DB=\")([a-zA-z.0-9_]*)(\"})/u", 
                        "<?=LoadData($2)?>", $template);
    
file_put_contents("temp", $template);
include "temp";
unlink("temp");