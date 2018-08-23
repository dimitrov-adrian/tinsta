<?php

if ( ! (is_singular() && get_page_template_slug() == 'template-fullscreen.php')) {
  locate_template('template-parts/theme/footer.php', true);
}

?>

</body>
</html>
