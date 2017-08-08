/**
 *
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */

(function ($) {

    function fetchBlock ($block) {

        var overlay = $('<div class="overlay"><div class="fa fa-refresh fa-spin"></div></div>');
        // missing AdminLTE css for info-box necessary for overlay
        $block.find('.overlay-wrapper').css('position','relative');
        // show a loading indicator
        $block.find('.overlay-wrapper').append(overlay);
        return $.ajax($block.data('url')).done(function (html) {
            // populate the block with html
            $block.find('.url-data').html(html);
        }).fail(function (xhr) {
            // show an error symbol
            $block.find('.url-data').html('<div class="fa fa-remove text-danger"></div>');
        }).always(function () {
            // hide the loading indicator
            $block.find(overlay).remove();
        })
    }

    $(function () {
        $('.sonata-dashboard-ajax-block').each(function (i, block) {
            var $block = $(block)
            fetchBlock($block)
        })
        // for users to be able to refresh the box content on click
        $(document).on('click', '.sonata-dashboard-ajax-block__refresh-btn', function (event) {
            var $block = $(event.target).closest('.sonata-dashboard-ajax-block')
                fetchBlock($block)
            })
        }) 

}(jQuery))
