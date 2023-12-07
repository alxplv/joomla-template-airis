// Custom JavaScript
jQuery(document).on("ready", function () {

	AddMainMenuIconsFontAwesome();
	CreateMainMenuSubmenuCatalog();

	CreateCarouselPartners();

	SetupFullWidthCategoryButtons();

	ShowFormCallback();
	ShowFormVirtueMartProductContactUs();

	CreateDoubleGisWidgetMap("#company-map-location-rabinovicha", 54.998062815504355, 73.36564779281618, "70000001029687006", "omsk");
	CreateDoubleGisWidgetMap("#company-map-location-solnechnaya", 55.00568087376743, 73.18819284439088, "70000001006810694", "omsk");
	CreateDoubleGisWidgetMap("#company-map-location-dianova", 54.996475, 73.249313, "70000001061219317", "omsk");

});

/* jQuery(window).on("load", function () {

}); */

function AddMainMenuIconsFontAwesome() {

	if (!Joomla.getOptions("tpl_airis") || !Joomla.getOptions("tpl_airis").loadFontAwesome) return;

	var iconClassPrefix = "menu-item-icon-";
	var iconClassPattern = new RegExp("(" + iconClassPrefix + "\\S+)", "i"); // Same as /(menu-item-icon-\S+)/i

	jQuery("[class^=\"" + iconClassPrefix + "\"]").each(function () {

		var $currentElement = jQuery(this);
		var iconClass = $currentElement.attr("class").match(iconClassPattern)[0].replace(iconClassPrefix, "");

		$currentElement.prepend("<span class=\"fas " + iconClass + " fa-fw\" aria-hidden=\"true\"></span>");
	});
}

function CreateMainMenuSubmenuCatalog() {

	var $sourceMenu, $targetMenuItem, $targetMenuItemParent, $targetMenuItemSubmenuContainer;

	$sourceMenu = jQuery(".catalog-menu-source .menu").first(); // Avoid possible duplicate menus
	if (!$sourceMenu.length) return;

	$targetMenuItem = jQuery(".main-menu-item-catalog");
	if (!$targetMenuItem.length) return;

	// Create a submenu container using Joomla!'s mod_menu template classes
	$targetMenuItemParent = $targetMenuItem.parent();
	$targetMenuItemParent.append("<ul class=\"nav-child unstyled small\"></ul>");
	$targetMenuItemSubmenuContainer = $targetMenuItemParent.children(".nav-child");

	// Populate the container using links from the source menu
	$sourceMenu.find("a").each(function () {

		var $currentMenuLink = jQuery(this).clone().wrap("<li></li>").parent();

		if (window.location.pathname == $currentMenuLink.children("a").attr("href")) $currentMenuLink.addClass("active");
		$currentMenuLink.appendTo($targetMenuItemSubmenuContainer);
	});

	// Use classes from Joomla!'s mod_menu template to enable the created submenu
	$targetMenuItemParent.addClass("deeper parent");

	// Create new submenu toggles using a function from template.js file
	CreateResponsiveMainMenuSubmenuToggleButtons();
}

function CreateCarouselPartners() {

	if (typeof tns !== "function") {
		console.log("tiny-slider not loaded.");
		return;
	}

	var partnersCarousel = tns({
		animateDelay: 10000,
		autoplay: true,
		autoplayButtonOutput: false,
		container: ".module-partners-carousel .airis-module-partners__list",
		controls: false,
		items: 1,
		loop: true,
		mouseDrag: true,
		nav: false,
		navPosition: "bottom",
		slideBy: "page",
		speed: 500,
		responsive: {
			768: {
				items: 2
			},
			980: {
				items: 3
			}/* ,
			1200: {
				items: 4
			} */
		}
	});
}

function SetupFullWidthCategoryButtons() {

	jQuery(".full-width-category-buttons .menu").find("a").prepend("<span class=\"fas fa-arrow-right\" aria-hidden=\"true\"></span>");

	jQuery(".catalog-category-buttons .menu").find("div").on("click", function () {
		window.location.assign(jQuery(this).children("a").attr("href"));
	});
}

function ShowFormCallback() {

	jQuery(".callback-button").on("click", function (event) {

		// Prevent the # symbol from showing up in address bar
		event.preventDefault();

		// DisplayMenuItemInFancybox(127); // /index.php?Itemid=101&tmpl=component works unreliably prior to J!4
		DisplayMenuItemAliasInFancybox("zakazat-obratnyj-zvonok");
	});
}

function ShowFormVirtueMartProductContactUs() {

	var productName = '';
	var productSku = '';
	var productCustomFields = [];

	jQuery(".category-view, .productdetails-view").on("click", ".virtuemart-product-link-contact-us", function (event) {

		event.preventDefault();
		var $clickedButton = jQuery(event.target);

		// Acquire VirtueMart product data exposed via the Contact Us button's attributes
		productName = $clickedButton.data('virtuemart-product-name');
		productSku = $clickedButton.data('virtuemart-product-sku');

		// Also acquire product variant data exposed as a custom field
		

		DisplayMenuItemAliasInFancybox("oformit-zayavku-na-tovar-kataloga");
	});

	jQuery(window).on("modal-form-load-virtuemart-product-contact-us", function () {
		jQuery(".airis-lightbox-iframe iframe").contents().find("#fox-m111-textarea1").text(productName + "\n" + productSku);
	});
}

function OnModalFormLoadVirtueMartProductContactUs() {
	jQuery(window).trigger("modal-form-load-virtuemart-product-contact-us");
}