<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title><?php
            if (isset($page_title)) {
                echo $page_title;
            }
        ?></title>
        <?php echo Asset::css('bootstrap.css'); ?>
        <?php echo Asset::css('style.css'); ?>
    </head>
    <body>
        <div class="container-wrap">
            <div class="navbar navbar-fixed-top header-wrap">
                <div class="navbar-inner">
                    <div class="navbar-wrap">
                        <?php echo Html::anchor('/', 'Pasākumu organizēšanas vietne', array('class' => 'brand')); ?>
                        <ul class="nav pull-right">
                            <?php echo $navbar; ?>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="content-wrap">
                <?php echo $content; ?>
            </div>
        </div>
    </body>
</html>