<?php

namespace fitPlugin\frontend\webHooks;

use Shopify;

class webHooks
{
    protected $client;
    protected $table_name;
    private $fit_booking_options;

    function __construct($table_name)
    {
        $this->fit_booking_options = get_option('fit_booking_option_name');
        if ($this->fit_booking_options) {
            $this->client = new Shopify\PrivateApi(array(
                'api_version' => '2020-10/',
                'api_key' => array_key_exists('api_key_0', $this->fit_booking_options) ? $this->fit_booking_options['api_key_0'] : '63fa022de5d39ebe5aec2dee6ad5a3c4',
                'password' => array_key_exists('api_pass_0', $this->fit_booking_options) ? $this->fit_booking_options['api_pass_0'] : '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',
                'shared_secret' => array_key_exists('api_shared_secret_0', $this->fit_booking_options) ? $this->fit_booking_options['api_shared_secret_0'] : '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',
                'myshopify_domain' => array_key_exists('api_shop_domain_0', $this->fit_booking_options) ? $this->fit_booking_options['api_shop_domain_0'] : 'repose-space.co.uk',
            ));
        } else {
            $this->client = false;
        }
        $this->table_name = $table_name;
    }

    public function receiver()
    {
        if (array_key_exists("HTTP_X_SHOPIFY_HMAC_SHA256", $_SERVER)) {
            $data = file_get_contents('php://input');

            $topic = $_SERVER['HTTP_X_SHOPIFY_TOPIC'];
            $verified = $this->verify_webhook($data, $_SERVER['HTTP_X_SHOPIFY_HMAC_SHA256']);
            if ($verified) {
                $table = new table($this->table_name);

                if (method_exists($table, str_replace('/', '_', $topic))) {
                    $data = json_decode($data, true);
                    call_user_func_array(array($table, str_replace('/', '_', $topic)), array($data));
                } else {
                    error_log($topic . ' Hook or method is not exist');
                }
            } else {
                error_log($topic . ' Hook is not verified');
            }
        }
    }

    private function verify_webhook($data, $hmac_header)
    {
        $calculated_hmac = base64_encode(hash_hmac('sha256', $data, $this->fit_booking_options['api_shared_secret_0'], true));
        return hash_equals($hmac_header, $calculated_hmac);
    }

    public function create_hook($hook)
    {
        if (!$this->client)
            return false;

        $service = new Shopify\Service\WebhookService($this->client);

        $attr = array(
            'topic' => $hook
        );

        $hooks = $service->count($attr);

        if ($hooks === 0) {
            $webhook = new Shopify\Object\Webhook();
            $webhook->topic = $hook;
            $webhook->address = str_replace('/wp', '', site_url());
            $webhook->format = 'json';
            $service->create($webhook);

            error_log($webhook->topic . ' Created');
        }

    }

    public function remove_hook($hook_id)
    {
        if (!$this->client)
            return false;

        $service = new Shopify\Service\WebhookService($this->client);
        $service->delete($hook_id);
    }

    public function remove_all_hooks()
    {
        if (!$this->client)
            return false;

        $service = new Shopify\Service\WebhookService($this->client);

        foreach ($service->all() as $hook) {
            $service->delete($hook);
            error_log($hook->topic . ' Removed');
        };

    }

    private function get_all_hooks()
    {
        if (!$this->client)
            return false;

        $service = new Shopify\Service\WebhookService($this->client);
        return $service->all();
    }

}