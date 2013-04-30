<h1><?php echo $user['username']; ?> profils</h1>
<div class="user-profile">
    <?php if ($onwer_access) : ?>
        <div class="user-profile-info clearfix">
            <p class="definition pull-left">Profila darbības</p>
            <div class="info pull-left">
                <?php echo Html::anchor('user/delete/', '<span class="label label-important">Dzēst!</span>', array('onclick' => "return confirm('Vai tiešām vēlaties neatgriezeniski dzēst savu profilu?')")); ?>
                <?php echo Html::anchor('user/edit/', '<span class="label label-warning">Labot!</span>'); ?>
                <?php echo Html::anchor('user/change_password/', '<span class="label label-warning">Mainīt paroli!</span>'); ?>
            </div>
        </div>
    <?php endif; ?>
    <div class="user-profile-info clearfix">
        <p class="definition pull-left">Vārds Uzvārds</p>
        <?php if ( ! is_null($user['name']) and ! is_null($user['surname'])) : ?>
        <div class="info pull-left">
            <p><?php echo $user['name'].' '.$user['surname']; ?></p>
        </div>
        <?php endif; ?>
    </div>
    <div class="user-profile-info clearfix">
        <p class="definition pull-left">E-pasts</p>
        <div class="info pull-left">
            <p><?php echo $user['email']; ?></p>
        </div>
    </div>
    <?php if (isset($invites)) : ?>
        <div class="user-profile-info clearfix">
            <p class="definition pull-left">Tev ir jauni ielūgumi</p>
            <div class="info pull-left">
                <ul>
                    <?php foreach ($invites as $invite) : ?>
                    <li>
                        <?php echo Html::anchor('user/view/'.$invite['sender_id'], $invite['sender_username']); ?>
                        Tevi uzaicināja kļūt par organizatoru pasākumā
                        <?php echo Html::anchor('event/view/'.$invite['event_id'], 'lol'); ?>
                        <?php echo Html::anchor('event/accept_invite/'.$invite['event_id'], '<span class="label label-success">Apstiprināt</span>'); ?>
                        <?php echo Html::anchor('event/view/'.$invite['event_id'], '<span class="label label-warning">Nē, paldies</span>'); ?>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    <?php endif; ?>
    <?php if (isset($event_author)) : ?>
        <div class="user-profile-info clearfix">
            <p class="definition pull-left">Autors pasākumos</p>
            <div class="info pull-left">
                <ul>
                    <?php foreach ($event_author as $event) : ?>
                    <li><?php echo Html::anchor('event/view/'.$event['id'], $event['title']); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    <?php endif; ?>
    <?php if (isset($event_organizator)) : ?>
        <div class="user-profile-info clearfix">
            <p class="definition pull-left">Organizators pasākumos</p>
            <div class="info pull-left">
                <ul>
                    <?php foreach ($event_organizator as $event) : ?>
                    <li><?php echo Html::anchor('event/view/'.$event['id'], $event['title']); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    <?php endif; ?>
</div>
