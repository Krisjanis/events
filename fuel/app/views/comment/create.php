<?php
echo Form::open();
echo Form::fieldset_open(null, $form_title);

// Check if there is no error in form submition
$errors = Session::get_flash('errors');
if (isset($errors)) :
?>
<div class="alert alert-error">
    <h4>Kļūda!</h4>
    <?php
        foreach ($errors as $error) {
            echo $error.'<br />';
        }
    ?>
</div>
<?php endif; ?>

<label class="control-label" for="comment">Komentārs</label>
<div class="controls">
    <textarea name="comment" id="comment" class="span12"><?php
        if (isset($_POST['comment'])) {
            echo $_POST['comment'];
        }
    ?></textarea>
</div>

<br />
<input type="Submit" value="Publicēt" class="btn" />

<?php
echo Form::fieldset_close();
echo Form::close();
?>