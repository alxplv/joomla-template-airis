<?php

// No direct access to this file outside of Joomla!
defined('_JEXEC') or exit;

// Joomla! imports
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Image\Image;
use Joomla\CMS\Language\Text;
// use Joomla\CMS\Layout\LayoutHelper;

// Make item strings HTML-safe
$item->title = htmlspecialchars(trim($item->title), ENT_QUOTES, 'UTF-8');

$itemCustomFields = [];
$itemImageContainerContent = '';
$itemPartnerUrl = '';

// Prepare item image
if ($params->get('img_intro_full') !== 'none'
&& isset($item->imageSrc)
&& $item->imageSrc !== ''
) {
    $itemImageAttributes = [
        'class' => 'airis-module-partners__image',
    ];

    // Use item title as item image alt if there was no alt value set for it
    if (isset($item->imageAlt) && $item->imageAlt !== '') {
        $item->imageAlt = htmlspecialchars(trim($item->imageAlt), ENT_QUOTES, 'UTF-8');

        if ($item->imageAlt === '') {
            $item->imageAlt = $item->title;
        }
    } else {
        $item->imageAlt = $item->title;
    }

    if (isset($item->imageCaption) && $item->imageCaption !== '') {
        $item->imageCaption = htmlspecialchars(trim($item->imageCaption), ENT_QUOTES, 'UTF-8');
    }

    $itemImageContainerContent = HTMLHelper::image(
        $item->imageSrc,
        $item->imageAlt,
        $itemImageAttributes,
    );
}

// Prepare custom fields
if (
    isset($item->jcfields)
    && is_countable($item->jcfields)
    && count($item->jcfields) !== 0
) {
    foreach ($item->jcfields as $jcfield) {
        $itemCustomFields[$jcfield->name] = $jcfield;
    }

    // Use custom field URL value as item link
    if (
        isset($itemCustomFields['airis-partners-url']->rawvalue)
        && $itemCustomFields['airis-partners-url']->rawvalue !== ''
    ) {
        $itemPartnerUrl = htmlspecialchars(
            trim($itemCustomFields['airis-partners-url']->rawvalue),
            ENT_QUOTES,
            'UTF-8',
        );

        if ($itemPartnerUrl !== '') {
            $item->link = $itemPartnerUrl;
        }
    }
}

if (
    $itemImageContainerContent !== ''
    && $itemPartnerUrl !== ''
    ) {
    $itemImageContainerContent = HTMLHelper::link(
        $item->link,
        $itemImageContainerContent,
        [
            'class' => 'airis-module-partners__image-link',
            'title' => $item->title,
            'target' => '_blank',
        ],
    );
}
?>

<li class="airis-module-partners__item" itemscope itemtype="https://schema.org/Article">

    <?php if ($itemImageContainerContent !== '') : ?>

        <div class="airis-module-partners__image-container">
            <figure class="airis-module-partners__figure">

                <?= $itemImageContainerContent; ?>

                <?php if (isset($item->imageCaption) && $item->imageCaption !== '') : ?>

                    <figcaption class="airis-module-partners__figure-caption">
                        <?= $item->imageCaption; ?>
                    </figcaption>

                <?php endif; ?>

            </figure>
        </div>

    <?php endif; ?>

    <?php if ($params->get('item_title')) : ?>

        <div class="airis-module-partners__title-container airis-module-header airis-header">
            <?php
                $itemHeadingTag = $params->get('item_heading', 'h4');

                echo "<$itemHeadingTag class=\"airis-module-partners__title\">";

                if (
                    $params->get('link_titles')
                    && isset($item->link)
                    && $item->link !== ''
                ) {
                    $titleLinkAttributes = [
                        'class' => 'airis-module-partners__title-link',
                    ];

                    // Open partner link in a new window if partner has an URL custom field defined
                    if ($itemPartnerUrl !== '') {
                        $titleLinkAttributes['target'] = "_blank";
                    }

                    echo HTMLHelper::link(
                        $item->link,
                        $item->title,
                        $titleLinkAttributes,
                    );
                } else {
                    echo $item->title;
                }

                echo "</$itemHeadingTag>";
            ?>
        </div>

    <?php endif; ?>

    <?php if ($params->get('intro_only') && isset($item->afterDisplayTitle) && $item->afterDisplayTitle !== '') : ?>

        <div class="airis-module-partners__title-after airis-item-content">
            <?= $item->afterDisplayTitle; ?>
        </div>

    <?php endif; ?>

    <?php if (isset($item->beforeDisplayContent) && $item->beforeDisplayContent !== '') : ?>

        <div class="airis-module-partners__content-before airis-item-content">
            <?= $item->beforeDisplayContent; ?>
        </div>

    <?php endif; ?>

    <?php if ($params->get('show_introtext') && isset($item->introtext) && $item->introtext !== '') : ?>

        <div class="airis-module-partners__content airis-item-content">
            <?= $item->introtext; ?>
        </div>

    <?php endif; ?>

    <?php if (isset($item->afterDisplayContent) && $item->afterDisplayContent !== '') : ?>

        <div class="airis-module-partners__content-after airis-item-content">
            <?= $item->afterDisplayContent; ?>
        </div>

    <?php endif; ?>

    <?php if ($params->get('readmore') && isset($item->link) && $item->link !== '' && ($itemPartnerUrl || $item->readmore !== 0)) : ?>

        <div class="airis-module-partners__readmore-container">
            <?php
                $readmoreLinkAttributes = [
                    'class' => 'airis-module-partners__readmore-link btn',
                ];

                // Open partner link in a new window if partner has an URL custom field defined and replace readmore link text
                if ($itemPartnerUrl) {
                    $item->linkText = Text::_('TPL_AIRIS_MOD_ARTICLES_NEWS_PARTNERS_READ_MORE_TITLE');
                    $readmoreLinkAttributes['target'] = "_blank";
                }

                // TODO: To be used on Joomla! 4+ with an overriden readmore layout which accepts link attributes and link text
/*                 echo LayoutHelper::render(
                    'joomla.content.readmore',
                    [
                        'item' => $item,
                        'params' => $item->params,
                        'link' => $item->link,
                        'text' => $item->linkText,
                        'attributes' => $readmoreLinkAttributes, // TODO: Unset possible $readmoreLinkAttributes['href'] in readmore layout so it doesn't double
                    ]
                ); */
            ?>

            <?= HTMLHelper::link(
                $item->link,
                htmlspecialchars(trim($item->linkText), ENT_QUOTES, 'UTF-8'),
                $readmoreLinkAttributes,
                );
            ?>
        </div>

    <?php endif; ?>

</li>