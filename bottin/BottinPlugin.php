<?php

require_once 'BottinCategoryMetaBox.php';

add_action('category_edit_form_fields', 'BottinCategoryMetaBox::category_metabox_edit', 10, 1);
add_action('edited_category', 'BottinCategoryMetaBox::save_category_metadata', 10, 1);

class BottinPlugin
{
    /**
     * Le plugin du bottin:
     * Ajoute une méta box lorsqu'on édite une catégorie
     *
     */
}
