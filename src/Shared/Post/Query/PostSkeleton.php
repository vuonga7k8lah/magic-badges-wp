<?php


namespace MyShopKitMBWP\Shared\Post\Query;


use MyShopKitMBWP\Illuminate\Prefix\AutoPrefix;
use WP_Post;

class PostSkeleton
{
    protected array   $aPluck
        = [
            'id',
            'title',
            'date',
            'config',
            'status',
            'urlImage',
            'badgeID',
            'content',
            'postType',
            'keywords'
        ];
    protected WP_Post $oPost;

    public function checkMethodExists($pluck): bool
    {
        $method = 'get' . ucfirst($pluck);

        return method_exists($this, $method);
    }

    public function getTitle(): string
    {
        return $this->oPost->post_title;
    }

    public function getStatus(): string
    {
        return ($this->oPost->post_status == 'publish') ? 'active' : 'deactive';
    }

    public function getDate(): string
    {
        return (string)strtotime(date(get_option('date_format'), strtotime($this->oPost->post_date)));
    }

    public function getBadgeID(): string
    {
        return get_post_meta($this->oPost->ID, AutoPrefix::namePrefix('badge_id'), true);
    }

    public function getConfig(): array
    {
        $jPostMeta = get_post_meta($this->oPost->ID, AutoPrefix::namePrefix('config'), true);
        $aPostMeta=json_decode($jPostMeta,true);
        return is_array($aPostMeta) ? $aPostMeta : [];
    }

    public function getId(): string
    {
        return (string)$this->oPost->ID;
    }

    public function setPost(WP_Post $oPost): PostSkeleton
    {
        $this->oPost = $oPost;

        return $this;
    }

    public function getPostData($pluck, array $aAdditionalInfo = []): array
    {
        $aData = [];

        if (empty($pluck)) {
            $aPluck = $this->aPluck;
        } else {
            $aPluck = $this->sanitizePluck($pluck);
        }

        foreach ($aPluck as $pluck) {
            $method = 'get' . ucfirst($pluck);
            if (method_exists($this, $method)) {
                $aData[$pluck] = call_user_func_array([$this, $method], [$aAdditionalInfo]);
            }
        }

        return $aData;
    }

    private function sanitizePluck($rawPluck): array
    {
        $aPluck = is_array($rawPluck) ? $rawPluck : explode(',', $rawPluck);

        return array_map(function ($pluck) {
            return trim($pluck);
        }, $aPluck);
    }
}
