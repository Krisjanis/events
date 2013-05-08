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

<h2>Jaunākie pasākumi</h2>
<?php foreach ($events as $event) : ?>
<div class="event-thumb">
    <a href="<?php echo Uri::base().'event/view/'.$event['id']; ?>">
        <h2><?php echo $event['title']; ?></h2>
        <p><?php echo $event['desc']; ?></p>
    </a>
</div>
<?php endforeach; ?>