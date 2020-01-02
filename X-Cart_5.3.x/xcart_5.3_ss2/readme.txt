====================================================
PlugnPay Smart Screens v2
Payment Module for X-Cart 5.3.x
====================================================

***** IMPORTANT NOTES *****
This module is being provided "AS IS".  Limited technical support assistance will
be given to help diagnose/address problems with this module.  The amount of
support provided is up to PlugnPay's staff.

It is recommended if you experience a problem with this module, first seek
assistance through this module's readme file, then check with the X-Cart community
at 'www.x-cart.com', and if you are still unable to resolve the issue, contact us
via PlugnPay's Online Helpdesk.

This module doesn't require your server to have SSL abilities, but it's recommended.
PlugnPay will ask the customer for all of credit card info on our server's SSL secured
billing pages. The authorization will be done via PlugnPay's Smart Screens v2 payment method.

At this time, the module does not intended to itemize the order within PlugnPay's system
using available information from the cart.  This will likely be addressed in future
releases to this payment module.

If you want to change the behavior of this module, please feel free to make changes
to the files yourself.  However, customized X-Cart modules will not be provided
support assistance.
***************************

This module is designed to use PlugnPay's Smart Screens v2 payment method.
It's designed for to process credit cards, ACH/eChecks & other payment types allowed.
Please use other PlugnPay modules for alternative payment abilities.

This module is intended to charge customer's order as a single payment.
This single payment is applied at time of the customer's purchase.


Installation:

1. Unzip the coments of this zip file into the root directory of X-Cart on your server.

* Ensure you preserve the folder structure, so that the files are dropped into the correct locations.

They should be as follows:
classes/XLite/Module/PlugnPay/Payment/Model/Payment/Processor/SS2Payment.php
classes/XLite/Module/PlugnPay/Payment/Model/install.yaml
classes/XLite/Module/PlugnPay/Payment/Model/Main.php
skins/admin/modules/PlugnPay/Payment/config.twig

2. Push the YAML file change to the X-Cart database.
(https://devs.x-cart.com/getting_started/x-cart_sdk.html#loading-yaml-file)

3. Re-deploy the store in order to see the module in your Admin area.

4. Go to the 'Store setup' > 'Payment methods' section in the Admin area, click the 'Add payment method' button there

5. Search for the keyword 'PlugnPay' in the given form and then add the 'PlugnPay SSv2' payment method by clicking on its 'Add' button

6. After the payment method has been added, you need to enable it by clicking on the 'Inactive/Active' slider & set this to 'Active'

7. Set your PnP account username in the settings for this module.


Test your payment module:

1. Go to your storefront, add some products to cart and proceed to checkout.
(If using PlugnPay Testing Mode, ensure the total amount is under 1000.00)

2. Proceed through the checkout process normally & the new option will appear in the 'Payment Methods' step.

3. Finally, click the Place Order button and submit the order.

4. You should be redirected to our payment gateway to complete the payment.

5. Complete the checkout on that end normally & submit your payment for authorization.
(If using our test card, ensure the Testing Mode is active on your PnP account & you follow all of the Testing Mode's usage instructions before submitting your payment)

6. Assuming the payment is successful, you should see a Thank you page, and the status of this new order should be set to Paid.


If you run into problems:

Check to be sure you actually uploaded the files in the correct folders.

Check the uploaded file's permissions:
-- .php files should be chmod 755
   (read/write/execute by owner, read/execute by all others)
-- all other files should be chmod 644
   (read/write by owner, read by all others)

When processing a transaction and it fails, there should be a PnP generated error message in the response to your shopping cart.
This would tell the customer why PnP could not process the order.  If this is blank, then you should check your cart/connection. 

Should any customizations not appear appear, after initially installing the payment module:
- clear your browser's cache
- re-submit the install.yaml file to X-Cart's database
- ensure you re-deplay your site, before attempting to try again


History:

01/02/2020
- initial release


