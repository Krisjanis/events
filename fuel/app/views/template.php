<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title><?php
            if (isset($page_title)) {
                echo $page_title . ' | Pasākumu organizēšanas vietne';
            }
        ?></title>
        <?php
            echo Asset::css('bootstrap.css');
            echo Asset::css('style.css');
            echo Asset::css('jquery.taghandler.css');
            echo Asset::js('jquery-1.9.1.min.js');
            echo Asset::js('jquery.taghandler.min.js');
            echo Asset::js('script.js');
        ?>
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
            <div class="content-wrap clearfix">
                <div class="alert alert-error alert-javascript">
                    <p>Jums ir izslēgts javascript, tādējādi nevarēsiet pilnībā izmantot vietnes funckijas.</p>
                </div>
                <?php echo $content; ?>
            </div>
        </div>
    </body>
</html>