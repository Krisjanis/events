<?php $errors = Session::get_flash('errors'); ?>
<?php if (isset($errors)) : ?>
<div class="alert alert-error">
    <h4>Kļūda!</h4>
    <?php foreach ($errors as $error) : ?>
        <?php echo $error.'<br />'; ?>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<?php echo Form::open('tag/search', array('class' => 'tag-search')); ?>
<h2>Meklē birku</h2>
<div class="input-append tag-search">
    <input type="text" name="tags" placeholder="Atdali birkas ar komentāru" <?php
        if (isset($_POST['tags'])) :
            echo 'value="'.$_POST['tags'].'"';
        endif;
    ?>/><button class="btn" type="submit">Meklēt!</button>
</div>
<?php echo Form::close(); ?>

<?php if (isset($events)) : ?>
    <?php foreach ($events as $event) : ?>
    <div class="event-thumb">
        <a href="<?php echo Uri::base().'event/view/'.$event['id']; ?>">
            <h2><?php echo $event['title']; ?></h2>
            <p><?php echo $event['desc']; ?></p>
        </a>
    </div>
    <?php endforeach; ?>
<?php endif; ?>