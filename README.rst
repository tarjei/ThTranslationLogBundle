ThTranslationLogBundle
======================

Log symfony translation misses.

The purpose of this bundle is to log translation misses as a way to get the spots where a normal translation:update does not work.

**Do not enable this bundle for all your envs!**



Usage
-----

1. Enable this bundle in a controlled setting, for example a dev env or with an IP check:

.. code-block :: php
 <?php

    // in AppKernel::registerBundles()
    if ($this->getEnvironment() == 'dev') {
        $bundles[] = new TH\TranslationLogBundle\THTranslationLogBundle();
    }

2. Surf around, maybe create a Selenium of Behat test that runs throught your application for a given locale.

3. Run the extrator on the logs to get missing messages:

.. code-block ::

    app/console translation:process-miss-log

4. Go through the lists (found in app/cache/dev/missing-translations) and move them to the correct places.



