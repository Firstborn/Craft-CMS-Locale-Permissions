
(function($) {

   Craft.LocalePermissions = Garnish.Base.extend({
      init: function (readOnlyAccessMessage) {
         if (!$('.localePermissionsWarning').length) {
            // disable input fields

            var mainEl = $('#main');
            mainEl.find('.field').addClass('localePermissionsDisabled');

            mainEl.find('.field textarea.text').attr('readonly', 'readonly');
            mainEl.find('.field input.text').attr('readonly', 'readonly');
            mainEl.find('.field input.text.hasDatepicker').attr('disabled', 'disabled');
            mainEl.find('.field .input select').attr('disabled', 'disabled');
            mainEl.find('.field input[type=button]').attr('readonly', 'readonly');

            // prevent saving the page
            $('form#container, #content form').on('submit', function() {
                return false;
            });

            // add warning label
            var html = $('\
            <div class="padded">\
                    <div class="localePermissionsWarning">' + readOnlyAccessMessage + '</div>\
            </div>');

            $('#main').prepend(html);
         }

      }


   });

})(jQuery);
