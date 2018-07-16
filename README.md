# magento2-module
Swift gift integration module for magento 2.

## Manual installation

Go to your magento2 instance's dir: `cd <magento2_root_dir>`

Put plugin's code into `<magento2_root_dir>/app/code/Swiftgift/Gift`. 

Run `./bin/magento setup:upgrade`

Run `./bin/magento setup:di:compile`

Run `./bin/magento cache:flush`

Go to Magento's admin panel and configure SwiftGift plugin: Stores -> Configuration -> Swiftgift

You have to specify client secret obtained on [b2b.swiftgift.me](https://b2b.swiftgift.me) there.

Save configuration and run `./bin/magento cache:flush` again.
