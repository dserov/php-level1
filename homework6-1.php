<?php
/**
 * Created by PhpStorm.
 * User: MegaVolt
 * Date: 16.11.2020
 * Time: 10:13
 */

$result = "Тут будет результат расчета";
$oper1 = '';
$oper2 = '';
$operationList = ['+' => 'Сложение', '-' => 'Вычитание', '*' => 'Умножение', '/' => 'Деление'];
$operation = '+';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        // проверка параметров
        $oper1 = isset($_POST['operand1']) ? doubleval(str_replace(',', '.', $_POST['operand1'])) : null;
        $oper2 = isset($_POST['operand2']) ? doubleval(str_replace(',', '.', $_POST['operand2'])) : null;
        $operation = isset($_POST['operation']) ? $_POST['operation'] : null;

        if (is_null($oper1) || !is_numeric($oper1)
            || is_null($oper2) || !is_numeric($oper2)
            || is_null($operation) || !isset($operationList[$operation])
        )
            throw new MyCalcException('Неверно переданные параметры');

        switch ($operation) {
            case '+':
                $result = $oper1 + $oper2;
                break;
            case '-':
                $result = $oper1 - $oper2;
                break;
            case '*':
                $result = $oper1 * $oper2;
                break;
            case '/':
                if ($oper2 == 0)
                    throw new MyCalcException("Делить на ноль нельзя!");
                $result = $oper1 / $oper2;
                break;
        }
    } catch (MyCalcException $e) {
        $result = $e->getMessage();
    }
}



class MyCalcException extends Exception
{
}

?>

<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
<h1>Калькулятор</h1>
<h3>целых и дробных чисел</h3>
<p>1. Создать форму-калькулятор операциями: сложение, вычитание, умножение, деление. Не забыть обработать деление на
    ноль!
    Выбор операции можно осуществлять с помощью тега 'select'.</p>
<hr>
<div style="display: flex">
    <form action="?" method="post" style="margin:0">
        <input type="text" name="operand1" value="<?=$oper1?>" placeholder="первое число">
        <select name="operation">
            <?php
                foreach ($operationList as $key => $description)
                    echo sprintf("<option value=\"%s\" %s>%s</option>", $key, ($key == $operation ? 'selected' : ''), $description);
            ?>
        </select>
        <input type="text" name="operand2" value="<?=$oper2?>" placeholder="второе число">
        <input type="submit" name="result" value="=">
    </form>
    <span style="margin-left: 10px; border: 1px solid black"><b><?= $result ?></b></span>
</div>
</body>
</html>
