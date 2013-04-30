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

<br />
<input type="Submit" value="Labot profilu" class="btn" />

<?php
echo Form::fieldset_close();
echo Form::close();
?>