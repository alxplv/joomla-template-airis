<?php

// No direct access to this file outside of Joomla!
defined('_JEXEC') or exit;

// Joomla! imports
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Image\Image;
// use Joomla\CMS\Layout\LayoutHelper;

// Make item title HTML-safe
$item->title = htmlspecialchars(trim($item->title), ENT_QUOTES, 'UTF-8');

$itemImageContainerContent = '';

// Prepare item image
if ($params->get('img_intro_full') !== 'none' && isset($item->imageSrc) && $item->imageSrc !== '') {
    $itemImageAttributes = [
        'class' => 'airis-module-articles-news-article__image',
        'loading' => 'lazy',
    ];

    // TODO: Manual width and height acquisition is unnecessary if LayoutHelper::render('joomla.html.image', []) is used
    // Load the item image file into an Image object in order to access its properties
    $itemImage = new Image($item->imageSrc);

    // In case if $item->imageSrc points to a non-existing file
    if ($itemImage->isLoaded()) {
        $itemImageAttributes['width'] = $itemImage->getWidth();
        $itemImageAttributes['height'] = $itemImage->getHeight();
    }

    // Use item title as item image alt if there was no alt value set for it
    if (isset($item->imageAlt) && $item->imageAlt !== '') {
        $item->imageAlt = htmlspecialchars(trim($item->imageAlt), ENT_QUOTES, 'UTF-8');

        // TODO: To be used in Joomla 4+
        // $itemImageAttributes['alt'] = !empty($item->imageAlt) ? $item->imageAlt : $item->title;

        if ($item->imageAlt === '') {
            $item->imageAlt = $item->title;
        }
    }

    if (isset($item->imageCaption) && $item->imageCaption !== '') {
        $item->imageCaption = htmlspecialchars(trim($item->imageCaption), ENT_QUOTES, 'UTF-8');
    }

    // TODO: To be used in Joomla! 4+
/* 	$itemImageContainerContent = LayoutHelper::render(
        'joomla.html.image',
        $itemImageAttributes,
    ); */

    $itemImageContainerContent = HTMLHelper::image(
        $item->imageSrc,
        $itemImageAlt,
        $itemImageAttributes,
    );

    // Add link to item image
    $itemImageContainerContent = HTMLHelper::link(
        $item->link,
        $itemImageContainerContent,
        ['title' => $item->title],
    );
}

?>

<li class="airis-module-articles-news__item <?php echo $itemDisplayModeClass; ?>">
    <article class="airis-module-articles__article">

        <?php if ($itemImageContainerContent) : ?>

            <div class="airis-module-articles-news__image-container">
                <figure class="airis-module-articles-news__figure">

                    <?php echo $itemImageContainerContent; ?>

                    <?php if (isset($item->imageCaption) && $item->imageCaption !== '') : ?>

                        <figcaption class="airis-module-articles-news__figure-caption">
                            <?php echo $item->imageCaption; ?>
                        </figcaption>

                    <?php endif; ?>

                </figure>
            </div>

        <?php endif; ?>

        <?php if ($params->get('item_title')) : ?>
            <div class="airis-module-articles-news__title-container airis-module-header airis-header">
                <?php
                    $itemHeadingTag = $params->get('item_heading', 'h4');

                    echo "<$itemHeadingTag class=\"airis-module-articles-news__title\">";

                    if ($params->get('link_titles') && isset($item->link) && $item->link !== '') {
                        $titleLinkAttributes = [
                            'class' => 'airis-module-articles-news__title-link',
                        ];

                        echo HTMLHelper::link($item->link, $item->title, $titleLinkAttributes);
                    } else {
                        echo $item->title;
                    }

                    echo "</$itemHeadingTag>"
                ?>
            </div>
        <?php endif; ?>

        <?php if (!$params->get('intro_only') && isset($item->afterDisplayTitle) && $item->afterDisplayTitle !== '') : ?>

            <div class="airis-module-articles-news__title-after airis-item-content">
                <?php echo $item->afterDisplayTitle; ?>
            </div>

        <?php endif; ?>

        <?php if (isset($item->beforeDisplayContent) && $item->beforeDisplayContent !== '') : ?>

            <div class="airis-module-articles-news__content-before airis-item-content">
                <?php echo $item->beforeDisplayContent; ?>
            </div>

        <?php endif; ?>

        <?php if ($params->get('show_introtext') && isset($item->introtext) && $item->introtext !== '') : ?>

            <div class="airis-module-articles-news__content airis-item-content">
                <?php echo $item->introtext; ?>
            </div>

        <?php endif; ?>

        <?php if (isset($item->afterDisplayContent) && $item->afterDisplayContent !== '') : ?>

            <div class="airis-module-articles-news__content-after airis-item-content">
                <?php echo $item->afterDisplayContent; ?>
            </div>

        <?php endif; ?>

        <?php if ($params->get('readmore') && $item->link && $item->readmore !== '') : ?>

            <div class="airis-module-articles-news__readmore-container">
                <?php
                    $readmoreLinkAttributes = [
                        'class' => 'airis-module-articles-news__readmore-link btn',
                    ];

                    // TODO: To be used on Joomla! 4+ with an overriden readmore layout which accepts link attributes and link text
/*                     echo LayoutHelper::render(
                        'joomla.content.readmore',
                        [
                            'item' => $item,
                            'params' => $item->params,
                            'link' => $item->link,
                            'text' => $item->linkText,
                            'attributes' => $readmoreLinkAttributes, // TODO: Unset possible $readmoreLinkAttributes['href'] in readmore layout so it doesn't double
                        ]
                    ); */

                    echo HTMLHelper::link(
                        $item->link,
                        htmlspecialchars(trim($item->linkText), ENT_QUOTES, 'UTF-8'),
                        $readmoreLinkAttributes,
                    );
                ?>
            </div>

        <?php endif; ?>

    </article>
</li>