<div class="panel-search-wrapper">
    <h2>Meklē pasākumu</h2>
    <?php echo Form::open(array('class' => 'form-inline')); ?>

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

    <label for="value">Saites nosaukums :</label>
    <input type="text" name="value" id="value" value="<?php if (isset($_POST['value'])) :
        echo $_POST['value'];
    endif; ?>" />

    <input type="Submit" value="Meklēt" class="btn" />
    <?php echo Form::close(); ?>
</div>
<?php if (isset($events)) : ?>
<div class="panel-table-wrapper">
    <h2>Pasākumi</h2>
    <table class="table table-striped">
        <thead>
            <tr>
                <th class="table-small">#</th>
                <th class="table-small">Tips</th>
                <th class="table-normal">Saites nosaukums</th>
                <th class="table-normal">Nosaukums</th>
                <th class="table-normal">Autors</th>
                <th class="table-actions">Darbības</th>
            </tr>
        </thead>
        <tbody>
        <?php $i = 1; ?>
        <?php foreach ($events as $event) : ?>
            <tr>
                <td><?php echo $i; ?></td>
                <td><?php echo $event['type']; ?></td>
                <td><?php echo Html::anchor('event/view/'.$event['id'], $event['id']); ?></td>
                <td><?php echo $event['title']; ?></td>
                <td>
                    <?php if ($event['author_id'] != 0) : ?>
                        <?php echo Html::anchor('user/view/'.$event['author_id'], $event['author']); ?>
                    <?php else : ?>
                        <?php echo '<span class="deleted-user">dzēsts lietotājs</span>'; ?>
                    <?php endif; ?>
                    <?php if (isset($event['admin']) and $event['admin']) : ?>
                    <i class="icon-exclamation-sign"></i>
                    <?php endif; ?>
                </td>
                <td class="table-actions">
                    <?php if ($event['author_id'] != 0) : ?>
                        <?php echo Html::anchor('admin/block_event/'.$event['id'], "<span class='label label-important'>Bloķēt un dzēst <i class=' icon-ban-circle icon-white'></i></span>", array('onclick' => "return confirm('Vai tiešām vēlaties bloķēt autoru un dzēst pasākumu?')")); ?>
                    <?php else : ?>
                        <?php echo Html::anchor('admin/delete_event/'.$event['id'], "<span class='label label-important'>Dzēst <i class=' icon-remove icon-white'></i></span>", array('onclick' => "return confirm('Vai tiešām vēlaties dzēst pasākumu?')")); ?>
                    <?php endif; ?>
                </td>
            </tr>
            <?php $i++; ?>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>