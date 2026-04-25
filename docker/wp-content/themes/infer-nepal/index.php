<?php
get_header();
?>

<div class="container" style="padding: 40px 0;">
  <?php if (have_posts()) : ?>
    <h1 style="font-size:28px; font-weight:700; margin: 0 0 18px;">
      <?php if (is_search()) : printf('Search results for "%s"', esc_html(get_search_query())); else : single_post_title(); endif; ?>
    </h1>

    <div style="display:grid; gap:14px;">
      <?php while (have_posts()) : the_post(); ?>
        <?php if (get_post_type() === 'software') : ?>
          <?php inp_render_software_row(get_the_ID()); ?>
        <?php else : ?>
          <article style="border:1px solid var(--border); border-radius:12px; padding:18px;">
            <h2 style="margin:0 0 6px; font-size:18px; font-weight:700;"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
            <p style="margin:0; color:var(--text-muted); font-size:13.5px;"><?php the_excerpt(); ?></p>
          </article>
        <?php endif; ?>
      <?php endwhile; ?>
    </div>

    <div class="pagination">
      <?php echo paginate_links(['mid_size' => 1, 'prev_text' => '←', 'next_text' => '→']); ?>
    </div>

  <?php else : ?>
    <h1>Nothing found</h1>
    <p>Try a different search or <a href="<?= esc_url(home_url('/')) ?>">go back home</a>.</p>
  <?php endif; ?>
</div>

<?php get_footer(); ?>
