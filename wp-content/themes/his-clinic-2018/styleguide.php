<?php
// Template Name: Style Guide

get_header();

while (have_posts()): the_post();
?>

<div class="styleguide">
    <div class="container">
        <h1 class="h1">This is a heading</h1>
        <h2 class="h2">This is a heading</h2>
        <h3 class="h3">This is a heading</h3>
        <h4 class="h4">This is a heading</h4>
        <h5 class="h5">This is a heading</h5>
        <h6 class="h6">This is a heading</h6>
        <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Eaque, repellendus accusamus? Dignissimos vero voluptatem dicta ut ad nostrum mollitia ipsum dolor, adipisci et totam asperiores ducimus quis, quod animi porro?</p>
        <ul>
            <li>This is an unsorted item</li>
            <li>This is an unsorted item</li>
            <li>This is an unsorted item</li>
            <li>This is an unsorted item</li>
            <li>This is an unsorted item</li>
        </ul>

        <ol>
            <li>This is a sorted item</li>
            <li>This is a sorted item</li>
            <li>This is a sorted item</li>
            <li>This is a sorted item</li>
            <li>This is a sorted item</li>
        </ol>

        <div class="buttons">
            <a href="" class="btn">Call to action</a>
            <a href="" class="btn teal">Call to action</a>
            <a href="" class="btn red">Call to action</a>
            <a href="" class="btn blue">Call to action</a>
            <a href="" class="btn blue to-white">Call to action</a>
            <a href="" class="btn transparent">Call to action</a>
            <a href="" class="btn to-orange">Call to action</a>
            <a href="" class="btn transparent to-orange">Call to action</a>
        </div>
    </div>
</div>

<?php endwhile; ?>

<?php get_footer() ?>