<h2><?php echo $title; ?></h2>
<?php foreach ($events as $event) : ?>
<div class="event-thumb">
    <a href="<?php echo Uri::base().'event/view/'.$event['id']; ?>">
        <h2><?php echo $event['title']; ?></h2>
        <p><?php echo $event['desc']; ?></p>
    </a>
</div>
<?php endforeach; ?>