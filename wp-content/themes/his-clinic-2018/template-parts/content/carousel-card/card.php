<?php 
global $part_args;
$image = $part_args['image'];
$title = $part_args['title'];
$content = $part_args['content'];
$link = (!empty($part_args['link'])) ? $part_args['link'] : null;
?>

<?php if ($link): ?>
    <a href="<?php echo $link ?>" class="custom-card flippable hover">
<?php else: ?>
    <div class="custom-card flippable hover">
<?php endif ?>

    <div class="inner">
        <div class="front" style="background-image: url('<?php echo $image ?>')">&nbsp;</div>
        <div class="back">
            <div class="dt">
                <div class="dtc">
                    <h5 class="h5"><?php echo $title ?></h5>
                    <div class="divider">&nbsp;</div>
                    <p><?php echo $content; ?></p>
                </div>
            </div>
        </div>
    </div>

<?php if ($link): ?>
    </a>
<?php else: ?>
    </div>
<?php endif ?>