<?php


namespace MyShopKitMBWP\Automatic\Services\Post;


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

    private string $postID   = '';
    private string $postType = '';

    public function setID($id): self
    {
        $this->postID = $id;

        return $this;
    }

    public function setPostType(string $postType): DeletePostService
    {
        $this->postType = $postType;
        return $this;
    }

    public function delete(): array
    {
        try {
            $this->isPostAuthor($this->postID);
            $this->isPostType($this->postID, $this->postType);
            $oPost = wp_delete_post($this->postID, true);

            if ($oPost instanceof WP_Post) {
                return MessageFactory::factory()->success(esc_html__('Congrats, the Automatic has been deleted.',
                    MYSHOPKIT_MB_WP_REST_NAMESPACE), [
                    'id' => (string)$oPost->ID
                ]);
            }

            return MessageFactory::factory()->error(
                esc_html__('Sorry, We could not delete this smart bar.', MYSHOPKIT_MB_WP_REST_NAMESPACE), 400
            );
        } catch (Exception $oException) {
            return MessageFactory::factory()->error(
                $oException->getMessage(),
                $oException->getCode()
            );
        }

    }
}
