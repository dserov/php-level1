<?php

$errors = [];
$uploadDir = 'foto' . DIRECTORY_SEPARATOR;
define('THUMBNAIL_DIR', 'thumb' . DIRECTORY_SEPARATOR);
define('THUMBNAIL_WIDTH', 128);
define('THUMBNAIL_HEIGHT', 128);

function readGallery($uploadDir, &$errors) {
    $files = @scandir($uploadDir);
    if (!$files) {
        $errors[] = 'Не удалось прочитать директорию галереи';
        return;
    }
    foreach ($files as $file) {
        $fullImageFile = $uploadDir . $file;
        if(!is_file($fullImageFile) || !is_readable($fullImageFile)) {
            continue;
        };
        // ссылка на превьюшку
        $thumbImageFile = $uploadDir . THUMBNAIL_DIR . $file;
        if (!is_readable($thumbImageFile)) {
            $thumbImageFile = $fullImageFile;
        }

        // из путей сделаем ссылки
        $fullImageFile = str_replace(DIRECTORY_SEPARATOR, '/', $fullImageFile);
        $thumbImageFile = str_replace(DIRECTORY_SEPARATOR, '/', $thumbImageFile);
        echo sprintf('<a class="gallery-link" data-fancybox="gallery" href="%s"><img class="gallery-img" src="%s" width="%d" height="%d"></a>', $fullImageFile, $thumbImageFile, THUMBNAIL_WIDTH, THUMBNAIL_HEIGHT);
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_FILES) {
    processFiles($uploadDir, $errors);
}

function processFiles($uploadDir, &$errors) {
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

function processFile($uploadDir, $tmpName) {
    // генерим уникальное имя
    $extension = getTrueExtension($tmpName);
    if ($extension === false)
        return 'Необрабатываемый тип файла';
    $newName = getUniqueName($uploadDir, $extension);
    if ($newName === false)
        return 'Не удалось получить уникальное имя файла';

    // переносим куда надо
    if(!move_uploaded_file($tmpName, $uploadDir . $newName)) {
        return 'Не удалось переместить/переименовать файл';
    }

    // создадим превьюшку
    if (resizeFile($uploadDir, $newName, $extension) === false)
        return 'Не удалось создать превьюшку';
}

/**
 * Ресайз картинки. Для упрощения кода не будем проверять существование фукций и результат их вызова
 * и НЕ БУДЕМ ЗАМОРАЧИВАТЬСЯ С ГЕОМЕТРИЕЙ
 *
 * @param $uploadDir
 * @param $newName
 * @param $extension
 * @return mixed
 */
function resizeFile($uploadDir, $newName, $extension) {
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
function getTrueExtension($tmpName) {
    $finfo = new finfo(FILEINFO_MIME_TYPE );
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
function getUniqueName($dir, $ext) {
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

?>
<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Галерея фотографий</title>

    <link rel="stylesheet" href="css/main.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.css" />
</head>
<body>
    <h1>Фотогалерея</h1>
    <div class="clearfix"></div>
    <div class="gallery">
        <?php readGallery($uploadDir, $errors) ?>
    </div>
    <form method="post" enctype="multipart/form-data">
        <h2>Загрузка новой картинки</h2>
        <input type="file" name="file-upload" id="file-upload"/>
        <button type="submit">Загрузить</button>
    </form>
    <div class="error-list"><?php if($errors) echo implode('<br>1', $errors) ?></div>
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.js"></script>
</body>
</html>
