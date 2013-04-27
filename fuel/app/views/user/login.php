<?php
echo Form::open();
echo Form::fieldset_open(null, "Pieslēdzies pasākumu organizēšanas vietnei");

// Check if there is no errors
$errors = Session::get_flash('errors');
if (isset($errors)) {
?>
<div class="alert alert-error">
    <h4>Kļūda!</h4>
    <?php
        foreach ($errors as $error) {
            echo $error.'<br />';
        }
    ?>
</div>
<?php
}
// Check if there is no success alerts
$success = Session::get_flash('success');
if (isset($success)) {
?>
<div class="alert alert-success">
    <h4>Apsveicu!</h4>
    <?php echo $success.'<br />'; ?>
</div>
<?php } ?>

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