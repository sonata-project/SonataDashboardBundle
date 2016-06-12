/**
 *
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */

(function ($, global) {

	const getAllBlocks = el => Array.from(el.querySelectorAll('.sonata-dashboard-ajax-block'))
	const populateBlock = (block, html) => block.innerHTML = html
	const fetchBlock = block => fetch(block.dataset.url)
	  .then(response => response.text())
	  .catch(error => `/!\ Failed to load ${block.dataset.url}`)
	  .then(html => populateBlock(block, html))
	const fetchAllBlocks = el => Promise.all(getAllBlocks(el).map(fetchBlock))

	document.addEventListener('DOMContentLoaded', () => fetchAllBlocks(document).then(() => {
	  // all blocks loaded
	}))

})(jQuery, window);
