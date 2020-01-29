<?php 
    global $part_args;
    $settings = $part_args['settings'];
    $items = $settings['items'];
?>

<div class="image-text-carousel">
    <div class="items">
        <?php
            foreach ($items as $item) {
                $image = (!empty($item['image']['url'])) ? $item['image']['url'] : null;
                
                get_template_part_with_args('template-parts/content/image-text-item/item', [
                    'image' => $image,
                    'title' => $item['title'],
                    'subtitle' => $item['subtitle'],
                    'content' => $item['content'],
                ]);
            }
        ?>
    </div>
</div>