<?php

use AcMarche\Bottin\src\Repository\BottinRepository;

class BottinCategoryMetaBox
{
    const KEY_NAME = 'bottin_refrubrique';

    public static function category_metabox_edit($tag)
    {
        $single = true;
        $term_id = $tag->term_id;
        $bottin_refrubrique = get_term_meta($term_id, self::KEY_NAME, $single);
        $bottinRepository = new BottinRepository();
      //  dump($bottinRepository->getTreeCategories());

        ?>
        <table class="form-table">
            <tr class="form-field">
                <th scope="row" valign="top"><label for="bottin_refrubrique">Référence rubrique bottin</label></th>
                <td>
                    <input type="text" name="bottin_refrubrique" id="bottin_refrubrique" size="3" style="width: 75px;"
                           value="<?php echo $bottin_refrubrique; ?>"><br/>
                    <p class="description">Indiquer le numéro correspondant à la rubrique.</p>
                </td>
            </tr>
        </table>
        <?php
    }

    public static function save_category_metadata($term_id)
    {
        $meta_key = self::KEY_NAME;

        if (isset($_POST[$meta_key]) && $_POST[$meta_key] != 0 && $_POST[$meta_key] != '') {
            $meta_value = (int)$_POST[$meta_key];
            update_term_meta($term_id, $meta_key, $meta_value);
        } else {
            delete_term_meta($term_id, $meta_key);
        }
    }

}

?>
