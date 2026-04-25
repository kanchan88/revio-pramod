<?php get_header(); ?>

<div class="container" style="padding: 40px 0 60px;">
  <?php while (have_posts()) : the_post(); ?>
    <article>
      <h1 style="font-size: clamp(28px, 4vw, 40px); font-weight: 800; margin: 0 0 16px; color: var(--ink);"><?php the_title(); ?></h1>
      <div class="page-content" style="font-size: 16px; color: var(--text); line-height: 1.7;">
        <?php the_content(); ?>
      </div>
    </article>
  <?php endwhile; ?>
</div>

<?php get_footer(); ?>
