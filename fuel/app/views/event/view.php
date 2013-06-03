<?php $errors = Session::get_flash('errors'); ?>
<?php if (isset($errors)) : ?>
<div class="alert alert-error">
    <h4>Kļūda!</h4>
    <?php
        foreach ($errors as $error) {
            echo $error.'<br />';
        }
    ?>
</div>
<?php endif; ?>
<?php $success = Session::get_flash('success'); ?>
<?php if (isset($success)) : ?>
<div class="alert alert-success">
    <h4>Apsveicu!</h4>
    <?php echo $success.'<br />'; ?>
</div>
<?php endif; ?>
<div class="span8 pull-left">
    <div class="event-heading clearfix">
        <h1 class="event-title"><?php echo $event['title']; ?></h1>
        <?php echo Html::anchor('comment/create/w/'.$event['id'], "<span class='label label-warning'>Komentēt pasākumu  <i class='icon-comment icon-white'></i></span>", array('class' => 'pull-right clearfix')); ?>
    </div>
    <p><?php echo $event['desc']; ?></p>
    <?php if (isset($comments['event'])) : ?>
    <div class="comments">
        <div class="comment-heading-wrapper">
            <p class="comment-heading"><span>Komentāri (<?php echo count($comments['event']); ?>)</span></p>
        </div>
        <ul>
        <?php foreach ($comments['event'] as $comment) : ?>
            <li class="comment-wrapper">
                <p class="comment-info">
                    <?php if ($user_id == $comment['author']) : ?>
                        <?php echo Html::anchor('comment/edit/'.$comment['id'].'/'.$event['id'], "<span class='label label-warning'>Labot  <i class='icon-edit icon-white'></i></span>", array('class' => 'pull-right comment-action')); ?>
                        <?php echo Html::anchor('comment/delete/'.$comment['id'].'/'.$event['id'], "<span class='label label-important'>Dzēst  <i class='icon-trash icon-white'></i></span>", array('onclick' => "return confirm('Vai tiešām vēlaties dzēst šo komentāru?')", 'class' => 'pull-right comment-action')); ?>
                    <?php endif; ?>
                    <?php if ($comment['author'] != 0) : ?>
                        <?php echo Html::anchor('user/view/'.$comment['author'], $comment['author_username'], array('class' => 'comment-author')); ?>
                    <?php else : ?>
                        <?php echo '<span class="deleted-user">dzēsts lietotājs</span>'; ?>
                    <?php endif; ?>
                    <span class="comment-date"><?php echo $comment['created_at']; ?></span>
                </p>
                <p class="comment"><?php echo $comment['message']; ?></p>
            </li>
        <?php endforeach; ?>
        </ul>
    </div>
    <?php endif; ?>
    <dl>
        <dt class="event-heading clearfix">
            <h2 class="event-title">Atrašanās vieta</h2>
            <?php echo Html::anchor('comment/create/l/'.$event['id'], "<span class='label label-warning'>Komentēt atrašanās vietu  <i class='icon-comment icon-white'></i></span>", array('class' => 'pull-right')); ?>
        </dt>
        <dd>
            <?php echo $event['location']; ?>
            <?php if (isset($comments['location'])) : ?>
            <div class="comments">
                <div class="comment-heading-wrapper">
                    <p class="comment-heading"><span>Komentāri (<?php echo count($comments['location']); ?>)</span></p>
                </div>
                <ul>
                <?php foreach ($comments['location'] as $comment) : ?>
                    <li class="comment-wrapper">
                        <p class="comment-info">
                            <?php if ($user_id == $comment['author']) : ?>
                                <?php echo Html::anchor('comment/edit/'.$comment['id'].'/'.$event['id'], "<span class='label label-warning'>Labot  <i class='icon-edit icon-white'></i></span>", array('class' => 'pull-right comment-action')); ?>
                                <?php echo Html::anchor('comment/delete/'.$comment['id'].'/'.$event['id'], "<span class='label label-important'>Dzēst  <i class='icon-trash icon-white'></i></span>", array('onclick' => "return confirm('Vai tiešām vēlaties dzēst šo komentāru?')", 'class' => 'pull-right comment-action')); ?>
                            <?php endif; ?>
                            <?php if ($comment['author'] != 0) : ?>
                                <?php echo Html::anchor('user/view/'.$comment['author'], $comment['author_username'], array('class' => 'comment-author')); ?>
                            <?php else : ?>
                                <?php echo '<span class="deleted-user">dzēsts lietotājs</span>'; ?>
                            <?php endif; ?>
                            <span class="comment-date"><?php echo $comment['created_at']; ?></span>
                        </p>
                        <p class="comment"><?php echo $comment['message']; ?></p>
                    </li>
                <?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>
        </dd>
        <dt class="event-heading clearfix">
            <h2 class="event-title">Norises laiks</h2>
            <?php echo Html::anchor('comment/create/d/'.$event['id'], "<span class='label label-warning'>Komentēt norises laiku  <i class='icon-comment icon-white'></i></span>", array('class' => 'pull-right')); ?>
        </dt>
        <dd>
            <?php echo $event['date']; ?>
            <?php if (isset($comments['date'])) : ?>
            <div class="comments">
                <div class="comment-heading-wrapper">
                    <p class="comment-heading"><span>Komentāri (<?php echo count($comments['date']); ?>)</span></p>
                </div>
                <ul>
                <?php foreach ($comments['date'] as $comment) : ?>
                    <li class="comment-wrapper">
                        <p class="comment-info">
                            <?php if ($user_id == $comment['author']) : ?>
                                <?php echo Html::anchor('comment/edit/'.$comment['id'].'/'.$event['id'], "<span class='label label-warning'>Labot  <i class='icon-edit icon-white'></i></span>", array('class' => 'pull-right comment-action')); ?>
                                <?php echo Html::anchor('comment/delete/'.$comment['id'].'/'.$event['id'], "<span class='label label-important'>Dzēst  <i class='icon-trash icon-white'></i></span>", array('onclick' => "return confirm('Vai tiešām vēlaties dzēst šo komentāru?')", 'class' => 'pull-right comment-action')); ?>
                            <?php endif; ?>
                            <?php if ($comment['author'] != 0) : ?>
                                <?php echo Html::anchor('user/view/'.$comment['author'], $comment['author_username'], array('class' => 'comment-author')); ?>
                            <?php else : ?>
                                <?php echo '<span class="deleted-user">dzēsts lietotājs</span>'; ?>
                            <?php endif; ?>
                            <span class="comment-date"><?php echo $comment['created_at']; ?></span>
                        </p>
                        <p class="comment"><?php echo $comment['message']; ?></p>
                    </li>
                <?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>
        </dd>
        <?php if (isset($event['part_min']) and isset($event['part_max'])) : ?>
            <dt class="event-heading clearfix">
                <h2 class="event-title">Dalībnieku skaits</h2>
                <?php echo Html::anchor('comment/create/p/'.$event['id'], "<span class='label label-warning'>Komentēt dalībnieku skaitu  <i class='icon-comment icon-white'></i></span>", array('class' => 'pull-right')); ?>
            <dt>
            <dd>
                No <?php echo $event['part_min']; ?> līdz <?php echo $event['part_max']; ?> dalībniekiem
                <?php if (isset($comments['participiants'])) : ?>
                <div class="comments">
                    <div class="comment-heading-wrapper">
                        <p class="comment-heading"><span>Komentāri (<?php echo count($comments['participiants']); ?>)</span></p>
                    </div>
                    <ul>
                    <?php foreach ($comments['participiants'] as $comment) : ?>
                        <li class="comment-wrapper">
                            <p class="comment-info">
                                <?php if ($user_id == $comment['author']) : ?>
                                    <?php echo Html::anchor('comment/edit/'.$comment['id'].'/'.$event['id'], "<span class='label label-warning'>Labot  <i class='icon-edit icon-white'></i></span>", array('class' => 'pull-right comment-action')); ?>
                                    <?php echo Html::anchor('comment/delete/'.$comment['id'].'/'.$event['id'], "<span class='label label-important'>Dzēst  <i class='icon-trash icon-white'></i></span>", array('onclick' => "return confirm('Vai tiešām vēlaties dzēst šo komentāru?')", 'class' => 'pull-right comment-action')); ?>
                                <?php endif; ?>
                                <?php if ($comment['author'] != 0) : ?>
                                    <?php echo Html::anchor('user/view/'.$comment['author'], $comment['author_username'], array('class' => 'comment-author')); ?>
                                <?php else : ?>
                                    <?php echo '<span class="deleted-user">dzēsts lietotājs</span>'; ?>
                                <?php endif; ?>
                                <span class="comment-date"><?php echo $comment['created_at']; ?></span>
                            </p>
                            <p class="comment"><?php echo $comment['message']; ?></p>
                        </li>
                    <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>
            </dd>
        <?php elseif (isset($event['part_min'])) : ?>
            <dt class="event-heading clearfix">
                <h2 class="event-title">Minimālais dalībnieku skaits</h2>
                <?php echo Html::anchor('comment/create/p/'.$event['id'], "<span class='label label-warning'>Komentēt dalībnieku skaitu  <i class='icon-comment icon-white'></i></span>", array('class' => 'pull-right')); ?>
            <dt>
            <dd>
                Vismaz <?php echo $event['part_min']; ?> dalībnieki
                <?php if (isset($comments['participiants'])) : ?>
                <div class="comments">
                    <div class="comment-heading-wrapper">
                        <p class="comment-heading"><span>Komentāri (<?php echo count($comments['participiants']); ?>)</span></p>
                    </div>
                    <ul>
                    <?php foreach ($comments['participiants'] as $comment) : ?>
                        <li class="comment-wrapper">
                            <p class="comment-info">
                                <?php if ($user_id == $comment['author']) : ?>
                                    <?php echo Html::anchor('comment/edit/'.$comment['id'].'/'.$event['id'], "<span class='label label-warning'>Labot  <i class='icon-edit icon-white'></i></span>", array('class' => 'pull-right comment-action')); ?>
                                    <?php echo Html::anchor('comment/delete/'.$comment['id'].'/'.$event['id'], "<span class='label label-important'>Dzēst  <i class='icon-trash icon-white'></i></span>", array('onclick' => "return confirm('Vai tiešām vēlaties dzēst šo komentāru?')", 'class' => 'pull-right comment-action')); ?>
                                <?php endif; ?>
                                <?php if ($comment['author'] != 0) : ?>
                                    <?php echo Html::anchor('user/view/'.$comment['author'], $comment['author_username'], array('class' => 'comment-author')); ?>
                                <?php else : ?>
                                    <?php echo '<span class="deleted-user">dzēsts lietotājs</span>'; ?>
                                <?php endif; ?>
                                <span class="comment-date"><?php echo $comment['created_at']; ?></span>
                            </p>
                            <p class="comment"><?php echo $comment['message']; ?></p>
                        </li>
                    <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>
            </dd>
        <?php elseif (isset($event['part_max'])) : ?>
            <dt class="event-heading clearfix">
                <h2 class="event-title">Maksimālais dalībnieku skaits</h2>
                <?php echo Html::anchor('comment/create/p/'.$event['id'], "<span class='label label-warning'>Komentēt dalībnieku skaitu  <i class='icon-comment icon-white'></i></span>", array('class' => 'pull-right')); ?>
            <dt>
            <dd>
                Ne vairāk par <?php echo $event['part_max']; ?> dalībniekiem
                <?php if (isset($comments['participiants'])) : ?>
                <div class="comments">
                    <div class="comment-heading-wrapper">
                        <p class="comment-heading"><span>Komentāri (<?php echo count($comments['participiants']); ?>)</span></p>
                    </div>
                    <ul>
                    <?php foreach ($comments['participiants'] as $comment) : ?>
                        <li class="comment-wrapper">
                            <p class="comment-info">
                                <?php if ($user_id == $comment['author']) : ?>
                                    <?php echo Html::anchor('comment/edit/'.$comment['id'].'/'.$event['id'], "<span class='label label-warning'>Labot  <i class='icon-edit icon-white'></i></span>", array('class' => 'pull-right comment-action')); ?>
                                    <?php echo Html::anchor('comment/delete/'.$comment['id'].'/'.$event['id'], "<span class='label label-important'>Dzēst  <i class='icon-trash icon-white'></i></span>", array('onclick' => "return confirm('Vai tiešām vēlaties dzēst šo komentāru?')", 'class' => 'pull-right comment-action')); ?>
                                <?php endif; ?>
                                <?php if ($comment['author'] != 0) : ?>
                                    <?php echo Html::anchor('user/view/'.$comment['author'], $comment['author_username'], array('class' => 'comment-author')); ?>
                                <?php else : ?>
                                    <?php echo '<span class="deleted-user">dzēsts lietotājs</span>'; ?>
                                <?php endif; ?>
                                <span class="comment-date"><?php echo $comment['created_at']; ?></span>
                            </p>
                            <p class="comment"><?php echo $comment['message']; ?></p>
                        </li>
                    <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>
            </dd>
        <?php endif; ?>
        <?php if (isset($event['entry_fee'])) : ?>
            <dt class="event-heading clearfix">
                <h2 class="event-title">Dalības maksa</h2>
                <?php echo Html::anchor('comment/create/f/'.$event['id'], "<span class='label label-warning'>Komentēt dalības maksu <i class='icon-comment icon-white'></i></span>", array('class' => 'pull-right')); ?>
            <dt>
            <dd>
                Dalības maksa ir <?php echo $event['entry_fee']; ?> Ls
                <?php if (isset($comments['entry_fee'])) : ?>
                <div class="comments">
                    <div class="comment-heading-wrapper">
                        <p class="comment-heading"><span>Komentāri (<?php echo count($comments['entry_fee']); ?>)</span></p>
                    </div>
                    <ul>
                    <?php foreach ($comments['entry_fee'] as $comment) : ?>
                        <li class="comment-wrapper">
                            <p class="comment-info">
                                <?php if ($user_id == $comment['author']) : ?>
                                    <?php echo Html::anchor('comment/edit/'.$comment['id'].'/'.$event['id'], "<span class='label label-warning'>Labot  <i class='icon-edit icon-white'></i></span>", array('class' => 'pull-right comment-action')); ?>
                                    <?php echo Html::anchor('comment/delete/'.$comment['id'].'/'.$event['id'], "<span class='label label-important'>Dzēst  <i class='icon-trash icon-white'></i></span>", array('onclick' => "return confirm('Vai tiešām vēlaties dzēst šo komentāru?')", 'class' => 'pull-right comment-action')); ?>
                                <?php endif; ?>
                                <?php if ($comment['author'] != 0) : ?>
                                    <?php echo Html::anchor('user/view/'.$comment['author'], $comment['author_username'], array('class' => 'comment-author')); ?>
                                <?php else : ?>
                                    <?php echo '<span class="deleted-user">dzēsts lietotājs</span>'; ?>
                                <?php endif; ?>
                                <span class="comment-date"><?php echo $comment['created_at']; ?></span>
                            </p>
                            <p class="comment"><?php echo $comment['message']; ?></p>
                        </li>
                    <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>
            </dd>
        <?php endif; ?>
        <?php if (isset($event['takeaway'])) : ?>
            <dt class="event-heading clearfix">
                <h2 class="event-title">Līdzi jāņem ...</h2>
                <?php echo Html::anchor('comment/create/t/'.$event['id'], "<span class='label label-warning'>Komentēt līdzi ņemamo <i class='icon-comment icon-white'></i></span>", array('class' => 'pull-right')); ?>
            <dt>
            <dd>
                <?php echo $event['takeaway']; ?>
                <?php if (isset($comments['takeaway'])) : ?>
                <div class="comments">
                    <div class="comment-heading-wrapper">
                        <p class="comment-heading"><span>Komentāri (<?php echo count($comments['takeaway']); ?>)</span></p>
                    </div>
                    <ul>
                    <?php foreach ($comments['takeaway'] as $comment) : ?>
                        <li class="comment-wrapper">
                            <p class="comment-info">
                                <?php if ($user_id == $comment['author']) : ?>
                                    <?php echo Html::anchor('comment/edit/'.$comment['id'].'/'.$event['id'], "<span class='label label-warning'>Labot  <i class='icon-edit icon-white'></i></span>", array('class' => 'pull-right comment-action')); ?>
                                    <?php echo Html::anchor('comment/delete/'.$comment['id'].'/'.$event['id'], "<span class='label label-important'>Dzēst  <i class='icon-trash icon-white'></i></span>", array('onclick' => "return confirm('Vai tiešām vēlaties dzēst šo komentāru?')", 'class' => 'pull-right comment-action')); ?>
                                <?php endif; ?>
                                <?php if ($comment['author'] != 0) : ?>
                                    <?php echo Html::anchor('user/view/'.$comment['author'], $comment['author_username'], array('class' => 'comment-author')); ?>
                                <?php else : ?>
                                    <?php echo '<span class="deleted-user">dzēsts lietotājs</span>'; ?>
                                <?php endif; ?>
                                <span class="comment-date"><?php echo $comment['created_at']; ?></span>
                            </p>
                            <p class="comment"><?php echo $comment['message']; ?></p>
                        </li>
                    <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>
            </dd>
        <?php endif; ?>
        <?php if (isset($event['dress_code'])) : ?>
            <dt class="event-heading clearfix">
                <h2 class="event-title">Ģērbšanās stils</h2>
                <?php echo Html::anchor('comment/create/dc/'.$event['id'], "<span class='label label-warning'>Komentēt ģebšanās stilu <i class='icon-comment icon-white'></i></span>", array('class' => 'pull-right')); ?>
            <dt>
            <dd>
                <?php echo $event['dress_code']; ?>
                <?php if (isset($comments['dress_code'])) : ?>
                <div class="comments">
                    <div class="comment-heading-wrapper">
                        <p class="comment-heading"><span>Komentāri (<?php echo count($comments['dress_code']); ?>)</span></p>
                    </div>
                    <ul>
                    <?php foreach ($comments['dress_code'] as $comment) : ?>
                        <li class="comment-wrapper">
                            <p class="comment-info">
                                <?php if ($user_id == $comment['author']) : ?>
                                    <?php echo Html::anchor('comment/edit/'.$comment['id'].'/'.$event['id'], "<span class='label label-warning'>Labot  <i class='icon-edit icon-white'></i></span>", array('class' => 'pull-right comment-action')); ?>
                                    <?php echo Html::anchor('comment/delete/'.$comment['id'].'/'.$event['id'], "<span class='label label-important'>Dzēst  <i class='icon-trash icon-white'></i></span>", array('onclick' => "return confirm('Vai tiešām vēlaties dzēst šo komentāru?')", 'class' => 'pull-right comment-action')); ?>
                                <?php endif; ?>
                                <?php if ($comment['author'] != 0) : ?>
                                    <?php echo Html::anchor('user/view/'.$comment['author'], $comment['author_username'], array('class' => 'comment-author')); ?>
                                <?php else : ?>
                                    <?php echo '<span class="deleted-user">dzēsts lietotājs</span>'; ?>
                                <?php endif; ?>
                                <span class="comment-date"><?php echo $comment['created_at']; ?></span>
                            </p>
                            <p class="comment"><?php echo $comment['message']; ?></p>
                        </li>
                    <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>
            </dd>
        <?php endif; ?>
        <?php if (isset($event['assistants'])) : ?>
            <dt class="event-heading clearfix">
                <h2 class="event-title">Nepieciešamie palīgi</h2>
                <?php echo Html::anchor('comment/create/a/'.$event['id'], "<span class='label label-warning'>Komentēt nepiciešamos palīgus <i class='icon-comment icon-white'></i></span>", array('class' => 'pull-right')); ?>
            <dt>
            <dd>
                <?php echo $event['assistants']; ?>
                <?php if (isset($comments['assistants'])) : ?>
                <div class="comments">
                    <div class="comment-heading-wrapper">
                        <p class="comment-heading"><span>Komentāri (<?php echo count($comments['assistants']); ?>)</span></p>
                    </div>
                    <ul>
                    <?php foreach ($comments['assistants'] as $comment) : ?>
                        <li class="comment-wrapper">
                            <p class="comment-info">
                                <?php if ($user_id == $comment['author']) : ?>
                                    <?php echo Html::anchor('comment/edit/'.$comment['id'].'/'.$event['id'], "<span class='label label-warning'>Labot  <i class='icon-edit icon-white'></i></span>", array('class' => 'pull-right comment-action')); ?>
                                    <?php echo Html::anchor('comment/delete/'.$comment['id'].'/'.$event['id'], "<span class='label label-important'>Dzēst  <i class='icon-trash icon-white'></i></span>", array('onclick' => "return confirm('Vai tiešām vēlaties dzēst šo komentāru?')", 'class' => 'pull-right comment-action')); ?>
                                <?php endif; ?>
                                <?php if ($comment['author'] != 0) : ?>
                                    <?php echo Html::anchor('user/view/'.$comment['author'], $comment['author_username'], array('class' => 'comment-author')); ?>
                                <?php else : ?>
                                    <?php echo '<span class="deleted-user">dzēsts lietotājs</span>'; ?>
                                <?php endif; ?>
                                <span class="comment-date"><?php echo $comment['created_at']; ?></span>
                            </p>
                            <p class="comment"><?php echo $comment['message']; ?></p>
                        </li>
                    <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>
            </dd>
        <?php endif; ?>
    </dl>
</div>
<div class="span3 pull-right">
    <h3>Organizatori</h3>
    <ul class="organizators">
    <?php if (isset($participants['author'])) : ?>
        <li>
            <?php if ($participants['author']['id'] != 0) : ?>
                <?php echo $participants['author']['username'].' '; ?>
            <?php else : ?>
                <?php echo '<span class="deleted-user">dzēsts lietotājs</span> '; ?>
            <?php endif; ?>
            <span class='label label-info pull-right'>Autors</i></span>
        </li>
    <?php endif; ?>
    <?php if (isset($participants['organizators'])) : ?>
        <?php foreach ($participants['organizators'] as $organizator) : ?>
            <li>
                <?php echo $organizator['username']; ?>
                <?php if ($author_access) : ?>
                    <div class="pull-right">
                        <a href="#"><?php echo Html::anchor('participant/delete/'.$event['id'].'/'.$organizator['id'], "<span class='label label-important pull-right'>Dzēst  <i class='icon-trash icon-white'></i></span>"); ?></a>
                    </div>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
    <?php endif; ?>
    </ul>
    <h3>Birkas</h3>
    <ul class="tags">
        <?php foreach ($tags as $tag) : ?>
        <li><?php echo Html::anchor('tag/view/'.$tag['id'], "<span class='label label-info'>".$tag['title']."</span>"); ?></li>
        <?php endforeach; ?>
    </ul>
    <?php if ($author_access and $event['type'] == 'private') : ?>
        <h3>Pārveidot par publisku</h3>
        <?php echo Html::anchor('event/change_to_public/'.$event['id'], 'Pārveidot!', array('class' => 'btn', 'onclick' => "return confirm('Vai tiešām vēlaties neatgriezeniski pārveidot pasākumu par publisku?')")); ?>
    <?php endif; ?>
    <?php if ($organizator_access) : ?>
        <h3>Labot atribūtus</h3>
        <?php echo Html::anchor('event/edit_attribute/'.$event['id'], 'Labot!', array('class' => 'btn')); ?>
        <h3>Labot birkas</h3>
        <?php echo Html::anchor('tag/edit/'.$event['id'], 'Labot!', array('class' => 'btn')); ?>
        <?php if ($event['type'] == 'public') : ?>
            <?php if (isset($requests)) : ?>
                <h3>Pieprasījumi kļūt par organizatoru</h3>
                <ul class="organizators">
                <?php foreach ($requests as $request) : ?>
                    <li>
                        <?php echo Html::anchor('user/view/'.$request['id'], $request['username']); ?>
                        <?php echo Html::anchor('participant/decline_request/'.$event['id'].'/'.$request['id'], "<span class='label label-important'><i class='icon-remove icon-white'></i></span>", array('class' => 'pull-right')); ?>
                        <?php echo Html::anchor('participant/accept_request/'.$event['id'].'/'.$request['id'], "<span class='label label-success'><i class='icon-ok icon-white'></i></span>", array('class' => 'pull-right')); ?>
                    </li>
                <?php endforeach; ?>
                </ul>
            <?php endif; ?>
            <?php echo Form::open('participant/add_organizator'); ?>
                <h3>Pievienot organizatoru</h3>
                <div class="input-append span3">
                    <input type="hidden" name="event_id" value="<?php echo $event['id']; ?>"/>
                    <input class="span2" type="text" name="organizator" placeholder="Ievadi lietotāja vārdu ..." /><button class="btn" type="submit">Meklēt!</button>
                </div>
            <?php echo Form::close(); ?>
            <h3>Vai pievieno no e-pasta</h3>
            <?php echo Html::anchor('participant/email/'.$event['id'], 'Pievienot!', array('class' => 'btn')); ?>
        <?php endif; ?>
    <?php elseif ($event['type'] == 'public') : ?>
        <h3>Kļūsti par organizatoru</h3>
        <?php echo Html::anchor('participant/request/'.$event['id'], 'Nosūtīt pieprasījumu', array('class' => 'btn')); ?>
    <?php endif; ?>
    <?php if (isset($participants['participants']) and $event['type'] == 'public') : ?>
        <h3>Dalībnieki</h3>
        <ul class="organizators">
        <?php foreach ($participants['participants'] as $participant) : ?>
            <li><?php echo $participant['username']; ?></li>
        <?php endforeach; ?>
    <?php endif; ?>
    </ul>
</div>