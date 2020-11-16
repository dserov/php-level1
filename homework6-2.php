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
$operationList = ['sum' => 'Сложение', 'sub' => 'Вычитание', 'mul' => 'Умножение', 'div' => 'Деление'];
$operation = 'sum';

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
            case 'sum':
                $result = $oper1 + $oper2;
                break;
            case 'sub':
                $result = $oper1 - $oper2;
                break;
            case 'mul':
                $result = $oper1 * $oper2;
                break;
            case 'div':
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
    <style>
        input[type=submit] {
            width: 50px;
            height: 50px;
            display: flex;
            justify-content: center;
            align-items: center;
            margin-left: 20px;
        }
    </style>
</head>
<body>
<h1>Калькулятор</h1>
<h3>целых и дробных чисел</h3>
<p>2. Создать калькулятор, который будет определять тип выбранной пользователем операции, ориентируясь на нажатую кнопку.</p>
<hr>
    <form action="?" method="post">
        <input type="text" name="operand1" value="<?=$oper1?>" placeholder="первое число">
        <input type="text" name="operand2" value="<?=$oper2?>" placeholder="второе число">
        <span>=</span>
        <span style="margin-left: 10px; border: 1px solid black"><b><?= $result ?></b></span>
        <br>
        <br>
        <div style="display: flex">
        <?php
            foreach ($operationList as $key => $description)
                echo sprintf("<input name=\"operation\" type='submit' value=\"%s\" title=\"%s\">", $key, $description);
        ?>
        </div>
    </form>
</body>
</html>
