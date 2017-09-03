# Symfony2 Message
Integrates messages (mailer, sms api) into Symfony2 project.

## Installation

1. Add as composer dependency:

  ```bash
  composer require jasuwienas/message
  ```
2. Add in application kernel:

  ```php
  class AppKernel extends Kernel
  {
      public function registerBundles()
      {
      //...
      $bundles[] = new \Jasuwienas\MessageBundle\MessageBundle();
      return $bundles;
      }
  }
  ```
3. Message sending command:
php app/console messages:send