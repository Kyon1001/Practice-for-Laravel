<?php

$file = 'tasks.txt';

$title = $_POST['title'] ?? '';
$deleteIndex = $_POST['delete_index']  ?? null;

$tasks = file_exists($file)
    ? file($file, FILE_IGNORE_NEW_LINES)
    : [];

//追加
if($title != ''){
    $tasks[] = $title;
}

//削除
if($deleteIndex !== null){
    unset($tasks[$deleteIndex]);
    $tasks = array_values($tasks);
}

//保存し直す
file_put_contents($file,  implode(PHP_EOL, $tasks) . PHP_EOL);

?>

<h1>ToDoアプリ</h1>

<form method="post">
    <input type="text" name="title">
    <button type="submit">追加</button>
</form>

<h2>タスク一覧</h2>

<ul>
    <?php foreach($tasks as $index => $task): ?>
        <li>
            <?php echo $task; ?>

            <form method="POST" style="display:inline;">
                <input type="hidden" name="delete_index" value="<?php echo $index; ?>">
                <button type="submit">削除</button>
            </form>
        </li>
    <?php endforeach; ?>
</ul>