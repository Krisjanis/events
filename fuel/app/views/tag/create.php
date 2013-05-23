<h2>Pievieno birku</h2>
<?php echo Form::open(array('action' => 'tag/create', 'class' => 'form-inline')); ?>

<?php $errors = Session::get_flash('errors'); ?>
<?php if (isset($errors)) : ?>
<div class="alert alert-error">
    <h4>Kļūda!</h4>
    <?php foreach ($errors as $error) : ?>
        <?php echo $error.'<br />'; ?>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<label for="title">Nosaukums :</label>
<input type="text" name="title" id="title" value="<?php if (isset($_POST['title'])) :
    echo $_POST['title'];
endif; ?>" />

<input type="Submit" value="Pievienot" class="btn" />
<?php echo Form::close(); ?>