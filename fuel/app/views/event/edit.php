<?php
echo Form::open(array('class' => 'form-horizontal', 'id' => 'event'));
echo Form::fieldset_open(null, $form_title);

// Check if there is no error in form submition
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
<?php } ?>

<div class="control-group">
    <label class="control-label" for="title">Nosaukums</label>
    <div class="controls">
        <input type="text" name="title" id="title" value="<?php
            if (isset($_POST['title'])) {
                echo $_POST['title'];
            }
        ?>"/>
        <span class="help-block">Pasākuma nosaukums.</span>
    </div>
</div>
<div class="control-group">
    <label class="control-label" for="link_title">Saites nosaukums</label>
    <div class="controls">
        <input type="text" name="link_title" id="link_title" value="<?php
            if (isset($_POST['link_title'])) {
                echo $_POST['link_title'];
            }
        ?>"/>
        <span class="help-block">
            Pasākuma nosaukums, no kura tiks izveidota unikāla pasākuma saite.
            <br />Drīkst saturēt tikai ciparus un burtus bez mīkstinājuma zīmēm.
            <br />Piemēram, http://notikumiem.lv/jaunsNotikums2013
        </span>
    </div>
</div>
<div class="control-group">
    <label class="control-label" for="desc">Apraksts</label>
    <div class="controls">
        <textarea name="desc" id="desc" class="span5"><?php
            if (isset($_POST['desc'])) {
                echo $_POST['desc'];
            }
        ?></textarea>
        <span class="help-block">Pasākuma Apraksts.</span>
    </div>
</div>
<div class="control-group">
    <label class="control-label" for="location">Norises vieta</label>
    <div class="controls">
        <textarea name="location" id="location" class="span5"><?php
            if (isset($_POST['location'])) {
                echo $_POST['location'];
            }
        ?></textarea>
        <span class="help-block">Pasākuma Atrašanās vieta.</span>
    </div>
</div>
<div class="control-group">
    <label class="control-label" for="date">Datums</label>
    <div class="controls">
        <textarea name="date" id="date" class="span5"><?php
            if (isset($_POST['date'])) {
                echo $_POST['date'];
            }
        ?></textarea>
        <span class="help-block">Pasākuma norises datums.</span>
    </div>
</div>
<div class="page-header">
    <h2>Neobligāti lauki <small>Uzspied uz lauka, lai pievienotu lauku, vai uzspied uz jau esoša laukuma, lai noņemtu to!</small></h2>
</div>
<div class="not-mandetory">
     <div class="well well-small">
        <p>Dalībnieku skaits</p>
    </div>
    <div class="control-group">
        <p class="control-label">Dalībnieku skaits</p>
        <div class="controls">
            <span>No</span>
            <input class="span1" type="text" name="part_min" value="<?php
                if (isset($_POST['part_min'])) {
                    echo $_POST['part_min'];
                }
            ?>"/>
            <span>Līdz</span>
            <input class="span1" type="text" name="part_max" value="<?php
                if (isset($_POST['part_max'])) {
                    echo $_POST['part_max'];
                }
            ?>"/>
            <span class="help-block">
                Dalībnieku skaita ierobežojums.
                <br />Ja pasākumam ir tikai minimālā, vai maksimālā vērtība,
                tad atstājiet otru lauku tukšu.
            </span>
        </div>
    </div>
</div>
<div class="not-mandetory">
     <div class="well well-small">
        <p>Dalības maksa</p>
    </div>
    <div class="control-group">
        <label class="control-label" for="entry_fee">Dalības maksa</label>
        <div class="controls">
            <input type="text" name="entry_fee" id="entry_fee" value="<?php
                if (isset($_POST['entry_fee'])) {
                    echo $_POST['entry_fee'];
                }
            ?>"/>
            <span class="help-block">Pasākuma dalības maksa.</span>
        </div>
    </div>
</div>
<div class="not-mandetory">
     <div class="well well-small">
        <p>Līdzi jāņem</p>
    </div>
    <div class="control-group">
        <label class="control-label" for="takeaway">Līdzi jāņem</label>
        <div class="controls">
            <textarea name="takeaway" id="takeaway" class="span5"><?php
                if (isset($_POST['takeaway'])) {
                    echo $_POST['takeaway'];
                }
            ?></textarea>
            <span class="help-block">Lietas, kas obligāti jāņem līdzi, vai ir ieteicams paņemt.</span>
        </div>
    </div>
</div>
<div class="not-mandetory">
     <div class="well well-small">
        <p>Ģērbšanās stils</p>
    </div>
    <div class="control-group">
        <label class="control-label" for="dress_code">Ģērbšanās stils</label>
        <div class="controls">
            <textarea name="dress_code" id="dress_code" class="span5"><?php
                if (isset($_POST['dress_code'])) {
                    echo $_POST['dress_code'];
                }
            ?></textarea>
            <span class="help-block">Ģērbšanās stils, kādā obligāti ir jāierodas.</span>
        </div>
    </div>
</div>
<div class="not-mandetory">
     <div class="well well-small">
        <p>Vajag cilvēkus, kas ...</p>
    </div>
    <div class="control-group">
        <label class="control-label" for="assistants">Vajag cilvēkus, kas ...</label>
        <div class="controls">
            <textarea name="assistants" id="assistants" class="span5"><?php
                if (isset($_POST['assistants'])) {
                    echo $_POST['assistants'];
                }
            ?></textarea>
            <span class="help-block">Nepieciešamie cilvēki, kas izdara vai atrod konkrētas lietas.</span>
        </div>
    </div>
</div>
<button type="submit" class="btn span3">Saglabāt</button>

<?php
echo Form::fieldset_close();
echo Form::close();
?>