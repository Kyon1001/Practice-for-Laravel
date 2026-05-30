<?php

// 保存先ファイル名
$file = 'tasks.txt';

// フォームから送られたら値を受け取る
// NULL合体演算子　??の左があれば使う、なければ右を使う
$title = $_POST['title'] ?? '';
$editIndex= $_POST['edit_index'] ?? null;
$deleteIndex = $_POST['delete_index']  ?? null;

// そのファイルは存在する？
// 三項演算子　条件 ? 真の場合 : 偽の場合
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
    <!-- タスク一覧を表示 -->
    <?php foreach($tasks as $index => $task): ?>
        <li>
            <?php echo $task; ?>

            <!-- 編集ボタン：今は番号を送るだけ -->
            <form method="POST" style="display:inline;">
                <input type="hidden" name="edit_index" value="<?php echo $index; ?>">
                <button type="submit">編集</button>
            </form>

            <!-- 削除ボタン：番号を送って削除する -->
            <form method="POST" style="display:inline;">
                <input type="hidden" name="delete_index" value="<?php echo $index; ?>">
                <button type="submit">削除</button>
            </form>
        </li>
    <?php endforeach; ?>
</ul>