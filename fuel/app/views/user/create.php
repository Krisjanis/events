<?php
echo Form::open();
echo Form::fieldset_open(null, "Reģistrējies pasākumu organizēšanas vietnē");

// Check if there is no error in form submition
$errors = Session::get_flash('errors');
if (isset($errors)) {
?>
<div class="alert alert-error">
    <h4>Kļūda!</h4>
    <?php
        foreach ($errors as $error) {
            echo $error . '<br />';
        }
    ?>
</div>
<?php } ?>

<label for="username">Lietotājvārds</label>
<input type="text" name="username" id="username" value="<?php
    if (isset($_POST['username'])) {
        echo $_POST['username'];
    }
?>"/>

<label for="name">Vārds</label>
<input type="text" name="name" id="name" value="<?php
    if (isset($_POST['name'])) {
        echo $_POST['name'];
    }
?>" />

<label for="surname">Uzvārds</label>
<input type="text" name="surname" id="surname" value="<?php
    if (isset($_POST['surname'])) {
        echo $_POST['surname'];
    }
?>" />

<label for="email">E-pasts</label>
<input type="email" name="email" id="email" value="<?php
    if (isset($_POST['email'])) {
        echo $_POST['email'];
    }
?>" />

<label for="password">Parole</label>
<input type="password" name="password" id="password" />

<label for="password_rep">Parole atkārtoti</label>
<input type="password" name="password_rep" id="password_rep" />

<br />
<input type="Submit" value="Reģistrēties" class="btn" />

<?php
echo Form::fieldset_close();
echo Form::close();
?>