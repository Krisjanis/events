<ul class="nav nav-tabs">
    <li<?php if (isset($event)) : echo ' class="active"'; endif; ?>><?php echo Html::anchor('admin/event', 'Pasākumi'); ?></li>
    <li<?php if (isset($user)) : echo ' class="active"'; endif; ?>><?php echo Html::anchor('admin/user', 'Lietotāji'); ?></li>
    <li<?php if (isset($comment)) : echo ' class="active"'; endif; ?>><?php echo Html::anchor('admin/comment', 'Komentāri'); ?></li>
    <li<?php if (isset($tag)) : echo ' class="active"'; endif; ?>><?php echo Html::anchor('admin/tag', 'Birkas'); ?></li>
</ul>
<div class="admin-panel">
   <?php echo $panel; ?>
</div>