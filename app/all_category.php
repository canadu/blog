<?php
require_once 'components/connect.php';
require_once 'components/functions.php';

session_start();

if (isset($_SESSION['user_id'])) {
  $user_id = $_SESSION['user_id'];
} else {
  $user_id = '';
}

require_once 'components/like_post.php';
?>
<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>カテゴリー</title>

  <!-- font awesome cdn link  -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

  <!-- custom css file link  -->
  <link rel="stylesheet" href="css/style.css">

</head>

<body>

  <?php include 'components/user_header.php'; ?>
  <section class="categories">

    <h1 class="heading">投稿カテゴリー</h1>

    <div class="box-container">
      <?php $i = 1; ?>
      <?php foreach ($category_array as $key => $value) {
        $num = str_pad($i, 2, 0, STR_PAD_LEFT);
      ?>
        <div class="box"><span><?php echo $num; ?></span><a href="category.php?category=<?php echo $key; ?>"><?php echo $value; ?></a></div>
      <?php
        $i++;
      }
      ?>
    </div>

  </section>

  <?php include 'components/footer.php'; ?>

  <script src="js/script.js"></script>

</body>

</html>