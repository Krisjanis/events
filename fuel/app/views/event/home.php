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

<h2>Birkas</h2>
<ul class="tags">
<?php foreach ($tags as $tag) : ?>
    <li><?php echo Html::anchor('tag/view/'.$tag['id'], "<span class='label label-info'>".$tag['title']."</span>"); ?></li>
<?php endforeach; ?>
</ul>

<?php echo Form::open('tag/search', array('class' => 'tag-search')); ?>
<h2>Meklē birku</h2>
<div class="input-append tag-search">
    <input type="text" name="tags" placeholder="Atdali birkas ar komentāru" /><button class="btn" type="submit">Meklēt!</button>
</div>
<?php echo Form::close(); ?>

<h2>Jaunākie pasākumi</h2>
<?php foreach ($events as $event) : ?>
<div class="event-thumb">
    <a href="<?php echo Uri::base().'event/view/'.$event['id']; ?>">
        <h2><?php echo $event['title']; ?></h2>
        <p><?php echo $event['desc']; ?></p>
    </a>
</div>
<?php endforeach; ?>