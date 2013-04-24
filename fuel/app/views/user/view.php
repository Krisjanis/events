<h1><?php echo $user['username']; ?> profils</h1>
<div class="user-profile">
    <div class="user-profile-info clearfix">
        <p class="definition pull-left">Vārds Uzvārds</p>
        <div class="info pull-left">
            <p><?php echo $user['name'] . ' ' . $user['surname']; ?></p>
        </div>
    </div>
    <?php if (isset($eventAuthor)) : ?>
        <div class="user-profile-info clearfix">
            <p class="definition pull-left">Autors pasākumos</p>
            <div class="info pull-left">
                <ul>
                    <?php foreach ($eventAuthor as $event) : ?>
                    <li><?php echo Html::anchor('event/view/' . $event['link'], $event['title']); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    <?php endif; ?>
    <?php if (isset($eventOrganizator)) : ?>
        <div class="user-profile-info clearfix">
            <p class="definition pull-left">Organizators pasākumos</p>
            <div class="info pull-left">
                <ul>
                    <?php foreach ($eventOrganizator as $event) : ?>
                    <li><?php echo Html::anchor('event/view/' . $event['link'], $event['title']); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    <?php endif; ?>
</div>
