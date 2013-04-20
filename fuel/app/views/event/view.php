<h1><?php echo $event['title']; ?></h1>
<p><?php echo $event['desc']; ?></p>
<dl>
    <dt><h2>Atrašanās vieta</h2></dt>
    <dd><?php echo $event['location']; ?></dd>
    <dt><h2>Norises datums</h2></dt>
    <dd><?php echo $event['date']; ?></dd>
    <?php if (isset($event['part_min']) && isset($event['part_max'])) : ?>
        <dt><h2>Dalībnieku skaits</h2><dt>
        <dd>No <?php echo $event['part_min']; ?> līdz <?php echo $event['part_max']; ?> dalībniekiem</dd>
    <?php elseif (isset($event['part_min'])) : ?>
        <dt><h2>Minimālais dalībnieku skaits</h2><dt>
        <dd>Vismaz <?php echo $event['part_min']; ?> dalībnieki</dd>
    <?php elseif (isset($event['part_max'])) : ?>
        <dt><h2>Maksimālais dalībnieku skaits</h2><dt>
        <dd>Ne vairāk par <?php echo $event['part_max']; ?> dalībniekiem</dd>
    <?php endif; ?>
    <?php if (isset($event['entry_fee'])) : ?>
        <dt><h2>Dalības maksa</h2><dt>
        <dd>Dalības maksa ir <?php echo $event['entry_fee']; ?> Ls</dd>
    <?php endif; ?>
    <?php if (isset($event['takeaway'])) : ?>
        <dt><h2>Līdzi jāņem ...</h2><dt>
        <dd><?php echo $event['takeaway']; ?></dd>
    <?php endif; ?>
    <?php if (isset($event['dress_code'])) : ?>
        <dt><h2>Ģērbšanās stils</h2><dt>
        <dd><?php echo $event['dress_code']; ?></dd>
    <?php endif; ?>
    <?php if (isset($event['assistants'])) : ?>
        <dt><h2>Nepieciešami cilvēki, kas ...</h2><dt>
        <dd><?php echo $event['assistants']; ?></dd>
    <?php endif; ?>
</dl>