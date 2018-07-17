# magento2-module
SwiftGift integration extension for Magento 2.x

## Manual installation

Go to your Magento 2.x directory: `cd <magento2_root_dir>`

Put the extension code into `<magento2_root_dir>/app/code/Swiftgift/Gift`.

Run `./bin/magento setup:upgrade`

Run `./bin/magento setup:di:compile`

Run `./bin/magento cache:flush`

Go to your Magento Admin Dashboard and configure SwiftGift extension at Stores -> Configuration -> SwiftGift.

You have to specify the Secret Key obtained at [b2b.swiftgift.me/registration](https://b2b.swiftgift.me/registration).

API base URL should remain `https://api.swiftgift.me/`

Save the configuration and run `./bin/magento cache:flush` again.
