
(function($){
  $(function(){

    // --- Variation selects -> radios (for small option counts) ---
    $('.variations_form').each(function(){
      var $form = $(this);

      function buildRadios($sel){
        if ($sel.data('en-radio')) return;
        var opts = $sel.find('option');
        if (opts.length > 9) return; // keep select if too many (incl. placeholder)
        var name = $sel.attr('name');
        var $wrap = $('<div class="en-attr-radios" data-name="'+name+'"></div>');

        opts.each(function(){
          var v = $(this).val();
          var t = $(this).text();
          if (!v) return;
          var id = (name+'-'+v).replace(/[^a-z0-9_-]/gi,'');
          var $r = $('<label class="en-radio" for="'+id+'"><input type="radio" id="'+id+'" name="'+name+'" value="'+v+'"><span>'+t+'</span></label>');
          $wrap.append($r);
        });

        $sel.after($wrap).hide().data('en-radio', true);

        // Sync initial value
        var current = $sel.val();
        if (current) $wrap.find('input[value="'+current+'"]').prop('checked', true);

        // Change handlers
        $wrap.on('change','input[type=radio]', function(){
          $sel.val(this.value).trigger('change');
        });
        $sel.on('change', function(){
          var v = $sel.val();
          $wrap.find('input').prop('checked', false);
          if (v) $wrap.find('input[value="'+v+'"]').prop('checked', true);
        });
      }

      $form.find('.value select').each(function(){ buildRadios($(this)); });

      $form.on('woocommerce_update_variation_values', function(){
        $form.find('.value select').each(function(){
          var $sel = $(this);
          // Keep radios in sync / rebuild if options changed
          if ($sel.data('en-radio')) {
            var $wrap = $sel.next('.en-attr-radios');
            if ($wrap.length){
              // remove radios for options not present
              var values = $sel.find('option').map(function(){return $(this).val();}).get();
              $wrap.find('input').each(function(){
                if (values.indexOf(this.value) === -1) $(this).closest('label').remove();
              });
            } else {
              buildRadios($sel);
            }
          } else {
            buildRadios($sel);
          }
        });
      });

      $form.on('reset_data', function(){
        $form.find('.en-attr-radios input').prop('checked', false);
      });
    });

    // --- Desktop sticky add-to-cart: show when purchase box is off-screen ---
    var $bar = $('.en-sticky-atc');
    if ($bar.length){
      function visible(el){
        if (!el) return false;
        var r = el.getBoundingClientRect();
        var h = (window.innerHeight || document.documentElement.clientHeight);
        // Consider it visible if at least 120px of it is inside the viewport
        var threshold = 120;
        return (r.top < h - threshold) && (r.bottom > threshold);
      }
      function toggle(){
        var box = document.getElementById('en-purchase-box');
        if (box && visible(box)) {
          $bar.removeClass('visible');
        } else {
          if (window.matchMedia('(min-width: 1025px)').matches) {
            $bar.addClass('visible');
          } else {
            $bar.removeClass('visible');
          }
        }
      }
      $(window).on('scroll resize', toggle);
      toggle();
    }

    // Smooth scroll for sticky buttons
    $(document).on('click', '.en-sticky-atc__btn, .en-jump-bar', function(e){
      var href = $(this).attr('href');
      if (href && href.indexOf('#') === 0) {
        var $t = $(href);
        if ($t.length){
          e.preventDefault();
          $('html, body').animate({scrollTop: $t.offset().top - 12}, 300);
        }
      }
    });

  });
})(jQuery);

