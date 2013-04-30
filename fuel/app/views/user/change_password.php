<?php
echo Form::open();
echo Form::fieldset_open(null, "Reģistrējies pasākumu organizēšanas vietnē");

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

<label for="password_old">Ievadiet savu paroli</label>
<input type="password" name="password_old" id="password" />

<label for="password">Jaunā parole</label>
<input type="password" name="password" id="password" />

<label for="password_rep">Jaunā parole atkārtoti</label>
<input type="password" name="password_rep" id="password_rep" />

<br />
<input type="Submit" value="Labot profilu" class="btn" />

<?php
echo Form::fieldset_close();
echo Form::close();
?>