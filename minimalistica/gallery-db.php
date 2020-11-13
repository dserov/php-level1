<?php

require_once 'db.inc';

$errors = [];
$uploadDir = 'foto' . DIRECTORY_SEPARATOR;
define('THUMBNAIL_DIR', 'thumb' . DIRECTORY_SEPARATOR);
define('THUMBNAIL_WIDTH', 128);
define('THUMBNAIL_HEIGHT', 128);

if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_FILES) {
    processFiles($uploadDir, $errors);
}

$pictureId = null;
if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['show'])) {
    $pictureId = $_GET['show'];
}

function showPicture($pictureId, &$errors)
{
    $picture = DBQueryOne("SELECT * from pictures where id=? limit 1;", $pictureId);
    if (!$picture) {
        $errors[] = sprintf("Фото с кодом '%d' не найдено!", $pictureId);
        return;
    }
    echo sprintf('<img class="single-picture" src="%s" alt="%s"><p>Просмотров: %d</p>',
        str_replace(DIRECTORY_SEPARATOR, '/', $picture['path'] . $picture['name']),
        $picture['alt'],
        $picture['click']
    );
    DBQueryOne("UPDATE pictures SET click=click+1 where id=? limit 1;", $pictureId);
}

function readGallery(&$errors)
{
    $pictures = DBQueryMany("SELECT * FROM pictures ORDER BY click DESC");
    if (!$pictures) {
        $errors[] = 'Галерея пуста';
        return;
    }
    foreach ($pictures as $picture) {
        $fullImageFile = $picture['path'] . DIRECTORY_SEPARATOR . $picture['name'];
        if (!is_file($fullImageFile) || !is_readable($fullImageFile)) {
            // Удалим отсутсвующий
            DBQueryOne("DELETE FROM pictures where id=? limit 1", $picture['id']);
            continue;
        };
        // ссылка на превьюшку
        $thumbImageFile = $picture['path'] . THUMBNAIL_DIR . $picture['name'];
        if (!is_readable($thumbImageFile)) {
            $thumbImageFile = $fullImageFile;
        }

        // из путей сделаем ссылки
        $thumbImageFile = str_replace(DIRECTORY_SEPARATOR, '/', $thumbImageFile);
        echo sprintf('<a class="gallery-link" data-fancybox="gallery" href="?show=%d">
            <img class="gallery-img" src="%s" width="%d" height="%d" alt="%s">
            <p>Просмотров: %d</p>
            <p>Размер: %s</p></a>',
            $picture['id'], $thumbImageFile, THUMBNAIL_WIDTH, THUMBNAIL_HEIGHT,
            $picture['alt'], $picture['click'], formatBytes($picture['size']));
    }
}

function processFiles($uploadDir, &$errors)
{
    foreach ($_FILES as $file) {
        if ($file['error'] != 0) {
            $errors[] = sprintf('Файл "%s" не загрузился, код ошибки %d', $file['name'], $file['error']);
            continue;
        }
        $result = processFile($uploadDir, $file['tmp_name']);
        if (is_string($result)) {
            $errors[] = sprintf('Файл "%s" не загрузился, ошибка "%s"', $file['name'], $result);;
        }
    }
}

function processFile($uploadDir, $tmpName)
{
    // генерим уникальное имя
    $extension = getTrueExtension($tmpName);
    if ($extension === false)
        return 'Необрабатываемый тип файла';
    $newName = getUniqueName($uploadDir, $extension);
    if ($newName === false)
        return 'Не удалось получить уникальное имя файла';

    // переносим куда надо
    if (!move_uploaded_file($tmpName, $uploadDir . $newName)) {
        return 'Не удалось переместить/переименовать файл';
    }

    // создадим превьюшку
    if (resizeFile($uploadDir, $newName, $extension) === false)
        return 'Не удалось создать превьюшку';

    // сохраним в БД инфу
    $fileSize = filesize($uploadDir . $newName);
    DBQueryOne("INSERT INTO pictures (path, `name`, `size`) values(?,?,?);",
        $uploadDir,
        $newName,
        (!$fileSize ? 0 : $fileSize)
    );
}

/**
 * Ресайз картинки. Для упрощения кода не будем проверять существование функций и результат их вызова
 * и НЕ БУДЕМ ЗАМОРАЧИВАТЬСЯ С ГЕОМЕТРИЕЙ
 *
 * @param $uploadDir
 * @param $newName
 * @param $extension
 * @return mixed
 */
function resizeFile($uploadDir, $newName, $extension)
{
    $functions = [
        'jpg' => ['imagecreatefromjpeg', 'imagejpeg'],
        'png' => ['imagecreatefrompng', 'imagepng'],
        'gif' => ['imagecreatefromgif', 'imagegif'],
    ];
    // make resource
    $image = $functions[$extension][0]($uploadDir . $newName);
    $image = imagescale($image, THUMBNAIL_WIDTH, THUMBNAIL_HEIGHT);
    // save picture
    return $functions[$extension][1]($image, $uploadDir . THUMBNAIL_DIR . $newName);
}

/**
 * Получим расширение по РЕАЛЬНОМУ содержимому файла
 *
 * @param $tmpName
 * @return bool|string
 */
function getTrueExtension($tmpName)
{
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mimetype = $finfo->file($tmpName);
    switch ($mimetype) {
        case 'image/jpeg':
            return 'jpg';
        case 'image/png':
            return 'png';
        case 'image/gif':
            return 'gif';
    }
    return false;
}

/**
 * Получение уникального имени файла в заданном каталоге
 *
 * @param string $dir путь, где лежат фотки
 * @param string $ext требуемое расширение
 * @return bool|string
 */
function getUniqueName($dir, $ext)
{
    $i = 100;
    do {
        $tmpName = mb_substr(uniqid(), 0, 8) . ($ext ? "." . $ext : "");
        $i--;
    } while (file_exists($dir . $tmpName) && $i > 0);
    if ($i <= 0) {
        // за 100 попыток уникальное имя не получили
        return false;
    }
    return $tmpName;
}

/**
 * Форматируем строку с размером
 *
 * @param $bytes
 * @param int $precision
 * @return string
 */
function formatBytes($bytes, $precision = 2) {
    $units = array('B', 'KB', 'MB', 'GB', 'TB');
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    $bytes /= pow(1024, $pow);
    return round($bytes, $precision) . ' ' . $units[$pow];
}

?>
<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Галерея фотографий с использованием СУБД</title>

    <link rel="stylesheet" href="css/gallery.css"/>
</head>
<body>
<h1>Фотогалерея</h1>
<div class="clearfix"></div>
<?php if ($pictureId) {
    echo "<div><a href='?'>Просмотр всех миниатюр</a></div>";
    showPicture($pictureId, $errors);
} else { ?>
    <div class="gallery">
        <?php readGallery($errors) ?>
    </div>
<?php } ?>
<?php  if($errors) echo '<div class="error-list">' . implode('<br>1', $errors) . '</div>' ?>
<form method="post" enctype="multipart/form-data">
    <h2>Загрузка новой картинки</h2>
    <input type="file" name="file-upload" id="file-upload"/>
    <button type="submit">Загрузить</button>
</form>
<script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.min.js"></script>
</body>
</html>
