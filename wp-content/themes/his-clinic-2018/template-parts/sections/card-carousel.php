<?php 
    global $part_args;
    $settings = $part_args['settings'];
    $cards = $settings['cards'];
?>

<div class="card-carousel">
    <div class="cards">
        <?php
            foreach ($cards as $card) {
                $image = (!empty($card['image']['url'])) ? $card['image']['url'] : null;
                
                get_template_part_with_args('template-parts/content/carousel-card/card', [
                    'image' => $image,
                    'title' => $card['title'],
                    'content' => $card['content'],
                ]);
            }
        ?>
    </div>
</div>