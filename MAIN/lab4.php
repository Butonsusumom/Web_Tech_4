<?php function check(&$result, $reg, $remplace) {
    $orig = $result;
    $result = preg_replace($reg, $remplace, $result);
    return $orig !== $result;
}


function stem_word($word) {
    mb_internal_encoding('UTF-8');
    $PERFECTIVE = '/((ив|ивши|ившись|ыв|ывши|ывшись)|((?<=[ая])(в|вши|вшись)))$/';
    $REFLEXIVE = '/(с[яь])$/';
    $ADJECTIVE = '/(ее|ие|ые|ое|ими|ыми|ей|ий|ый|ой|ем|им|ым|ом|его|ого|еых|ую|юю|ая|яя|ою|ею)$/';
    $PARTICIPLE = '/((ивш|ывш|ующ)|((?<=[ая])(ем|нн|вш|ющ|щ)))$/';
    $VERB = '/((ила|ыла|ена|ейте|уйте|ите|или|ыли|ей|уй|ил|ыл|им|ым|ены|ить|ыть|ишь|ую|ю)|((?<=[ая])(ла|на|ете|йте|ли|й|л|ем|н|ло|но|ет|ют|ны|ть|ешь|нно)))$/';
    $NOUN = '/(а|о|ев|ов|ие|ье|е|иями|ями|ами|еи|ии|и|ией|ей|ой|ий|й|и|ы|ь|ию|ью|ю|ия|ья|я)$/';
    $IS_VOWEL = '/^(.*?[аеиоуыэюя])(.*)$/';
    // перевод слова к нижнему регистру
    $word = mb_strtolower($word);
    $word = str_replace('ё', 'е', $word);
    $result = $word;
    do {// избавление от окончаний
        if (!preg_match($IS_VOWEL, $word, $p)) break;
        $temp = $p[0];
        if (!$temp) break;
        if (!check($temp, $PERFECTIVE, '')) {
            check($temp,$REFLEXIVE, '');
            if (check($temp, $ADJECTIVE, '')) {
                check($temp, $PARTICIPLE, '');
            } else {
                if (!check($temp, $VERB, ''))
                    check($temp, $NOUN, '');
            }
        }
        check($temp, '/ь$/', '');
        $result = $temp;
    } while(false);
    return $result;
}



function button($search_word,$input_text) {
    if (file_exists($input_text)) {
        $file=file_get_contents($input_text);
        $text = htmlentities($file);
        if (preg_match("/^\".*\"$/", $search_word)) { // если введено несколько слов
            $search_word = substr($search_word, 1, strlen($search_word) - 2);
            $words = (preg_split('/\s+/', $search_word)); //преобразование строки в массив слов
            $res = NULL;
            foreach ($words as $key => $value) {
                $word = stem_word($value);
                $res .= $word . "\w*?\s";
            }
            $text = preg_replace("/(" . $res . ")/iu",
                "<span style=\"background:#8e2de2; color:white\">\\1</span>", $text);
        } else {
            $words = (preg_split('/\s+/', $search_word)); //преобразование строки в массив слов
            foreach ($words as $key => $value) {
                $word = stem_word($value);
                $text = preg_replace("/\s(" . $word . ")w*?/iu",
                    " <span style=\"background:#8e2de2; color:white\">\\1</span>", $text);
            }
        }
    }
    else{
        $text='<font color = #8b0000>Can`t open file</font>';
    }

    return $text;
}


?>


<!DOCTYPE html>
<html lang="ru-RU">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <link rel="stylesheet" href="/css/main.css">
    <link rel="stylesheet" href="/css/index.css">
    <link rel="stylesheet" href="/css/contact.css">
</head>
<body>
<section style="padding-top: 100px; padding-bottom: 90px;" class="about">
    <div class="about-header">

    </div>


    <div class="card">
        <form method="POST" >
            <input name="input_text" type="text" min="0"  class="feedback-input" placeholder="File"  value="<?php if (isset($_POST['enter'])) { echo $_POST['input_text'];}?>" required="required" >
            <input name="words_text" placeholder="Search" class="feedback-input"     value="<?php if (isset($_POST['enter'])) { echo $_POST['words_text'];}?>" type="text" required="required">

            <br><input type="submit" name="enter" value="SUBMIT">

            <?php
            $A = 1;

            try {
                $A = $A + 1;
            } catch (Exception $err) {
                $A += 2;
            } finally {
                $A++;
            }
            echo $A;
            $input_text = isset($_POST["input_text"]) ? $_POST["input_text"] : '';
            $search_word = isset($_POST["words_text"]) ? $_POST["words_text"] : '';
            if (isset($_POST['enter'])) {?>
            <h3><?php echo button($search_word,$input_text); echo $A; }?></h3>
        </form>
    </div>
</section>
<br>
<br>
<br>
<br>
<br>
</body>
</body>
</html>

