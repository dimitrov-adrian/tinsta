    <?php

      if (!(is_singular() && get_page_template_slug() == 'template-fullpage.php')) {
        locate_template('template-parts/misc/footer.php', true);
      }

    ?>


  </body>
</html>
