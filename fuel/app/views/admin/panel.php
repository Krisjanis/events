<div class="events-table-wrapper">
    <h2>Jaunākie pasākumi</h2>
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
                <td><?php echo Html::anchor('user/view/'.$event['author_id'], $event['author']); ?></td>
                <td class="table-actions"><?php echo Html::anchor('admin/delete/', "<span class='label label-important'>Bloķēt un dzēst <i class=' icon-ban-circle icon-white'></i></span>", array('onclick' => "return confirm('Vai tiešām vēlaties bloķēt autoru un dzēst pasākumu?')")); ?></td>
            </tr>
            <?php $i++; ?>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<div class="comments-table-wrapper">
    <h2>Jaunākie komentāri</h2>
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
        <?php foreach ($newest_comments as $comment) : ?>
            <tr>
                <td><?php echo $comment['id']; ?></td>
                <td><?php echo Html::anchor('user/view/'.$comment['author_id'], $comment['author']); ?></td>
                <td><?php echo Html::anchor('event/view/'.$comment['event_id'], $comment['event_title']); ?></td>
                <td><?php echo $comment['message']; ?></td>
                <td class="table-actions"><?php echo Html::anchor('admin/delete/', "<span class='label label-important'>Bloķēt un dzēst <i class=' icon-ban-circle icon-white'></i></span>", array('onclick' => "return confirm('Vai tiešām vēlaties bloķēt autoru un dzēst komentāru?')")); ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<div class="comments-table-wrapper">
    <h2>Jaunākie labotie komentāri</h2>
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
                <td><?php echo Html::anchor('user/view/'.$comment['author_id'], $comment['author']); ?></td>
                <td><?php echo Html::anchor('event/view/'.$comment['event_id'], $comment['event_title']); ?></td>
                <td><?php echo $comment['message']; ?></td>
                <td class="table-actions"><?php echo Html::anchor('admin/delete/', "<span class='label label-important'>Bloķēt un dzēst <i class=' icon-ban-circle icon-white'></i></span>", array('onclick' => "return confirm('Vai tiešām vēlaties bloķēt autoru un dzēst komentāru?')")); ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>