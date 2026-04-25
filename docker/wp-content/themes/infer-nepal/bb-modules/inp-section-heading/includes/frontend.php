<?php
$s = $module->settings;
$title    = $s->title ?? '';
$subtitle = $s->subtitle ?? '';
$linkText = $s->link_text ?? '';
$linkUrl  = $s->link_url ?? '';
?>
<div class="sec-head">
  <div>
    <?php if ($title): ?><h2><?= esc_html($title) ?></h2><?php endif; ?>
    <?php if ($subtitle): ?><p><?= esc_html($subtitle) ?></p><?php endif; ?>
  </div>
  <?php if ($linkText && $linkUrl): ?>
    <a href="<?= esc_url($linkUrl) ?>" class="more"><?= esc_html($linkText) ?></a>
  <?php endif; ?>
</div>
