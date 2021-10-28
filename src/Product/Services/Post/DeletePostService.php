<?php


namespace MyShopKitMBWP\Product\Services\Post;


use Exception;
use MyShopKitMBWP\Illuminate\Message\MessageFactory;
use MyShopKitMBWP\Shared\Post\IDeleteUpdateService;
use MyShopKitMBWP\Shared\Post\TraitIsPostAuthor;
use MyShopKitMBWP\Shared\Post\TraitIsPostType;
use WP_Post;

class DeletePostService implements IDeleteUpdateService
{
    use TraitIsPostAuthor;
    use TraitIsPostType;

    private string $postID;

    public function setID($id): self
    {
        $this->postID = $id;

        return $this;
    }


    public function delete(): array
    {
        try {
            $aConfig = include plugin_dir_path(__FILE__) . '../../Configs/PostType.php';
            $this->isPostAuthor($this->postID);
            $this->isPostType($this->postID, $aConfig['postType']);

            $oPost = wp_delete_post($this->postID, true);

            if ($oPost instanceof WP_Post) {
                return MessageFactory::factory()->success(esc_html__('Congrats, the product manual has been deleted.',
                    MYSHOPKIT_MB_WP_REST_NAMESPACE), [
                    'id' => (string)$oPost->ID
                ]);
            }

            return MessageFactory::factory()->error(esc_html__('Sorry, We could not delete this product manual.',
                MYSHOPKIT_MB_WP_REST_NAMESPACE), 400);
        } catch (Exception $oException) {
            return MessageFactory::factory()->error(
                $oException->getMessage(),
                $oException->getCode()
            );
        }

    }
}
