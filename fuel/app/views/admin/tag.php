<div class="panel-search-wrapper">
    <h2>Meklē birku</h2>
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

    <label for="title">Birkas nosaukums :</label>
    <input type="text" name="title" id="title" value="<?php if (isset($_POST['title'])) :
        echo $_POST['title'];
    endif; ?>" />

    <input type="Submit" value="Meklēt" class="btn" />
    <?php echo Form::close(); ?>

    <h2>Pievieno birku</h2>
    <?php echo Form::open(array('action' => 'admin/tag_create', 'class' => 'form-inline')); ?>

    <?php $errors = Session::get_flash('tag_errors'); ?>
    <?php if (isset($errors)) : ?>
    <div class="alert alert-error">
        <h4>Kļūda!</h4>
        <?php foreach ($errors as $error) : ?>
            <?php echo $error.'<br />'; ?>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <?php $success = Session::get_flash('tag_success'); ?>
    <?php if (isset($success)) : ?>
    <div class="alert alert-success">
        <h4>Apsveicu!</h4>
        <?php echo $success.'<br />'; ?>
    </div>
    <?php endif; ?>

    <label for="title">Nosaukums :</label>
    <input type="text" name="title" id="title" value="<?php if (isset($_POST['title'])) :
        echo $_POST['title'];
    endif; ?>" />

    <input type="Submit" value="Pievienot" class="btn" />
    <?php echo Form::close(); ?>
</div>
<?php if (isset($tags)) : ?>
<div class="panel-table-wrapper">
    <h2>Birkas</h2>
    <table class="table table-striped">
        <thead>
            <tr>
                <th class="table-small">ID</th>
                <th class="table-normal">Nosaukums</th>
                <th class="table-normal">Autors</th>
                <th class="table-actions">Darbības</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($tags as $tag) : ?>
            <tr>
                <td><?php echo $tag['id']; ?></td>
                <td><?php echo $tag['title']; ?></td>
                <td>
                    <?php if ($tag['author_id'] != 0) : ?>
                        <?php echo Html::anchor('user/view/'.$tag['author_id'], $tag['author']); ?>
                    <?php else : ?>
                        <?php echo '<span class="deleted-user">dzēsts lietotājs</span>'; ?>
                    <?php endif; ?>
                    <?php if (isset($tag['admin']) and $tag['admin']) : ?>
                    <i class="icon-exclamation-sign"></i>
                    <?php endif; ?>
                </td>
                <td class="table-actions">
                    <?php if ($tag['author_id'] != 0) : ?>
                        <?php if (isset($tag['admin']) and $tag['admin']) : ?>
                            <?php echo Html::anchor('admin/delete_tag/'.$tag['id'], "<span class='label label-important'>Dzēst <i class=' icon-remove icon-white'></i></span>", array('onclick' => "return confirm('Vai tiešām vēlaties dzēst birku?')")); ?>
                        <?php else : ?>
                            <?php echo Html::anchor('admin/demote_tag/'.$tag['id'], "<span class='label label-important'>Noņemt prasmīgu un dzēst <i class=' icon-ban-circle icon-white'></i></span>", array('onclick' => "return confirm('Vai tiešām vēlaties bloķēt pazemināt lietotāja statusu par lietotāju un dzēst birku?')")); ?>
                        <?php endif; ?>
                    <?php else : ?>
                        <?php echo Html::anchor('admin/delete_tag/'.$tag['id'], "<span class='label label-important'>Dzēst <i class=' icon-remove icon-white'></i></span>", array('onclick' => "return confirm('Vai tiešām vēlaties dzēst birku?')")); ?>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>