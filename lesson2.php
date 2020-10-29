<?php
/**
 * 1. Объявить две целочисленные переменные $a и $b и задать им произвольные начальные значения. Затем написать скрипт, который работает по следующему принципу:
 * если $a и $b положительные, вывести их разность;
 * если $а и $b отрицательные, вывести их произведение;
 * если $а и $b разных знаков, вывести их сумму;
 * ноль можно считать положительным числом.
 */


$a = rand(-10, 10);
$b = rand(-10, 10);

if ($a >= 0 && $b >= 0) {
    echo "оба положительные, разность = " . ($a - $b);
} elseif ($a < 0 && $b < 0) {
    echo "оба отрицательные, произведение = " . ($a * $b);
} else {
    // похоже, знаки у чисел не совпадают
    echo "разнознаковые, сумма = " . ($a + $b);
}
echo PHP_EOL . PHP_EOL;

/**
 * 2. Присвоить переменной $а значение в промежутке [0..15]. С помощью оператора switch организовать вывод чисел от $a до 15.
 */

// вариант без рекурсии
$a = rand(0, 15);
while ($a <= 15) {
    switch ($a) {
        case 0:
            echo "0";
            break;
        case 1:
            echo "1";
            break;
        case 2:
            echo "2";
            break;
        case 3:
            echo "3";
            break;
        case 4:
            echo "4";
            break;
        case 5:
            echo "5";
            break;
        case 6:
            echo "6";
            break;
        case 7:
            echo "7";
            break;
        case 8:
            echo "8";
            break;
        case 9:
            echo "9";
            break;
        case 10:
            echo "10";
            break;
        case 11:
            echo "11";
            break;
        case 12:
            echo "12";
            break;
        case 13:
            echo "13";
            break;
        case 14:
            echo "14";
            break;
        case 15:
            echo "15";
            break;
    }
    echo PHP_EOL;
    $a++;
}

// вариант с рекурсией
$a = rand(0, 15);
prn_number($a);
function prn_number($x)
{
    if ($x > 15)
        return;
    else
        echo $x . PHP_EOL;
    prn_number($x + 1);
}


/**
 * 3. Реализовать основные 4 арифметические операции в виде функций с двумя параметрами. Обязательно использовать оператор return.
 *
 * 4. Реализовать функцию с тремя параметрами: function mathOperation($arg1, $arg2, $operation), где $arg1, $arg2 – значения аргументов,
 * $operation – строка с названием операции. В зависимости от переданного значения операции выполнить одну из арифметических операций
 * (использовать функции из пункта 3) и вернуть полученное значение (использовать switch).
 *
 */

function summa($a, $b)
{
    return $a + $b;
}

function difference($a, $b)
{
    return $a - $b;
}

function multiply($a, $b)
{
    return $a * $b;
}

function division($a, $b)
{
    if ($b == 0) throw new Exception('Делить на ноль нельзя!');
    return $a / $b;
}

function mathOperation($arg1, $arg2, $operation)
{
    switch ($operation) {
        case '+':
            return sprintf("сумма\t\t %d и %d = %d\n", $arg1, $arg2, summa($arg1, $arg2));
        case '-':
            return sprintf("разница\t\t %d и %d = %d\n", $arg1, $arg2, difference($arg1, $arg2));
        case '*':
            return sprintf("умножение\t %d и %d = %d\n", $arg1, $arg2, multiply($arg1, $arg2));
        case '/':
            return sprintf("деление\t\t %d и %d = %f\n", $arg1, $arg2, division($arg1, $arg2));
        default:
            throw new  Exception('неизвестный оператор = ' . $operation);
    }
}

try {
    $a = rand(-10, 10);
    $b = rand(-10, 10);
    echo mathOperation($a, $b, '*');
    echo mathOperation($a, $b, '+');
    echo mathOperation($a, $b, '-');
    echo mathOperation($a, $b, '/');
    echo mathOperation($a, $b, '^');
} catch (Exception $e) {
    echo $e->getMessage();
}

/**
 * 5. Посмотреть на встроенные функции PHP. Используя имеющийся HTML шаблон, вывести текущий год в подвале при помощи встроенных функций PHP.
 */
echo "Выполнено. Смотреть в папке lesson1\n";


/**
 * 6. *С помощью рекурсии организовать функцию возведения числа в степень. Формат: function power($val, $pow), где $val – заданное число, $pow – степень.
 * ТОЛЬКО ДЛЯ $pow >= 0 !!!!!!
 */

$val = rand(0, 10);
$pow = rand(0, 5);
function power($val, $pow)
{
    if ($pow == 1) return $val;
    if ($pow == 0) return 1;
    return $val * power($val, --$pow);
}

// test
$i = 100;
while ($i > 0) {
    echo $i . PHP_EOL;
    $i--;
    $val = rand(0, 10);
    $pow = rand(0, 10);
    if (power($val, $pow) != pow($val, $pow)) {
        throw new Exception('error!!!');
    }
}

/**
 * 7. *Написать функцию, которая вычисляет текущее время и возвращает его в формате с правильными склонениями, например:
 * 22 часа 15 минут
 * 21 час 43 минуты
 */

$hh = date('H');
$mm = date('i');
echo $hh . ' ' . format_hh($hh) . ' ' . $mm . ' ' . format_mm($mm) . PHP_EOL;

$hh = 22;
$mm = 15;

echo $hh . ' ' . format_hh($hh) . ' ' . $mm . ' ' . format_mm($mm) . PHP_EOL;
$hh = 21;
$mm = 43;
echo $hh . ' ' . format_hh($hh) . ' ' . $mm . ' ' . format_mm($mm) . PHP_EOL;

function format_hh($hh)
{
    switch ($hh) {
        case 1:
        case 21:
            return "час";
        case 2:
        case 3:
        case 4:
        case 22:
        case 23:
            return "часа";
        default:
            return "часов";
    }
}

function format_mm($mm)
{
    if ($mm >= 10 && $mm <= 19)
        return "минут";
    $ed = $mm % 10;
    switch ($ed) {
        case 1;
            return "минута";
        case 2;
        case 3;
        case 4;
            return "минуты";
        default:
            return "минут";
    }
}
