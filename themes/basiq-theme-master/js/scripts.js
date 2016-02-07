jQuery(function ($) {
  Js.Behaviors.modal = function(container) {
    console.log('success');
    var id = $(container).attr('data-modal');
    var modal = document.getElementById(id);

    $(container).on('click', function(){
      $('body').addClass('is-fixed');
      $(modal).addClass('is-open');
    });

    $(modal).on('click', '.js-close', function(){
      $('body').removeClass('is-fixed');
      $(modal).removeClass('is-open');
    })
  }

  Js.Behaviors.menu = function(container) {
    var $this = $(container);

    $this.on('click', '.js-open', function(){
      $this.addClass('is-open');
      $('body').addClass('is-fixed');
    });

    $this.on('click', '.js-close', function(){
      $this.removeClass('is-open');
      $('body').removeClass('is-fixed');
    });
  }

  Js.Behaviors.header = function(container) {
    $('.js-hero').on('inview', function(event, isInView) {
      if (isInView) {
        $(container).addClass('in-view');
      } else {
        $(container).removeClass('in-view');
      }
    });
  }
});
