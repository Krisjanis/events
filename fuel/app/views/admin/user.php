<div class="panel-search-wrapper">
    <h2>Meklē lietotāju</h2>
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

    <label for="username">Lietotājvārds :</label>
    <input type="text" name="username" id="username" value="<?php if (isset($_POST['username'])) :
        echo $_POST['username'];
    endif; ?>" />

    <input type="Submit" value="Meklēt" class="btn" />
    <?php echo Form::close(); ?>
</div>
<?php if (isset($users)) : ?>
<div class="panel-table-wrapper user-table">
    <h2>Lietotāji</h2>
    <table class="table table-striped">
        <thead>
            <tr>
                <th class="table-small">ID</th>
                <th class="table-small">Lietotājvārds</th>
                <th class="table-small">Vārds</th>
                <th class="table-small">Uzvārds</th>
                <th class="table-small">E-pasts</th>
                <th class="table-small">Pēdējo reizi pieslēdzies</th>
                <th class="table-small">Reģistrējās</th>
                <th class="table-small">Grupa</th>
                <th class="table-actions">Darbības</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($users as $user) : ?>
            <tr>
                <td><?php echo $user['id']; ?></td>
                <td><?php echo Html::anchor('user/view/'.$user['id'], $user['username']); ?></td>
                <td>
                    <?php if (isset($user['name'])) : ?>
                    <?php echo $user['name']; ?>
                    <?php else : ?>
                    <?php echo '---'; ?>
                    <?php endif; ?>
                </td>
                <td><?php if (isset($user['surname'])) : ?>
                    <?php echo $user['surname']; ?>
                    <?php else : ?>
                    <?php echo '---'; ?>
                    <?php endif; ?></td>
                <td><?php echo $user['email']; ?></td>
                <td><?php echo $user['last_login']; ?></td>
                <td><?php echo $user['registered']; ?></td>
                <td><?php echo $user['group']; ?></td>
                <td class="table-actions">
                    <?php if ($user['group'] != 'Operators') : ?>

                        <?php if ($user['group'] == 'Prasmīgs') : ?>
                            <?php echo Html::anchor('admin/demote_power_user/'.$user['id'], "<span class='label label-warning'>Noņemt prasmīgu <i class='icon-circle-arrow-down icon-white'></i></span>", array('onclick' => "return confirm('Vai tiešām vēlaties lietotāju pazemināt no prasmīga lietotāja uz lietotāju?')")); ?>
                        <?php elseif ($user['group'] != 'Bloķēts') : ?>
                            <?php echo Html::anchor('admin/power_user/'.$user['id'], "<span class='label label-success'>Prasmīgs <i class='icon-circle-arrow-up icon-white'></i></span>", array('onclick' => "return confirm('Vai tiešām vēlaties lietotāju izveidot par prasmīgu lietotāju?')")); ?>
                        <?php endif; ?>

                        <?php if ($user['group'] != 'Bloķēts') : ?>
                            <?php echo Html::anchor('admin/block_user/'.$user['id'], "<span class='label label-important'>Bloķēt <i class='icon-ban-circle icon-white'></i></span>", array('onclick' => "return confirm('Vai tiešām vēlaties bloķēt lietotāju?')")); ?>
                        <?php else : ?>
                            <?php echo Html::anchor('admin/unblock_user/'.$user['id'], "<span class='label label-success'>Atbloķēt <i class='icon-circle-arrow-up icon-white'></i></span>", array('onclick' => "return confirm('Vai tiešām vēlaties atbloķēt lietotāju?')")); ?>
                        <?php endif; ?>

                        <?php echo Html::anchor('admin/delete_user/'.$user['id'], "<span class='label label-important'>Dzēst  <i class='icon-trash icon-white'></i></span>", array('onclick' => "return confirm('Vai tiešām vēlaties dzēst lietotāju?')")); ?>

                    <?php elseif ($user['id'] != $user_id) : ?>
                        <?php echo Html::anchor('admin/demote_admin/'.$user['id'], "<span class='label label-important'>Noņemt operatoru <i class='icon-circle-arrow-down icon-white'></i></span>", array('onclick' => "return confirm('Vai tiešām vēlaties noņemt operatora statusu?')")); ?>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>