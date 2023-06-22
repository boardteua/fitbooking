<?php


namespace fitPlugin\backend;

use Shopify;

class shopifyApi
{

    public $client;

    public $fit_booking_options;

    public function __construct()
    {

        $this->fit_booking_options = get_option('fit_booking_option_name');
        if ($this->fit_booking_options) {
            $this->client = new Shopify\PrivateApi(array(
                'api_version' => '2020-10/',
                'api_key' => array_key_exists('api_key_0', $this->fit_booking_options) ? $this->fit_booking_options['api_key_0'] : '%%',
                'password' => array_key_exists('api_pass_0', $this->fit_booking_options) ? $this->fit_booking_options['api_pass_0'] : '%%',
                'shared_secret' => array_key_exists('api_shared_secret_0', $this->fit_booking_options) ? $this->fit_booking_options['api_shared_secret_0'] : '%%',
                'myshopify_domain' => array_key_exists('api_shop_domain_0', $this->fit_booking_options) ? $this->fit_booking_options['api_shop_domain_0'] : '%%',
            ));
        } else {
            $this->client = false;
        }
    }

    public function order_cancel($id)
    {
        if (!$this->client)
            return false;

        $service = new Shopify\Service\OrderService($this->client);
        $order = $service->get($id);

        if (is_object($order)) {
            $params = [
                'reason' => 'customer',
                'email' => true,
                "amount" => $order->total_price,
                "currency" => $order->currency
            ];
            $req = $service->cancel($order, $params);
            return $req;
        } else {
            return $order;
        }
    }

    public function get_products()
    {
        if (!$this->client)
            return false;

        $service = new Shopify\Service\ProductService($this->client);

        $attr = array(
            'collection_id' => $this->fit_booking_options['collection_id_0'],
            'limit' => 250
        );

        if (false === ($req = get_transient('all_products_cache'))) {
            $req = $service->all($attr);
            set_transient('all_products_cache', $req, 120 + 20);
        }

        return $req; #Fetch all products, with optional params

    }

    public function get_price($id)
    {
        if (!$this->client)
            return false;

        $service = new Shopify\Service\ProductVariantService($this->client);

        if (false === ($req = get_transient('price_cache_' . $id))) {
            $req = $service->get($id)->price;
            set_transient('price_cache_' . $id, $req, 120 + 40);
        }

        return $req;
    }

    public function get_store_info()
    {
        if (!$this->client)
            return false;

        $service = new Shopify\Service\ShopService($this->client);

        if (false === ($req = get_transient('store_info_cache'))) {
            $req = $service->get();
            set_transient('store_info_cache', $req, 120 + 60);
        }

        return $req;
    }


}
