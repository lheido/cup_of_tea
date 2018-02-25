/**
 * @file
 * Cup of tea js behavior.
 */
(function ($, Drupal, drupalSettings) {
  Drupal.behaviors.cupOfTea = {
    attach: function (context) {
      $('#' + drupalSettings.cup_of_tea.component, context).once('cup-of-tea-behavior').each(function (index, elt) {
        var $elt = $(elt);
        var $autocomplete = $elt.find('[data-autocomplete]');
        var dialog = Drupal.dialog(elt, {
          width: 'auto',
          height: 296,
          autoResize: false,
          draggable: false,
          resizable: false,
          classes: {
            'ui-dialog': 'cup-of-tea-dialog'
          },
          title: Drupal.t('Cup of Tea')
        });
        // Handle keyboard shortcut.
        Mousetrap.bind(drupalSettings.cup_of_tea.shortcut, function (event) {
          $autocomplete.val('');
          dialog.showModal();
          event.preventDefault();
          return false;
        });
        // Get autocomplete data and init jQuery ui autocomplete element.
        $.getJSON(drupalSettings.cup_of_tea.data_route, function ($data) {
          $autocomplete.autocomplete({
            source: $data,
            select: function (event, ui) {
              window.location = ui.item.link;
              event.preventDefault();
              return false;
            },
            appendTo: '#' + drupalSettings.cup_of_tea.component + ' [data-autocomplete-results]'
          });
          $autocomplete.data('ui-autocomplete')._renderItem = function (ul, item) {
            return $('<li class="ui-menu-item"></li>')
              .data('item.autocomplete', item)
              .append('<div class="ui-menu-item-wrapper"><span>' + item.label + '</span><small class="item-link">' +
                item.link + '</small></div>')
              .appendTo(ul);
          };
        });

      });
    }
  };

})(jQuery, Drupal, drupalSettings);