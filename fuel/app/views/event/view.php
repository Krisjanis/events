<?php $errors = Session::get_flash('errors'); ?>
<?php if (isset($errors)) : ?>
<div class="alert alert-error">
    <h4>Kļūda!</h4>
    <?php
        foreach ($errors as $error) {
            echo $error . '<br />';
        }
    ?>
</div>
<?php endif; ?>
<?php $success = Session::get_flash('success'); ?>
<?php if (isset($success)) : ?>
<div class="alert alert-success">
    <h4>Apsveicu!</h4>
    <?php echo $success . '<br />'; ?>
</div>
<?php endif; ?>
<div class="span8 pull-left">
    <h1><?php echo $event['title']; ?></h1>
    <p><?php echo $event['desc']; ?></p>
    <dl>
        <dt><h2>Atrašanās vieta</h2></dt>
        <dd><?php echo $event['location']; ?></dd>
        <dt><h2>Norises datums</h2></dt>
        <dd><?php echo $event['date']; ?></dd>
        <?php if (isset($event['part_min']) && isset($event['part_max'])) : ?>
            <dt><h2>Dalībnieku skaits</h2><dt>
            <dd>No <?php echo $event['part_min']; ?> līdz <?php echo $event['part_max']; ?> dalībniekiem</dd>
        <?php elseif (isset($event['part_min'])) : ?>
            <dt><h2>Minimālais dalībnieku skaits</h2><dt>
            <dd>Vismaz <?php echo $event['part_min']; ?> dalībnieki</dd>
        <?php elseif (isset($event['part_max'])) : ?>
            <dt><h2>Maksimālais dalībnieku skaits</h2><dt>
            <dd>Ne vairāk par <?php echo $event['part_max']; ?> dalībniekiem</dd>
        <?php endif; ?>
        <?php if (isset($event['entry_fee'])) : ?>
            <dt><h2>Dalības maksa</h2><dt>
            <dd>Dalības maksa ir <?php echo $event['entry_fee']; ?> Ls</dd>
        <?php endif; ?>
        <?php if (isset($event['takeaway'])) : ?>
            <dt><h2>Līdzi jāņem ...</h2><dt>
            <dd><?php echo $event['takeaway']; ?></dd>
        <?php endif; ?>
        <?php if (isset($event['dress_code'])) : ?>
            <dt><h2>Ģērbšanās stils</h2><dt>
            <dd><?php echo $event['dress_code']; ?></dd>
        <?php endif; ?>
        <?php if (isset($event['assistants'])) : ?>
            <dt><h2>Nepieciešami cilvēki, kas ...</h2><dt>
            <dd><?php echo $event['assistants']; ?></dd>
        <?php endif; ?>
    </dl>
</div>
<div class="span3 pull-right">
    <h3>Organizatori</h3>
    <ul class="organizators">
    <?php if (isset($organizators['author'])) : ?>
        <li><?php echo $organizators['author']['username']; ?> <span class="label label-info pull-right">Autors</span></li>
    <?php endif; ?>
    <?php foreach ($organizators['organizators'] as $organizator) : ?>
        <li>
            <?php echo $organizator['username']; ?>
            <?php if ($authorAccess) : ?>
                <div class="pull-right">
                    <a href="#"><?php echo Html::anchor('event/delete_organizator/' . $event['link_title'] . '/' . $event['id'] . '/' . $organizator['id'], '<span class="label label-warning pull-right">Dzēst</span>'); ?></a>
                </div>
            <?php endif; ?>
        </li>
    <?php endforeach; ?>
    </ul>
    <?php if ($organizatorAccess) : ?>
        <?php echo Form::open('event/add_organizator'); ?>
            <h3>Pievienot Organizatoru</h3>
            <div class="input-append span3">
                <input type="hidden" name="event_id" value="<?php echo $event['id']; ?>"/>
                <input type="hidden" name="link_title" value="<?php echo $event['link_title']; ?>"/>
                <input class="span2" type="text" name="organizator" placeholder="Ievadi lietotāja vārdu ..." />
                <button class="btn" type="submit">Meklēt!</button>
            </div>
        <?php echo Form::close(); ?>
    <?php endif; ?>
</div>