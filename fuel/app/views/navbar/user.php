<li class="navbar-profile">
    <?php $label = "Pieslēdzies kā $username"; ?>
    <?php if (isset($invites_count)) : ?>
        <?php $label .= "<span class='badge badge-info'>$invites_count</span>"; ?>
    <?php endif; ?>
    <?php echo Html::anchor('user/view', $label); ?>
</li>
<li><?php echo Html::anchor('user/logout', 'Iziet'); ?></li>