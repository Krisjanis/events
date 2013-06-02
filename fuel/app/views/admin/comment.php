<div class="panel-search-wrapper">
    <h2>Meklē Komentāru</h2>
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
    <label for="search_type">Meklēt pēc :</label>
    <select name="search_type" id="search_type">
        <option value="event"<?php if (Session::get_flash('search_type') == 'event') : ?>
            <?php echo ' selected'; ?>
        <?php endif; ?>>Pasājuma saites nosaukuma/ID</option>
        <option value="user"<?php if (Session::get_flash('search_type') == 'user') : ?>
            <?php echo ' selected'; ?>
        <?php endif; ?>>Lietotājvārda</option>
        <option value="string" default<?php if (Session::get_flash('search_type') == 'string') : ?>
            <?php echo ' selected'; ?>
        <?php endif; ?>>Kas satur frāzi</option>
    </select>

    <label for="value">Vērtība :</label>
    <input type="text" name="value" id="value" value="<?php echo Session::get_flash('value'); ?>" />

    <input type="Submit" value="Meklēt" class="btn" />
    <?php echo Form::close(); ?>
</div>
<div class="comments-table-wrapper">
    <h2>Komentāri</h2>
    <table class="table table-striped">
        <thead>
            <tr>
                <th class="table-small">ID</th>
                <th class="table-normal">Autors</th>
                <th class="table-normal">Pasākums</th>
                <th class="table-large">Ziņa</th>
                <th class="table-actions">Darbības</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($comments as $comment) : ?>
            <tr>
                <td><?php echo $comment['id']; ?></td>
                <td>
                    <?php if ($comment['author_id'] != 0) : ?>
                        <?php echo Html::anchor('user/view/'.$comment['author_id'], $comment['author']); ?>
                    <?php else : ?>
                        <?php echo '<span class="deleted-user">dzēsts lietotājs</span>'; ?>
                    <?php endif; ?>
                    <?php if (isset($comment['admin']) and $comment['admin']) : ?>
                    <i class="icon-exclamation-sign"></i>
                    <?php endif; ?>
                </td>
                <td><?php echo Html::anchor('event/view/'.$comment['event_id'], $comment['event_id']); ?></td>
                <td><?php echo $comment['message']; ?></td>
                <td class="table-actions">
                    <?php if ($comment['author_id'] != 0 and ! isset($comment['admin'])) : ?>
                        <?php echo Html::anchor('admin/block_comment/'.$comment['id'], "<span class='label label-important'>Bloķēt un dzēst <i class=' icon-ban-circle icon-white'></i></span>", array('onclick' => "return confirm('Vai tiešām vēlaties bloķēt autoru un dzēst komentāru?')")); ?>
                    <?php else : ?>
                        <?php echo Html::anchor('admin/delete_comment/'.$comment['id'], "<span class='label label-important'>Dzēst <i class=' icon-remove icon-white'></i></span>", array('onclick' => "return confirm('Vai tiešām vēlaties dzēst komentāru?')")); ?>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php if (isset($newest_edited_comments)) : ?>
<div class="comments-table-wrapper">
    <h2>Labotie komentāri</h2>
    <table class="table table-striped">
        <thead>
            <tr>
                <th class="table-small">ID</th>
                <th class="table-normal">Autors</th>
                <th class="table-normal">Pasākums</th>
                <th class="table-large">Ziņa</th>
                <th class="table-actions">Darbības</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($newest_edited_comments as $comment) : ?>
            <tr>
                <td><?php echo $comment['id']; ?></td>
                <td>
                    <?php if ($comment['author_id'] != 0) : ?>
                        <?php echo Html::anchor('user/view/'.$comment['author_id'], $comment['author']); ?>
                    <?php else : ?>
                        <?php echo '<span class="deleted-user">dzēsts lietotājs</span>'; ?>
                    <?php endif; ?>
                    <?php if (isset($comment['admin']) and $comment['admin']) : ?>
                        <i class="icon-exclamation-sign"></i>
                    <?php endif; ?>
                </td>
                <td><?php echo Html::anchor('event/view/'.$comment['event_id'], $comment['event_id']); ?></td>
                <td><?php echo $comment['message']; ?></td>
                <td class="table-actions">
                     <?php if ($comment['author_id'] != 0 and ! isset($comment['admin'])) : ?>
                        <?php echo Html::anchor('admin/block_comment/'.$comment['id'], "<span class='label label-important'>Bloķēt un dzēst <i class=' icon-ban-circle icon-white'></i></span>", array('onclick' => "return confirm('Vai tiešām vēlaties bloķēt autoru un dzēst komentāru?')")); ?>
                    <?php else : ?>
                        <?php echo Html::anchor('admin/delete_comment/'.$comment['id'], "<span class='label label-important'>Dzēst <i class=' icon-remove icon-white'></i></span>", array('onclick' => "return confirm('Vai tiešām vēlaties dzēst komentāru?')")); ?>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>