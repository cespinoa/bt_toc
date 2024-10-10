(function (Drupal, once) {
  Drupal.behaviors.toggleStickyAside = {
    attach: function (context, settings) {
      once('toggleStickyAside', '#toggle-sticky', context).forEach(function (element) {

        var selector = drupalSettings.bt_toc.toc_selector
        var stickyClass = drupalSettings.bt_toc.sticky_class
        var stickyArea = element.closest(selector);
        
        // Apply sticky class
        stickyArea.classList.add(stickyClass);

        // Add click event
        element.addEventListener('click', function () {

          // Toogle stickyClass
          stickyArea.classList.toggle(stickyClass);
         
          // Toogle button class
          if (stickyArea.classList.contains(stickyClass)) {
            this.classList.remove('pin-button');
            this.classList.add('unpin-button');
          } else {
            this.classList.remove('unpin-button');
            this.classList.add('pin-button');
          }
        });
      });
    }
  };
})(Drupal, once);
