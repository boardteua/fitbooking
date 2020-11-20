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

        $this->client = new Shopify\PrivateApi(array(
            'api_version' => '2020-10/',
            'api_key' => $this->fit_booking_options['api_key_0'] ? $this->fit_booking_options['api_key_0'] : '0118036ed7bea77e1b194d46edfd6554',
            'password' => $this->fit_booking_options['api_pass_0'] ? $this->fit_booking_options['api_pass_0'] : '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',
            'shared_secret' => $this->fit_booking_options['api_shared_secret_0'] ? $this->fit_booking_options['api_shared_secret_0'] : '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',
            'myshopify_domain' => $this->fit_booking_options['api_shop_domain_0'] ? $this->fit_booking_options['api_shop_domain_0'] : 'org100h.myshopify.com',
        ));
    }

    public function get_products()
    {
        $service = new Shopify\Service\ProductService($this->client);

        $attr = array(
            'collection_id' => $this->fit_booking_options['collection_id_0'],
            'limit' => 250
        );
        return $service->all($attr); #Fetch all products, with optional params
    }


    public function get_price($id)
    {
        $service = new Shopify\Service\ProductVariantService($this->client);
        $product = $service->get($id); # Get a single product
        return $product->price;
    }

    public function get_store_info()
    {
        $service = new Shopify\Service\ShopService($this->client);
        return $service->get();
    }

}