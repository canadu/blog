<?php

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
  return htmlspecialchars($str, ENT_QUOTES);
}
