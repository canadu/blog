<?php

define('STATUS', array('active', 'deactive'));

$category_array = array(
  'education' => '教育',
  'animals' => 'ペットや動物',
  'technology' => 'テクノロジー',
  'fashion' => 'ファッション',
  'entertainment' => '娯楽',
  'movies' => '映画',
  'gaming' => 'ゲーム',
  'music' => '音楽',
  'sports' => 'スポーツ',
  'news' => 'ニュース',
  'travel' => '旅行',
  'comedy' => 'お笑い',
  'design' => 'デザインや開発',
  'food' => '食べ物',
  'lifestyle' => '生活',
  'personal' => '人物',
  'health' => '健康',
  'business' => '仕事',
  'shopping' => '買い物',
  'animations' => 'アニメ',
);

function h($str)
{
  return htmlspecialchars(strval($str), ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

// ファイル名を元に拡張子を返す関数
function getExtensions(string $file): string
{
  return pathinfo($file, PATHINFO_EXTENSION);
}

// アップロードファイルの妥当性をチェックする関数
function validateImage(): array
{
  // PHPによるエラーを確認する
  if ($_FILES['image']['error'] !== UPLOAD_ERR_OK) {
    return [false, 'アップロードエラーを検出しました'];
  }

  // ファイル名から拡張子をチェックする
  if (!in_array(getExtensions($_FILES['image']['name']), ['jpg', 'jpeg', 'png', 'gif'])) {
    return [false, '画像ファイルのみアップロード可能です'];
  }

  // ファイルの中身を見てMIMEタイプをチェックする
  $finfo = finfo_open(FILEINFO_MIME_TYPE);
  $mimeType = finfo_file($finfo, $_FILES['image']['tmp_name']);
  finfo_close($finfo);
  if (!in_array($mimeType, ['image/jpeg', 'image/png', 'image/gif'])) {
    return [false, '不正確な画像ファイル形式です'];
  }

  //ファイルサイズをチェックする
  if (filesize($_FILES['image']['tmp_name']) > 1024 * 1024 * 2) {
    return [false, '画像のサイズが大きすぎます。画像サイズは2MBまでです。'];
  }
  return [true, null];
}

// アップロード後に保存ファイル名を生成して返す関数
function generateImageName($name): string
{
  return date('Ymd-His-') . rand(100000, 99999) . '.' . getExtensions($name);
}
