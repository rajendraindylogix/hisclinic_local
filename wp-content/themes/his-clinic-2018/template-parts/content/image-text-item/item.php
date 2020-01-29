<?php 
global $part_args;
$image = $part_args['image'];
$title = $part_args['title'];
$subtitle = $part_args['subtitle'];
$content = $part_args['content'];
?>

<div class="image-text-item">
    <div class="inner">
        <div class="image">
            <img src="<?php echo $image ?>" alt="<?php echo $title ?>">
        </div>
        <div class="text">
            <h5 class="h6 bold"><?php echo $title ?></h5>
            <h6 class="h5 subtitle"><?php echo $subtitle ?></h6>
            <p><?php echo $content; ?></p>
        </div>
    </div>
</div>