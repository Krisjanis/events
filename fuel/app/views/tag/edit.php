<?php $errors = Session::get_flash('errors'); ?>
<?php if (isset($errors)) : ?>
<div class="alert alert-error">
    <h4>Kļūda!</h4>
    <?php foreach ($errors as $error) : ?>
        <?php echo $error.'<br />'; ?>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<?php echo Form::open(); ?>
<h2>Labo birkas</h2>
<div class="input-append tag-search">
    <input type="text" name="tags" placeholder="Atdali birkas ar komentāru" <?php
        if (isset($_POST['tags'])) :
            echo 'value="'.$_POST['tags'].'"';
        endif;
    ?>/><button class="btn" type="submit">Saglabāt!</button>
</div>
<?php echo Form::close(); ?>