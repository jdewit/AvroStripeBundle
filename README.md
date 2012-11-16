AvroStripeBundle
================

A symfony2 bundle for interacting with the awesome Stripe payment service.

Features:
- Allow users to pay and receive money
- Subscribe a user to a plan
- Update a users plan
- View/print invoices & charges
- Create coupons
- Create plans

### Status
WIP 
[![Build Status](https://travis-ci.org/jdewit/AvroStripeBundle.png)](https://travis-ci.org/jdewit/AvroStripeBundle)

### Step 1: Download AvroStripeBundle using composer

Add AvroStripeBundle in your composer.json:

```js
{
    "require": {
        "jdewit/stripe-bundle": "*"
    }
}
```

Now tell composer to download the bundle by running the command:

``` bash
$ php composer.phar update jdewit/stripe-bundle
```

### Step 2: Enable the bundle

Enable the bundle in the kernel:

``` php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new Avro\StripeBundle\AvroStripeBundle(),
    );
}
```

### Step 3: Update your user class

``` php
<?php
namespace Application\UserBundle\Document;

use FOS\UserBundle\Document\User as BaseUser;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * @ODM\Document
 */
class User extends BaseUser
{
    /**
     * @ODM\Id(strategy="auto")
     */
    protected $id;

    /**
     * @ODM\String
     */
    protected $stripeCustomerId;

    /**
     * @ODM\Boolean
     */
    protected $isStripeCustomerActive;

    /**
     * @ODM\String
     */
    protected $stripeAccessToken;

    /**
     * @ODM\String
     */
    protected $stripePublishableKey;

    /**
     * @ODM\ReferenceOne(targetDocument="Avro\StripeBundle\Document\Plan")
     */
    protected $plan;


    public function __construct()
    {
        parent::__construct();
        // your own logic
    }

    public function getStripeAccessToken()
    {
        return $this->stripeAccessToken;
    }

    public function setStripeAccessToken($stripeAccessToken)
    {
        $this->stripeAccessToken = $stripeAccessToken;
        return $this;
    }

    public function getStripePublishableKey()
    {
        return $this->stripePublishableKey;
    }

    public function setStripePublishableKey($stripePublishableKey)
    {
        $this->stripePublishableKey = $stripePublishableKey;
        return $this;
    }

    public function getStripePublishableKey()
    {
        return $this->stripePublishableKey;
    }

    public function setStripePublishableKey($stripePublishableKey)
    {
        $this->stripePublishableKey = $stripePublishableKey;
        return $this;
    }
    public function getStripeCustomerId()
    {
        return $this->stripeCustomerId;
    }

    public function setStripeCustomerId($stripeCustomerId)
    {
        $this->stripeCustomerId = $stripeCustomerId;
        return $this;
    }

    public function getIsStripeCustomerActive()
    {
        return $this->isStripeCustomerActive;
    }

    public function setIsStripeCustomerActive($isStripeCustomerActive)
    {
        $this->isStripeCustomerActive = $isStripeCustomerActive;
        return $this;
    }

    public function getPlan()
    {
        return $this->plan;
    }

    public function setPlan(\Avro\StripeBundle\Document\Plan $plan)
    {
        $this->plan = $plan;
        return $this;
    }

    /**
     * Get user information to prefill stripe signup form
     */
    public function getStripeConnectionParameters()
    {
        return array(
            'stripe_user[email]' => $user->getEmail() ?: '',
            //'stripe_user[url]' => $user->getWebsite() ?: '',
            //'stripe_user[phone_number]' => $user->getPhone() ?: '',
            //'stripe_user[business_name]' => $user->getCompany() ?: '',
            //'stripe_user[first_name]' => $user->getFirstName() ?: '',
            //'stripe_user[last_name]' => $user->getLastName() ?: '',
            //'stripe_user[street_address]' => $user->getAddress() ?: '',
            //'stripe_user[city]' => $user->getCity() ? $user->getCity()->getName() : '',
            //'stripe_user[state]' => $user->getProvince() ? $user->getProvince()->getName() : '',
            //'stripe_user[country]' => $user->getCountry() ? $user->getCountry()->getName() : '',
            //'stripe_user[zip]' => $user->getPostalCode() ?: '',
        );
    }
}

### Step 5: Extend the bundle

Create a bundle skeleton that extends this bundle 

``` php
namespace Application\StripeBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class ApplicationStripeBundle extends Bundle
{
    public function getParent()
    {
        return 'AvroStripeBundle';
    }
}
```

### Step 6: Create a plan class 
The plan class is a superclass which needs to be extended. This allows you to add custom methods such as usage limits etc...

``` php
<?php
//Application/StripeBundle/Document/Plan
namespace Application\StripeBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Symfony\Component\Validator\Constraints as Assert;

use Avro\StripeBundle\Document\Plan as BasePlan;

/**
 * @ODM\Document
 */
class Plan extends BasePlan
{
    /**
     * @ODM\Id(strategy="none")
     */
    public $id;

// customize to your needs, not required
//    /**
//     * @ODM\Int
//     */
//    protected $limit;
//
//    /**
//     * @ODM\Boolean
//     */
//    protected $phoneSupport = false;

    /**
     * Get id
     *
     * @return id $id
     */
    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }
}
```

### Step 7: Configure your application's security.yml

Make sure the routes are accessible only to authenticated users

``` yaml
# app/config/security.yml
security:
    access_control:
        - { path: ^/stripe/, role: ROLE_USER }
```

### Step 8: Configure the AvroStripeBundle

Add your Stripe API keys to your `config.yml` 

``` yaml
# app/config/config.yml

avro_stripe:
#required
    client_id: %stripe_client_id% // define these in your parameters.yml and parameters_prod.yml
    secret_key: %stripe_secret_key%
    publishable_key: %stripe_publishable_key%

#optional
    db_driver: mongodb # other storage is yet to be implemented by... you?
    hooks_enabled: false #use bundles default hook events (send emails etc...)
    prorate: false #prorate updated subscription charges 
    redirect_routes: #set routes to redirect to, default is to redirect to homepage 
        customer_new: application_user_settings_payment
        customer_update: application_user_settings_payment
        customer_disable: application_user_settings_payment
        subscription_update: application_user_settings_subscription
        account_confirm: application_user_settings_payment
        account_disconnect: application_user_settings_payment
```

### Step 9: Import AvroStripeBundle routing files

Import the AvroStripeBundle routing files.

In YAML:

``` yaml
# app/config/routing.yml
avro_stripe:
    resource: "@AvroStripeBundle/Resources/config/routing/routing.yml"
```

### Step 10: Update your database schema

Now that the bundle is configured, the last thing you need to do is update your
database schema.

For MongoDB, you can run the following command to create the indexes.

``` bash
$ php app/console doctrine:mongodb:schema:create --index
```

### Hooks
The bundle receives hooks at "/stripe/hook" and dispatches the event which you can listen for

for example.

the 'charge.succeeded' event is dispatched as 'avro_stripe.charge.succeeded' by the HookController

###Notes

If you wish to use default texts provided in this bundle, you have to make
sure you have translator enabled in your config.

``` yaml
# app/config/config.yml

framework:
    translator: ~
```

In order to use the built-in email functionality, you must activate and configure the SwiftmailerBundle.

### Next Steps


