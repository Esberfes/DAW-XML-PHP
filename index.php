<?php
require_once('php/MoviesManager.php');
require_once('php/LayoutBuilder.php');
$manager = new MoviesManager("movies.xml");
?>
<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <?php LayoutBuilder::get_styles(); ?>
    <title>Home</title>
  </head>
  <body>
    <?php LayoutBuilder::get_the_nav("home"); ?>

    <main class="container">
        <?php $manager->render_items(); ?>
    </main>

    <?php LayoutBuilder::get_the_footer(); ?>
    <?php LayoutBuilder::get_scripts(); ?>

  </body>
</html>