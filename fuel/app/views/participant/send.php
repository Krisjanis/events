<?php
echo Form::open('participant/email/'.$event_id);
echo Form::fieldset_open(null, 'Nosūti uzaicinājumu');

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

<label class="control-label" for="email">E-pasts</label>
<div class="controls">
    <input type="text" name="email" id="email" value="<?php
        if (isset($_POST['email'])) {
            echo $_POST['email'];
        }
    ?>" />
</div>

<label class="control-label" for="message">Ziņa</label>
<div class="controls">
    <textarea name="message" id="message" class="span5"><?php
        if (isset($_POST['message'])) {
            echo $_POST['message'];
        }
    ?></textarea>
</div>

<br />
<input type="Submit" value="Nosūtīt" class="btn" />

<?php
echo Form::fieldset_close();
echo Form::close();
?>