<li class="navbar-profile">
    <?php $label = "Pieslēdzies kā $username"; ?>
    <?php if (isset($invites_count)) : ?>
        <?php $label .= "<span class='label label-info'>$invites_count <i class='icon-envelope icon-white'></i></span>"; ?>
    <?php endif; ?>
    <?php if (isset($promote_count)) : ?>
        <?php if ($promote_count != 1) : ?>
            <?php $label .= "<span class='label label-success'>$promote_count <i class='icon-exclamation-sign icon-white'></i></span>"; ?>
        <?php else : ?>
            <?php $label .= "<span class='label label-success'><i class='icon-exclamation-sign icon-white'></i></span>"; ?>
        <?php endif; ?>
    <?php endif; ?>
    <?php if (isset($demote_count)) : ?>
        <?php if ($demote_count != 1) : ?>
            <?php $label .= "<span class='label label-important'>$demote_count <i class='icon-exclamation-sign icon-white'></i></span>"; ?>
        <?php else : ?>
            <?php $label .= "<span class='label label-important'><i class='icon-exclamation-sign icon-white'></i></span>"; ?>
        <?php endif; ?>
    <?php endif; ?>
    <?php echo Html::anchor('user/view', $label); ?>
</li>
<li><?php echo Html::anchor('user/logout', 'Iziet'); ?></li>