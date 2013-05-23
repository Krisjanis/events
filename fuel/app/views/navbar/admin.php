<li><?php echo Html::anchor('event/create', 'Izveidot pasākumu'); ?></li>
<li><?php echo Html::anchor('admin/event', 'Operatora panelis'); ?></li>
<li class="navbar-profile">
    <?php $label = "Pieslēdzies kā $username"; ?>
    <?php if (isset($invites_count)) : ?>
        <?php $label .= "<span class='label label-info'>$invites_count <i class='icon-envelope icon-white'></i></span>"; ?>
    <?php endif; ?>
    <?php echo Html::anchor('user/view', $label); ?>
</li>
<li><?php echo Html::anchor('user/logout', 'Iziet'); ?></li>