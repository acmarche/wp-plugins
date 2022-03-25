<?php

new PostExpiration();

class PostExpiration
{
    const NAME_META = 'acmarche_expire_date';

    public function __construct()
    {
        add_action('add_meta_boxes', [$this, 'add_date_metabox']);
        add_action('save_post', [$this, 'save_expire_date_meta']);
        register_meta('post', self::NAME_META, [
            'show_in_rest' => true,
        ]);
    }

    function add_date_metabox(): void
    {
        $screens = array('post');

        foreach ($screens as $screen) {
            add_meta_box(
                'acmarche_expire_date_metabox',
                'Expiration date',
                [$this, 'date_metabox_callback'],
                $screen,
                'side',
                'high'
            );
        }
    }

    function date_metabox_callback(WP_Post $post)
    {
        ?>
        <form action="" method="post">
            <?php
            wp_nonce_field('acmarche_expire_date_metabox_nonce', 'hugu_nonce');
            $hugu_expire_date = get_post_meta($post->ID, self::NAME_META, true);
            ?>

            <label for "hugu_expire_date">Date</label>
            <input id="hugu_expire_date" type="datetime-local" class="MyDate" name="hugu_expire_date"
                   value="<?php echo $hugu_expire_date ?>"/>
        </form>

    <?php }

    function save_expire_date_meta($post_id): void
    {
        if ( ! isset($_POST['hugu_nonce']) ||
             ! wp_verify_nonce(
                 $_POST['hugu_nonce'],
                 'acmarche_expire_date_metabox_nonce'
             )) {
            return;
        }

        // CHECK FOR USER PERMISSION
        if ( ! current_user_can('edit_post', $post_id)) {
            return;
        }

        if ( ! empty($_POST['hugu_expire_date'])) {
            if ($this->convertToDateTime($_POST['hugu_expire_date']) instanceof DateTimeInterface) {
                update_post_meta($post_id, self::NAME_META, $_POST['hugu_expire_date']);
            } else {
                delete_post_meta(
                    $post_id,
                    self::NAME_META
                );  //If you remove the expiration date in the form, it will remove also from the meta
            }
        } else {
            delete_post_meta($post_id, self::NAME_META);
        }
    }

    function convertToDateTime($date): ?DateTimeInterface
    {
        try {
            return new DateTime($date);
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * @return \WP_Post[]
     */
    public function getPostsWithExpiration(): array
    {
        $query = new \WP_Query(
            array(
                'post_status' => 'publish',
                'post_type' => 'post',
                'meta_key' => self::NAME_META,
                'orderby' => 'title',
                'order' => 'ASC',
                'posts_per_page' => -1,
            )
        );

        return $query->get_posts();
    }

    function deleteExpirePost(): void
    {
        $today = new DateTime();
        foreach ($this->getPostsWithExpiration() as $post) {
            $expireDate = $this->convertToDateTime(get_post_meta($post->ID, self::NAME_META, true));
            if ($expireDate != null && $expireDate->format('Y-m-d') < $today->format('Y-m-d')) {
               // echo $post->post_title.'expire le '.$expireDate->format('Y-m-d').'\n';
                wp_delete_post($post->ID, false);
            }
        }
    }

}
