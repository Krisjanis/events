<?php $errors = Session::get_flash('errors'); ?>
<?php if (isset($errors)) : ?>
<div class="alert alert-error">
    <h4>Kļūda!</h4>
    <?php foreach ($errors as $error) : ?>
        <?php echo $error.'<br />'; ?>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<?php $success = Session::get_flash('success'); ?>
<?php if (isset($success)) : ?>
<div class="alert alert-success">
    <h4>Apsveicu!</h4>
    <?php echo $success.'<br />'; ?>
</div>
<?php endif; ?>

<?php if ($onwer_access and isset($alerts)) : ?>
    <?php foreach ($alerts as $alert) : ?>
        <?php if ($alert['type'] == 'promote') : ?>
            <div class="alert alert-success">
                <?php echo Html::anchor('user/dismiss_alert/'.$alert['id'], '&times;', array('class' => 'close')); ?>
                <h4>Apsveicu!</h4>
                <?php echo $alert['message']; ?>
            </div>
        <?php else : ?>
            <div class="alert alert-error">
                <?php echo Html::anchor('user/dismiss_alert/'.$alert['id'], '&times;', array('class' => 'close')); ?>
                <h4>:(</h4>
                <?php echo $alert['message']; ?>
            </div>
        <?php endif; ?>
    <?php endforeach; ?>
<?php endif; ?>
<h1><?php echo $user['username']; ?> profils</h1>
<div class="user-profile">
    <?php if ($onwer_access) : ?>
        <div class="user-profile-info clearfix">
            <p class="definition pull-left">Profila darbības</p>
            <div class="info pull-left">
                <?php echo Html::anchor('user/delete/', "<span class='label label-important'>Dzēst  <i class='icon-trash icon-white'></i></span>", array('onclick' => "return confirm('Vai tiešām vēlaties neatgriezeniski dzēst savu profilu?')")); ?>
                <?php echo Html::anchor('user/edit/', "<span class='label label-warning'>Labot  <i class='icon-edit icon-white'></i></span>"); ?>
                <?php echo Html::anchor('user/change_password/', "<span class='label label-warning'>Mainīt paroli <i class='icon-edit icon-white'></i></span>"); ?>
            </div>
        </div>
    <?php endif; ?>
    <div class="user-profile-info clearfix">
        <?php if ( ! is_null($user['name']) and ! is_null($user['surname'])) : ?>
        <p class="definition pull-left">Vārds Uzvārds</p>
        <div class="info pull-left">
            <p><?php echo $user['name'].' '.$user['surname']; ?></p>
        </div>
        <?php endif; ?>
    </div>
    <?php if ($onwer_access) : ?>
        <div class="user-profile-info clearfix">
            <p class="definition pull-left">E-pasts</p>
            <div class="info pull-left">
                <p><?php echo $user['email']; ?></p>
            </div>
        </div>
    <?php endif; ?>
    <?php if (isset($invites)) : ?>
        <div class="user-profile-info clearfix">
            <p class="definition pull-left">Tev ir jauni ielūgumi</p>
            <div class="info pull-left">
                <ul>
                    <?php foreach ($invites as $invite) : ?>
                    <li>
                        <?php echo Html::anchor('user/view/'.$invite['sender_id'], $invite['sender_username']); ?>
                        Tevi uzaicināja kļūt par organizatoru pasākumā
                        <?php echo Html::anchor('event/view/'.$invite['event_id'], $invite['event_title']); ?>
                        <?php echo Html::anchor('participant/accept_invite/'.$invite['event_id'], "<span class='label label-success'>Apstiprināt  <i class='icon-ok icon-white'></i></span>"); ?>
                        <?php echo Html::anchor('event/view/'.$invite['event_id'], "<span class='label label-warning'>Nē, paldies  <i class='icon-remove icon-white'></i></span>"); ?>
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
