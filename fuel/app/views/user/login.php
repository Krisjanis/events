<?php echo Form::open(); ?>
<?php echo Form::fieldset_open(null, "Pieslēdzies pasākumu organizēšanas vietnei"); ?>

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

<label for="email">E-pasts</label>
<input type="email" name="email" id="email" value="<?php
    if (isset($_POST['email'])) {
        echo $_POST['email'];
    }
?>" />

<label for="password">Parole</label>
<input type="password" name="password" id="password" />

<br />
<input type="Submit" value="Pieslēgties" class="btn" />
<br />

<?php
echo Form::fieldset_close();
echo Form::close();
echo Html::anchor('user/create', 'Reģistrējies', array('class' => 'btn'));
?>